@extends('layouts.app')

{{--@section('page-title')--}}
{{--    {{ __('Register') }}--}}
{{--@endsection--}}

@section('content')

<div class="container my-5">
    @if(session('succeed'))
    <div class="alert alert-success flash mb-3" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>{{ session('succeed') }}</strong>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8 thanks">
            <div class="card">
                <div class="circle">
                    <i class="checkmark">✓</i>
                </div>
                <h1>Success</h1>
                <ul class="mt-5">
                    <li>Please click <a href="{{ url('request-activation') }}">Request Activation</a> to remind Admin to activate your account</li>
                    <li>Please check your registered email for a verification link.</li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection
