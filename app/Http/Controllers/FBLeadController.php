<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FacebookLeads;
use App\Models\User;
use App\Models\FacebookAdAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\Exports\FacebookLeadExport;
use Barryvdh\DomPDF\Facade\Pdf;
use FPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
require_once app_path('Libraries/fpdf.php');
use App\Events\LeadsFetched;

class FBLeadController extends Controller
{
    //
    protected $graphURL = 'https://graph.facebook.com/';
    protected $apiVersion = 'v21.0';
    protected $leadDataArray = [];
    
    public function index() {
         try{
            if (auth()->user()->hasRole('admin')):

                $campaignFilter =  FacebookLeads::select('campaign_id','campaign_name')
                                    ->groupBy('campaign_id')
                                    ->get();
                $campaignData = FacebookLeads::select('*');
                
                if(isset($_GET['campaign']) && !empty($_GET['campaign'])):
                    $campaignData = $campaignData->where('campaign_id',$_GET['campaign']);    
                endif;

                $campaignData = $campaignData->orderBy('created_time','desc')->paginate(10)->appends($_GET);
                

                return view('fbleads.index', [
                    'fbLeads'=>$campaignData,
                    'campaignFilter'=>$campaignFilter,
                    'parameters'=>count($_GET) != 0,
                ]);
            else:
                return redirect()->route('dashboard')->withStatus(__('No Access'));
            endif;
         }catch(\Exception $e){
<<<<<<< HEAD
            echo '<pre>';
            print_r($e->getMessage());
            die;
=======
            \Log::error('FB Lead error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
>>>>>>> akanksha
            return redirect()->route('dashboard')->withErrors($e->getMessage());
         }
    }

    // public function getLeadData(Request $request){
    //     try{
    //         $validator = Validator::make($request->all(),[
    //             'id'=>'required'
    //         ]);
    //         if($validator->fails()):
    //             return response()->json(['status'=>false,'message'=>$validator->errors()->first()]);
    //         endif;
    //         $leadData = FacebookLeads::findOrFail($request->id);
    //         // echo "<pre>";
    //         // print_r($leadData);die;
    //         $res['html'] = view('fbleads.view')->with(['leadData'=>$leadData])->render();
    //         return response()->json(['status'=>true,'message'=>'leaddata','data'=>$res]);
    //     }catch(\Exception $e){
    //         return response()->json(['status'=>false,'message'=>'Error:'.$e->getMessage()]);
    //     }
    // }
    
    public function getLeadData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
    
            $leadData = FacebookLeads::findOrFail($request->id);
    
            // Prevent field_data or all_field_data from causing errors
            unset($leadData->all_field_data, $leadData->field_data);
    
            $html = view('fbleads.view', compact('leadData'))->render();
    
            return response()->json([
                'status' => true,
                'message' => 'Lead data loaded',
                'data' => ['html' => $html]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // public function fetchAllCampaignLeads(Request $request)
    // {
    //     ini_set('max_execution_time', 240);
    //     $leads_limit = 100;
    //     // $fields = "campaigns{ads{leads.limit(".$leads_limit."){id,created_time,field_data,campaign_id,campaign_name,ad_id,ad_name,platform}},id,name}";
    //     // $url = "https://graph.facebook.com/v21.0/".config('otp.ad_account_id')."?access_token=".config('otp.token')."&fields=".$fields;
    //     // Step 1: Request ad account with full nested fields
    //     $fields = "id,name,account_id,account_status,business_name,currency,timezone_name," . "campaigns{id,name," . "adsets{id,name," . "ads{id,name," . "leads.limit($leads_limit){id,created_time,field_data}" . "}" . "}" . "}";
    
    //     $url = "https://graph.facebook.com/v21.0/" . config('otp.ad_account_id') . "?access_token=" . config('otp.token') . "&fields=" . urlencode($fields);

    //     try{
    //         $response = $this->makeRequest($url);
    //         if($response):
         
    //             $this->leadDataArray=[];
    //             $responseBody = $response->getBody()->getContents();
    //             $campaignData = json_decode($responseBody, true);
    //             // prince start
    //             // $clientId = $request->get('client_id');
    //             // $this->processCampaignsWithPagination($campaignData, $clientId);
    //             // prince end
    //             $this->processCampaignsWithPagination($campaignData);
    //             $leadIds = array_column($this->leadDataArray,'lead_id');
    //             $existingLeadIds = FacebookLeads::whereIn('lead_id', $leadIds)->pluck('lead_id')->toArray();
    //             $newLeads = array_filter($this->leadDataArray, function ($lead) use ($existingLeadIds) {
    //                 return !in_array($lead['lead_id'], $existingLeadIds);
    //             });

    //             if (count($newLeads) > 0) {
    //                 FacebookLeads::insert($newLeads);
    //             }
    //             // prince start
    //             // if (count($newLeads) > 0) {
    //             //     FacebookLeads::insert($newLeads);
    //             //     broadcast(new LeadsFetched('New Facebook leads fetched', count($newLeads)));
    //             // } else {
    //             //     broadcast(new LeadsFetched('No new leads fetched', 0));
    //             // }
    //             // prince end
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Facebook leads fetched successfully!  '.count($newLeads) .' New leads fetched ',
    //                 'new_entries_count' => count($newLeads),
    //             ], 200);
    //         else:
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'No leads found.'
    //             ], 404);
    //         endif;
    //     }catch(\Exception $e){
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to fetch leads.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function fetchAllCampaignLeads___(Request $request)
    {
        ini_set('max_execution_time', 240);
        $leads_limit = 100;
    
        $fields = "id,name,account_id,account_status,business_name,currency,timezone_name," .
                  "campaigns{id,name," .
                  "adsets{id,name," .
                  "ads{id,name," .
                  "leads.limit($leads_limit){id,created_time,field_data}" .
                  "}}}";
    
    
        $url = "https://graph.facebook.com/v21.0/" . config('otp.ad_account_id') . 
               "?access_token=" . config('otp.token') . 
               "&fields=" . urlencode($fields);
    
        try {
            $response = $this->makeRequest($url);
    
            if ($response) {
                $this->leadDataArray = [];
    
                $responseBody = $response->getBody()->getContents();
                $campaignData = json_decode($responseBody, true);
    
                $this->processCampaignsWithPagination($campaignData);
    
                $leadIds = array_column($this->leadDataArray, 'lead_id');
                $existingLeadIds = FacebookLeads::whereIn('lead_id', $leadIds)->pluck('lead_id')->toArray();
    
                $newLeads = array_filter($this->leadDataArray, function ($lead) use ($existingLeadIds) {
                    return !in_array($lead['lead_id'], $existingLeadIds);
                });
    
                if (count($newLeads) > 0) {
                    FacebookLeads::insert($newLeads);
                    session()->flash('success', count($newLeads) . ' new Facebook leads inserted successfully.');
                } else {
                    session()->flash('info', 'No new Facebook leads found.');
                }
    
                return redirect()->back();
            } else {
                session()->flash('error', 'No leads found.');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to fetch leads: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    public function fetchAllCampaignLeads(Request $request)
    {
        
    //         $companyId = $request->get('company_id');
    // if (!$companyId) {
    //     return response()->json(['error' => 'company_id is required'], 400);
    // }
    
    
        ini_set('max_execution_time', 240);
        $leads_limit = 100;
    
        $fields = "id,name,account_id,account_status,business_name,currency,timezone_name," .
                  "campaigns{id,name," .
                  "adsets{id,name," .
                  "ads{id,name," .
                  "leads.limit($leads_limit){id,created_time,field_data}" .
                  "}}}";
    
        $user = auth()->user();
        $company = \App\Models\Company::find($user->company_id);
    
        $configs = $company->getAllConfigs();
        $accessToken = $configs['whatsapp_permanent_access_token'] ?? null;
    
        if (!$accessToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp access token not found for this company.'
            ], 400);
        }
    
        $adAccounts = FacebookAdAccount::where('company_id', $user->company_id)->get();
    
        if ($adAccounts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No Facebook ad accounts found for this company.'
            ], 404);
        }
    
        $totalNewLeads = 0;
        $allLeadData = [];
    
        foreach ($adAccounts as $adAccount) {
            $ad_account_id = $adAccount->account_id;
            $url = "https://graph.facebook.com/v21.0/act_{$ad_account_id}?access_token={$accessToken}&fields=" . urlencode($fields);
            // print_r($url);die;
            try {
                $response = $this->makeRequest($url);
    
                if ($response) {
                    $this->leadDataArray = [];
    
                    $responseBody = $response->getBody()->getContents();
                    $campaignData = json_decode($responseBody, true);
    
                    $this->processCampaignsWithPagination($campaignData);
    
                    $leadIds = array_column($this->leadDataArray, 'lead_id');
                    $existingLeadIds = FacebookLeads::whereIn('lead_id', $leadIds)->pluck('lead_id')->toArray();
    
                    $newLeads = array_filter($this->leadDataArray, function ($lead) use ($existingLeadIds) {
                        return !in_array($lead['lead_id'], $existingLeadIds);
                    });
    
                    $totalNewLeads += count($newLeads);
                    $allLeadData = array_merge($allLeadData, $newLeads);
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching leads for ad account {$ad_account_id}: " . $e->getMessage());
                continue;
            }
        }
    
        $allLeadData = collect($allLeadData)->unique('lead_id')->values()->toArray();
        // print_r($url);die;
        if (!empty($allLeadData)) {
            FacebookLeads::insertOrIgnore($allLeadData);
            session()->flash('success', count($allLeadData) . ' new Facebook leads inserted.');
        } else {
            session()->flash('info', 'No new Facebook leads found.');
        }
    
    
        return redirect()->back();
    }
    
//     public function fetchAllCampaignLeads(Request $request)
// {
//     ini_set('max_execution_time', 240);
//     $leads_limit = 100;

//     $fields = "id,name,account_id,account_status,business_name,currency,timezone_name," .
//               "campaigns{id,name," .
//               "adsets{id,name," .
//               "ads{id,name," .
//               "leads.limit($leads_limit){id,created_time,field_data}" .
//               "}}}";

//     $user = auth()->user();
//     $company = \App\Models\Company::find($user->company_id);

//     $configs = $company->getAllConfigs();
//     $accessToken = $configs['whatsapp_permanent_access_token'] ?? null;

//     if (!$accessToken) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'WhatsApp access token not found for this company.'
//         ], 400);
//     }

//     $adAccounts = FacebookAdAccount::where('company_id', $user->company_id)->get();

//     if ($adAccounts->isEmpty()) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'No Facebook ad accounts found for this company.'
//         ], 404);
//     }

//     $allLeadData = [];

//     foreach ($adAccounts as $adAccount) {
//         $ad_account_id = $adAccount->account_id;
//         $url = "https://graph.facebook.com/v21.0/act_{$ad_account_id}?access_token={$accessToken}&fields=" . urlencode($fields);
//         // print_r($url);die;
//         try {
//             $response = $this->makeRequest($url);

//             if ($response) {
//                 $this->leadDataArray = [];

//                 $responseBody = $response->getBody()->getContents();
//                 $campaignData = json_decode($responseBody, true);

//                 // Custom function that populates $this->leadDataArray
//                 $this->processCampaignsWithPagination($campaignData);

//                 $allLeadData = array_merge($allLeadData, $this->leadDataArray);
//             }
//         } catch (\Exception $e) {
//             \Log::error("Error fetching leads for ad account {$ad_account_id}: " . $e->getMessage());
//             continue;
//         }
//     }

//     $allLeadData = collect($allLeadData)->unique('lead_id')->values()->toArray();

//     return response()->json([
//         'status' => 'success',
//         'total_leads' => count($allLeadData),
//         'leads' => $allLeadData
//     ], 200);
// }


    protected function makeRequest($url)
    {
        try{
            return (new \GuzzleHttp\Client())->request('GET', $url);
        }catch(\Exception $e){
            return false;
        }
    }
    
    protected function processCampaignsWithPagination(array $data )
    {
        if (!isset($data['campaigns']['data'])) return;
        $user = auth()->user();
        $userId = $user->id;
        $adAccountId = $data['account_id'] ?? null;
        $adAccountName = $data['name'] ?? null;
    
        foreach ($data['campaigns']['data'] as $campaign) {
            $campaignId = $campaign['id'] ?? null;
            $campaignName = $campaign['name'] ?? null;
    
            if (isset($campaign['adsets']['data'])) {
                foreach ($campaign['adsets']['data'] as $adset) {
                    $adsetId = $adset['id'] ?? null;
                    $adsetName = $adset['name'] ?? null;
    
                    if (isset($adset['ads']['data'])) {
                        foreach ($adset['ads']['data'] as $ad) {
                            $adId = $ad['id'] ?? null;
                            $adName = $ad['name'] ?? null;
    
                            if (isset($ad['leads']['data'])) {
                                foreach ($ad['leads']['data'] as $lead) {
                                    $fieldData = $lead['field_data'] ?? [];
    
                                    // Default values
                                    $yourRequirement = $fullName = $phoneNumber = $email = $city = null;
    
                                    foreach ($fieldData as $field) {
                                        $name = strtolower(trim($field['name']));
                                        $value = $field['values'][0] ?? null;
    
                                        switch ($name) {
                                            case 'your_requirement?':
                                            case 'your_requirement':
                                                $yourRequirement = $value;
                                                break;
                                            case 'full_name':
                                            case 'Full_Name':
                                            case 'FULL_NAME':
                                            case 'fullname':
                                            case 'full name':
                                                $fullName = $value;
                                                break;
                                            case 'phone_number':
                                            case 'phone':
                                                $phoneNumber = $value;
                                                break;
                                            case 'email':
                                                $email = $value;
                                                break;
                                            case 'city':
                                                $city = $value;
                                                break;
                                        }
                                    }
    
                                    $this->leadDataArray[] = [
                                        'lead_id'          => $lead['id'] ?? '',
                                        'created_time'     => $lead['created_time'] ?? null,
                                        'all_field_data'   => json_encode($fieldData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                                        'your_requirement' => $yourRequirement,
                                        'full_name'        => $fullName,
                                        'phone_number'     => $phoneNumber,
                                        'email'            => $email,
                                        'city'             => $city,
                                        'campaign_id'      => $campaignId,
                                        'campaign_name'    => $campaignName,
                                        'ad_id'            => $adId,
                                        'ad_name'          => $adName,
                                        'adset_id'         => $adsetId,
                                        'adset_name'       => $adsetName,
                                        'ad_account_id'    => 'act_' . $adAccountId,
                                        'ad_account_name'  => $adAccountName,
                                        'platform'         => 'facebook',
                                        'user_id'          => $userId, // prince
                                        'created_at'       => now(),
                                        'updated_at'       => now(),
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function fetchAllCampaign(Request $request)
    {
        ini_set('max_execution_time', 240);
        $fields = "id,name,adaccounts{account_id,name,campaigns{id,name,adsets{id,name,ads{id,name}}}}";
        
        
        $user = auth()->user();
        $company = \App\Models\Company::find($user->company_id);
        
        $configs = $company->getAllConfigs();
        $accessToken = $configs['whatsapp_permanent_access_token'] ?? null;
        // print_r($accessToken);die;
        if (!$accessToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp access token not found for this company.'
            ], 400);
        }
        $url = "https://graph.facebook.com/v21.0/me?access_token={$accessToken}&fields=" . urlencode($fields);
    
        try {
            $response = $this->makeRequest($url);
    
            if ($response) {
                $responseBody = $response->getBody()->getContents();
                $decoded = json_decode($responseBody, true);
    
                $adDataArray = $this->processAccountHierarchy($decoded);
    
                if (empty($adDataArray)) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'No ads found.',
                        'new_entries_count' => 0
                    ]);
                }
    
                // Filter out already existing ads
                $existingAdIds = FacebookAdAccount::whereIn('ad_id', array_column($adDataArray, 'ad_id'))->pluck('ad_id')->toArray();
    
                $newAds = array_filter($adDataArray, function ($ad) use ($existingAdIds) {
                    return !in_array($ad['ad_id'], $existingAdIds);
                });
    
                if (!empty($newAds)) {
                    FacebookAdAccount::insert($newAds);
                }
    
                return response()->json([
                    'status' => 'success',
                    'message' => count($newAds) . ' new ads inserted.',
                    'new_entries_count' => count($newAds),
                ]);
            }
    
            return response()->json(['status' => 'error', 'message' => 'No ads found.'], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch ad data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function processAccountHierarchy(array $data)
    {
        $adRows = [];
    
        $user = auth()->user();
        if (!$user || !$user->company_id) {
            // Optionally log or throw an error
            return $adRows;
        }
    
        $companyId = $user->company_id;
    
        if (!isset($data['adaccounts']['data'])) return $adRows;
    
        foreach ($data['adaccounts']['data'] as $account) {
            $accountId = $account['account_id'] ?? null;
            $accountName = $account['name'] ?? null;
    
            if (!isset($account['campaigns']['data'])) continue;
    
            foreach ($account['campaigns']['data'] as $campaign) {
                $campaignId = $campaign['id'] ?? null;
                $campaignName = $campaign['name'] ?? null;
    
                if (!isset($campaign['adsets']['data'])) continue;
    
                foreach ($campaign['adsets']['data'] as $adset) {
                    $adsetId = $adset['id'] ?? null;
                    $adsetName = $adset['name'] ?? null;
    
                    if (!isset($adset['ads']['data'])) continue;
    
                    foreach ($adset['ads']['data'] as $ad) {
                        $adRows[] = [
                            'company_id'     => $companyId,
                            'account_id'     => $accountId,
                            'account_name'   => $accountName,
                            'campaign_id'    => $campaignId,
                            'campaign_name'  => $campaignName,
                            'adset_id'       => $adsetId,
                            'adset_name'     => $adsetName,
                            'ad_id'          => $ad['id'] ?? null,
                            'ad_name'        => $ad['name'] ?? null,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                }
            }
        }
    
        return $adRows;
    }

    // protected function processCampaignsWithPagination($campaignData)
    // {
        
    //     do {
    //         if (isset($campaignData['campaigns']['data'])) {
    //             foreach($campaignData['campaigns']['data'] as $campaign):
    //                 if(isset($campaign['ads'])):
    //                     if (isset($campaign['ads']['data'])):
    //                         foreach ($campaign['ads']['data'] as $leads):
    //                             if (isset($leads['leads'])):
    //                                 $this->fetchLeadsWithPagination($campaign['id'],$campaign['name'], $leads['leads']);
    //                             endif;
    //                         endforeach;
    //                     else:
    //                         // will handle Campaing Entry only
    //                     endif;
    //                 endif;
    //             endforeach;
    //         }

    //         // Check for campaign-level pagination
    //         if (isset($campaignData['campaigns']['paging']['next'])):
    //             $nextCampaignUrl = $campaignData['campaigns']['paging']['next'];
    //             $campaignData = null;
    //             $response = $this->makeRequest($nextCampaignUrl);
    //             if($response):
    //                 $campaignData = json_decode($response->getBody()->getContents(), true);
    //             endif;
    //         else:
    //             $campaignData = null;
    //         endif;
    //     } while ($campaignData); 

        
    // }

    /**
     * Fetch leads for a specific ad with pagination.
     */
    protected function fetchLeadsWithPagination($campaignId, $campaignName,$leads)
    {
        do {
            foreach ($leads['data'] as $lead):
                $this->leadDataArray[]=[
                    'lead_id'=>$lead['id'],
                    'campaign_id' => $lead['campaign_id'],
                    'campaign_name' => $lead['campaign_name'],
                    'ad_id' => $lead['ad_id'],
                    'ad_name'=>$lead['ad_name'],
                    'created_time' => $lead['created_time'],
                    'platform'=>$lead['platform'],
                    'your_requirement'=>$this->extractField($lead['field_data'], 'your_requirement?_'),
                    'full_name' => $this->extractField($lead['field_data'], 'full_name'),
                    'phone_number' => $this->extractField($lead['field_data'], 'phone_number'),
                    'email' => $this->extractField($lead['field_data'], 'email'),
                    'city' => $this->extractField($lead['field_data'], 'city'),
                    'all_field_data'=>json_encode($this->convertFieldDataArray($lead['field_data'])),
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ];
            endforeach;

            // Check for leads-level pagination (inside each ad)
            if (isset($leads['paging']['next'])):
                $nextLeadUrl = $leads['paging']['next'];
                $leads =  null;
                try {
                    $response = $this->makeRequest($nextLeadUrl);
                    if ($response):
                        $responseBody = $response->getBody()->getContents();
                        $leads = json_decode($responseBody, true);        
                    endif;
                }catch(\Exception $e){}
            else:
                $leads = null;
            endif;
        } while ($leads);
    }

    protected function extractField($fieldData, $fieldName)
    {
        foreach ($fieldData as $field):
            if ($field['name'] === $fieldName) :
                return $field['values'][0] ?? '';
            endif;
        endforeach;
        return null;
    }

    protected function convertFieldDataArray($fieldata ){
        $convertedArray = [];
        foreach ($fieldata as $field):
            $convertedArray[$field['name']] = $field['values'][0] ?? '';
        endforeach;
        return $convertedArray;
    }

    public function downlaodCsv()
    {
        $user = auth()->user();
        $user_id = $user->id;
    $fbleads = FacebookLeads::select('campaign_name', 'full_name', 'platform', 'created_time','your_requirement','phone_number','email','city')->where('user_id', $user_id)->get();

        // Add `details` for each lead
        $fbleads->map(function ($fblead) {
            $detail = " via {$fblead->campaign_name}. Full Name: {$fblead->full_name}";
            if ($fblead->platform == 'ig') {
                $fblead->details = "Instagram Lead" . $detail;
            } elseif ($fblead->platform == 'fb') {
                $fblead->details = "Facebook Lead" . $detail;
            } else {
                $fblead->details = $detail; // Default if platform is neither 'ig' nor 'fb'
            }
            return $fblead;
        });

        // Prepare items for export
        $items = [];
        foreach ($fbleads as $vendor) {
            $item = [
                'name' => $vendor->full_name,
                'details' => $vendor->details,
                'last_activity' => '-', // Placeholder for last activity
                'date_added' => $vendor->created_time
                    ->locale(Config::get('app.locale'))
                    ->isoFormat('LLLL'), // Corrected variable
                 'your_requirement' =>  $vendor->your_requirement,
                 'phone_number' =>  $vendor->phone_number,
                 'email' =>  $vendor->email,
                 'city' =>  $vendor->city,
                  
            ];
            array_push($items, $item);
        }

        // Export as CSV
        return Excel::download(new FacebookLeadExport($items), 'facebookLead_' . time() . '.csv', \Maatwebsite\Excel\Excel::CSV);        
          
    }

    public function downlaodPDF()
    {
    // Include DOMPDF autoload file
    require_once base_path('vendor/dompdf/autoload.php');
    $user = auth()->user();
        $user_id = $user->id;
    $fbleads = FacebookLeads::select(
        'campaign_name', 
        'full_name', 
        'platform', 
        'created_time', 
        'phone_number', 
        'email', 
        'city'
    )->where('user_id',$user_id)
    ->get()
    ->map(function ($fblead) {
        $detail = "via {$fblead->campaign_name}. Full Name: {$fblead->full_name}";
        $fblead->details = $fblead->platform == 'ig' ? "Instagram Lead $detail" :
                          ($fblead->platform == 'fb' ? "Facebook Lead $detail" : $detail);
        return $fblead;
    });

    // Load view as HTML
    $html = view('pdf.facebook_leads', compact('fbleads'))->render();

    // Configure DOMPDF options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    // Create DOMPDF instance
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A2', 'portrait');
    $dompdf->render();

    // Output the PDF
    return $dompdf->stream('facebook_leads_' . time() . '.pdf', ['Attachment' => true]);
}

    public function downlaodPDF_bk()
    {


// Retrieve Facebook Leads
$fbleads = FacebookLeads::select('campaign_name', 'full_name', 'platform', 'created_time', 'your_requirement', 'phone_number', 'email', 'city')
    ->get()
    ->map(function ($fblead) {
        $detail = " via {$fblead->campaign_name}. Full Name: {$fblead->full_name}";
        if ($fblead->platform == 'ig') {
            $fblead->details = "Instagram Lead" . $detail;
        } elseif ($fblead->platform == 'fb') {
            $fblead->details = "Facebook Lead" . $detail;
        } else {
            $fblead->details = $detail; // Default if platform is neither 'ig' nor 'fb'
        }
        return $fblead;
    });

// Initialize FPDF
$pdf = new FPDF();
$pdf->SetMargins(10, 10, 10); // Set margins
$pdf->AddPage();

// Set title and headers
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Facebook Leads Report', 0, 1, 'C');
$pdf->Ln(10); // Add a line break

// Table header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 220, 255); // Light blue background for header

// Define header columns
$headers = ['Full Name', 'Details', 'Phone', 'Email', 'City', 'Requirement', 'Created Time'];
$headerWidths = [30, 50, 30, 50, 30, 40, 40]; // Adjusted widths

foreach ($headers as $index => $header) {
    $pdf->Cell($headerWidths[$index], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln(); // Line break after headers

// Table content
$pdf->SetFont('Arial', '', 9);
foreach ($fbleads as $lead) {
    // Calculate maximum height for the row based on MultiCell content
    $cellData = [
        $lead->full_name,
        $lead->details,
        $lead->phone_number ?? 'N/A',
        $lead->email ?? 'N/A',
        $lead->city ?? 'N/A',
        $lead->your_requirement ?? 'N/A',
        $lead->created_time->format('Y-m-d H:i:s')
    ];
    
    $rowHeights = [];
    foreach ($cellData as $index => $text) {
        $rowHeights[] = $pdf->GetStringWidth($text) > $headerWidths[$index] ? ceil($pdf->GetStringWidth($text) / $headerWidths[$index]) * 5 : 10;
    }
    $maxHeight = max($rowHeights);

    // Draw cells
    foreach ($cellData as $index => $text) {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($headerWidths[$index], 5, $text, 1, 'L');
        $pdf->SetXY($x + $headerWidths[$index], $y);
    }
    $pdf->Ln($maxHeight); // Move to next row
}

// Add Footer
$pdf->SetY(-15); // Position footer at 1.5 cm from the bottom
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 0, 'C');

// Output PDF
$pdf->Output('facebookLeads_' . time() . '.pdf', 'D');

    }
    
}