{{-- CTWA Navigation (used in header context) --}}
<nav class="ctwa-nav">
    <a href="{{ route('ctwa.panel') }}" class="{{ request()->routeIs('ctwa.panel') ? 'active' : '' }}"><i class="ni ni-app me-1"></i> {{ __('Panel') }}</a>
    <a href="{{ route('ctwa.index') }}" class="{{ request()->routeIs('ctwa.index') ? 'active' : '' }}"><i class="ni ni-chart-bar-32 me-1"></i> {{ __('Ads') }}</a>
    <a href="{{ route('ctwa.leads') }}" class="{{ request()->routeIs('ctwa.leads') ? 'active' : '' }}"><i class="ni ni-single-02 me-1"></i> {{ __('Leads') }}</a>
    <a href="{{ route('ctwa.create_ads') }}" class="{{ request()->routeIs('ctwa.create_ads') ? 'active' : '' }}"><i class="ni ni-fat-add me-1"></i> {{ __('Create Ads') }}</a>
</nav>
