@extends('layouts.app')
@section('title', "Beranda")
@section('content')
<div class="content">
    <div class="row invisible" data-toggle="appear">
        <div class="col-12 col-xs-3">
            <div class="block">
                <ul class="nav nav-pills push">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fa fa-line-chart mr-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-line-chart mr-2"></i>GTC Graph</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-line-chart mr-2"></i>MTC Graph</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row invisible" data-toggle="appear">
        <div class="col-6 col-xl-3">
            <a class="block block-link-shadow text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-left mt-10 d-none d-sm-block">
                        <i class="si si-bag fa-3x text-body-bg-dark"></i>
                    </div>
                    <div class="font-size-h3 font-w600" data-toggle="countTo" data-speed="1000" data-to="1500">0</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Sales</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="block block-link-shadow text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-left mt-10 d-none d-sm-block">
                        <i class="si si-wallet fa-3x text-body-bg-dark"></i>
                    </div>
                    <div class="font-size-h3 font-w600">$<span data-toggle="countTo" data-speed="1000" data-to="780">0</span></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Earnings</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="block block-link-shadow text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-left mt-10 d-none d-sm-block">
                        <i class="si si-envelope-open fa-3x text-body-bg-dark"></i>
                    </div>
                    <div class="font-size-h3 font-w600" data-toggle="countTo" data-speed="1000" data-to="15">0</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Messages</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="block block-link-shadow text-right" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-left mt-10 d-none d-sm-block">
                        <i class="si si-users fa-3x text-body-bg-dark"></i>
                    </div>
                    <div class="font-size-h3 font-w600" data-toggle="countTo" data-speed="1000" data-to="4252">0</div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Online</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/plugins/chartjs/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/be_pages_dashboard.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['table-tools', 'ckeditor']); });</script>
@endsection