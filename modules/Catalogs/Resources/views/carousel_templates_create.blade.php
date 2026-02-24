@extends('general.index', ['title' => __('Carousel Templates')])

@section('content')
    <!--begin::Container-->
    <div class="container py-8">
        <!--begin::Card-->
        <div class="card card-custom border-0 shadow-lg">
            <div class="card-header bg-primary border-0 py-6">
                <div class="card-title">
                    <h2 class="card-label text-white font-weight-bolder text-uppercase">
                        <i class="fas fa-layer-group mr-3"></i>{{ __('Create Carousel Template') }}
                    </h2>
                </div>
            </div>

            <form method="POST" action="" id="template_creator" enctype="multipart/form-data">
                @csrf
                <div class="container-fluid px-8 py-6" id="template_management">
                    <div class="row">
                        <!-- Template Basics Section -->
                        <div class="col-12 mb-8">
                            <div class="card card-custom border-0">
                                <div class="card-header align-items-center border-0">
                                    <h3 class="card-title text-dark-75 font-size-h4 mb-0">
                                        <i class="fas fa-cog text-primary mr-3"></i>
                                        {{ __('Template Basics') }}
                                    </h3>
                                </div>
                                <div class="card-body py-7">
                                    <div class="row">
                                        <!-- Template Name -->
                                        <div class="col-md-6 mb-7">
                                            <label class="font-weight-bolder text-dark mb-3">{{ __('Name') }}</label>
                                            <div class="input-icon input-icon-right">
                                                <input v-model="template_name" type="text" name="name"
                                                    class="form-control form-control-solid form-control-lg h-auto"
                                                    placeholder="{{ __('Template name') }}" required>

                                            </div>
                                        </div>

                                        <!-- Template Language -->
                                        <div class="col-md-6 mb-7">
                                            <label class="font-weight-bolder text-dark mb-3">{{ __('Language') }}</label>
                                            <div class="input-icon input-icon-right">
                                                <select v-model="language" name="language"
                                                    class="form-control form-control-solid form-control-lg h-auto">
                                                    <option value="">{{ __('Select language') }}</option>
                                                    @foreach ($languages as $language)
                                                        <option value="{{ $language[1] }}">{{ $language[0] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Products -->
                                        <div class="col-12 mb-3">
                                            <label class="font-weight-bolder text-dark mb-3">{{ __('Products') }}</label>
                                            <select v-model="product" name="product[]"
                                                class="form-control form-control-solid form-control-lg select2" multiple>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->retailer_id }}">
                                                        {{ $product->product_name }}</option>
                                                @endforeach
                                            </select>
                                            <span
                                                class="form-text text-muted">{{ __('Select products for this template') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="col-xl-6 mb-8">
                            <div class="card card-custom border-0 h-100">
                                <div class="card-header align-items-center border-0">
                                    <h3 class="card-title text-dark-75 font-size-h4 mb-0">
                                        <i class="fas fa-edit text-primary mr-3"></i>
                                        {{ __('Content Editor') }}
                                    </h3>
                                </div>
                                <div class="card-body py-7">
                                    <!-- Body Content -->
                                    <div class="form-group mb-8">
                                        <label class="font-weight-bolder text-dark mb-3">
                                            {{ __('Body') }}
                                            <span class="label label-inline label-primary ml-2">{{ __('Required') }}</span>
                                        </label>
                                        <p class="text-muted font-size-sm mb-4">
                                            {{ __('Enter message text in selected language') }}</p>
                                        <textarea rows="5" v-model="bodyText" name="body" class="form-control form-control-lg form-control-solid"
                                            placeholder="{{ __('Type your message here...') }}"></textarea>

                                        <div class="text-muted fs-7 mt-2">
                                            {{ __('Shortcut Keys: CTRL + B for Bold, CTRL + I for Italics, CTRL + SHIFT + C for Monospace, CTRL + SHIFT + X for Strikethrough. Character limit: 1024.') }}
                                        </div>
                                        <div class="d-flex justify-content-end gap-2 mt-3">
                                            <button @click="addBold()" class="btn btn-icon btn-light btn-sm" type="button">
                                                <span class="btn-inner--icon">B</span>
                                            </button>
                                            <button @click="addItalic()" class="btn btn-icon btn-light btn-sm"
                                                type="button">
                                                <span class="btn-inner--icon">I</span>
                                            </button>
                                            <button @click="addCode()" class="btn btn-icon btn-light btn-sm" type="button">
                                                <span class="btn-inner--icon">&lt;&gt;</span>
                                            </button>
                                            <button @click="addVariable()" class="btn btn-light btn-sm" type="button">
                                                {{ __('Add variable') }}
                                            </button>
                                        </div>

                                        <!-- Variables Section -->
                                        <div class="mt-8 bg-light-warning rounded p-6" v-if="bodyVariables">
                                            <div class="d-flex align-items-center mb-4">
                                                <i class="fas fa-variable text-warning mr-3 fa-lg"></i>
                                                <h5 class="font-weight-bolder text-dark mb-0">{{ __('Variable Samples') }}
                                                </h5>
                                            </div>
                                            <p class="text-muted font-size-sm mb-5">
                                                {{ __('Provide example values for variables') }}
                                            </p>
                                            <div v-for="(v, index) in bodyVariables" class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span
                                                        class="input-group-text bg-white font-weight-bolder">@{{ v }}</span>
                                                </div>
                                                <input v-model="bodyExampleVariable[index]" type="text"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="{{ __('Example content') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="col-xl-6 mb-8">
                            <div class="card-body"
                                style="justify-content: flex-end; text-align: right; background: url('{{ asset('uploads/default/dotflo/bg.png') }}');"
                                v-cloak>
                                <div class="card mt-4"
                                    style="min-width: 18rem; text-align: left; border-top-left-radius: 0;">
                                    <div class="card-header align-items-center border-0">
                                        <h3 class="card-title text-dark-75 font-size-h4 mb-0">
                                            <i class="fas fa-eye text-primary mr-3"></i>
                                            {{ __('Template Preview') }}
                                        </h3>
                                    </div>
                                    <div class="card-body py-7">
                                        <!-- Preview Card -->
                                        <div class="card border-left-5 border-primary rounded-lg shadow-sm">
                                            <div class="card-body p-6">
                                                <!-- Header Media -->
                                                <div class="mb-6 text-center">
                                                    <template v-if="headerType=='image'">
                                                        <img :src="headerImage" class="img-fluid rounded-lg"
                                                            style="max-height: 200px">
                                                    </template>

                                                    <template v-if="headerType=='video' && headerVideo.length>4">
                                                        <video :src="headerVideo" class="img-fluid rounded-lg" controls
                                                            style="max-height: 200px"></video>
                                                    </template>

                                                    <h4 v-if="headerType=='text'" class="text-dark font-weight-bold mb-4">
                                                        @{{ headerReplacedWithExample }}
                                                    </h4>
                                                </div>

                                                <!-- Message Content -->
                                                <div class="mb-6">
                                                    <p class="text-dark-75 font-size-lg mb-4" style="overflow-wrap: break-word; text-align: initial; white-space: pre-wrap;">@{{ bodyReplacedWithExample }}</p>
                                                    <span class="text-muted font-size-sm">@{{ footerText }}</span>
                                                </div>

                                                <!-- Interactive Elements -->
                                                <div class="border-top border-light pt-5">
                                                    <div v-for="(reply, index) in quickReplies"
                                                        class="d-flex align-items-center mb-3">
                                                        <i class="fas fa-reply text-primary mr-3"></i>
                                                        <span
                                                            class="text-primary font-weight-bold">@{{ reply }}</span>
                                                    </div>

                                                    <div v-for="(website, index) in vistiWebsite"
                                                        class="d-flex align-items-center mb-3">
                                                        <i class="fas fa-link text-success mr-3"></i>
                                                        <span
                                                            class="text-success font-weight-bold">@{{ website.title }}</span>
                                                    </div>

                                                    <div v-if="hasPhone" class="d-flex align-items-center mb-3">
                                                        <i class="fas fa-phone-alt text-danger mr-3"></i>
                                                        <span
                                                            class="text-danger font-weight-bold">@{{ callPhoneButtonText }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                            <!-- Submit Button -->
                            <div class="text-right mt-8">
                                <button type="button" @click="submitTemplate()"
                                    class="btn btn-primary btn-lg font-weight-bold px-10 py-4">
                                    <i class="fas fa-save mr-3"></i>
                                    {{ __('Save Template') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    </form>
    </div>
    </div>

    @include('Catalogs::carousel_scripts')
@endsection
