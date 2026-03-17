@extends('layouts.app')

{{--@section('page-title')--}}
{{--    {{ __('Login') }}--}}
{{--@endsection--}}

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if(session('succeed'))
                    <div class="alert alert-success flash mb-5" role="alert">
                        <a href="#" class="close" data-dismiss="alert" arial-lable="close">&times;</a>
                        <strong>{{ session('succeed') }}</strong>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">{{ __('Change password') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('changepassword') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="oldpassword" class="col-md-4 col-form-label text-md-right">{{ __('Current password') }}</label>

                                <div class="col-md-6">
                                    <input id="oldpassword" name="oldpassword" type="password" class="form-control @error('oldpassword') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('oldpassword')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="newpassword" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                                <div class="col-md-6">
                                    <input id="newpassword" name="newpassword" type="password" class="form-control @error('newpassword') is-invalid @enderror" required autocomplete="current-password">

                                    @error('newpassword')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="newpassword_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
