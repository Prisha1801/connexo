@extends('general.index', $setup)
@section('thead')
@endsection
@section('tbody')
<div class="container">
    <form id="restorant-apps-form" method="post" autocomplete="off" enctype="multipart/form-data" action="{{route('catalog.orderUpdate',$order->id)}}">
            @csrf  
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name">{{__('Address')}}</label>
                        <textarea rows="5"  name="address" id="address" class="form-control" placeholder="{{__('address')}}" >{{$order->address}}</textarea>                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="name">{{__('State')}}</label>
                        <input type="text" name="state" id="state" class="form-control" placeholder="{{__('state')}}" value="{{$order->state}}" >
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="name">{{__('City')}}</label>
                        <input type="text" name="city" id="city" class="form-control" placeholder="{{__('City')}}" value="{{$order->city}}" >
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="name">{{__('Pin Code')}}</label>
                        <input type="text" name="pin_code" id="pin_code" class="form-control" placeholder="{{__('Pin Code')}}" value="{{$order->pin_code}}" >
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="name">{{__('Building Name')}}</label>
                        <input type="text" name="building_name" id="building_name" class="form-control" placeholder="{{__('Building Name')}}" value="{{$order->building_name}}" >
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="name">{{__('House Number')}}</label>
                        <input type="text" name="house_number" id="house_number" class="form-control" placeholder="{{__('House Number')}}" value="{{$order->house_number}}" >
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="name">{{__('Landmark Area')}}</label>
                        <input type="text" name="landmark_area" id="landmark_area" class="form-control" placeholder="{{__('Landmark Area')}}" value="{{$order->landmark_area}}" >
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="name">{{__('Tower Number')}}</label>
                        <input type="text" name="tower_number" id="tower_number" class="form-control" placeholder="{{__('Tower Number')}}" value="{{$order->tower_number}}" >
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-success mt-4">Update</button>
            <a  href="{{route('catalog.orderINdex')}}" class="btn btn-primary mt-4">Cancel</a>
        </div> 
    </form> 
</div>    
@endsection