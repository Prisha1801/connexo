<header class="header sticky-bar header-home6">
    <div class="container">
        <div class="main-header">
            <div class="header-left">
                <div class="header-logo"><a class="d-flex" href="{{route('landing')}}"><img alt="{{config('app.name')}}" src="{{ config('settings.logo') }}"></a></div>
                <div class="header-nav">
                    <nav class="nav-main-menu d-none d-xl-block">
                        <ul class="main-menu">
                            <li class=""><a href="{{route('front.pricing')}}">Pricing</a></li>
                            <li class=""><a href="{{route('front.features')}}">Features</a></li>
                            <li class="has-children"><a href="#">Resources</a>
                                <ul class="sub-menu">
                                    <li><a href="{{route('front.help')}}">Help Center</a></li>
                                    <li><a href="@if (Route::currentRouteName() == 'landing')#faqs @else {{route('landing')}}#faqs @endif">FAQs</a></li>
                                    <li><a href="{{route('front.help')}}">Knowledge Base</a></li>
                                </ul>
                            </li>
                            <li><a href="{{route('front.contact')}}">Contact</a></li>
                        </ul>
                    </nav>
                    <div class="burger-icon burger-icon-white"><span class="burger-icon-top"></span><span class="burger-icon-mid"></span><span class="burger-icon-bottom"></span></div>
                </div>
                <div class="header-right">
                    @guest
                    <div class="d-none d-sm-inline-block" style="margin-right: 10px;">
                        <a class="btn btn-brand-1 hover-up" href="{{route('register')}}">Start {{config('settings.free_trail_days')}}-Days Free Trial</a>
                    </div>
                    <div class="d-none d-sm-inline-block">
                        <a class="btn btn-brand-1 hover-up" href="{{route('login')}}">{{__('Login')}}</a>
                    </div>
                    @endguest
                    @auth
                    <div class="d-none d-sm-inline-block">
                        <a class="btn btn-brand-1 hover-up" href="{{ route('home') }}">{{ __('wpbox.dashboard')}}</a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
</header>