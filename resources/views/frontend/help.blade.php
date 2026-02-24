@extends('frontend.layout.master')
@section('content')
<main class="main">
    <section class="section mt-90">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-40"><span class="title-line line-48 wow animate__animated animate__fadeInUp" data-wow-delay=".0s">Support Center</span>
                    <h2 class="color-brand-1 mt-15 mb-30 wow animate__animated animate__fadeInUp" data-wow-delay=".2s">How can we help you?</h2>
                    <p class="font-md color-grey-500 wow animate__animated animate__fadeInUp" data-wow-delay=".4s">Search here to get answers to your questions</p>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="box-notify-me mt-15">
                                <div class="inner-notify-me">
                                    <input class="form-control" type="text" placeholder="Search the doc ..">
                                    <button class="btn btn-brand-1 font-md">Search
                                        <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-45 wow animate__animated animate__fadeInUp" data-wow-delay=".0s">
                        <p class="font-sm-bold color-brand-1">Suggested Search:</p>
                    </div>
                    <div class="mt-10 wow animate__animated animate__fadeInUp" data-wow-delay=".0s"><a class="btn btn-tag-circle mr-10 mb-10" href="#">guest users</a><a class="btn btn-tag-circle mr-10 mb-10" href="#">create account</a><a class="btn btn-tag-circle mr-10 mb-10" href="#">invoice</a><a class="btn btn-tag-circle mb-10" href="#">security</a></div>
                </div>
                <div class="col-lg-7 mb-40 d-none d-md-block">
                    <div class="box-banner-help">
                        <div class="box-cruelty shape-1"><img src="assets/imgs/page/help/cruelty.png" alt="iori"></div>
                        <div class="banner-img-1"><img src="assets/imgs/page/help/banner1.png" alt="iori"></div>
                        <div class="banner-img-2"><img src="assets/imgs/page/help/banner2.png" alt="iori"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section mt-40 pt-50 pb-15 bg-grey-80">
        <div class="container">
            <div class="box-swiper">
                <div class="swiper-container swiper-group-3">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="card-guide">
                                <div class="card-image"><img src="assets/imgs/page/help/icon1.svg" alt="iori"></div>
                                <div class="card-info">
                                    <h5 class="color-brand-1 mb-15">Knowledge Base</h5>
                                    <p class="font-xs color-grey-500">Aliquam a augue suscipit, luctus neque purus ipsum neque dolor primis a libero tempus</p>
                                    <div class="mt-10"><a class="btn btn-default font-sm-bold pl-0" href="#">Get Started
                                            <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg></a></div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card-guide">
                                <div class="card-image"><img src="assets/imgs/page/help/icon2.svg" alt="iori"></div>
                                <div class="card-info">
                                    <h5 class="color-brand-1 mb-15">Community Forums</h5>
                                    <p class="font-xs color-grey-500">Aliquam a augue suscipit, luctus neque purus ipsum neque dolor primis a libero tempus</p>
                                    <div class="mt-10"><a class="btn btn-default font-sm-bold pl-0" href="#">Get Started
                                            <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg></a></div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card-guide">
                                <div class="card-image"><img src="assets/imgs/page/help/icon3.svg" alt="iori"></div>
                                <div class="card-info">
                                    <h5 class="color-brand-1 mb-15">Documentation</h5>
                                    <p class="font-xs color-grey-500">Aliquam a augue suscipit, luctus neque purus ipsum neque dolor primis a libero tempus</p>
                                    <div class="mt-10"><a class="btn btn-default font-sm-bold pl-0" href="#">Get Started
                                            <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section mt-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="color-brand-1 mb-20 wow animate__animated animate__fadeInUp" data-wow-delay=".0s">Explore Topics</h2>
                    <p class="font-lg color-gray-500 wow animate__animated animate__fadeInUp" data-wow-delay=".2s">Easily create Documentation, Knowledge-base, FAQ, Forum and more</p>
                </div>
            </div>
            <div class="mt-50">
                <div class="box-swiper swiper-style-2">
                    <div class="swiper-container swiper-group-4">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="card-offer-style-3">
                                    <div class="card-head">
                                        <div class="card-image"><img src="assets/imgs/page/homepage1/cross.png" alt="iori"></div>
                                        <div class="carrd-title">
                                            <h4 class="color-brand-1">Getting Started</h4>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        <ul class="list-dots mb-10">
                                            <li class="font-sm color-grey-500 mb-5">Welcome to our platform</li>
                                            <li class="font-sm color-grey-500 mb-5">Writing posts with CMS</li>
                                            <li class="font-sm color-grey-500 mb-5">Publishing options</li>
                                            <li class="font-sm color-grey-500 mb-5">Managing admin settings</li>
                                            <li class="font-sm color-grey-500 mb-5">Create your content</li>
                                        </ul>
                                        <div class="box-button-offer">
                                            <!-- <a class="btn btn-default font-sm-bold pl-0 color-brand-1 hover-up">Learn More
                                                <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg></a> -->
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide head-bg-brand-2">
                                <div class="card-offer-style-3">
                                    <div class="card-head">
                                        <div class="card-image"><img src="assets/imgs/page/homepage1/cross2.png" alt="iori"></div>
                                        <div class="carrd-title">
                                            <h4 class="color-brand-1">Strategic Consulting</h4>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        <ul class="list-dots mb-10">
                                            <li class="font-sm color-grey-500 mb-5">Welcome to our platform</li>
                                            <li class="font-sm color-grey-500 mb-5">Writing posts with CMS</li>
                                            <li class="font-sm color-grey-500 mb-5">Publishing options</li>
                                            <li class="font-sm color-grey-500 mb-5">Managing admin settings</li>
                                            <li class="font-sm color-grey-500 mb-5">Create your content</li>
                                        </ul>
                                        <div class="box-button-offer">
                                            <!-- <a class="btn btn-default font-sm-bold pl-0 color-brand-1 hover-up">Learn More
                                                <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg></a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide head-bg-2">
                                <div class="card-offer-style-3">
                                    <div class="card-head">
                                        <div class="card-image"><img src="assets/imgs/page/homepage1/business.svg" alt="iori"></div>
                                        <div class="carrd-title">
                                            <h4 class="color-brand-1">Local Marketing</h4>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        <ul class="list-dots mb-10">
                                            <li class="font-sm color-grey-500 mb-5">Welcome to our platform</li>
                                            <li class="font-sm color-grey-500">Writing posts with CMS</li>
                                            <li class="font-sm color-grey-500 mb-5">Publishing options</li>
                                            <li class="font-sm color-grey-500 mb-5">Managing admin settings</li>
                                            <li class="font-sm color-grey-500 mb-5">Create your content</li>
                                        </ul>
                                        <div class="box-button-offer">
                                            <!-- <a class="btn btn-default font-sm-bold pl-0 color-brand-1 hover-up">Learn More
                                                <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg></a> -->
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide head-bg-5">
                                <div class="card-offer-style-3">
                                    <div class="card-head">
                                        <div class="card-image"><img src="assets/imgs/page/homepage1/cross4.png" alt="iori"></div>
                                        <div class="carrd-title">
                                            <h4 class="color-brand-1">Cognitive Solution</h4>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        <ul class="list-dots mb-10">
                                            <li class="font-sm color-grey-500 mb-5">Welcome to our platform</li>
                                            <li class="font-sm color-grey-500 mb-5">Writing posts with CMS</li>
                                            <li class="font-sm color-grey-500 mb-5">Publishing options</li>
                                            <li class="font-sm color-grey-500 mb-5">Managing admin settings</li>
                                            <li class="font-sm color-grey-500 mb-5">Create your content</li>
                                        </ul>
                                        <div class="box-button-offer">
                                            <!-- <a class="btn btn-default font-sm-bold pl-0 color-brand-1 hover-up">Learn More
                                                <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg></a> -->
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-button-slider-bottom">
                        <div class="swiper-button-prev swiper-button-prev-group-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </div>
                        <div class="swiper-button-next swiper-button-next-group-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section mt-20 mb-40">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="color-brand-1 mb-20 wow animate__animated animate__fadeInUp" data-wow-delay=".0s">From Community Forums</h2>
                    <p class="font-lg color-gray-500 wow animate__animated animate__fadeInUp" data-wow-delay=".2s">Updated on September 24, 2023</p>
                </div>
            </div>
            <div class="table-box-help mt-50">
                <div class="table-responsive">
                    <table class="table table-forum">
                        <thead>
                            <tr>
                                <th class="width-50">Forum</th>
                                <th class="width-16">Topics</th>
                                <th class="width-16">Comments</th>
                                <th class="width-18">Latest Post</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="item-forum">
                                        <div class="item-image"><img src="assets/imgs/page/homepage1/cross5.png" alt="iori"></div>
                                        <div class="item-info">
                                            <h4 class="color-brand-1 mb-15">Announcements</h4>
                                            <p class="font-md color-grey-500">Seamless importing and round-tripping of Microsoft Project plans, Excel files & CSV files.</p>
                                        </div>
                                    </div>
                                </td>
                                <td>3</td>
                                <td>16</td>
                                <td>
                                    <div class="box-author"><span><img src="assets/imgs/page/help/author.png" alt="iori"></span>
                                        <div class="author-info"><span class="font-lg color-brand-1 author-name">Steven Job</span><span class="font-sm color-grey-500 department">16 mins ago</span></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="item-forum">
                                        <div class="item-image"><img src="assets/imgs/page/homepage2/creation.png" alt="iori"></div>
                                        <div class="item-info">
                                            <h4 class="color-brand-1 mb-15">User Feedback</h4>
                                            <p class="font-md color-grey-500">Seamless importing and round-tripping of Microsoft Project plans, Excel files & CSV files.</p>
                                        </div>
                                    </div>
                                </td>
                                <td>3</td>
                                <td>16</td>
                                <td>
                                    <div class="box-author"><span><img src="assets/imgs/page/help/author2.png" alt="iori"></span>
                                        <div class="author-info"><span class="font-lg color-brand-1 author-name">Steven Job</span><span class="font-sm color-grey-500 department">16 mins ago</span></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="item-forum">
                                        <div class="item-image"><img src="assets/imgs/page/homepage1/cross4.png" alt="iori"></div>
                                        <div class="item-info">
                                            <h4 class="color-brand-1 mb-15">Technology support center</h4>
                                            <p class="font-md color-grey-500">Seamless importing and round-tripping of Microsoft Project plans, Excel files & CSV files.</p>
                                        </div>
                                    </div>
                                </td>
                                <td>3</td>
                                <td>16</td>
                                <td>
                                    <div class="box-author"><span><img src="assets/imgs/page/help/author3.png" alt="iori"></span>
                                        <div class="author-info"><span class="font-lg color-brand-1 author-name">Steven Job</span><span class="font-sm color-grey-500 department">16 mins ago</span></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="item-forum">
                                        <div class="item-image"><img src="assets/imgs/page/homepage1/cross2.png" alt="iori"></div>
                                        <div class="item-info">
                                            <h4 class="color-brand-1 mb-15">Product Support</h4>
                                            <p class="font-md color-grey-500">Seamless importing and round-tripping of Microsoft Project plans, Excel files & CSV files.</p>
                                        </div>
                                    </div>
                                </td>
                                <td>3</td>
                                <td>16</td>
                                <td>
                                    <div class="box-author"><span><img src="assets/imgs/page/help/author4.png" alt="iori"></span>
                                        <div class="author-info"><span class="font-lg color-brand-1 author-name">Steven Job</span><span class="font-sm color-grey-500 department">16 mins ago</span></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="item-forum">
                                        <div class="item-image"><img src="assets/imgs/page/homepage3/certification.png" alt="iori"></div>
                                        <div class="item-info">
                                            <h4 class="color-brand-1 mb-15">Market research</h4>
                                            <p class="font-md color-grey-500">Seamless importing and round-tripping of Microsoft Project plans, Excel files & CSV files.</p>
                                        </div>
                                    </div>
                                </td>
                                <td>3</td>
                                <td>16</td>
                                <td>
                                    <div class="box-author"><span><img src="assets/imgs/page/help/author5.png" alt="iori"></span>
                                        <div class="author-info"><span class="font-lg color-brand-1 author-name">Steven Job</span><span class="font-sm color-grey-500 department">16 mins ago</span></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="item-forum">
                                        <div class="item-image"><img src="assets/imgs/page/homepage2/identity.png" alt="iori"></div>
                                        <div class="item-info">
                                            <h4 class="color-brand-1 mb-15">Strategic Consulting</h4>
                                            <p class="font-md color-grey-500">Seamless importing and round-tripping of Microsoft Project plans, Excel files & CSV files.</p>
                                        </div>
                                    </div>
                                </td>
                                <td>3</td>
                                <td>16</td>
                                <td>
                                    <div class="box-author"><span><img src="assets/imgs/page/help/author6.png" alt="iori"></span>
                                        <div class="author-info"><span class="font-lg color-brand-1 author-name">Steven Job</span><span class="font-sm color-grey-500 department">16 mins ago</span></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="item-forum">
                                        <div class="item-image"><img src="assets/imgs/page/homepage3/enterprise.png" alt="iori"></div>
                                        <div class="item-info">
                                            <h4 class="color-brand-1 mb-15">Cognitive Solution</h4>
                                            <p class="font-md color-grey-500">Seamless importing and round-tripping of Microsoft Project plans, Excel files & CSV files.</p>
                                        </div>
                                    </div>
                                </td>
                                <td>3</td>
                                <td>16</td>
                                <td>
                                    <div class="box-author"><span><img src="assets/imgs/page/help/author7.png" alt="iori"></span>
                                        <div class="author-info"><span class="font-lg color-brand-1 author-name">Steven Job</span><span class="font-sm color-grey-500 department">16 mins ago</span></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <section class="section mt-50 mb-30">
        <div class="container">
            <div class="box-radius-border box-radius-border-help">
                <div class="row align-items-center">
                    <div class="col-xl-6 col-lg-6"><img class="d-block" src="assets/imgs/page/help/answer.png" alt="iori"></div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="box-info-answer"><span class="btn btn-tag">More help</span>
                            <h2 class="color-brand-1 mt-10 mb-15">Can’t find an answer?</h2>
                            <p class="color-grey-500 font-md">Make use of a qualified tutor to get the answer</p>
                            <div class="box-button mt-30"> <a class="btn btn-brand-1 hover-up" href="#">Ask a Question</a><a class="btn btn-default font-sm-bold hover-up" href="{{route('front.contact')}}">
                                    Contact Us
                                    <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- <section class="section mt-50">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-lg-8 col-md-8">
                    <h2 class="color-brand-1 mb-20 wow animate__animated animate__fadeInUp" data-wow-delay=".0s">From our blog</h2>
                    <p class="font-lg color-gray-500 wow animate__animated animate__fadeInUp" data-wow-delay=".2s">Aenean velit nisl, aliquam eget diam eu, rhoncus tristique dolor.<br class="d-none d-lg-block">Aenean vulputate sodales urna ut vestibulum</p>
                </div>
                <div class="col-lg-4 col-md-4 text-md-end text-start wow animate__animated animate__fadeInUp" data-wow-delay=".4s"><a class="btn btn-default font-sm-bold pl-0">View All
                        <svg class="w-6 h-6 icon-16 ml-5" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg></a></div>
            </div>
            <div class="row mt-55">
                <div class="col-xl-3 col-lg-6 col-md-6 wow animate__animated animate__fadeIn" data-wow-delay=".0s">
                    <div class="card-blog-grid card-blog-grid-2 hover-up">
                        <div class="card-image img-reveal"><a href="blog-detail.html"><img src="assets/imgs/page/homepage2/news1.png" alt="iori"></a></div>
                        <div class="card-info"><a href="blog-detail.html">
                                <h6 class="color-brand-1">Easy Ways to Make Money While You Sleep</h6>
                            </a>
                            <p class="font-sm color-grey-500 mt-20">Fusce dictum ullamcorper dui, id dignissim arcu volutpat vitae. Aenean mattis vestibulum odio eu facilisis. Aenean quam arcu, blandit at aliquet sit amet, convallis nec risus.</p>
                            <div class="mt-20 d-flex align-items-center border-top pt-20"><a class="btn btn-border-brand-1 mr-15" href="/">Technology</a><span class="date-post font-xs color-grey-300 mr-15">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>29 May 2022</span><span class="time-read font-xs color-grey-300">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>3 mins read</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 wow animate__animated animate__fadeIn" data-wow-delay=".1s">
                    <div class="card-blog-grid card-blog-grid-2 hover-up">
                        <div class="card-image img-reveal"><a href="blog-detail.html"><img src="assets/imgs/page/homepage2/news2.png" alt="iori"></a></div>
                        <div class="card-info"><a href="blog-detail.html">
                                <h6 class="color-brand-1">Tiktok video size guide: Everything you need to know</h6>
                            </a>
                            <p class="font-sm color-grey-500 mt-20">Fusce dictum ullamcorper dui, id dignissim arcu volutpat vitae. Aenean mattis vestibulum odio eu facilisis. Aenean quam arcu, blandit at aliquet sit amet, convallis nec risus.</p>
                            <div class="mt-20 d-flex align-items-center border-top pt-20"><a class="btn btn-border-brand-1 mr-15" href="/">Marketting</a><span class="date-post font-xs color-grey-300 mr-15">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>29 May 2022</span><span class="time-read font-xs color-grey-300">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>3 mins read</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 wow animate__animated animate__fadeIn" data-wow-delay=".2s">
                    <div class="card-blog-grid card-blog-grid-2 hover-up">
                        <div class="card-image img-reveal"><a href="blog-detail.html"><img src="assets/imgs/page/homepage1/news2.png" alt="iori"></a></div>
                        <div class="card-info"><a href="blog-detail.html">
                                <h6 class="color-brand-1">How to convert video to MP4 for free online</h6>
                            </a>
                            <p class="font-sm color-grey-500 mt-20">Fusce dictum ullamcorper dui, id dignissim arcu volutpat vitae. Aenean mattis vestibulum odio eu facilisis. Aenean quam arcu, blandit at aliquet sit amet, convallis nec risus.</p>
                            <div class="mt-20 d-flex align-items-center border-top pt-20"><a class="btn btn-border-brand-1 mr-15" href="/">Media</a><span class="date-post font-xs color-grey-300 mr-15">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>29 May 2022</span><span class="time-read font-xs color-grey-300">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>3 mins read</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 wow animate__animated animate__fadeIn" data-wow-delay=".3s">
                    <div class="card-blog-grid card-blog-grid-2 hover-up">
                        <div class="card-image img-reveal"><a href="blog-detail.html"><img src="assets/imgs/page/homepage2/news3.png" alt="iori"></a></div>
                        <div class="card-info"><a href="blog-detail.html">
                                <h6 class="color-brand-1">5 fastest ways to increase search engine rankings</h6>
                            </a>
                            <p class="font-sm color-grey-500 mt-20">Fusce dictum ullamcorper dui, id dignissim arcu volutpat vitae. Aenean mattis vestibulum odio eu facilisis. Aenean quam arcu, blandit at aliquet sit amet, convallis nec risus.</p>
                            <div class="mt-20 d-flex align-items-center border-top pt-20"><a class="btn btn-border-brand-1 mr-15" href="/">SEO</a><span class="date-post font-xs color-grey-300 mr-15">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>29 May 2022</span><span class="time-read font-xs color-grey-300">
                                    <svg class="w-6 h-6 icon-16" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>3 mins read</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
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