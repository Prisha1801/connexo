<?php

use App\Http\Controllers\Auth\MyWelcomeController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\CreditsController;
use App\Http\Controllers\CRUD\PostsController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\FacebookLeadsController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\CTWAController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\FBLeadController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Spatie\WelcomeNotification\WelcomesNewUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Events\WhatsAppTriggerRequested;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\UserProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', [FrontEndController::class, 'index'])->name('landing');
// Route::get('/new', [FrontEndController::class, 'register'])->name('newcompany.register');
// Route::get('/'.config('settings.url_route', 'company').'/{alias}', [FrontEndController::class, 'company'])->name('vendor');
// Route::get('/notify/{type}/{id}/{message}', [CompaniesController::class, 'notify'])->name('company.notify');
// Route::middleware('web', WelcomesNewUsers::class)->group(function () {
//     Route::get('welcome/{user}', [MyWelcomeController::class, 'showWelcomeForm'])->name('welcome');
//     Route::post('welcome/{user}', [MyWelcomeController::class, 'savePassword']);
// });




Route::get('/migrate', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true,
    ]);
    $output = \Illuminate\Support\Facades\Artisan::output();
    return nl2br($output);
});

Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:cache');
        return response()->json(['status' => 'success', 'message' => 'All caches cleared successfully!']);
    } catch (\Exception $e) {
        print_r($e->getMessage());
    }
});
// Secured admin routes - only accessible in local environment or with proper authentication
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/migrate', function () {
        if (!config('app.debug') && config('app.env') !== 'local') {
            abort(403, 'This route is only available in development environment');
        }
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--force' => true,
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return nl2br($output);
    });

    Route::get('/clear-cache', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('config:cache');
            return response()->json(['status' => 'success', 'message' => 'All caches cleared successfully!']);
        } catch (\Exception $e) {
            \Log::error('Cache clear error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to clear cache'
            ], 500);
        }
    });
});

Route::get('/ctwa/webhook/{token}', [CtwaController::class, 'verify']);
// Route::post('/ctwa/webhook/{token}', [CtwaController::class, 'receive']);
Route::post('/ctwa/webhook/{token}', [CtwaController::class, 'receive'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/ctwa/campaigns', [CtwaController::class, 'listCampaigns']);
Route::post('/ctwa/webhook/{token}', [CtwaController::class, 'handle'])->name('webhook.ctwa');
Route::get('/getPayMetaData', [CtwaController::class, 'getPayMetaData']);

Route::get('/', [FrontEndController::class, 'index'])->name('landing');
//*** CUSTOM ROUTES */
Route::get('/pricing', [FrontEndController::class, 'pricing'])->name('front.pricing');
Route::get('/features', [FrontEndController::class, 'features'])->name('front.features');
Route::get('/help', [FrontEndController::class, 'help'])->name('front.help');
Route::get('/contact', [FrontEndController::class, 'contact'])->name('front.contact');
Route::get('/careers', [FrontEndController::class, 'careers'])->name('front.careers');
Route::get('/partner-program', [FrontEndController::class, 'partner_program'])->name('front.partner_program');
Route::get('/agreement', [FrontEndController::class, 'agreement'])->name('front.agreement');
Route::get('/getFacebookLeadInfo', [FrontEndController::class, 'getFacebookLeadInfo'])->name('front.getFacebookLeadInfo');


Route::middleware(['auth'])->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'show'])->name('verify-otp');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('resend.otp');
});

Route::middleware(['web', 'auth', 'impersonate'])->group(function () {
    Route::name('admin.')->group(function () {
        Route::get('/admin/facebook-leads', [App\Http\Controllers\FBLeadController::class, 'index'])->name('fblead.index');
        Route::get('/sync-leads', [App\Http\Controllers\FBLeadController::class, 'fetchAllCampaignLeads'])->name('fblead.synced');
        Route::get('/get-lead-data', [App\Http\Controllers\FBLeadController::class, 'getLeadData'])->name('fblead.get');
        // Route::post('/get-lead-data', [App\Http\Controllers\FBLeadController::class, 'getLeadData'])->name('fblead.get');
        // Route::match(['get', 'post'], '/get-lead-data', [App\Http\Controllers\FBLeadController::class, 'getLeadData'])->name('fblead.get');
        Route::get('/downlaodCsv', [App\Http\Controllers\FBLeadController::class, 'downlaodCsv'])->name('downlaodCsv');
        Route::get('/downlaodPDF', [App\Http\Controllers\FBLeadController::class, 'downlaodPDF'])->name('downlaodPDF');
    });
});
//*** CUSTOM ROUTES ENDS */
Route::get('/new', [FrontEndController::class, 'register'])->name('newcompany.register');
Route::get('/' . config('settings.url_route', 'company') . '/{alias}', [FrontEndController::class, 'company'])->name('vendor');
Route::get('/notify/{type}/{id}/{message}', [CompaniesController::class, 'notify'])->name('company.notify');
Route::middleware('web', WelcomesNewUsers::class)->group(function () {
    Route::get('welcome/{user}', [MyWelcomeController::class, 'showWelcomeForm'])->name('welcome');
    Route::post('welcome/{user}', [MyWelcomeController::class, 'savePassword']);
});

//AUTH
Route::middleware('web')->group(function () {
    Route::get('/login/google', [SocialController::class, 'googleRedirectToProvider'])->name('google.login');
    Route::get('/login/google/redirect', [SocialController::class, 'googleHandleProviderCallback']);
    Route::get('/login/facebook', [App\Http\Controllers\Auth\SocialController::class, 'facebookRedirectToProvider'])->name('facebook.login');
    Route::get('/login/facebook/redirect', [SocialController::class, 'facebookHandleProviderCallback']);


    //password/reset to /forgot-password
    Route::get('password/reset', function () {
        return redirect('forgot-password');
    });

});

Route::middleware(['web', 'auth', 'isMenuMiddleware', 'impersonate','acivatedProject','redirect.otp.not.verified'])->group(function () {
    Route::get('/dashboard/{lang?}', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/home/{lang?}', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('home');

    Route::name('admin.')->group(function () {
        Route::resource(config('settings.url_route_plural', 'companies'), 'App\Http\Controllers\CompaniesController', [
            'names' => [
                'index' => 'companies.index',
                'store' => 'companies.store',
                'edit' => 'companies.edit',
                'create' => 'companies.create',
                'destroy' => 'companies.destroy',
                'update' => 'companies.update',
                'show' => 'companies.show',
            ],
        ]);

        //Other companies routes
        Route::get('removecompany/{company}', [App\Http\Controllers\CompaniesController::class, 'remove'])->name('company.remove');
        Route::get('/company/{company}/activate', [App\Http\Controllers\CompaniesController::class, 'activateCompany'])->name('company.activate');
        Route::put('companies_app_update/{company}', [App\Http\Controllers\CompaniesController::class, 'updateApps'])->name('company.updateApps');
        Route::get('companies/loginas/{company}', [App\Http\Controllers\CompaniesController::class, 'loginas'])->name('companies.loginas');
       
        //Switch company
        Route::get('companies/switch/{company}', [App\Http\Controllers\CompaniesController::class, 'switch'])->name('companies.switch');

        //Organization management
        Route::get('organizations/manage', [App\Http\Controllers\CompaniesController::class, 'manage'])->name('organizations.manage');
        Route::post('organizations/create', [App\Http\Controllers\CompaniesController::class, 'createOrganization'])->name('organizations.create');

       
        Route::get('stopimpersonate', [App\Http\Controllers\CompaniesController::class, 'stopImpersonate'])->name('companies.stopImpersonate');
        Route::get('/share', [App\Http\Controllers\CompaniesController::class, 'share'])->name('share');

        Route::resource('settings', 'App\Http\Controllers\SettingsController');

        // Landing page settings
        Route::get('landing', [App\Http\Controllers\SettingsController::class, 'landing'])->name('landing');
        Route::controller(PostsController::class)->prefix('landing')->name('landing.')->group(function () {
            Route::get('posts/{type}', 'index')->name('posts');
            Route::get('posts/{type}/create', 'create')->name('posts.create');
            Route::post('posts/{type}', 'store')->name('posts.store');

            Route::get('posts/edit/{post}', 'edit')->name('posts.edit');
            Route::put('posts/{post}', 'update')->name('posts.update');
            Route::get('posts/del/{post}', 'destroy')->name('posts.delete');

        });

        //Apps
        Route::get('apps', [App\Http\Controllers\AppsController::class, 'index'])->name('apps.index');
        Route::get('company_apps', [App\Http\Controllers\AppsController::class, 'companyApps'])->name('apps.company');
        Route::get('appremove/{alias}', [App\Http\Controllers\AppsController::class, 'remove'])->name('apps.remove');
        Route::post('apps', [App\Http\Controllers\AppsController::class, 'store'])->name('apps.store');
        Route::put('company_apps_update', [App\Http\Controllers\AppsController::class, 'updateApps'])->name('owner.updateApps');
        Route::get('apps/update_plugin_via_file', [App\Http\Controllers\AppsController::class, 'store'])->name('apps.update_plugin_via_file');
    });

    Route::resource('plans', PlansController::class);
    Route::controller(PlansController::class)->group(function () {
        Route::get('/plan', 'current')->name('plans.current');
        Route::post('/subscribe/plan', 'subscribe')->name('plans.subscribe');
        Route::get('/subscribe/cancel', 'cancelStripeSubscription')->name('plans.cancel');
        Route::get('/subscribe/plan3d/{plan}/{user}', 'subscribe3dStripe')->name('plans.subscribe_3d_stripe');
        Route::post('/subscribe/update', 'adminupdate')->name('update.plan');
    });
    
    Route::get('/facebook-leads', [FacebookLeadsController::class, 'index'])->name('facebooklead.index');
    Route::get('/automation/reconnect', [AutomationController::class, 'showReconnectForm'])->name('automation.reconnect');
    Route::get('/automation/fb_automation', [AutomationController::class, 'fb_automation'])->name('automation.fb_automation');
    Route::delete('/automation/{id}', [AutomationController::class, 'destroy'])->name('automation.delete');
    Route::post('/automation/{id}/toggle-status', [AutomationController::class, 'toggleStatus'])->name('automation.toggleStatus');

    // POST: to handle form submission
    Route::post('/automation/reconnect', [AutomationController::class, 'reconnect'])->name('automation.reconnect.form');

    Route::get('/automation-form', [AutomationController::class, 'index'])->name('automationform.index');
    Route::post('/automation/fetch-campaign-data', [AutomationController::class, 'fetchCampaignData'])->name('automation.fetch');
    Route::post('/automation/fetch-campaigns', [AutomationController::class, 'fetchCampaigns'])->name('automation.fetchCampaigns');
    Route::post('/automation/fetch-adsets', [AutomationController::class, 'fetchAdSets'])->name('automation.fetchAdSets');
    Route::post('/automation/fetch-ads', [AutomationController::class, 'fetchAds'])->name('automation.fetchAds');
    Route::post('/automation/store-or-update', [AutomationController::class, 'storeOrUpdate'])->name('automation.storeOrUpdate');
    
    // CTWA
    Route::get('/ctwa', [CTWAController::class, 'index'])->name('ctwa.index');
    Route::get('/ctwa/create_ads', [CTWAController::class, 'create_ads'])->name('ctwa.create_ads');
    Route::get('/meta/countries', [CTWAController::class, 'getCountries']);
    Route::get('/meta/locations', [CTWAController::class, 'getLocations']);
    Route::get('/meta/meta-interests', [CTWAController::class, 'searchMetaInterests']);
    Route::get('/meta/pages', [CTWAController::class, 'getUserPages']);
    Route::post('/meta/page-profile', [CTWAController::class, 'getMetaProfileFromSelection']);
    Route::get('/meta/ad-accounts', [CTWAController::class, 'getMetaAdAccounts']);
    Route::post('/meta/ads/create', [CTWAController::class, 'submitCtwaAd'])->name('ctwa.create');
    Route::get('/ctwa/fetch-ads', [CtwaController::class, 'fetchAds'])->name('ctwa.fetch_ads');
    Route::get('/ctwa/fetch-store', [CtwaController::class, 'fetchAndStoreAds'])->name('ctwa.fetch_store_ads');
    Route::get('/ad-details/{adId}', [CtwaController::class, 'show'])->name('ad.details');
    Route::get('/ads/{ad}', [CtwaController::class, 'show'])->name('ads.show');
    Route::get('/leads/filter', [CtwaController::class, 'filter']);
    Route::post('/campaigns/send', [CtwaController::class, 'sendCampaign'])->name('campaign.send');









    Route::resource('credits', CreditsController::class);
    Route::post('/credits/costs', [CreditsController::class, 'updateCosts'])->name('credits.costs');
    Route::get('/billing', function (Request $request) {
        return $request->user()->redirectToBillingPortal(route('plans.current'));
    })->name('billing');

    // FlowGround - WhatsApp Flow Management
    Route::prefix('flow-ground')->name('flow-ground.')->namespace('Modules\FlowGround\Http\Controllers')->group(function () {
        Route::get('/', 'FlowGroundController@index')->name('index');
        Route::get('create', 'FlowGroundController@create')->name('create');
        Route::post('store', 'FlowGroundController@store')->name('store');
        Route::get('sync', 'FlowGroundController@sync')->name('sync');
        Route::get('view-data', 'FlowGroundController@viewData')->name('view_data');
        Route::get('{flowGround}/edit', 'FlowGroundController@edit')->name('edit');
        Route::put('{flowGround}', 'FlowGroundController@update')->name('update');
        Route::get('del/{flowGround}', 'FlowGroundController@destroy')->name('destroy');
    });
});


//Verify
Route::middleware('web')->group(function () {
    Route::get('/activation/{code}', [SettingsController::class, 'activation'])->name('project.activation');
});


// terms-conditions
Route::get('/terms-conditions', function () {
    return view('frontend.term_conditions');
})->name('terms-conditions');


// privacy_policy
Route::get('/privacy-policy', function () {
    return view('frontend.privacy_policy');
})->name('privacy-policy');

Route::get('/refund-policy', function () {
    return view('frontend.refund_policy');
})->name('refund-policy');

Route::get('/cancellation-policy', function () {
    return view('frontend.cancellation_policy');
})->name('cancellation-policy');


Route::get('cron-sync-leads',[App\Http\Controllers\FBLeadController::class, 'fetchAllCampaignLeads'])->name('cron-fb-leads');
// routes/web.php

Route::get('/fetch-facebook-accounts', [FBLeadController::class, 'fetchAllCampaign']);


Route::get('/test-send-whatsapp', function () {
    Artisan::call('automation:send-leads-to-whatsapp');
    return nl2br(Artisan::output());
});



Route::get('/automation/trigger-whatsapp', function (Request $request) {
    if ($request->query('key') !== env('CRON_SECRET_KEY')) {
        abort(403, 'Unauthorized');
    }

    return app(AutomationController::class)->triggerLeadWhatsApp($request);
});

Route::get('/trigger-leads', [AutomationController::class, 'triggerLeadWhatsApp'])->name('whatsapp.trigger');

Route::get('/broadcast-whatsapp-trigger', [AutomationController::class, 'fireWhatsAppTrigger'])->name('whatsapp.broadcast');

Route::get('/test-whatsapp-trigger', function () {
    broadcast(new WhatsAppTriggerRequested(env('CRON_SECRET_KEY')));
    return '📨 WhatsAppTriggerRequested event fired!';
})->name('whatsapp.test');

Route::post('/apply', [CandidateController::class, 'store'])->name('apply.store');


Route::get('account/profile/show', [UserProfileController::class, 'show'])->name('account.profile.show');
Route::get('account/profile/api', [UserProfileController::class, 'api'])->name('account.profile.api');
Route::get('account/profile/billing', [UserProfileController::class, 'billing'])->name('account.profile.billing');
Route::put('account/profile/{id}/update', [UserProfileController::class, 'update'])->name('account.profile.update');
Route::put('profile/update/billing', [UserProfileController::class, 'updateBilling'])->name('account.profile.billing.update');

Route::post('/save_data_google_facebook', [UserProfileController::class, 'saveData'])->name('save_data_google_facebook');
Route::post('/store/billing_data', [UserProfileController::class, 'storeBilling'])->name('store.billing');
Route::post('/omit_modal', [UserProfileController::class, 'omitModal'])->name('omit_modal');
