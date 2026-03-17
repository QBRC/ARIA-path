@extends('errors::illustrated-layout')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('ARIA-path is under maintenance!'))

@section('image')
    <img src="{{ asset('img/undraw_maintenance_re_59vn.svg') }}" width="98%"/>
@endsection
