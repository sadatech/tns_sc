@extends('layouts.auth')

@section('content')
    <main id="main-container">
        <div class="bg-body-dark bg-pattern" style="background-image: url('assets/media/various/bg-pattern-inverse.png');">
            <div class="row mx-0 justify-content-center">
                <div class="hero-static col-lg-6 col-xl-6">
                    <div class="content content-full overflow-hidden">
                        <div class="py-30 text-center">
                            <a class="link-effect font-w700" href="index.html">
                                <i class="si si-fire"></i>
                                <span class="font-size-xl text-primary-dark">code</span><span class="font-size-xl">base</span>
                            </a>
                            <h1 class="h4 font-w700 mt-30 mb-10">Create New Account</h1>
                            <h2 class="h5 font-w400 text-muted mb-0">We’re excited to have you on board!</h2>
                        </div>
                        <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}
                        <div class="block block-themed block-rounded block-shadow">
                            <div class="block-header bg-gd-sun p-15">
                                <h3 class="block-title">Please add your details</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option">
                                        <i class="si si-wrench"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <div class="col-12">
                                        <label for="signup-username">Name</label>
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <div class="col-12">
                                        <label for="signup-email">Email</label>
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <div class="col-12">
                                        <label for="signup-password">Password</label>
                                        <input id="password" type="password" class="form-control" name="password" required>
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="signup-password-confirm">Password Confirmation</label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-sm-6 push">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="signup-terms" name="signup-terms" required>
                                            <label class="custom-control-label" for="signup-terms">I agree to Terms &amp; Conditions</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-sm-right push">
                                        <button type="submit" class="btn btn-alt-primary">
                                            <i class="fa fa-plus mr-10"></i> Create Account
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="block-content bg-body-light">
                                <div class="form-group text-center">
                                    <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="#" data-toggle="modal" data-target="#modal-terms">
                                        <i class="fa fa-book text-muted mr-5"></i> Read Terms
                                    </a>
                                    <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="{{ route('login') }}">
                                        <i class="fa fa-user text-muted mr-5"></i> Sign In
                                    </a>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('script')
    <script src="{{ asset('assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/op_auth_signup.js') }}"></script>
@endsection