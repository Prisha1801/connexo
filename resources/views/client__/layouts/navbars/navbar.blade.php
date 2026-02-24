@auth()

@if(!isset($hideActions))
    @include('client.layouts.navbars.navs.auth')
@endif

@endauth

@guest()
@if(\Request::route()->getName() != "order.success")
    @include('client.layouts.navbars.navs.guest')
@endif
@endguest

