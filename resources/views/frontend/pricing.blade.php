@extends('frontend.layout.master')

@section('content')
<main class="main">
      <section class="section bg-plan-3 pt-110 pb-0">
        <div class="container">
          <div class="row align-items-end">
            <div class="col-lg-8 col-md-8">
              <h2 class="color-brand-1 mb-20 wow animate__animated animate__fadeIn" data-wow-delay=".0s">Choose The Best Plan</h2>
              <p class="font-lg color-gray-500 wow animate__animated animate__fadeIn" data-wow-delay=".2s">Pick your plan.<br class="d-none d-lg-block"> Change whenever you want.</p>
            </div>
            <div class="col-lg-4 col-md-4 text-md-end text-start wow animate__animated animate__fadeIn" data-wow-delay=".4s">
              <!-- <a class="btn btn-default font-sm-bold pl-0">Compare plans
                <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg></a> -->
              </div>
          </div>
          <div class="row mt-50">
             @foreach ($plans as $keyp => $plan)
                <div class="col-xl-3 col-lg-6 col-md-6 wow animate__animated animate__fadeIn" data-wow-delay=".0s">
                    <div class="card-plan hover-up">
                        <div class="card-image-plan">
                            <div class="icon-plan"><img src="/assets/imgs/page/homepage1/free.svg" alt="iori"></div>
                            <div class="info-plan">
                                <h4 class="color-brand-1">{{ $plan->name }}</h4>
                                <p class="font-md color-grey-400">{{ $plan->description }}</p>
                            </div>
                        </div>
                        <div class="box-day-trial">
                            <span class="font-lg-bold color-brand-1">
                                {{ config('money')[strtoupper(config('settings.cashier_currency'))]['symbol'] }} 
                                {{ $plan->price }}
                            </span>
                            <span class="font-md color-grey-500">
                                @if(config('settings.free_pricing_id') == $plan->id)
                                    - {{config('settings.free_trail_days')}} days trial
                                @else
                                    - {{ $plan['period'] == 1? __('month') :  __('year') }}</span></p>
                                @endif
                            </span><br>
                            <span class="font-xs color-grey-500">{{ __('wpbox.no_contracts')}} </span>
                        </div>
                        <div class="mt-30 mb-30">
                            <ul class="list-ticks list-ticks-2">
                                @foreach (explode(",",$plan['features']) as $feature)
                                <li>
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>{{ $feature }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @if (!config('settings.disable_registration_page',false))
                        <div class="mt-20">
                            <a class="btn btn-brand-1-full hover-up" href="{{ route('register') }}">      
                                @if(config('settings.free_pricing_id') == $plan->id)
                                {{__('Try for free')}}
                                @else
                                {{ __('wpbox.start_now')}}
                                @endif
                                <svg class="w-6 h-6 icon-16 ml-10" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
                @endforeach
          </div>
        </div>
      </section>
      <!-- <section class="section mt-50">
        <div class="container">
          <div class="text-center">
            <h2 class="color-brand-1 mb-40 wow animate__animated animate__fadeInUp" data-wow-delay=".0s">Compare Plans</h2>
          </div>
          <div class="table-responsive table-responsive-sm table-responsive-md">
            <table class="table table-compare">
              <thead>
                <tr>
                  <th class="width-28 color-success">Data</th>
                  <th class="width-18">Free</th>
                  <th class="width-18">Standard</th>
                  <th class="width-18">Business</th>
                  <th class="width-18">Enterprise</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Requests Quota</td>
                  <td>50k Requests/Day</td>
                  <td>100k Requests/Day</td>
                  <td>500k Requests/Day</td>
                  <td>Unlimited</td>
                </tr>
                <tr>
                  <td>Account Acess</td>
                  <td>35</td>
                  <td>85</td>
                  <td>120</td>
                  <td>Unlimited</td>
                </tr>
                <tr>
                  <td>Service Analystic</td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                </tr>
                <tr>
                  <td>Achive Nodes</td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                </tr>
                <tr>
                  <td>Enriched APIs</td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                </tr>
                <tr>
                  <td>Rosetta APIs</td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                </tr>
                <tr>
                  <td>Priority Support Response</td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                </tr>
                <tr>
                  <td>Dedicated Account Manager</td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                </tr>
                <tr>
                  <td>Custom SLAs</td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-disable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                  <td>
                    <svg class="w-6 h-6 icon-18 icon-enable" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section> -->
      <section class="section mt-50">
        <div class="container">
          <div class="row">
            <div class="col-lg-4 wow animate__animated animate__fadeIn" data-wow-delay=".0s">
              <div class="card-support">
                <div class="card-image">
                  <div class="box-circle-img">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                  </div>
                </div>
                <div class="card-info">
                  <h4 class="color-brand-1 mb-15">Daily Updates</h4>
                  <p class="font-md color-grey-500 mb-15">Share updates instantly within our project management software, and get the entire team collaborating</p>
                    <!-- <a class="btn btn-default pl-0 font-sm-bold hover-up" href="#">Learn More
                    <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg></a> -->
                </div>
              </div>
            </div>
            <div class="col-lg-4 wow animate__animated animate__fadeIn" data-wow-delay=".2s">
              <div class="card-support">
                <div class="card-image">
                  <div class="box-circle-img">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="card-info">
                  <h4 class="color-brand-1 mb-15">24/7 Support</h4>
                  <p class="font-md color-grey-500 mb-15">Share updates instantly within our project management software, and get the entire team collaborating</p>
                  <!-- <a class="btn btn-default pl-0 font-sm-bold hover-up" href="#">Learn More
                    <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg></a> -->
                </div>
              </div>
            </div>
            <div class="col-lg-4 wow animate__animated animate__fadeIn" data-wow-delay=".4s">
              <div class="card-support">
                <div class="card-image">
                  <div class="box-circle-img">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                </div>
                <div class="card-info">
                  <h4 class="color-brand-1 mb-15">Weekly Reports</h4>
                  <p class="font-md color-grey-500 mb-15">Share updates instantly within our project management software, and get the entire team collaborating</p>
                  <!-- <a class="btn btn-default pl-0 font-sm-bold hover-up" href="#">Learn More
                    <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg></a> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      </div>
      <section class="section mt-50 mb-40 box-testimonial-2">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12 text-center mb-70">
              <h2 class="color-brand-1 mb-20 wow animate__animated animate__fadeIn" data-wow-delay=".0s">What our custommers are saying</h2>
              <p class="font-lg color-gray-500 wow animate__animated animate__fadeIn" data-wow-delay=".2s">Hear from our users who have saved thousands on<br class="d-none d-lg-block">their Startup and SaaS solution spend.</p>
            </div>
          </div>
          <div class="row align-items-start" data-masonry="{&quot;percentPosition&quot;: true }">
            <div class="col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".0s">
              <div class="card-testimonial-grid">
                <div class="box-author mb-10"><a href="#"><img src="assets/imgs/page/homepage1/author.png" alt="iori"></a>
                  <div class="author-info"><a href="#"><span class="font-md-bold color-brand-1 author-name">Guy Hawkins</span></a><span class="font-xs color-grey-500 department">Bank of America</span></div>
                </div>
                <p class="font-md color-grey-500">Access the same project through five different dynamic views: a kanban board, Gantt chart, spreadsheet, calendar or simple task list. When team members can choose the work style that suits them best, productivity and engagement skyrocket. Maecenas lobortis risus.</p>
                <div class="card-bottom-info"><span class="font-xs color-grey-500 date-post">29 November 2022</span>
                  <div class="rating text-end"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".1s">
              <div class="card-testimonial-grid">
                <div class="box-author mb-10"><a href="#"><img src="assets/imgs/page/pricing/author1.png" alt="iori"></a>
                  <div class="author-info"><a href="#"><span class="font-md-bold color-brand-1 author-name">Bessie Cooper</span></a><span class="font-xs color-grey-500 department">Bank of America</span></div>
                </div>
                <p class="font-md color-grey-500">Et velit laborum cum temporibus similique qui nostrum odit hic Quis cupiditate et facilis sunt. Et dolorum nostrum qui culpa ullam ut consequatur voluptas eum modi nobis. Ut voluptatem voluptatum hic praesentium eveniet non quae laboriosam nam architecto consequatur aut obcaecati consequatur sit dolores labore ut officia velit. Eos officia consectetur sit labore voluptatem et quia recusandae</p>
                <div class="card-bottom-info"><span class="font-xs color-grey-500 date-post">29 November 2022</span>
                  <div class="rating text-end"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".2s">
              <div class="card-testimonial-grid">
                <div class="box-author mb-10"><a href="#"><img src="assets/imgs/page/pricing/author2.png" alt="iori"></a>
                  <div class="author-info"><a href="#"><span class="font-md-bold color-brand-1 author-name">Bessie Cooper</span></a><span class="font-xs color-grey-500 department">Bank of America</span></div>
                </div>
                <p class="font-md color-grey-500">Duis justo nulla, pulvinar at dolor dapibus, finibus cursus massa. Praesent quam diam, faucibus tristique enim in, rhoncus aliquam lorem. Vestibulum vestibulum pellentesque nunc sit amet ullamcorper. Praesent ligula felis</p>
                <div class="card-bottom-info"><span class="font-xs color-grey-500 date-post">29 November 2022</span>
                  <div class="rating text-end"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".3s">
              <div class="card-testimonial-grid">
                <div class="box-author mb-10"><a href="#"><img src="assets/imgs/page/homepage2/author2.png" alt="iori"></a>
                  <div class="author-info"><a href="#"><span class="font-md-bold color-brand-1 author-name">Esther Howard</span></a><span class="font-xs color-grey-500 department">Bank of America</span></div>
                </div>
                <p class="font-md color-grey-500">Vivamus venenatis turpis at elit aliquam, in mattis felis ullamcorper. Donec vel elit at diam suscipit volutpat. Donec rhoncus, magna vitae gravida condimentum, massa mauris fermentum est, vitae euismod leo magna vestibulum nunc</p>
                <div class="card-bottom-info"><span class="font-xs color-grey-500 date-post">29 November 2022</span>
                  <div class="rating text-end"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-30 testimonial-warning wow animate__animated animate__fadeIn" data-wow-delay=".4s">
              <div class="card-testimonial-grid">
                <div class="box-author mb-10"><a href="#"><img src="assets/imgs/page/pricing/author3.png" alt="iori"></a>
                  <div class="author-info"><a href="#"><span class="font-md-bold color-brand-1 author-name">Albert Flores</span></a><span class="font-xs color-grey-500 department">Bank of America</span></div>
                </div>
                <p class="font-md color-grey-500">Vivamus hendrerit molestie mi, a volutpat ipsum volutpat sit amet. Aenean sed metus pulvinar, efficitur lectus sit amet, consectetur nisl. Vivamus hendrerit moles Maecenas lobortis risus Maecenas lobortis risus</p>
                <div class="card-bottom-info"><span class="font-xs color-grey-500 date-post">29 November 2022</span>
                  <div class="rating text-end"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".5s">
              <div class="card-testimonial-grid">
                <div class="box-author mb-10"><a href="#"><img src="assets/imgs/page/pricing/author4.png" alt="iori"></a>
                  <div class="author-info"><a href="#"><span class="font-md-bold color-brand-1 author-name">Albert Flores</span></a><span class="font-xs color-grey-500 department">Bank of America</span></div>
                </div>
                <p class="font-md color-grey-500">Vivamus hendrerit molestie mi, a volutpat ipsum volutpat sit amet. Aenean sed metus pulvinar, efficitur lectus sit amet, consectetur nisl. Vivamus hendrerit moles Maecenas lobortis risus Maecenas lobortis risus</p>
                <div class="card-bottom-info"><span class="font-xs color-grey-500 date-post">29 November 2022</span>
                  <div class="rating text-end"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"><img src="assets/imgs/template/icons/star.svg" alt="iori"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- <section class="section mt-50">
        <div class="container">
          <div class="box-newsletter box-newsletter-2 wow animate__animated animate__fadeIn">
            <div class="row align-items-center">
              <div class="col-lg-6 col-md-7 m-auto text-center"><span class="font-lg color-brand-1 wow animate__animated animate__fadeIn" data-wow-delay=".0s">Newsletter</span>
                <h2 class="color-brand-1 mb-15 mt-5 wow animate__animated animate__fadeIn" data-wow-delay=".1s">Subcribe our newsletter</h2>
                <p class="font-md color-grey-500 wow animate__animated animate__fadeIn" data-wow-delay=".2s">Do not miss the latest information from us about the trending in the market. By clicking the button, you are agreeing with our Term & Conditions</p>
                <div class="form-newsletter mt-30 wow animate__animated animate__fadeIn" data-wow-delay=".3s">
                  <form action="#">
                    <input type="text" placeholder="Enter you mail ..">
                    <button class="btn btn-submit-newsletter" type="submit">
                      <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                      </svg>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section> -->
    </main>
@endsection