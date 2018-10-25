<nav id="sidebar">
    <div id="sidebar-scroll">
       
        <div class="content-header content-header-fullrow px-15">
                <div class="content-header-section sidebar-mini-visible-b">
                    <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                        <span class="text-dual-primary-dark">c</span><span class="text-primary">b</span>
                    </span>
                </div>

                <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                    <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                        <i class="fa fa-times text-danger"></i>
                    </button>

                    <div class="content-header-item">
                        <a class="link-effect font-w700" href="index.html">
                            <i class="si si-ghost text-primary"></i>
                            <span class="font-size-xl text-dual-primary-dark">ez</span><span class="font-size-xl text-primary">pz</span>
                        </a>
                    </div>
                </div>
        </div>
        <div class="content-side content-side-full content-side-user px-10 align-parent">
                <div class="sidebar-mini-visible-b align-v animated fadeIn">
                    <img class="img-avatar img-avatar32" src="{{ asset('assets/media/avatars/avatar15.jpg') }}" alt="">
                </div>
                <div class="sidebar-mini-hidden-b text-center">
                    <a class="img-link" href="be_pages_generic_profile.html">
                        <img class="img-avatar" src="{{ asset('assets/media/avatars/avatar15.jpg') }}" alt="">
                    </a>
                    <ul class="list-inline mt-10">
                        <li class="list-inline-item">
                            <a class="link-effect text-dual-primary-dark font-size-xs font-w600 text-uppercase" href="#">J. Smith</a>
                        </li>
                        <li class="list-inline-item">
                            <a class="link-effect text-dual-primary-dark" data-toggle="layout" data-action="sidebar_style_inverse_toggle" href="javascript:void(0)">
                                <i class="si si-drop"></i>
                            </a>
                        </li>           
                        <li class="list-inline-item">
                            <a class="link-effect text-dual-primary-dark" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="si si-logout"></i>
                            </a>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </ul>
                </div>
        </div>
        <div class="content-side content-side-full">
                <ul class="nav-main">
                    <li>
                        <a class="{{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="si si-cup"></i><span class="sidebar-mini-hide">Dashboard</span></a>
                    </li>
                    @if(Auth::user()->level == "administrator") 
                    <!-- <li>
                        <a class="{{ request()->is('company') ? 'active' : '' }}" href="{{ route('company') }}"><i class="si si-briefcase"></i><span class="sidebar-mini-hide">Company Profile</span></a>
                    </li> -->
                    @endif
                    <li class="nav-main-heading"><span class="sidebar-mini-visible">UI</span><span class="sidebar-mini-hidden">Master Data</span></li>
                    {{-- Store --}}
                    <li class="{{ request()->is('store/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="fa fa-shopping-cart"></i><span class="sidebar-mini-hide">Store(s)</span></a>
                        <ul>
                            <li>
                                <a class="{{ request()->is('store/region') ? 'active' : '' }}" href="{{ route('region') }}">Region</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/area') ? 'active' : '' }}" href="{{ route('area') }}">Area</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/subarea') ? 'active' : '' }}" href="{{ route('subarea') }}">Sub Area</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/channel') ? 'active' : '' }}" href="{{ route('channel') }}">Channel</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/account') ? 'active' : '' }}" href="{{ route('account') }}">Account</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/distributor') ? 'active' : '' }}" href="{{ route('distributor') }}">Distributor</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/summary') ? 'active' : '' }}" href="{{ route('store') }}">Store</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/pasar') ? 'active' : '' }}" href="{{ route('pasar') }}">Pasar</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/place') ? 'active' : '' }}" href="{{ route('place') }}">Place</a>
                            </li>
                        </ul>
                    </li>
                    {{-- Employee --}}
                    <li class="{{ request()->is('employee/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="fa fa-users"></i><span class="sidebar-mini-hide">Employee(s)</span></a>
                        <ul>
                            <li>
                                <a class="{{ request()->is('employee/position') ? 'active' : '' }}" href="{{ route('position') }}">Position</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/agency') ? 'active' : '' }}" href="{{ route('agency') }}">Agency</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/summary') ? 'active' : '' }}" href="{{ route('employee') }}">Employee</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/pasar') ? 'active' : '' }}" href="{{ route('employee.pasar') }}">Employee Pasar</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/dc') ? 'active' : '' }}" href="{{ route('employee.dc') }}">Demo Cooking</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/resign') ? 'active' : '' }}" href="{{ route('resign') }}">Resign</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/rejoin') ? 'active' : '' }}" href="{{ route('rejoin') }}">Rejoin</a>
                            </li>
                        </ul>
                    </li>
                    {{-- Product --}}
                    <li class="{{ request()->is('product/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="fa fa-list-alt"></i><span class="sidebar-mini-hide">Product(s)</span></a>
                        <ul>
                            <li>
                                <a class="{{ request()->is('product/brand') ? 'active' : '' }}" href="{{ route('brand') }}">Brand</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/category') ? 'active' : '' }}" href="{{ route('category') }}">Category</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/sub-category') ? 'active' : '' }}" href="{{ route('sub-category') }}">Sub Category</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/summary') ? 'active' : '' }}" href="{{ route('product') }}">Product</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/product-competitor') ? 'active' : '' }}" href="{{ route('product-competitor') }}">Product Competitor</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/price') ? 'active' : '' }}" href="{{ route('price') }}">Price</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/fokus') ? 'active' : '' }}" href="{{ route('fokus') }}">Fokus</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/target') ? 'active' : '' }}" href="{{ route('target') }}">Target</a>
                            </li>
                            <!-- <li>
                                <a class="{{ request()->is('product/promo') ? 'active' : '' }}" href="{{ route('promo') }}">Promo</a>
                            </li> -->
                        </ul>
                    </li>
                    {{-- Target --}}
                    <li class="{{ request()->is('target/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="si si-target"></i><span class="sidebar-mini-hide">Target(s)</span></a>
                        <ul>
                            <li>
                                <a class="{{ request()->is('target/dc') ? 'active' : '' }}" href="{{ route('target.dc') }}">Demo Cooking</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('target/smd') ? 'active' : '' }}" href="{{ route('target.smd') }}">SMD Pasar</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('target/spg') ? 'active' : '' }}" href="{{ route('target.spg') }}">SPG Pasar</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-main-heading"><span class="sidebar-mini-visible">UI</span><span class="sidebar-mini-hidden">REPORT</span></li>
                    {{-- SALES --}}
                    <li class="{{ request()->is('report/sales/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="fa fa-list-alt"></i><span class="sidebar-mini-hide">Sales</span></a>
                        <ul>
                            <li>
                                <a class="{{ request()->is('report/sales/sellin') ? 'active' : '' }}" href="{{ route('sellin') }}">Sell In</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('report/sales/sellout') ? 'active' : '' }}" href="{{ route('sellout') }}">Sell Out</a>
                            </li>
                        </ul>
                    </li>
                    {{-- Stock In Hand--}}
                    <li>
                        <a class="{{ request()->is('report/stock') ? 'active' : '' }}" href="{{ route('stock') }}"><i class="si si-handbag"></i><span class="sidebar-mini-hide">Stock In Hand</span></a>
                    </li>  
                </ul>
        </div>
     
    </div>
</nav>

