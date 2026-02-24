<?php

namespace Modules\CTWA\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\FacebookLeads;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\CTWA\Models\CtwaAd;
use App\Models\FacebookAd;
use App\Models\ScheduledCampaign;
use Modules\CTWA\Models\CtwaCampaign;
use Modules\CTWA\Models\CtwaMessage;
use Modules\CTWA\Models\CTWAAdsClickLead;
use App\Models\Config;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Modules\Contacts\Models\Contact;

class CTWAController extends Controller
{
    /** Current company ID (session or logged-in user's company). */
    private function companyId()
    {
        $user = auth()->user();
        return $user ? (session('company_id') ?? $user->company_id) : null;
    }

    /** User IDs belonging to the current company (for scoping ads/data to company only). */
    private function companyUserIds()
    {
        $cid = $this->companyId();
        if (!$cid) {
            return collect();
        }
        return User::where('company_id', $cid)->pluck('id');
    }

    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }

        $token = $user->fb_long_lived_token;
        $companyUserIds = $this->companyUserIds();
        $query = FacebookAd::whereIn('user_id', $companyUserIds);

        $allowedAdAccountIds = [];
        $selectedAccountId = null;

        // Restrict to ad accounts the current user's token can access (avoid showing "all" accounts)
        if ($token) {
            $accountsResponse = Http::timeout(15)->get("https://graph.facebook.com/v22.0/me/adaccounts", [
                'fields' => 'id,name,account_status',
                'access_token' => $token,
            ]);
            $accountsList = $accountsResponse->json('data') ?? [];
            $allowedAdAccountIds = collect($accountsList)->pluck('id')->filter()->values()->toArray();
            if (!empty($allowedAdAccountIds)) {
                $companyId = $this->companyId();
                $selectedAccountId = $companyId ? Config::where('key', 'ctwa_selected_ad_account_id')
                    ->where('model_type', Company::class)
                    ->where('model_id', $companyId)
                    ->value('value') : null;
                if ($selectedAccountId && in_array($selectedAccountId, $allowedAdAccountIds, true)) {
                    $query->where('ad_account_id', $selectedAccountId);
                } else {
                    $query->whereIn('ad_account_id', $allowedAdAccountIds);
                }
            }
        }

        if (request()->has('search') && !empty(request('search'))) {
            $query->where('ad_name', 'like', '%' . request('search') . '%');
        }

        $ads = $query->latest('ad_created_at')->paginate(10)->withQueryString();

        // Keep totals lightweight on index to avoid slow loads / timeouts.
        // Detailed insights can be fetched on-demand from other endpoints.
        $finalTotals = [
            'impressions' => 0,
            'reach'      => 0,
            'spend'      => 0,
            'chats'      => 0,
            'leads'      => 0,
            'clicks'     => 0,
        ];

        return view('ctwa::ctwa', compact('ads', 'finalTotals'));
    }

    public function panel()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }

        $companyUserIds = $this->companyUserIds();
        $adIds = collect();
        $adIds = $adIds->merge(FacebookAd::whereIn('user_id', $companyUserIds)->pluck('ad_id'));
        $adIds = $adIds->merge(CtwaAd::whereIn('user_id', $companyUserIds)->pluck('ad_id'));
        $adIds = $adIds->unique()->values();

        $adsCount = FacebookAd::whereIn('user_id', $companyUserIds)->count();
        $ctwaOnlyCount = CtwaAd::whereIn('user_id', $companyUserIds)
            ->whereNotIn('ad_id', FacebookAd::whereIn('user_id', $companyUserIds)->pluck('ad_id'))
            ->count();
        $adsCount = $adsCount + $ctwaOnlyCount;

        $companyId = $this->companyId();
        $leadsCount = 0;
        if ($adIds->isNotEmpty()) {
            $leadsCount = CTWAAdsClickLead::where('company_id', $companyId)
                ->whereIn('source_id', $adIds)
                ->count();
        }

        // Keep panel fast: avoid looping over many ads with external API calls here.
        $insights = ['impressions' => 0, 'reach' => 0, 'spend' => 0];

        return view('ctwa::panel', compact('adsCount', 'leadsCount', 'insights'));
    }

    public function leadsIndex()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }

        $companyId = $this->companyId();
        $companyUserIds = $this->companyUserIds();
        $adIds = collect(FacebookAd::whereIn('user_id', $companyUserIds)->pluck('ad_id'))
            ->merge(CtwaAd::whereIn('user_id', $companyUserIds)->pluck('ad_id'))
            ->unique()
            ->values()
            ->toArray();

        $query = CTWAAdsClickLead::query()
            ->select('ctwa_ads_click_leads.*', \DB::raw('COALESCE(fb.ad_name, ctwa.ad_name) as ad_name'))
            ->leftJoin('facebook_ads as fb', 'ctwa_ads_click_leads.source_id', '=', 'fb.ad_id')
            ->leftJoin('ctwa_ads as ctwa', 'ctwa_ads_click_leads.source_id', '=', 'ctwa.ad_id')
            ->where('ctwa_ads_click_leads.company_id', $companyId);

        if (!empty($adIds)) {
            $query->whereIn('ctwa_ads_click_leads.source_id', $adIds);
        } else {
            $query->whereRaw('1 = 0'); // no ads = no leads from user's ads
        }

        if (request()->filled('source_id')) {
            $query->where('ctwa_ads_click_leads.source_id', request('source_id'));
        }
        if (request()->filled('search')) {
            $s = request('search');
            $query->where(function ($q) use ($s) {
                $q->where('ctwa_ads_click_leads.source_id', 'like', "%{$s}%")
                    ->orWhere('ctwa_ads_click_leads.wa_id', 'like', "%{$s}%")
                    ->orWhere('fb.ad_name', 'like', "%{$s}%")
                    ->orWhere('ctwa.ad_name', 'like', "%{$s}%");
            });
        }

        $leads = $query->with('contact')->latest('ctwa_ads_click_leads.created_at')->paginate(20)->withQueryString();
        $companyUserIds = $this->companyUserIds();
        $fbAds = FacebookAd::whereIn('user_id', $companyUserIds)->orderBy('ad_name')->get(['ad_id', 'ad_name']);
        $fbAdIds = $fbAds->pluck('ad_id');
        $ctwaAds = CtwaAd::whereIn('user_id', $companyUserIds)->whereNotIn('ad_id', $fbAdIds)->orderBy('ad_name')->get(['ad_id', 'ad_name']);
        $adsForFilter = $fbAds->merge($ctwaAds)->sortBy('ad_name')->values();

        return view('ctwa::leads', compact('leads', 'adsForFilter'));
    }

    public function show($adId)
    {
        $user = auth()->user();

        $ad = FacebookAd::where('ad_id', $adId)->whereIn('user_id', $this->companyUserIds())->firstOrFail();
        $companyId = $this->companyId();
        $CTWAAdsClickLead = CTWAAdsClickLead::where('company_id', $companyId)->where('source_id', $adId)->get();

        $token = $user->fb_long_lived_token;

        $insights = Http::get("https://graph.facebook.com/v22.0/{$ad->ad_id}/insights", [
            'access_token' => $token,
            'fields' => 'impressions,spend,reach,cpc,clicks,unique_clicks,inline_link_clicks,actions',
            'date_preset' => 'last_30d',
        ])->json('data.0') ?? [];

        $adDetails = Http::get("https://graph.facebook.com/v22.0/{$ad->ad_id}", [
            'access_token' => $token,
            'fields' => 'name,status,adset_id,campaign_id,created_time,updated_time,adcreatives{id,name}'
        ])->json();

        $adsetId = $adDetails['adset_id'] ?? null;
        $campaignId = $adDetails['campaign_id'] ?? null;
        $creativeId = $adDetails['adcreatives']['data'][0]['id'] ?? null;

        $adSetDetails = Http::get("https://graph.facebook.com/v22.0/{$adsetId}", [
            'access_token' => $token,
            'fields' => 'name,effective_status,daily_budget,start_time,end_time,targeting'
        ])->json();

        $targeting = $adSetDetails['targeting'] ?? [];
        $age_from = $targeting['age_min'] ?? '18';
        $age_to = $targeting['age_max'] ?? '65';
        $gender = $targeting['genders'][0] ?? 'All';

        $genderText = match($gender) {
            1 => 'Male',
            2 => 'Female',
            default => 'All',
        };

        $campaignDetails = Http::get("https://graph.facebook.com/v22.0/{$campaignId}", [
            'access_token' => $token,
            'fields' => 'name,status,effective_status,objective,buying_type,start_time,stop_time'
        ])->json();

        $formId = null;
        if ($creativeId) {
            $creativeDetails = Http::get("https://graph.facebook.com/v22.0/{$creativeId}", [
                'access_token' => $token,
                'fields' => 'object_story_spec{link_data{call_to_action}}'
            ])->json();

            $formId = $creativeDetails['object_story_spec']['link_data']['call_to_action']['value']['lead_gen_form_id'] ?? null;
        }

        $leads = [];

        if ($formId) {
            $url = "https://graph.facebook.com/v22.0/{$ad->ad_id}/leads?access_token={$token}&limit=100";

            do {
                $response = Http::get($url);

                if (!$response->successful()) break;

                $json = $response->json();
                $rawLeads = $json['data'] ?? [];

                foreach ($rawLeads as $lead) {
                    $leadDetails = [
                        'created_time' => $lead['created_time'] ?? now(),
                        'fields' => collect($lead['field_data'] ?? [])->mapWithKeys(function ($field) {
                            return [$field['name'] => $field['values'][0] ?? null];
                        })->toArray()
                    ];
                    $leads[] = $leadDetails;
                }

                $url = $json['paging']['next'] ?? null;
            } while ($url);
        }

        $templates = Template::where('company_id', $this->companyId())->get();

        $summary = [
            'ad' => $ad,
            'CTWAAdsClickLead' => $CTWAAdsClickLead,
            'ad_name' => $adDetails['name'] ?? '',
            'ad_status' => $adDetails['status'] ?? '',
            'ad_created' => $adDetails['created_time'] ?? '',
            'ad_updated' => $adDetails['updated_time'] ?? '',
            'age_from' => $age_from,
            'age_to' => $age_to,
            'gender' => $genderText,
            'daily_budget' => $adSetDetails['daily_budget'] ?? '',
            'start_time' => $adSetDetails['start_time'] ?? '',
            'end_time' => $adSetDetails['end_time'] ?? '',
            'campaign_name' => $campaignDetails['name'] ?? '',
            'campaign_status' => $campaignDetails['status'] ?? '',
            'campaign_objective' => $campaignDetails['objective'] ?? '',
            'campaign_start' => $campaignDetails['start_time'] ?? '',
            'campaign_stop' => $campaignDetails['stop_time'] ?? '',
        ];

        return view('ctwa::partials.ad_summary', compact('summary', 'insights', 'leads', 'templates', 'CTWAAdsClickLead'));
    }

    public function filter(Request $request)
    {
        $start = Carbon::parse($request->start)->startOfDay()->toIso8601String();
        $end = Carbon::parse($request->end)->endOfDay()->toIso8601String();

        $user = auth()->user();
        $token = $user->fb_long_lived_token;

        $ads = FacebookAd::whereIn('user_id', $this->companyUserIds())->get();
        $total = 0;
        $new = 0;
        $existing = 0;

        foreach ($ads as $ad) {
            $response = Http::get("https://graph.facebook.com/v22.0/{$ad->ad_id}/leads", [
                'access_token' => $token,
                'limit' => 100,
            ]);

            if ($response->successful()) {
                $leads = $response->json()['data'] ?? [];

                foreach ($leads as $lead) {
                    $createdAt = Carbon::parse($lead['created_time']);
                    if ($createdAt >= $start && $createdAt <= $end) {
                        $total++;
                        $existingEmail = FacebookLeads::where('lead_id', $lead['id'])->exists();
                        $existingEmail ? $existing++ : $new++;
                    }
                }
            }
        }

        return response()->json([
            'total' => $total,
            'new' => $new,
            'existing' => $existing,
        ]);
    }

    public function fetchAndStoreAds(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }

        $fbToken = $request->input('fbtoken') ?? $user->fb_long_lived_token;
        $appId = $request->input('client_id') ?? $user->client_id;

        if (!$fbToken || !$appId) {
            return redirect()->back()->withErrors('Facebook App ID or token is missing.');
        }

        try {
            $accountsResponse = Http::get("https://graph.facebook.com/v22.0/me/adaccounts", [
                'fields' => 'id,name,account_status',
                'access_token' => $fbToken,
            ]);

            $accounts = $accountsResponse->json()['data'] ?? [];

            if (empty($accounts)) {
                return redirect()->back()->withErrors('No ad accounts found for this token.');
            }

            foreach ($accounts as $account) {
                $adAccountId = str_replace('act_', '', $account['id']);

                $campaignsResponse = Http::get("https://graph.facebook.com/v22.0/act_{$adAccountId}/campaigns", [
                    'fields' => 'id,name,status',
                    'access_token' => $fbToken,
                ]);

                $campaigns = $campaignsResponse->json()['data'] ?? [];

                foreach ($campaigns as $campaign) {
                    $adsResponse = Http::get("https://graph.facebook.com/v22.0/{$campaign['id']}/ads", [
                        'fields' => 'id,name,status,created_time,creative',
                        'access_token' => $fbToken,
                    ]);

                    $ads = $adsResponse->json()['data'] ?? [];

                    foreach ($ads as $ad) {
                        FacebookAd::updateOrCreate(
                            ['ad_id' => $ad['id']],
                            [
                                'user_id' => $user->id,
                                'campaign_id' => $campaign['id'],
                                'campaign_name' => $campaign['name'],
                                'ad_name' => $ad['name'],
                                'status' => $ad['status'],
                                'ad_account' => $account['name'],
                                'ad_account_id' => $account['id'],
                                'ad_created_at' => \Carbon\Carbon::parse($ad['created_time'])->toDateString(),
                                'creative' => $ad['creative'] ?? [],
                            ]
                        );
                    }
                }
            }

            return redirect()->route('ctwa.index')->with('success', 'Ads fetched and stored successfully.');

        } catch (\Exception $e) {
            \Log::error('FB Ads fetch failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null,
            ]);

            return redirect()->back()->withErrors('Something went wrong while fetching ads.');
        }
    }

    /**
     * Fetch leads from Meta Graph API for the user's ads and store in ctwa_ads_click_leads.
     */
    public function fetchAndStoreLeads(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }

        $token = $user->fb_long_lived_token;
        if (!$token) {
            return redirect()->back()->withErrors('Facebook token is missing. Please connect your Meta account.');
        }

        $companyId = $this->companyId();
        $companyUserIds = $this->companyUserIds();
        $adIds = collect(FacebookAd::whereIn('user_id', $companyUserIds)->pluck('ad_id'))
            ->merge(CtwaAd::whereIn('user_id', $companyUserIds)->pluck('ad_id'))
            ->unique()
            ->values();

        if ($adIds->isEmpty()) {
            return redirect()->back()->withErrors('No ads found. Fetch ads first from the CTWA dashboard.');
        }

        $stored = 0;
        $skipped = 0;
        $errors = [];

        foreach ($adIds as $adId) {
            $nextUrl = "https://graph.facebook.com/v22.0/{$adId}/leads?access_token=" . urlencode($token) . "&limit=100";

            do {
                $response = Http::get($nextUrl);
                if (!$response->successful()) {
                    $errors[] = "Ad {$adId}: " . ($response->json('error.message') ?? $response->reason());
                    break;
                }

                $json = $response->json();
                $rawLeads = $json['data'] ?? [];

                foreach ($rawLeads as $lead) {
                    $metaLeadId = $lead['id'] ?? null;
                    if (!$metaLeadId) {
                        $skipped++;
                        continue;
                    }

                    if (CTWAAdsClickLead::where('meta_lead_id', $metaLeadId)->exists()) {
                        $skipped++;
                        continue;
                    }

                    $fieldData = collect($lead['field_data'] ?? [])->mapWithKeys(function ($field) {
                        return [$field['name'] => $field['values'][0] ?? null];
                    })->toArray();

                    $phone = $this->normalizePhone($fieldData['phone_number'] ?? $fieldData['phone'] ?? $fieldData['full_phone_number'] ?? null);
                    $waId = $phone ?: ('meta_lead_' . $metaLeadId);
                    $email = $fieldData['email'] ?? null;
                    $name = $fieldData['full_name'] ?? $fieldData['first_name'] ?? $fieldData['last_name'] ?? null;
                    $name = is_array($name) ? trim(implode(' ', $name)) : (string) $name;
                    if (empty($name) && !empty($fieldData['first_name'])) {
                        $name = trim(($fieldData['first_name'] ?? '') . ' ' . ($fieldData['last_name'] ?? ''));
                    }

                    $contactId = null;
                    if ($phone) {
                        $normalized = (strpos($phone, '+') === 0) ? $phone : ('+' . $phone);
                        $contact = Contact::where('company_id', $companyId)
                            ->where(function ($q) use ($phone, $normalized) {
                                $q->where('phone', $phone)->orWhere('phone', $normalized);
                            })
                            ->first();
                        if (!$contact) {
                            $contact = Contact::create([
                                'company_id' => $companyId,
                                'phone' => $normalized,
                                'name' => $name ?: ('Lead ' . substr($metaLeadId, -6)),
                            ]);
                        }
                        $contactId = $contact->id;
                    }

                    try {
                        CTWAAdsClickLead::create([
                            'company_id' => $companyId,
                            'contact_id' => $contactId,
                            'source_id' => $adId,
                            'source_type' => 'facebook',
                            'source_url' => $lead['ad_id'] ?? null,
                            'wa_id' => $waId,
                            'meta_lead_id' => $metaLeadId,
                        ]);
                        $stored++;
                    } catch (\Exception $e) {
                        Log::warning('CTWA lead store failed', ['meta_lead_id' => $metaLeadId, 'error' => $e->getMessage()]);
                        $skipped++;
                    }
                }

                $nextUrl = $json['paging']['next'] ?? null;
            } while ($nextUrl);
        }

        $message = "Fetched leads: {$stored} new, {$skipped} skipped or already stored.";
        if (!empty($errors)) {
            $message .= ' Some ads had errors: ' . implode('; ', array_slice($errors, 0, 3));
        }

        return redirect()->route('ctwa.leads')->with('success', $message);
    }

    private function normalizePhone($value)
    {
        if (empty($value) || !is_string($value)) {
            return null;
        }
        $digits = preg_replace('/\D/', '', trim($value));
        return $digits === '' ? null : ('+' . $digits);
    }

    public function create_ads()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }

        return view('ctwa::createads', ['token' => $user->fb_long_lived_token ?? '']);
    }

    public function searchMetaInterests(Request $request)
    {
        $searchTerm = $request->get('q');
        $token = auth()->user()->fb_long_lived_token;

        if (!$token) {
            return response()->json(['error' => 'Meta token missing'], 403);
        }

        $response = Http::get('https://graph.facebook.com/v22.0/search', [
            'type' => 'adinterest',
            'q' => $searchTerm,
            'access_token' => $token,
        ]);

        return $response->json();
    }

    public function getUserPages()
    {
        $user = auth()->user();
        $token = $user->fb_long_lived_token;

        if (!$token) {
            return response()->json(['error' => 'Token missing'], 403);
        }

        $response = Http::get("https://graph.facebook.com/v22.0/me/accounts", [
            'access_token' => $token,
        ]);

        if ($response->failed() || empty($response['data'])) {
            return response()->json(['error' => 'Failed to fetch pages'], 500);
        }

        $pages = collect($response['data'])->map(function ($page) {
            return [
                'id' => $page['id'],
                'name' => $page['name'],
                'access_token' => $page['access_token'],
            ];
        });

        return response()->json(['pages' => $pages]);
    }

    public function getMetaProfileFromSelection(Request $request)
    {
        $pageId = $request->input('page_id');
        $pageToken = $request->input('page_token');

        if (!$pageId || !$pageToken) {
            return response()->json(['error' => 'Page ID or token missing'], 400);
        }

        $profileResponse = Http::get("https://graph.facebook.com/v22.0/{$pageId}", [
            'fields' => 'name,picture',
            'access_token' => $pageToken,
        ]);

        if ($profileResponse->failed()) {
            return response()->json(['error' => 'Failed to fetch profile'], 500);
        }

        return response()->json([
            'name' => $profileResponse['name'],
            'picture' => $profileResponse['picture']['data']['url'],
        ]);
    }

    public function getMetaAdAccounts()
    {
        $user = auth()->user();
        $token = $user->fb_long_lived_token;

        if (!$token) {
            return response()->json(['error' => 'Token missing'], 403);
        }

        $response = Http::get("https://graph.facebook.com/v22.0/me/adaccounts", [
            'access_token' => $token,
            'fields' => 'id,name',
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Graph API call failed',
                'details' => $response->json()
            ], 500);
        }

        $data = $response->json();

        if (empty($data['data'])) {
            return response()->json(['error' => 'No ad accounts found'], 404);
        }

        return response()->json($data['data']);
    }

    public function getCountries(Request $request)
    {
        $user = auth()->user();
        $token = $user->fb_long_lived_token;

        if (!$token) {
            return response()->json(['error' => 'Meta token not found'], 500);
        }

        $countries = [];
        $seen = [];

        foreach (range('a', 'z') as $letter) {
            $response = Http::get('https://graph.facebook.com/v22.0/search', [
                'type' => 'adgeolocation',
                'location_types' => 'country',
                'q' => $letter,
                'access_token' => $token,
            ]);

            if ($response->ok() && isset($response['data'])) {
                foreach ($response['data'] as $item) {
                    $key = $item['key'] ?? null;
                    $code = $item['country_code'] ?? null;

                    if ($key && $code && !isset($seen[$code])) {
                        $seen[$code] = true;
                        $countries[] = [
                            'id' => $code,
                            'text' => $item['name'] . ' (' . $code . ')',
                        ];
                    }
                }
            }
        }

        return response()->json(['results' => array_values($countries)]);
    }

    public function getLocations(Request $request)
    {
        $user = auth()->user();
        $token = $user->fb_long_lived_token;

        if (!$token) {
            return response()->json(['error' => 'Meta token not found'], 500);
        }

        $query = $request->get('q', 'a');
        $locations = [];
        $seen = [];

        $response = Http::get('https://graph.facebook.com/v22.0/search', [
            'type' => 'adgeolocation',
            'location_types' => 'country,region,city',
            'q' => $query,
            'access_token' => $token,
        ]);

        if ($response->ok() && isset($response['data'])) {
            foreach ($response['data'] as $item) {
                $uniqueKey = $item['key'] . '_' . $item['type'];
                if (!isset($seen[$uniqueKey])) {
                    $seen[$uniqueKey] = true;

                    $label = $item['name'];
                    if (!empty($item['region'])) {
                        $label .= ', ' . $item['region'];
                    }
                    if (!empty($item['country_name'])) {
                        $label .= ', ' . $item['country_name'];
                    }

                    $locationId = $item['type'] === 'country'
                        ? ($item['country_code'] ?? $item['key'])
                        : $item['key'];

                    $locations[] = [
                        'id' => $locationId,
                        'text' => $label . ' (' . ucfirst($item['type']) . ')',
                        'type' => $item['type'],
                    ];
                }
            }
        } else {
            return response()->json([
                'error' => 'Failed to fetch locations',
                'details' => $response->json(),
            ], 500);
        }

        return response()->json(['results' => $locations]);
    }

    public function submitCtwaAd(Request $request)
    {
        $user = auth()->user();
        $token = $user->fb_long_lived_token;

        if (!$token) {
            return response()->json(['error' => 'Facebook token missing'], 401);
        }

        $mediaFile = $request->file('mediaFile');
        if (!$mediaFile || !$mediaFile->isValid()) {
            return response()->json(['error' => 'Invalid or missing media file'], 400);
        }

        $isVideo = Str::contains($mediaFile->getMimeType(), 'video');
        $adAccountId = ltrim($request->ad_account_id);
        $adAccountPrefixed = "{$adAccountId}";

        $uploadUrl = $isVideo
            ? "https://graph.facebook.com/v22.0/{$adAccountPrefixed}/advideos"
            : "https://graph.facebook.com/v22.0/{$adAccountPrefixed}/adimages";

        $uploadRes = Http::attach('source', file_get_contents($mediaFile), $mediaFile->getClientOriginalName())
            ->post($uploadUrl, ['access_token' => $token]);

        if ($uploadRes->failed()) {
            return response()->json([
                'error' => 'Media upload failed',
                'details' => $uploadRes->json(),
            ], 500);
        }

        $mediaId = $isVideo
            ? $uploadRes['id'] ?? null
            : $uploadRes['images'][$mediaFile->getClientOriginalName()]['hash'] ?? null;

        if (!$mediaId) {
            return response()->json(['error' => 'Invalid media ID returned from upload'], 500);
        }

        $campaignRes = Http::post("https://graph.facebook.com/v22.0/{$adAccountPrefixed}/campaigns", [
            'name' => $request->adName,
            'objective' => 'OUTCOME_ENGAGEMENT',
            'status' => 'PAUSED',
            'special_ad_categories' => ['NONE'],
            'access_token' => $token,
        ]);

        if (!isset($campaignRes['id'])) {
            return response()->json([
                'error' => 'Campaign creation failed',
                'details' => $campaignRes->json(),
            ], 500);
        }

        $campaignId = $campaignRes['id'];

        $geoTargetingJson = $request->input('geo_targeting');
        $decoded = is_string($geoTargetingJson) ? json_decode($geoTargetingJson, true) : (array) $geoTargetingJson;

        $countries = collect($decoded)
            ->filter(fn($code) => is_string($code) && strlen($code) === 2)
            ->map(fn($code) => strtoupper($code))
            ->unique()
            ->values()
            ->toArray();

        if (empty($countries)) {
            return response()->json([
                'error' => 'At least one valid 2-letter country code must be selected for targeting.'
            ], 422);
        }

        $ageMin = max((int) $request->ageFrom, 18);
        $ageMax = max($ageMin, (int) $request->ageTo);

        $targeting = [
            'geo_locations' => ['countries' => $countries],
            'age_min' => $ageMin,
            'age_max' => $ageMax,
            'genders' => match ($request->gender) {
                'male' => [1],
                'female' => [2],
                default => [1, 2],
            },
        ];

        if (!empty($request->interests)) {
            $targeting['interests'] = collect($request->interests)->map(fn($id) => ['id' => $id])->toArray();
        }

        $dailyBudgetInput = intval($request->input('dailyBudget', 0));
        if ($dailyBudgetInput <= 0) {
            return response()->json(['error' => 'Please enter a valid daily budget amount greater than ₹0.'], 400);
        }

        $startDate = Carbon::parse($request->durationPicker)->addMinutes(5);
        $durationDays = max((int) $request->durationSlider, 1);
        $endDate = $startDate->copy()->addDays($durationDays);

        $adSetRes = Http::post("https://graph.facebook.com/v22.0/{$adAccountPrefixed}/adsets", [
            'name' => 'Ad Set - ' . now()->format('Ymd_His'),
            'daily_budget' => $dailyBudgetInput * 100,
            'campaign_id' => $campaignId,
            'billing_event' => 'IMPRESSIONS',
            'optimization_goal' => 'IMPRESSIONS',
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'destination_type' => 'WHATSAPP',
            'targeting' => json_encode($targeting),
            'start_time' => $startDate->toIso8601String(),
            'end_time' => $endDate->toIso8601String(),
            'status' => 'PAUSED',
            'access_token' => $token,
            'promoted_object' => ['page_id' => $request->page_id],
        ]);

        $data = $adSetRes->json();
        if (!isset($data['id'])) {
            return response()->json(['error' => 'Ad Set creation failed', 'details' => $data], 500);
        }
        $adSetId = $data['id'];

        $companyId = $this->companyId();
        $whatsappNumber___ = Config::where('key', 'whatsapp_number')
            ->where('model_type', Company::class)
            ->where('model_id', $companyId)
            ->value('value');
        if ($whatsappNumber___ === null) {
            $whatsappNumber___ = Config::where('key', 'whatsapp_number')
                ->where('model_type', \App\Models\User::class)
                ->where('model_id', Auth::id())
                ->value('value');
        }

        $whatsappNumber = preg_replace('/\D/', '', $whatsappNumber___ ?? '');

        if (strlen($whatsappNumber) < 8) {
            return response()->json(['error' => 'Invalid WhatsApp number format'], 422);
        }

        $prefilledMessage = $request->prefilled_message ?? '';
        $ctaUrl = "https://wa.me/{$whatsappNumber}" .
            ($prefilledMessage ? "?text=" . urlencode($prefilledMessage) : "");

        $linkData = [
            'message' => $request->adName ?? 'Chat with us!',
            'link' => $ctaUrl,
            'call_to_action' => [
                'type' => 'WHATSAPP_MESSAGE',
                'value' => [
                    'app_destination' => 'WHATSAPP',
                    'link' => $ctaUrl,
                ],
            ],
        ];

        if (!empty($request->headline)) $linkData['name'] = $request->headline;
        if (!empty($request->adCaption)) $linkData['description'] = $request->adCaption;
        if (!empty($mediaId)) {
            $linkData[$isVideo ? 'video_id' : 'image_hash'] = $mediaId;
        }

        $objectStorySpec = [
            'page_id' => $request->page_id,
            'link_data' => $linkData,
        ];

        $creativeRes = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://graph.facebook.com/v22.0/{$request->ad_account_id}/adcreatives", [
                'name' => 'Creative - ' . now()->format('Ymd_His'),
                'object_story_spec' => $objectStorySpec,
                'access_token' => $token,
            ])->json();

        if (!isset($creativeRes['id'])) {
            return response()->json(['error' => 'Ad Creative creation failed', 'details' => $creativeRes], 500);
        }

        $adRes = Http::post("https://graph.facebook.com/v22.0/{$adAccountPrefixed}/ads", [
            'name' => $request->adName,
            'adset_id' => $adSetId,
            'creative' => json_encode(['creative_id' => $creativeRes['id']]),
            'status' => 'ACTIVE',
            'access_token' => $token,
        ]);

        if (!isset($adRes['id'])) {
            return response()->json(['error' => 'Ad creation failed', 'details' => $adRes->json()], 500);
        }

        CtwaAd::create([
            'user_id' => $user->id,
            'ad_name' => $request->adName,
            'ad_id' => $adRes['id'],
            'adset_id' => $adSetId,
            'campaign_id' => $campaignId,
            'page_id' => $request->page_id,
            'whatsapp_phone_number' => $whatsappNumber,
            'cta_url' => $ctaUrl,
            'prefilled_message' => $request->prefilled_message ?? null,
            'message' => $request->message ?? null,
            'geo_targeting' => $request->geo_targeting ?? null,
            'age_from' => $ageMin,
            'age_to' => $ageMax,
            'gender' => $request->gender,
            'daily_budget' => $dailyBudgetInput * 100,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'media_type' => $isVideo ? 'video' : 'image',
            'media_id' => $mediaId,
            'interests' => $request->targeting ? array_values($request->targeting) : null,
        ]);

        FacebookAd::updateOrCreate(
            ['ad_id' => $adRes['id']],
            [
                'user_id' => $user->id,
                'campaign_id' => $campaignId,
                'campaign_name' => $request->adName,
                'ad_name' => $request->adName,
                'status' => $adRes['status'] ?? 'ACTIVE',
                'ad_account' => $request->ad_account_id ?? '',
                'ad_created_at' => Carbon::now()->toDateString(),
                'creative' => [],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'CTWA Ad created successfully.',
            'ad_id' => $adRes['id'],
            'campaign_id' => $campaignId,
            'adset_id' => $adSetId,
        ]);
    }

    public function sendCampaign(Request $request)
    {
        $data = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'template_id' => 'required|exists:wa_templates,id',
            'selected_leads' => 'required|json',
            'scheduled_at' => 'nullable|date',
        ]);

        $leads = json_decode($data['selected_leads'], true);
        $user = auth()->user();

        if (!$user->fb_long_lived_token || !$user->whatsapp_sender_id) {
            return response()->json(['error' => 'WhatsApp token or sender ID is not configured.'], 400);
        }

        $template = Template::findOrFail($data['template_id']);
        $scheduledAt = $data['scheduled_at'] ? Carbon::parse($data['scheduled_at']) : now();
        $rawComponents = json_decode($template->components, true);

        $success = [];
        $failed = [];
        $failureReasons = [];

        foreach ($leads as $lead) {
            $phone = $lead['wa_id'] ?? null;
            $name = $lead['name'] ?? 'User';

            if (!$phone) {
                $failed[] = $phone;
                $failureReasons[] = ['phone' => $phone, 'reason' => 'Missing phone number'];
                continue;
            }

            if ($scheduledAt->isFuture()) {
                ScheduledCampaign::create([
                    'campaign_name' => $data['campaign_name'],
                    'lead_name' => $name,
                    'lead_phone' => $phone,
                    'template_id' => $template->id,
                    'user_id' => $user->id,
                    'scheduled_at' => $scheduledAt,
                    'status' => 'pending',
                ]);
                continue;
            }

            $components = [];
            foreach ($rawComponents as $component) {
                $type = strtolower($component['type']);
                $format = strtolower($component['format'] ?? '');

                if ($type === 'header') {
                    if (in_array($format, ['image', 'video', 'document']) && !empty($component['example']['header_handle'][0])) {
                        try {
                            $mediaUrl = $component['example']['header_handle'][0];
                            $tempFile = tempnam(sys_get_temp_dir(), 'media_');
                            file_put_contents($tempFile, file_get_contents($mediaUrl));
                            $filename = basename(parse_url($mediaUrl, PHP_URL_PATH));

                            $upload = Http::withToken($user->fb_long_lived_token)
                                ->attach('file', fopen($tempFile, 'r'), $filename)
                                ->post("https://graph.facebook.com/v21.0/{$user->whatsapp_sender_id}/media", [
                                    'messaging_product' => 'whatsapp',
                                    'type' => $format
                                ]);

                            unlink($tempFile);

                            if ($upload->failed() || !$upload->json('id')) {
                                $failed[] = $phone;
                                $failureReasons[] = ['phone' => $phone, 'reason' => 'Media upload failed'];
                                continue 2;
                            }

                            $components[] = ['type' => 'header', 'parameters' => [['type' => $format, $format => ['id' => $upload->json('id')]]]];
                        } catch (\Exception $e) {
                            $failed[] = $phone;
                            $failureReasons[] = ['phone' => $phone, 'reason' => 'Exception: ' . $e->getMessage()];
                            continue 2;
                        }
                    } elseif ($format === 'text' && isset($component['text'])) {
                        $params = [];
                        if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                            foreach ($matches[0] as $match) {
                                $params[] = ['type' => 'text', 'text' => $name];
                            }
                        }
                        $components[] = ['type' => 'header', 'parameters' => $params];
                    }
                }

                if ($type === 'body' && isset($component['text'])) {
                    $params = [];
                    if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                        foreach ($matches[0] as $match) {
                            $params[] = ['type' => 'text', 'text' => $name];
                        }
                    }
                    $components[] = ['type' => 'body', 'parameters' => $params];
                }

                if ($type === 'buttons' && isset($component['buttons'])) {
                    foreach ($component['buttons'] as $index => $btn) {
                        $subType = strtolower($btn['type']);
                        if ($subType === 'quick_reply') {
                            $components[] = [
                                'type' => 'button',
                                'sub_type' => 'quick_reply',
                                'index' => (string)$index,
                                'parameters' => [['type' => 'payload', 'payload' => $btn['text'] ?? 'Reply']]
                            ];
                        }
                    }
                }
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'template',
                'template' => [
                    'name' => $template->name,
                    'language' => ['code' => $template->language],
                    'components' => $components
                ]
            ];

            $response = Http::withToken($user->fb_long_lived_token)
                ->post("https://graph.facebook.com/v21.0/{$user->whatsapp_sender_id}/messages", $payload);

            $json = $response->json();

            if ($response->successful() && isset($json['messages'][0]['id'])) {
                $success[] = $phone;
                $status = 'sent';
            } else {
                $failed[] = $phone;
                $status = 'failed';
                $failureReasons[] = ['phone' => $phone, 'reason' => $json['error']['message'] ?? 'Unknown error'];
                Log::error("Failed to send to {$phone}: " . json_encode($json));
            }

            ScheduledCampaign::create([
                'campaign_name' => $data['campaign_name'],
                'lead_name' => $name,
                'lead_phone' => $phone,
                'template_id' => $template->id,
                'user_id' => $user->id,
                'scheduled_at' => $scheduledAt,
                'status' => $status,
            ]);
        }

        return response()->json([
            'status' => 'complete',
            'message' => 'Campaign processed.',
            'success_count' => count($success),
            'failed_count' => count($failed),
            'success_numbers' => $success,
            'failed_numbers' => $failed,
            'failure_reasons' => $failureReasons,
        ]);
    }

    public function verify(Request $request, $token)
    {
        Log::info('Incoming Meta webhook request', ['ip' => $request->ip(), 'method' => $request->method()]);

        if ($request->isMethod('get')) {
            $verifyToken = $request->query('hub_verify_token');
            $mode = $request->query('hub_mode');
            $challenge = $request->query('hub_challenge');

            $config = DB::table('configs')
                ->where('key', 'ctwa_webhook_token')
                ->where('value', $token)
                ->where('model_type', Company::class)
                ->first();
            if (!$config) {
                $config = DB::table('configs')
                    ->where('key', 'ctwa_webhook_token')
                    ->where('value', $token)
                    ->first();
            }

            if ($mode === 'subscribe' && $verifyToken === $token && $config) {
                return response($challenge, 200);
            }

            return response('Invalid verification', 403);
        }

        return response('Method Not Allowed', 405);
    }

    public function handle(Request $request, $token)
    {
        $config = DB::table('configs')
            ->where('key', 'ctwa_webhook_token')
            ->where('value', $token)
            ->where('model_type', Company::class)
            ->first();
        if (!$config) {
            $config = DB::table('configs')
                ->where('key', 'ctwa_webhook_token')
                ->where('value', $token)
                ->first();
        }

        if (!$config) {
            Log::warning('Invalid webhook token in POST', ['token' => $token]);
            return response()->json(['error' => 'Invalid token'], 403);
        }

        Log::info('CTWA Webhook Payload Received', $request->all());
        return response()->json(['success' => true], 200);
    }

    public function receive(Request $request, $token)
    {
        $request->headers->set('Accept', 'application/json');

        $user = User::where('ctwa_webhook_token', $token)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        $payload = $request->all();
        Log::info('CTWA Webhook Payload', ['user_id' => $user->id, 'payload' => $payload]);

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? '') === 'messages') {
                    $campaign = CtwaCampaign::firstOrCreate([
                        'company_id' => $user->company_id,
                        'page_id' => $entry['id'],
                        'sender_id' => $change['value']['messages'][0]['from'] ?? null,
                    ]);

                    foreach ($change['value']['messages'] ?? [] as $msg) {
                        CtwaMessage::create([
                            'campaign_id' => $campaign->id,
                            'sender_id' => $msg['from'] ?? null,
                            'receiver_id' => $change['value']['metadata']['phone_number_id'] ?? null,
                            'message' => $msg['text']['body'] ?? null,
                            'message_type' => $msg['type'] ?? 'text',
                            'received_at' => now(),
                        ]);
                    }
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function listCampaigns()
    {
        $companyId = $this->companyId();
        $campaigns = CtwaCampaign::where('company_id', $companyId)->with('messages')->get();
        return response()->json(['campaigns' => $campaigns]);
    }

    public function getPayMetaData()
    {
        $user = auth()->user();

        $businesses = Http::get("https://graph.facebook.com/v22.0/me/businesses", [
            'access_token' => $user->fb_long_lived_token,
        ])->json();

        $data = [];

        foreach ($businesses['data'] ?? [] as $business) {
            $adAccounts = Http::get("https://graph.facebook.com/v22.0/{$business['id']}/owned_ad_accounts", [
                'access_token' => $user->fb_long_lived_token,
                'fields' => 'id,account_id,name'
            ])->json();

            $data[] = [
                'business_id' => $business['id'],
                'asset_id' => $adAccounts['data'][0]['account_id'] ?? null,
                'payment_account_id' => $adAccounts['data'][0]['id'] ?? null
            ];
        }

        return $data;
    }

    public function fetchAds(Request $request)
    {
        return $this->fetchAndStoreAds($request);
    }

    public function viewLeads(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $leads = CTWAAdsClickLead::where('company_id', $this->companyId())->with('contact')->get();
        return response()->json(['leads' => $leads]);
    }
}
