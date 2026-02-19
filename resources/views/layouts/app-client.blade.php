@extends('layouts.app')

@section('head')
    {{-- Allows module views to push page-level css via @section('css') --}}
    @yield('css')
    @yield('head')
@endsection

@section('content')
    @yield('content')
@endsection

@section('js')
    @yield('js')
@endsection

