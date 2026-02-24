<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Contacts\Models\Contact;
use App\Models\SubscriptionTransactionLog;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Contacts\Models\Country;
use App\Http\Controllers\SubscriptionController;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Wpbox\Models\Campaign;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserProfileController extends Controller
{
    protected $subscriptionController;

    public function __construct(SubscriptionController $subscriptionController)
    {
        $this->subscriptionController = $subscriptionController;
    }

    public function show(Request $request)
    {
        // $user = auth()->user();
        // $company = auth()->user()->company;
        // $Subscription = $company->activeSubscription;
        // $billingHistory = [];
        // if ($company->stripe_customer_id) {
        //     $billingHistory = $this->subscriptionController->getCustomerPaymentsAndInvoice($company->stripe_customer_id);
        // }
        // $log_details = SubscriptionTransactionLog::where('company_id', auth()->user()->company_id)
        //     ->latest()
        //     ->paginate(10);
        // $total_team = User::where('company_id', auth()->user()->company_id)
        //     ->where('status', 1)
        //     ->count();
        // $total_contacts = Contact::where('company_id', auth()->user()->company_id)
        //     ->where('status', 1)
        //     ->count();
        // $teams_remaining = $Subscription->team_limit ?? 0 - $total_team;
        // $contacts_remaining = $Subscription->contact_limit ?? 0 - $total_contacts;
        // $company = auth()->user()->resolveCurrentCompany();
        // $data = [
        //     'company' => $company,
        //     'team_remaining' => $teams_remaining,
        //     'contact_remaining' => $contacts_remaining,
        //     'active_subscription' => $company->activeSubscription,
        //     'log_detail' => $log_details,
        //     'billingHistory' => $billingHistory,
        // ];
        // $countries = Country::all();

        // return view('profile.show', [
        //     'request' => $request,
        //     'user' => $user,
        //     'subs_data' => $data,
        //     'countries' => $countries,
        // ]);

        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'No associated company found.');
        }

        $Subscription = optional($company)->activeSubscription;
        $billingHistory = [];

        if ($company->stripe_customer_id) {
            $billingHistory = $this->subscriptionController->getCustomerPaymentsAndInvoice($company->stripe_customer_id);
        }

        $log_details = SubscriptionTransactionLog::where('company_id', $company->id)->latest()->paginate(10);

        $total_team = User::where('company_id', $company->id)->where('status', 1)->count();

        $total_contacts = Contact::where('company_id', $company->id)->where('status', 1)->count();

        $teams_remaining = optional($Subscription)->team_limit ? $Subscription->team_limit - $total_team : 0;
        $contacts_remaining = optional($Subscription)->contact_limit ? $Subscription->contact_limit - $total_contacts : 0;

        $data = [
            'company' => $company,
            'team_remaining' => $teams_remaining,
            'contact_remaining' => $contacts_remaining,
            'active_subscription' => $Subscription,
            'log_detail' => $log_details,
            'billingHistory' => $billingHistory,
        ];

        $countries = Country::all();

        return view('profile.show', [
            'request' => $request,
            'user' => $user,
            'subs_data' => $data,
            'countries' => $countries,
        ]);
    }
    public function billing(Request $request)
    {
        $campaigns = Campaign::where('company_id', auth()->user()->company_id)
            ->where('send_to', '>', 1)
            ->get();
        if ($campaigns->count() > 0 && $campaigns->sum('delivered_to') > 0) {
            $campaign_count = $campaigns->count();
        } else {
            $campaign_count = 0;
        }
        $user = auth()->user();
        $company = auth()->user()->resolveCurrentCompany();

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'No associated company found.');
        }

        $Subscription = $company->activeSubscription;
        $billingHistory = [];
        if ($company->stripe_customer_id) {
            $billingHistory = $this->subscriptionController->getCustomerPaymentsAndInvoice($company->stripe_customer_id);
        }
        $log_details = SubscriptionTransactionLog::where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(10);
        $total_team = User::where('company_id', auth()->user()->company_id)
            ->where('status', 1)
            ->count();
        $total_contacts = Contact::where('company_id', auth()->user()->company_id)
            ->where('status', 1)
            ->count();
        $teams_remaining = $Subscription->team_limit ?? 0 - $total_team;
        $contacts_remaining = $Subscription->contact_limit ?? 0 - $total_contacts;
        $company = auth()->user()->resolveCurrentCompany();
        $data = [
            'company' => $company,
            'team_remaining' => $teams_remaining,
            'contact_remaining' => $contacts_remaining,
            'active_subscription' => $company->activeSubscription,
            'log_detail' => $log_details,
            'billingHistory' => $billingHistory,
        ];
        $countries = Country::all();

        return view('profile.show-billing', [
            'request' => $request,
            'user' => $user,
            'subs_data' => $data,
            'countries' => $countries,
            'campaing_count' => $campaign_count,
        ]);
    }
    public function api(Request $request)
    {
        $user = auth()->user();
        $token = PersonalAccessToken::where('tokenable_id', auth()->user()->id)
            ->where('tokenable_type', 'App\Models\User')
            ->first();
        $company = $this->getCompany();

        if (!$token || $company->getConfig('whatsapp_webhook_verified', 'no') != 'yes' || $company->getConfig('whatsapp_settings_done', 'no') != 'yes') {
            $data_api = '';
        }

        $planText = $company->getConfig('plain_token', '');
        $data_api = ['token' => $planText, 'company' => $company];

        $company = auth()->user()->resolveCurrentCompany();
        $Subscription = $company->activeSubscription;
        $billingHistory = [];
        if ($company->stripe_customer_id) {
            $billingHistory = $this->subscriptionController->getCustomerPaymentsAndInvoice($company->stripe_customer_id);
        }
        $log_details = SubscriptionTransactionLog::where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(10);
        $total_team = User::where('company_id', auth()->user()->company_id)
            ->where('status', 1)
            ->count();
        $total_contacts = Contact::where('company_id', auth()->user()->company_id)
            ->where('status', 1)
            ->count();
        $teams_remaining = $Subscription->team_limit ?? 0 - $total_team;
        $contacts_remaining = $Subscription->contact_limit ?? 0 - $total_contacts;
        $company = auth()->user()->resolveCurrentCompany();
        $data = [
            'company' => $company,
            'team_remaining' => $teams_remaining,
            'contact_remaining' => $contacts_remaining,
            'active_subscription' => $company->activeSubscription,
            'log_detail' => $log_details,
            'billingHistory' => $billingHistory,
        ];
        $countries = Country::all();

        return view('profile.show-api', [
            'request' => $request,
            'user' => $user,
            'subs_data' => $data,
            'data_api' => $data_api,
            'countries' => $countries,
        ]);
    }

    public function update(Request $request, $id)
    {
        //  dd($request);
        $request->validate([
            'fname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'name_company' => 'nullable|string|max:255',
            // 'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $phone = '+' . $request->country_code . trim($request->phone);
        $user = User::findOrFail($id);
        $user->name = $request->fname;
        $user->phone = $phone;
        $user->name_company = $request->name_company;

        // if ($request->hasFile('avatar')) {
        //     $avatarPath = $request->file('avatar')->store('avatars', 'public');
        //     $user->avatar = $avatarPath;
        // }

        if ($request->hasFile('avatar')) {
            if ($user->profile_photo_path) {
                $fileName = basename($user->profile_photo_path);
                if (config('settings.use_s3_as_storage', false)) {
                    $filePath = "uploads/companies/{$user->id}/photo/{$fileName}";
                    if (Storage::disk('s3')->exists($filePath)) {
                        Storage::disk('s3')->delete($filePath);
                    }
                } else {
                    $filePath = str_replace(url('/'), '', $user->profile_photo_path);
                    $filePath = ltrim($filePath, '/');

                    if (Storage::disk('public_path')->exists($filePath)) {
                        Storage::disk('public_path')->delete($filePath);
                    }
                }
            }

            if (config('settings.use_s3_as_storage', false)) {
                $path = $request->file('avatar')->storePublicly("uploads/companies/{$user->id}/photo", 's3');
                $user->profile_photo_path = Storage::disk('s3')->url($path);
            } else {
                $path = $request->file('avatar')->store("/{$user->id}/photo", 'public_user_profile_uploads');
                $user->profile_photo_path = url(Storage::disk('public_user_profile_uploads')->url($path)); // Store full URL
            }
        }

        if ($request->avatar_remove == 1) {
            $user->profile_photo_path = '';
        }
        $user->save();

        return redirect()->route('account.profile.show')->with('status', 'Profile updated successfully');
    }

    public function omitModal(Request $request)
    {
        // Verifica si la sesión 'first_login' existe
        if ($request->session()->has('first_login')) {
            // Elimina la sesión
            $request->session()->forget('first_login');
        }
        return response()->json(['success' => true]);
    }

    public function saveData(Request $request)
    {
        // Validación de los datos
        $validatedData = $request->validate([
            'phone' => 'required|string|max:15', // Asegúrate de ajustar las reglas según tus necesidades
            'name_company' => 'required|string|max:255',
        ]);
        $phone = '+' . $request->country_code . trim($request->phone);
        $user = User::findOrFail(auth()->user()->id);
        $user->phone = $phone;
        $user->name_company = $request->name_company;

        $user->save();

        if ($request->session()->has('first_login')) {
            $request->session()->forget('first_login');
        }
        return response()->json(['success' => true, 'message' => 'Datos guardados correctamente.']);
    }
    public function storebilling(Request $request)
    {
        $userCompanyId = auth()->user()->company_id;
        $company = Company::findorfail($userCompanyId);
        $company->billing_name = $request->billing_name;
        $company->billing_email = $request->billing_email;
        $company->billing_address = $request->billing_address;
        $company->billing_city = $request->billing_city;
        $company->billing_zip_code = $request->billing_zipcode;
        $company->billing_state = $request->billing_state;
        $phone = '+' . $request->country_code . trim($request->billing_phone);
        $company->billing_phone = $phone;
        $company->billing_country = $request->billing_country;
        $company->save();
        return response()->json(['success' => true, 'message' => 'Datos guardados correctamente.']);
    }

    public function updateBilling(Request $request)
    {
        $request->validate([
            'billing_address' => 'required|string|max:255',
            'billing_state' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_zip_code' => 'required|string|max:255',
        ]);
        $userCompanyId = auth()->user()->company_id;
        $company = Company::findorfail($userCompanyId);
        $company->billing_address = $request->billing_address;
        $company->billing_city = $request->billing_city;
        $company->billing_zip_code = $request->billing_zip_code;
        $company->billing_state = $request->billing_state;
        $company->save();

        return redirect()->route('account.profile.show')->with('status', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
                'password_confirmation' => ['required'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Password updated successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'errors' => $e->errors(),
                    'message' => 'Validation failed',
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
