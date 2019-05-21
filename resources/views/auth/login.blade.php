@extends('layouts.auth')
@section('title', 'Login')
@section('content')
<div class="bg-image" style="background-image: url('assets/media/photos/Sasa_Melezatkan_Desktop-01.jpg');">
    <div class="row mx-0 bg-black-op">
        <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
            <div class="p-30 invisible" data-toggle="appear">
                <p class="font-size-h3 font-w600 text-white">
                    SASA Melezatkan.
                </p>
                <p class="font-italic text-white-op">
                    Copyright &copy; <span class="js-year-copy">2018</span>
                </p>
            </div>
        </div>
        <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-white invisible" data-toggle="appear" data-class="animated fadeInRight">
            <div class="content content-full">     
                <div class="px-30 py-10">
                    <a class="link-effect font-w700" href="#">
                        <i class="si si-fire"></i>
                        <span class="font-size-xl text-dual-primary-dark">SASA</span> <span class="font-size-xl text-primary">MTCGTC</span>
                    </a>
                    <h1 class="h3 font-w700 mt-30 mb-10">Welcome.. </h1>
                    <h2 class="h5 font-w400 text-muted mb-0">Please sign in</h2>
                </div>
                <form class="js-validation-signin px-30"  method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                    <div class="form-group row {{ $errors->has('email') ? ' has-error' : '' }}">
                        <div class="col-12">
                            <div class="form-material floating">
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            <label for="email">Email</label>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <div class="col-16">
                            <div class="form-material floating">
                            <input id="password" type="password" class="form-control" name="password" required>
                            <label for="login-password">Password</label>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="login-remember-me" name="login-remember-me">
                                <label class="custom-control-label" for="login-remember-me">Remember Me</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-hero btn-alt-primary">
                            <i class="si si-login mr-10"></i> Sign In
                        </button>
                        <div class="mt-30">
                            <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="{{ route('register') }}">
                                <i class="fa fa-plus mr-5"></i> Create Account
                            </a>
                            <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="{{ route('password.request') }}">
                                <i class="fa fa-warning mr-5"></i> Forgot Password
                            </a>
                        </div>
                    </div>
                </form>             
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ ('assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ ('assets/js/pages/op_auth_signin.js') }}"></script>
@endsection