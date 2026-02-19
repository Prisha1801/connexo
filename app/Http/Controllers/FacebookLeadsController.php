<?php

namespace App\Http\Controllers;

use App\Models\CompanyCampaign;
use App\Models\FacebookLeads;
use Illuminate\Http\Request;

class FacebookLeadsController extends Controller
{
    
    public function index() 
    {
        try {
            $user = auth()->user();
    
            if ($user->id) {
                $user_id = $user->id;
    
                // Start query on facebook_leads table
                $campaignData = FacebookLeads::where('user_id', $user_id); 
                
                // Apply optional campaign filter from GET
                if (!empty($_GET['campaign'])) {
                    $campaignData->where('campaign_id', $_GET['campaign']);
                }
    
                // Paginate results
                $campaignData = $campaignData
                    ->orderBy('created_time', 'desc')
                    ->paginate(10)
                    ->appends($_GET);
    
                return view('facebookleads.index', [
                    'fbLeads'    => $campaignData,
                    'parameters' => count($_GET) !== 0,
                ]);
            }
    
            return redirect()->route('dashboard')->withErrors('User not found.');
        } catch (\Exception $e) {
            \Log::error('Facebook Leads Index Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Something went wrong. Please try again.');
        }
    }
    
    public function index___() {
         try{
            if (auth()->user()->company_id != null) {

            $company_campaign = CompanyCampaign::where('company_id',auth()->user()->company_id)->pluck('campaign_id')->toArray();


            $campaignData = FacebookLeads::select('*');

            if(isset($_GET['campaign']) && !empty($_GET['campaign'])):
                $campaignData = $campaignData->where('campaign_id',$_GET['campaign']);
            endif;

            $campaignData = $campaignData->whereIn('campaign_id',$company_campaign)->orderBy('created_time','desc')->paginate(10)->appends($_GET);


            return view('facebookleads.index', [
                'fbLeads'=>$campaignData,
                'parameters'=>count($_GET) != 0,
            ]);
            }
         }catch(\Exception $e){
            echo '<pre>';
            print_r($e->getMessage());
            die;
            return redirect()->route('dashboard')->withErrors($e->getMessage());
         }
    }
}
