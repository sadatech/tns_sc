@extends('layouts.auth')
@section('title', 'Portal')
@section('content')
<div class="bg-image" style="background-image: url('assets/media/photos/photo34@2x.jpg');">
    <div class="row mx-0 bg-black-op">
        <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
            <div class="p-30 invisible" data-toggle="appear">
                <p class="font-size-h3 font-w600 text-white">
                    Halaman ini hanya untuk demontrasi project HKBP.
                </p>
                <p class="font-italic text-white-op m-0 p-0">
                    Copyright &copy; <span class="js-year-copy">2018</span>
                </p>
                <p class="font-italic text-white-op m-0 p-0">
                    Created with <span style="color:red;">❤</span> Sada Technology
                </p>
            </div>
        </div> 
        <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-white invisible" data-toggle="appear" data-class="animated fadeInRight">
            <div class="content content-full">     
                <div class="px-30 py-10">
                    <a class="link-effect font-w700" href="#">
                        <i class="si si-fire"></i>
                        <span class="font-size-xl text-dual-primary-dark">HKBP</span> <span class="font-size-xl text-primary">online</span>
                    </a>
                    <h1 class="h3 font-w700 mt-30 mb-10">Selamat Datang</h1>
                    <h2 class="h5 font-w400 text-muted mb-0">Silahkan pilih halaman yang ingin dilihat</h2>
                    <br>
                    <table class="table">
                        <tr>
                            <td>Email:</td>
                            <td><code>admin@sada.co.id</code></td>
                        </tr>
                        <tr>
                            <td>Password:</td>
                            <td><code>admin0123</code></td>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <a class="btn btn-primary btn-block mb-10" href="{{ route('login') }}">Dashboard</a>
                        </div>
                        <div class="col-md-6">
                            <a class="btn btn-primary btn-block" href="/site">CMS</a>
                        </div>
                    </div>
                    <hr>
                    <small>* Halaman ini hanya untuk demonstrasi tidak akan tampil pada saat production.</small> 
                    <br>
                    <small>* Halaman cms masih dalam tahap pengembangan.</small> 
                    <p class="font-italic mt-10 p-0">
                        Created with <span style="color:red;">❤</span> Sada Technology
                    </p>    
                </div> 
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ ('assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ ('assets/js/pages/op_auth_signin.js') }}"></script>
@endsection