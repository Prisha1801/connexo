@extends('layouts.app', ['title' => __("CSV Contacts Import")])

@section('content')
<style>
    .btn-sample-download {
        background-color: #fff;
        border: 1px solid #007bff;
        color: #007bff;
    }

    .btn-sample-download:hover {
        background-color: #007bff;
        color: #fff;
    }
</style>

<div class="header pb-8 pt-5 pt-md-8"></div>

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <!-- Header -->
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __("CSV Contacts Import") }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Flash messages -->
                <div class="col-12">
                    @include('partials.flash')
                </div>

                <!-- Body -->
                <div class="card-body">
                    @if(session('sweet_success'))
                        <div class="alert alert-success">
                            {{ session('sweet_success') }}
                        </div>
                    @endif
                    
                    @if(session('sweet_error'))
                        <div class="alert alert-danger">
                            {{ session('sweet_error') }}
                        </div>
                    @endif

                    <form action="{{ route('contacts.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Important note -->
                        <div class="alert alert-warning border-left border-warning shadow-sm p-3 mb-3">
                            <strong>Important:</strong> Please ensure your CSV file contains the following <strong>required headers</strong> exactly in this format:
                            <b>phone</b>, <b>name</b>, <b>custom_field_name_1</b>, <b>custom_field_name_2</b>.
                            <br>
                            All fields must be <strong>non-empty</strong> for each contact row.
                            <br>
                          <a href="{{ route('download.sample.csv') }}"
                               class="btn btn-sm btn-sample-download mt-2"
                               download
                               target="_blank">
                                <i class="fas fa-download"></i> Download Sample CSV
                            </a>

                        </div>

                        <!-- CSV file input -->
                        @include('partials.input', [
                            'additionalInfo' => "Required headers: phone, name, custom_field_name_1, custom_field_name_2",
                            'class' => 'col-md-4',
                            'name' => "CSV File",
                            'id' => 'csv',
                            'type' => 'file',
                            'placeholder' => "",
                            'required' => true,
                            'accept' => ".csv"
                        ])

                        <!-- Group select -->
                        @include('partials.select', [
                            'class' => 'col-md-4',
                            'name' => "Group to insert into",
                            'id' => 'group',
                            'placeholder' => "",
                            'required' => false,
                            'data' => $groups
                        ])

                        <!-- Submit button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-success ml-3 mt-2" id="import-btn">
                                {{ __('Import Contacts') }}
                            </button>
                        </div>
                    </form>
                </div> <!-- /card-body -->

            </div> <!-- /card -->
        </div>
    </div>

    @include('layouts.footers.auth')
</div>
@endsection













<!--extends('layouts.app', ['title' =>  __("CSV contacts Import ") ])-->


<!--section('content')-->
<!--    <div class="header  pb-8 pt-5 pt-md-8">-->
<!--    </div>-->
<!--    <div class="container-fluid mt--7">-->
<!--        <div class="row">-->
<!--            <div class="col">-->
<!--                <div class="card shadow">-->
<!--                    <div class="card-header border-0">-->
<!--                        <div class="row align-items-center">-->
<!--                            <div class="col-8">-->
<!--                                <h3 class="mb-0">{{ __("CSV contacts Import ") }}</h3>-->
                  
   
                                
<!--                            </div>-->
                            
                               
<!--                        </div>-->
                       
<!--                    </div>-->

<!--                    <div class="col-12">-->
<!--                        @include('partials.flash')-->
<!--                    </div>-->

                   
<!--                       <div class="card-body">-->
<!--                            <form action="{{ route('contacts.import.store') }}" method="POST" enctype="multipart/form-data">-->
<!--                                @csrf-->
<!--                                @include('partials.input',['additionalInfo'=>"Headers phone,name,custom_field_name_1,custom_field_name_2",'class'=>'col-md-4','name'=>"CSV file",'id'=>'csv','type'=>'file','placeholder'=>"",'required'=>true,'accept'=>".csv"])-->
<!--                                @include('partials.select',['class'=>'col-md-4','name'=>"Group to insert into",'id'=>'group','placeholder'=>"",'required'=>false,'data'=>$groups])-->
<!--                                <div class="form-group">-->
<!--                                    <button type="submit" class="btn btn-success ml-3 mt-2" >{{ __('Import contact')}}</button>-->
<!--                                </div>-->
                                
<!--                            </form>-->
<!--                        </div>-->
                   
                    
     
         


<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

<!--        @include('layouts.footers.auth')-->
<!--    </div>-->
<!--endsection-->
