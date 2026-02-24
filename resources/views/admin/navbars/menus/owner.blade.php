<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link @if (Route::currentRouteName() == 'dashboard') active @endif"
            href="{{ route('dashboard') }}">
            <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
        </a>
    </li>

    @include('admin.navbars.menus.extra')

    @if (!config('settings.hide_company_profile',false))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'admin.companies.edit') active @endif"
                href="{{ route('admin.companies.edit', auth()->user()->currentCompany()->id) }}">
                <i class="ni ni-shop text-primary"></i> {{ __('Company') }}
            </a>
        </li>
    @endif

    <!--  @if(config('settings.enable_leads'))-->
    <!--    <li class="nav-item dropdown">-->
    <!--        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button"-->
    <!--           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
    <!--            <i class="ni ni-diamond text-orange mr-2"></i>-->
    <!--            <span class="font-weight-bold">Automation</span>-->
    <!--            <i class="ml-1 fas fa-caret-down text-light"></i>-->
    <!--        </a>-->
    <!--        <div class="dropdown-menu dropdown-menu-right shadow animated fadeIn rounded-lg border-0 p-2 bg-white" aria-labelledby="navbarDropdown">-->
    <!--            <a class="dropdown-item d-flex align-items-center rounded hover-bg-light px-3 py-2" href="{{route('automation.reconnect')}}">-->
    <!--                <i class="fas fa-users text-primary mr-2"></i>-->
    <!--                <div>-->
    <!--                    <div class="font-weight-bold">Re-Connect</div>-->
    <!--                </div>-->
    <!--            </a>-->
    <!--            <a class="dropdown-item d-flex align-items-center rounded hover-bg-light px-3 py-2" href="{{ route('facebooklead.index') }}">-->
    <!--                <i class="fas fa-users text-primary mr-2"></i>-->
    <!--                <div>-->
    <!--                    <div class="font-weight-bold">Leads</div>-->
    <!--                </div>-->
    <!--            </a>-->
    <!--            <a class="dropdown-item d-flex align-items-center rounded hover-bg-light px-3 py-2" href="{{ route('automationform.index') }}">-->
    <!--                <i class="fas fa-users text-primary mr-2"></i>-->
    <!--                <div>-->
    <!--                    <div class="font-weight-bold">Form</div>-->
    <!--                </div>-->
    <!--            </a>-->
    <!--        </div>-->
    <!--    </li>-->
    <!--@endif-->
    
    @if(config('settings.enable_leads'))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'automation.fb_automation') active @endif"
                href="{{ route('automation.fb_automation') }}">
                <i class="ni ni-atom text-red"></i> {{ __('Automation') }}
            </a>
        </li>
    @endif
  @if(config('settings.enable_leads'))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'pay_meta.pay_meta') active @endif"
                href="https://business.facebook.com/billing_hub/accounts/details/?asset_id=410443552149991&business_id=1728068524239304&external_flow_id=SU-1754652354562--2037419559-2209991327&placement=whatsapp_ads&payment_account_id=1436753670278013">
                <i class="ni ni-credit-card text-red"></i> {{ __('Pay Meta') }}
            </a>
        </li>
    @endif


    @if (!config('settings.hide_company_apps',false))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'admin.apps.company') active @endif"
                href="{{ route('admin.apps.company') }}">
                <i class="ni ni-spaceship text-red"></i> {{ __('Apps') }}
            </a>
        </li>
    @endif
   

    @if(config('settings.enable_pricing'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('plans.current') }}">
                <i class="ni ni-credit-card text-orange"></i> {{ __('Plan') }}
            </a>
        </li>
    @endif
    <!--@if(config('settings.enable_leads'))-->
    <!--    <li class="nav-item">-->
    <!--        <a class="nav-link" href="{{ route('facebooklead.index') }}">-->
    <!--            <i class="ni ni-diamond text-orange"></i> {{ __('FaceBook Automation') }}-->
    <!--        </a>-->
    <!--         <ul class="nxl-submenu">-->
    <!--             <li class="nxl-item"><a class="nxl-link" href="{{ route('facebooklead.index') }}">Leads</a></li>-->
    <!--         </ul>-->
    <!--    </li>-->
    <!--@endif-->


    
   



    @if (!config('settings.hide_share_link',false))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.share') }}">
                <i class="ni ni-send text-green"></i> {{ __('Share') }}
            </a>
        </li>
        
    @endif
    

       
    

</ul>
@if (config('vendorlinks.enable',false))
<hr class="my-3">
<h6 class="navbar-heading p-0 text-muted">
    <span class="docs-normal">{{__(config('vendorlinks.name',""))}}</span>
</h6>
<ul class="navbar-nav mb-md-3">
    @if (strlen(config('vendorlinks.link1link',""))>4)
        <li class="nav-item">
            <a class="nav-link" href="{{config('vendorlinks.link1link',"")}}" target="_blank">
                <span class="nav-link-text">{{__(config('vendorlinks.link1name',""))}}</span>
            </a>
        </li>
    @endif

    @if (strlen(config('vendorlinks.link2link',""))>4)
        <li class="nav-item">
            <a class="nav-link" href="{{config('vendorlinks.link2link',"")}}" target="_blank">
                <span class="nav-link-text">{{__(config('vendorlinks.link2name',""))}}</span>
            </a>
        </li>
    @endif

    @if (strlen(config('vendorlinks.link3link',""))>4)
        <li class="nav-item">
            <a class="nav-link" href="{{config('vendorlinks.link3link',"")}}" target="_blank">
                <span class="nav-link-text">{{__(config('vendorlinks.link3name',""))}}</span>
            </a>
        </li>
    @endif
    
</ul>
@endif