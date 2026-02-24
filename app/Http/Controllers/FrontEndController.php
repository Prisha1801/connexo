<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use App\Models\Plans;
use App\Models\Posts;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
class FrontEndController extends Controller
{
    public function register(): RedirectResponse
    {
        return redirect()->route('register');
    }

    public function index()
    {

        //1. Subdomain mode
        if ($this->getSubDomain()) {
            return $this->subdomainMode();
        }

        //1a. Custom domain mode
        $customDomain = $this->customDomainMode();
        if ($customDomain != '') {
            return $this->company($customDomain);
        }

        //2. Landing page
        //Check if landing is disabled
        if (config('settings.disable_landing_page', false)) {
            return redirect()->route('home');
        }
        return $this->landing();
        // $landingClassToUse = config('settings.landing_page');

        // return (new $landingClassToUse())->landing();
    }

    function getFacebookLeadInfo(Request $request) {
        try {
            // Validate input
            $validated = $request->validate([
                'to' => 'required|string',
                'template_name' => 'required|string',
            ]);

            // Get token from environment or config
            $accessToken = config('services.facebook.access_token') ?? env('FACEBOOK_ACCESS_TOKEN');
            
            if (!$accessToken) {
                return response()->json([
                    'error' => 'Facebook access token not configured',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Get business ID from config
            $businessId = config('services.facebook.business_id') ?? env('FACEBOOK_BUSINESS_ID', '390881210779682');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://graph.facebook.com/v21.0/{$businessId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $validated['to'],
                'type' => 'template',
                'template' => [
                    'name' => $validated['template_name'],
                    'language' => [
                        'code' => 'en_US',
                    ],
                ],
            ]);

            if ($response->successful()) {
                return response()->json([
                    'message' => 'Message sent successfully',
                    'response' => $response->json(),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'error' => 'Failed to send the message',
                    'response' => $response->json(),
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            \Log::error('Facebook lead info error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 2. Subdomain mode - directly show store.
     */
    public function subdomainMode()
    {
        $subDomain = $this->getSubDomain();
        if ($subDomain) {
            $company = Company::whereRaw('REPLACE(subdomain, "-", "") = ?', [str_replace('-', '', $subDomain)])->get();
            if (count($company) != 1) {
                //When Subdomain mode is disabled, show the error
                if (! config('settings.wildcard_domain_ready')) {
                    return view('companies.alertdomain', ['subdomain' => $subDomain]);
                } else {
                    abort(404);
                }

            }

            return $this->company($subDomain);
        }
    }

    /**
     * Gets subdomain.
     */
    public function getSubDomain()
    {
        $host = request()->getHost();
        $subdomain = substr_count(str_replace('www.', '', $host), '.') > 1 
            ? substr(str_replace('www.', '', $host), 0, strpos(str_replace('www.', '', $host), '.')) 
            : '';
        
        if ($subdomain == '' || in_array($subdomain, config('settings.ignore_subdomains', []))) {
            return false;
        }

        return $subdomain;
    }

    private function customDomainMode()
    {
        //1 - Make sure the module is installed
        if (! in_array('domain', config('global.modules', []))) {
            return '';
        }

        //2 - Extract the domain
        $domain = request()->getHost();

        //3 - Make sure, this is no the project domain itself,
        if (strpos(config('app.url'), $domain) !== false) {
            return '';
        }

        //4 - The extracted domain is in the list of custom values
        $theConfig = Config::where('value', 'like', '%'.$domain.'%')->first();
        if ($theConfig) {
            //5 - Return the company subdomain if company is active
            $vendor_id = $theConfig->model_id;

            $vendor = Company::where('id', $vendor_id)->first();
            if ($vendor) {
                return $vendor->subdomain;
            } else {
                return '';
            }

        } else {
            //By default return no domain
            return '';
        }
    }

    public function company($subdomain)
    {
        // Company page
        $pageClassToUse = config('settings.company_page');

        return (new $pageClassToUse())->companyLanding(Company::where('subdomain', $subdomain)->firstOrFail());
    }

     /** CUSTOME LANDINGPAGE */
     public function landing()
     {
 
         //Change Language
         $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
         if(request()->has('lang')){
              //this is language route
              $locale = request()->input('lang');
         }
 
         if($locale!="android-chrome-256x256.png"){
             App::setLocale(strtolower($locale));
             session(['applocale_change' => strtolower($locale)]);
         }
 
    
 
          //Landing page content
        //   $features = Posts::where('post_type', 'feature')->get();
        //   $testimonials = Posts::where('post_type', 'testimonial')->get();
          $faqs = Posts::where('post_type', 'faq')->get();
        //   $mainfeatures = Posts::where('post_type', 'mainfeature')->get();
 
          
 
         
          $colCounter = [1, 2, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];
          $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get():Plans::get();
         $data=[
             'col' => count($plans)>0?$colCounter[count($plans)-1]:4,
             'plans'=>$plans,
            //  'features' => $features,
            //  'processes' => $features,
            //  'mainfeatures' => $mainfeatures,
             'locale'=>strtolower($locale),
             'faqs' => $faqs,
            //  'testimonials' => $testimonials,
             'hasAIBots'=>Module::has('flowiseai')
         ];
         
 
         
         try {
             $response = new \Illuminate\Http\Response(view('frontend.landing', $data));
         } catch (\Throwable $th) {
             \Log::error('Landing page error: ' . $th->getMessage());
             if (config('app.debug')) {
                 return response()->view('errors.500', ['message' => $th->getMessage()], 500);
             }
             abort(500, 'Please read the update guide for version 3.2.0. You need to upload the landing page module');
         }
 
        
         App::setLocale(strtolower($locale));
         $response->withCookie(cookie('lang', $locale, 120));
         App::setLocale(strtolower($locale));
         
 
         return $response;
     }
 
     public function pricing(){
         try{   
            $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get():Plans::get();
            $data=[
                'plans'=>$plans,
            ];
             $response = new \Illuminate\Http\Response(view('frontend.pricing',$data));
             return $response;
         }catch(\Exception $e){
             
         }
     }
 
     public function features(){
         try{
            $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get():Plans::get();
            $data=[
                'plans'=>$plans,
            ];
             $response = new \Illuminate\Http\Response(view('frontend.features',$data));
             return $response;
         }catch(\Exception $e){
             \Log::error('Features page error: ' . $e->getMessage());
             return redirect()->route('landing')->with('error', 'An error occurred while loading features.');
         }
     }
 
     public function help(){
         try{
             $response = new \Illuminate\Http\Response(view('frontend.help'));
             return $response;
         }catch(\Exception $e){
 
         }
     }

     public function partner_program(){
        try{
            $response = new \Illuminate\Http\Response(view('frontend.partner_program'));
            return $response;
        }catch(\Exception $e){

        }
    }

    public function agreement(){
        try{
            $response = new \Illuminate\Http\Response(view('frontend.agreement'));
            return $response;
        }catch(\Exception $e){

        }
    }

    public function privacy_policy(){
        try{
            $response = new \Illuminate\Http\Response(view('frontend.privacy_policy'));
            return $response;
        }catch(\Exception $e){

        }
    }
    
 public function careers()
{
    try {
        return view('frontend.careers');
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Careers page error: '.$e->getMessage());

        // Optionally show a fallback page or error
        return response()->view('errors.500', [], 500);
    }
}


    
    
     
 
     public function contact(){
         try{
            $faqs = Posts::where('post_type', 'faq')->get();
            $data=['faqs'=>$faqs];
             $response = new \Illuminate\Http\Response(view('frontend.contact',$data));
             return $response;
         }catch(\Exception $e){
 
         }
     }
}
