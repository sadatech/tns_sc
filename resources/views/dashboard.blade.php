@extends('layouts.app')
@section('title', "Beranda")
@section('content')
<div class="content">
    <div class="row">
        <div class="col-12 col-xs-3">
            <div class="block block-content">
                <div class="nav nav-pills push">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }} {{ request()->is('/') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fa fa-line-chart mr-2"></i>Dashboard</a>
                    </li>

                    @if(Auth::user()->role->level == 'AdminGtc' || Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'ViewAll')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard/gtc/*') ? 'active' : '' }} {{ request()->is('dashboard/gtc') ? 'active' : '' }}" href="{{ route('dashboard.gtc') }}"><i class="fa fa-line-chart mr-2"></i>GTC Graph</a>
                    </li>
                    @endif
                    @if(Auth::user()->role->level == 'AdminMtc' || Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard/mtc/*') ? 'active' : '' }}" href="{{ route('dashboard.mtc') }}"><i class="fa fa-line-chart mr-2"></i>MTC Graph</a>
                    </li>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @yield('konten')
</div>
@endsection
@section('script')
<script src="{{ asset('assets/js/plugins/chartjs/Chart.min.js') }}"></script>
@yield('js')
@yield('chartjs')
@endsection