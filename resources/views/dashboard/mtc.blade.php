@extends('dashboard')
@section('konten')
<div class="row invisible" data-toggle="appear">
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-briefcase fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600" data-toggle="countTo" data-speed="1000" data-to="1500"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Product</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-basket fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600"><span data-toggle="countTo" data-speed="1000" data-to="780"><i class="fa fa-spin fa-spinner"></i></span></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Store</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-handbag fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600" data-toggle="countTo" data-speed="1000" data-to="15"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Pasar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-users fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600"  data-toggle="countTo" data-speed="1000" data-to="4252"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Employee</div>
            </div>
        </a>
    </div>
</div>
@endsection