@extends('dashboard')
@section('konten')
<div class="row invisible" data-toggle="appear">
    <div class="col-md-12">
        <div class="block">
            <ul class="nav nav-tabs nav-tabs-alt">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/gtc') ? 'active' : '' }} {{ request()->is('dashboard/gtc/smd') ? 'active' : '' }}" href="{{ route('dashboard.gtc.smd') }}">VDO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/gtc/spg') ? 'active' : '' }}" href="{{ route('dashboard.gtc.spg') }}">SPG</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/gtc/demo-cooking') ? 'active' : '' }}" href="{{ route('dashboard.gtc.dc') }}">Demo Cooking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/gtc/motorik') ? 'active' : '' }}" href="{{ route('dashboard.gtc.motorik') }}">Motoris</a>
                </li>
            </ul>
            <div class="block-content tab-content">
                <div class="tab-pane active">
                    @yield('tabs')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection