<?php

namespace App\Http\Middleware;

use App\Models\CompanyCampaign;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsMenuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && Auth::user()->hasRole('owner')) {

            if (auth()->user()->company_id != null) {
                $company_campaign = CompanyCampaign::where('company_id',auth()->user()->company_id)->pluck('campaign_id')->toArray();
                config(['settings.is_facebook_leads_exist' => count($company_campaign) ? true : false]);
            }
        }
        return $next($request);
    }
}
