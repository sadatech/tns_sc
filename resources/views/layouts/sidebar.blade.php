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
                            <span class="font-size-xl text-dual-primary-dark">SASA</span>
                        </a>
                    </div>
                </div>
        </div>
        <div class="content-side content-side-full">
                <ul class="nav-main">
                    <li>
                        <a class="{{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="si si-cup"></i><span class="sidebar-mini-hide">Dashboard</span></a>
                    </li>
                    @if(Auth::user()->level == "administrator") 
                    {{-- <li>
                        <a class="{{ request()->is('company') ? 'active' : '' }}" href="{{ route('company') }}"><i class="si si-briefcase"></i><span class="sidebar-mini-hide">Company Profile</span></a>
                    </li> --}}
                    @endif
                @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc' || !Auth::user()->role->level == 'ViewAll') 
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
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                            <li>
                                <a class="{{ request()->is('store/root') ? 'active' : '' }}" href="{{ route('root') }}">Route</a>
                            </li>
                            @endif
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminMtc')
                            <li>
                                <a class="{{ request()->is('store/channel') ? 'active' : '' }}" href="{{ route('channel') }}">Channel</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/dc_channel') ? 'active' : '' }}" href="{{ route('dc_channel') }}">DC Channel</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/account') ? 'active' : '' }}" href="{{ route('account') }}">Account</a>
                            </li>
                            <!--<li>
                                <a class="{{ request()->is('store/distributor') ? 'active' : '' }}" href="{{ route('distributor') }}">Distributor</a>
                            </li>-->
                            <li>
                                <a class="{{ request()->is('store/sales_tiers') ? 'active' : '' }}" href="{{ route('sales_tiers') }}">Sales Tiers</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/summary') ? 'active' : '' }}" href="{{ route('store') }}">Store</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('store/place') ? 'active' : '' }}" href="{{ route('place') }}">Place</a>
                            </li>
                            @endif
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                            <li>
                                <a class="{{ request()->is('store/pasar') ? 'active' : '' }}" href="{{ route('pasar') }}">Pasar</a>
                            </li>
                            @endif
                         
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
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminMtc')
                            <li>
                                <a class="{{ request()->is('employee/summary') ? 'active' : '' }}" href="{{ route('employee') }}">MTC</a>
                            </li>
                            @endif
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                            <li>
                                <a class="{{ request()->is('employee/summary/pasar') ? 'active' : '' }}" href="{{ route('employee.pasar') }}">GTC</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/summary/dc') ? 'active' : '' }}" href="{{ route('employee.dc') }}">Demo Cooking</a>
                            </li>
                            @endif
                            <li>
                                <a class="{{ request()->is('employee/resign') ? 'active' : '' }}" href="{{ route('resign') }}">Resign</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('employee/rejoin') ? 'active' : '' }}" href="{{ route('rejoin') }}">Turn Over</a>
                            </li>
                        </ul>
                    </li>
                    {{-- PlannDc --}}
                    @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                     <li>
                        <a class="{{ request()->is('planDc') ? 'active' : '' }}" href="{{ route('planDc') }}"><i class="fa fa-pied-piper"></i><span class="sidebar-mini-hide">Plan Dc</span></a>
                    </li>
                    {{-- PropertiDc  --}}
                     <li>
                        <a class="{{ request()->is('propertiDc') ? 'active' : '' }}" href="{{ route('propertiDc') }}"><i class="fa fa-suitcase"></i><span class="sidebar-mini-hide">Properti Dc</span></a>
                    </li>
                    @endif
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
                            <!-- <li>
                                <a class="{{ request()->is('product/sku-unit') ? 'active' : '' }}" href="{{ route('sku-unit') }}">SKU Unit</a>
                            </li> -->
                            <li>
                                <a class="{{ request()->is('product/stock-type') ? 'active' : '' }}" href="{{ route('stock-type') }}">Stock Type</a>
                            </li>
                            <li>
                                <a class="{{ request()->is('product/summary') ? 'active' : '' }}" href="{{ route('product') }}">Product</a>
                            </li>
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminMtc')
                            <li>
                                <a class="{{ request()->is('product/product-competitor') ? 'active' : '' }}" href="{{ route('product-competitor') }}">Product Competitor</a>
                            </li>
                            @endif
                            <li>
                                <a class="{{ request()->is('product/price') ? 'active' : '' }}" href="{{ route('price') }}">Price</a>
                            </li>
                            <!--<li>
                                <a class="{{ request()->is('product/fokus') ? 'active' : '' }}" href="{{ route('fokus') }}">Fokus</a>
                            </li> -->
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminMtc')
                             <li>
                                <a class="{{ request()->is('product/fokus-mtc') ? 'active' : '' }}" href="{{ route('fokusMtc') }}">Fokus MTC</a>
                            </li>
                            @endif
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                             <li>
                                <a class="{{ request()->is('product/fokusGTC') ? 'active' : '' }}" href="{{ route('fokusGTC') }}">Fokus GTC</a>
                            </li>
                            {{-- <li>
                                <a class="{{ request()->is('product/fokusMD') ? 'active' : '' }}" href="{{ route('fokusMD') }}">Fokus MD</a>
                            </li> --}}

                            <li>
                                <a class="{{ request()->is('product/fokusSpg') ? 'active' : '' }}" href="{{ route('fokusSpg') }}">Fokus SPG Pasar</a>
                            </li>
                            @endif
                            <!-- <li>
                                <a class="{{ request()->is('product/promo') ? 'active' : '' }}" href="{{ route('promo') }}">Promo</a>
                            </li> -->
                        </ul>
                    </li>
                    {{-- Target --}}
                    <li class="{{ request()->is('target/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="si si-target"></i><span class="sidebar-mini-hide">Target(s)</span></a>
                        <ul>
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminMtc')
                            <li>
                                <a class="{{ request()->is('target/mtc') ? 'active' : '' }}" href="{{ route('mtc') }}">MTC</a>
                            </li>
                            @endif
                            <!--<li>
                                <a class="{{ request()->is('target/dc') ? 'active' : '' }}" href="{{ route('target.dc') }}">Demo Cooking</a>
                            </li>-->
                            @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                            <li>
                                <a class="{{ request()->is('target/smd') ? 'active' : '' }}" href="{{ route('target.smd') }}">VDO</a>
                            </li>
                            @endif
                            <!--<li>
                                <a class="{{ request()->is('target/spg') ? 'active' : '' }}" href="{{ route('target.spg') }}">SPG Pasar</a>
                            </li>-->
                        </ul>
                    </li>
                    @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                    <li>
                        <a class="{{ request()->is('pf') ? 'active' : '' }}" href="{{ route('pf') }}"><i class="si si-settings"></i><span class="sidebar-mini-hide">Setting PF</span></a>
                    </li>  
                    @endif
                    </li> 
                    {{-- USERS--}}

                    @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator')
                    <li>
                        <a class="{{ request()->is('user/index') ? 'active' : '' }}" href="{{ route('user') }}"><i class="si si-user-follow"></i><span class="sidebar-mini-hide">User</span></a>
                    </li>                      
                    @endif

                @endif

                @if(Auth::user()->role->level == 'ViewAll') 
                    {{-- Utilities --}}
                      <li class="nav-main-heading"><span class="sidebar-mini-visible">UI</span><span class="sidebar-mini-hidden">Utilities</span></li>
                    <li>
                        <a class="{{ request()->is('utility/export-download') ? 'active' : '' }}" href="{{ route('export-download') }}"><i class="si si-cloud-download"></i><span class="sidebar-mini-hide">Download Export(s)</span></a>
                    </li> 
                    
                @endif
                @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc' || !Auth::user()->role->level == 'ViewAll') 
                    {{-- Utilities --}}
                      <li class="nav-main-heading"><span class="sidebar-mini-visible">UI</span><span class="sidebar-mini-hidden">Utilities</span></li>
                    <li>
                        <a class="{{ request()->is('news/index') ? 'active' : '' }}" href="{{ route('news') }}"><i class="si si-speech"></i><span class="sidebar-mini-hide">News</span></a>
                    </li>  
                    <li>
                        <a class="{{ request()->is('pk/index') ? 'active' : '' }}" href="{{ route('pk') }}"><i class="si si-notebook"></i><span class="sidebar-mini-hide">Product Knowledge</span></a>
                    </li>  
                    <li>
                        <a class="{{ request()->is('faq/index') ? 'active' : '' }}" href="{{ route('faq') }}"><i class="si si-bubbles"></i><span class="sidebar-mini-hide">FAQ</span></a>
                    </li>  
                    <li>
                        <a class="{{ request()->is('utility/export-download') ? 'active' : '' }}" href="{{ route('export-download') }}"><i class="si si-cloud-download"></i><span class="sidebar-mini-hide">Download Export(s)</span></a>
                    </li> 
                    
                    {{-- EDIT --}}
                    <li class="nav-main-heading"><span class="sidebar-mini-visible">RT</span><span class="sidebar-mini-hidden">EDIT</span></li>

                    @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc')
                    <li class="{{ request()->is('edit/gtc/smd/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="fa fa-edit"></i><span class="sidebar-mini-hide">VDO</span></a>
                        <ul>
                             <li>
                                <a class="{{ request()->is('edit/gtc/smd/sales') ? 'active' : '' }}" href="{{ route('edit.gtc.smd.sales') }}">Sales</a>
                            </li>
                             <li>
                                <a class="{{ request()->is('edit/gtc/smd/new-cbd') ? 'active' : '' }}" href="{{ route('edit.gtc.smd.new-cbd') }}">CBD</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ request()->is('edit/gtc/dc/*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="fa fa-edit"></i><span class="sidebar-mini-hide">Dc</span></a>
                        <ul>
                             <li>
                                <a class="{{ request()->is('edit/gtc/dc/sales') ? 'active' : '' }}" href="{{ route('edit.gtc.dc.sales') }}">Sales</a>
                            </li>
                        </ul>
                    </li>
                    @endif
                @endif

                    <li class="nav-main-heading"><span class="sidebar-mini-visible">RT</span><span class="sidebar-mini-hidden">REPORT</span></li>
                    {{-- REPORT GTC --}}
                    @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminGtc' || Auth::user()->role->level == 'ViewAll')
                    
                            {{-- SMD PASAR --}}
                            <li class="{{ request()->is('report/gtc/smd/*') ? 'open' : '' }}">
                            <a class="nav-submenu" data-toggle="nav-submenu"><span class="sidebar-mini-hide">VDO</span></a>
                                <ul>
                                    {{-- ATTENDANCE SMD --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/attendanceSMD') ? 'active' : '' }}" href="{{ route('report.attendance.smd') }}"><span class="sidebar-mini-hide">Attendance</span></a>
                                    </li>
                                    {{-- STOCKIST MTC --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/stockist') ? 'active' : '' }}" href="{{ route('report.stockist') }}"><span class="sidebar-mini-hide">Stockist</span></a>
                                    </li>
                                    {{-- NEW CBD --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/new-cbd') ? 'active' : '' }}" href="{{ route('report.new-cbd') }}"><span class="sidebar-mini-hide">CBD</span></a>
                                    </li>
                                    {{-- Dist PF MTC--}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/distributorPf') ? 'active' : '' }}" href="{{ route('report.dist.pf') }}"><span class="sidebar-mini-hide">Distribusi PF</span></a>
                                    </li> 
                                    {{-- SALES--}}
                                    <!-- <li>
                                        <a class="{{ request()->is('report/gtc/smd/sales') ? 'active' : '' }}" href="{{ route('report.sales.pasar') }}"><span class="sidebar-mini-hide">Sales</span></a>
                                    </li> 
 -->                                    {{-- New SALES--}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/new-sales') ? 'active' : '' }}" href="{{ route('report.new-sales.pasar') }}"><span class="sidebar-mini-hide">Sales</span></a>
                                    </li> 
                                    {{-- SUMMARY REPORT--}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/summary') ? 'active' : '' }}" href="{{ route('report.summary') }}"><span class="sidebar-mini-hide">Detail VDO</span></a>
                                    </li>
                                    {{-- SALES SUMMARY --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/sales-summary') ? 'active' : '' }}" href="{{ route('report.sales.summary.smd') }}"><span class="sidebar-mini-hide">Sales Summary</span></a>
                                    </li>
                                    {{-- KPI --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/smd/kpi') ? 'active' : '' }}" href="{{ route('report.kpi.smd') }}"><span class="sidebar-mini-hide">KPI</span></a>
                                    </li>
                                    <!-- <li>
                                        <a class="{{ request()->is('report/gtc/smd/new-kpi') ? 'active' : '' }}" href="{{ route('report.new-kpi.smd') }}"><span class="sidebar-mini-hide">New KPI</span></a>
                                    </li> -->
                                    {{-- TARGET KPI --}}
                                    <!-- <li>
                                        <a class="{{ request()->is('report/gtc/smd/target-kpi') ? 'active' : '' }}" href="{{ route('report.target.kpi.smd') }}"><span class="sidebar-mini-hide">Target KPI</span></a>
                                    </li>  -->                                   
                                </ul>
                            </li>
                            {{-- SPG PASAR --}}
                            <li class="{{ request()->is('report/gtc/spg/*') ? 'open' : '' }}">
                            <a class="nav-submenu" data-toggle="nav-submenu"><span class="sidebar-mini-hide">SPG Pasar</span></a>
                                <ul>
                                    {{-- ATTENDANCE SPG --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/spg/attendance') ? 'active' : '' }}" href="{{ route('report.spg.attendance') }}"><span class="sidebar-mini-hide">Attendance</span></a>
                                    </li>
                                    {{-- SALES SPG --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/spg/sales') ? 'active' : '' }}" href="{{ route('report.sales.spg') }}"><span class="sidebar-mini-hide">Sales</span></a>
                                    </li>

                                    {{-- RECAP SPG --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/spg/recap') ? 'active' : '' }}" href="{{ route('report.recap.spg') }}"><span class="sidebar-mini-hide">Recap</span></a>
                                    </li>

                                    {{-- SALES SUMMARY SPG --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/spg/sales-summary') ? 'active' : '' }}" href="{{ route('report.sales.summary.spg') }}"><span class="sidebar-mini-hide">Sales Summary</span></a>
                                    </li>

                                    {{-- Achievement SPG --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/spg/achievement') ? 'active' : '' }}" href="{{ route('report.achievement.spg') }}"><span class="sidebar-mini-hide">Achievement</span></a>
                                    </li>
                                </ul>
                            </li>
                            {{-- Demo Cooking --}}
                            <li class="{{ request()->is('report/gtc/demo/*') ? 'open' : '' }}">
                            <a class="nav-submenu" data-toggle="nav-submenu"><span class="sidebar-mini-hide">Demo Cooking</span></a>
                                <ul>
                                    {{-- Plan Kunjungan --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/demo/kunjungan') ? 'active' : '' }}" href="{{ route('report.demo.kunjungan') }}"><span class="sidebar-mini-hide">Plan Kunjungan</span></a>
                                    </li>
                                    {{-- Sampling --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/demo/sampling') ? 'active' : '' }}" href="{{ route('report.demo.sampling') }}"><span class="sidebar-mini-hide">Sampling</span></a>
                                    </li>
                                    {{-- Sales DC --}}
                                    <!-- <li>
                                        <a class="{{ request()->is('report/gtc/demo/salesDC') ? 'active' : '' }}" href="{{ route('report.demo.salesDC') }}"><span class="sidebar-mini-hide">Sales DC</span></a>
                                    </li> -->
                                    {{-- Sales DC --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/demo/new-salesDC') ? 'active' : '' }}" href="{{ route('report.demo.new-salesDC') }}"><span class="sidebar-mini-hide">Sales DC</span></a>
                                    </li>
                                    {{-- Activity --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/demo/activity') ? 'active' : '' }}" href="{{ route('report.demo.activity') }}"><span class="sidebar-mini-hide">Activity</span></a>
                                    </li>
                                    {{-- Cash Advance --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/demo/cashAdvance') ? 'active' : '' }}" href="{{ route('report.demo.cashAdvance') }}"><span class="sidebar-mini-hide">Cash Advance</span></a>
                                    </li>
                                    {{-- Inventori --}}
                                    <li>
                                        <a class="{{ request()->is('report/gtc/demo/inventori') ? 'active' : '' }}" href="{{ route('report.demo.inventori') }}"><span class="sidebar-mini-hide">Inventori</span></a>
                                    </li>
                                </ul>
                            </li>
                            {{-- MOTORIK --}}
                            <li class="{{ request()->is('report/gtc/motorik/*') ? 'open' : '' }}">
                            <a class="nav-submenu" data-toggle="nav-submenu"><span class="sidebar-mini-hide">Motoris</span></a>
                            <ul>
                                {{-- ATTENDANCE MOTORIK --}}
                                <li>
                                    <a class="{{ request()->is('report/gtc/motorik/attendance') ? 'active' : '' }}" href="{{ route('report.motorik.attendance') }}"><span class="sidebar-mini-hide">Attendance</span></a>
                                </li>
                                {{-- DIST PF MOTORIK --}}
                                <li>
                                    <a class="{{ request()->is('report/gtc/motorik/distPF') ? 'active' : '' }}" href="{{ route('report.motorik.distPF') }}"><span class="sidebar-mini-hide">Dist PF</span></a>
                                </li>
                                {{-- SALES MOTORIK --}}
                                <li>
                                    <a class="{{ request()->is('report/gtc/motorik/sales') ? 'active' : '' }}" href="{{ route('report.motorik.sales') }}"><span class="sidebar-mini-hide">Sales</span></a>
                                </li>
                            </ul>
                            </li>
                    @endif
                    @if(Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'AdminMtc')
                    {{-- REPORT MTC --}}
                    <!-- <li class="{{ request()->is('report/mtc*') ? 'open' : '' }}">
                        <a class="nav-submenu" data-toggle="nav-submenu"><i class="si si-bar-chart"></i><span class="sidebar-mini-hide">MTC</span></a>
                        <ul>
                            {{-- ATTENDANCE MTC --}}
                            <li>
                                <a class="{{ request()->is('report/mtc/attendance') ? 'active' : '' }}" href="{{ route('attendance') }}"><span class="sidebar-mini-hide">Attendance</span></a>
                            </li>
                            {{-- SALES MTC --}}
                            <li>
                                <a class="{{ request()->is('report/mtc/salesmtc') ? 'active' : '' }}" href="{{ route('salesmtc') }}"><span class="sidebar-mini-hide">Sales Summary</span></a>
                            </li>                            
                            {{-- Display Share--}}
                            <li>
                                <a class="{{ request()->is('report/mtc/display_share') ? 'active' : '' }}" href="{{ route('display_share') }}"><span class="sidebar-mini-hide">Display Share</span></a>
                            </li>                              
                            {{-- Additional Display--}}
                            <li>
                                <a class="{{ request()->is('report/mtc/additional_display') ? 'active' : '' }}" href="{{ route('additional_display') }}"><span class="sidebar-mini-hide">Additional Display</span></a>
                            </li>                              
                            {{-- Price Row--}}
                            <li>
                                <a class="{{ request()->is('report/mtc/priceData/row') ? 'active' : '' }}" href="{{ route('priceData.row') }}"><span class="sidebar-mini-hide">Price Row</span></a>
                            </li>                              
                            {{-- Price Summary--}}
                            <li>
                                <a class="{{ request()->is('report/mtc/priceData/summary') ? 'active' : '' }}" href="{{ route('priceData.summary') }}"><span class="sidebar-mini-hide">Price Summary</span></a>
                            </li>                
                            {{-- Price Vs--}}
                            <li>
                                <a class="{{ request()->is('report/mtc/priceData/vs') ? 'active' : '' }}" href="{{ route('priceData.vs') }}"><span class="sidebar-mini-hide">Price Vs Competitor</span></a>
                            </li>                
                            {{-- OOS--}}
                            <li>
                                <a class="{{ request()->is('report/mtc/oos/row') ? 'active' : '' }}" href="{{ route('oos.row') }}"><span class="sidebar-mini-hide">OOS</span></a>
                            </li> 
                            {{-- Availability--}}
                            <li>
                                <a class="{{ request()->is('report/mtc/availability/row') ? 'active' : '' }}" href="{{ route('availability.row') }}"><span class="sidebar-mini-hide">Availability</span></a>
                            </li> 
                            {{-- Promo Activity--}}
                            <li>
                                <a class="{{ request()->is('promoactivity') ? 'active' : '' }}" href="{{ route('promoactivity') }}"><span class="sidebar-mini-hide">Promo Activity</span></a>
                            </li>
                            {{-- Achievement --}}
                            <li class="{{ request()->is('report/mtc/achievement') ? 'open' : '' }} {{ request()->is('report/mtc/display_share/ach') ? 'open' : '' }} {{ request()->is('report/mtc/additional_display/ach') ? 'open' : '' }}">
                            <a class="nav-submenu" data-toggle="nav-submenu"><span class="sidebar-mini-hide">Achievement</span></a>
                                <ul>
                                    {{-- SALES MTC REVIEW --}}
                                    <li>
                                        <a class="{{ request()->is('report/mtc/achievement') ? 'active' : '' }}" href="{{ route('achievement-salesmtc') }}"><span class="sidebar-mini-hide">Achievement MTC</span></a>
                                    </li>
                                    <li>
                                        <a class="{{ request()->is('report/mtc/display_share/ach') ? 'active' : '' }}" href="{{ route('display_share.ach') }}"><span class="sidebar-mini-hide">Ach Display Share</span></a>
                                    </li>  
                                    <li>
                                        <a class="{{ request()->is('report/mtc/additional_display/ach') ? 'active' : '' }}" href="{{ route('additional_display.ach') }}"><span class="sidebar-mini-hide">Ach Additional Display</span></a>
                                    </li>  
                                    <li>
                                        <a class="{{ request()->is('report/mtc/availability') ? 'active' : '' }}" href="{{ route('availability') }}"><span class="sidebar-mini-hide">Availability</span></a>
                                    </li> 
                                </ul>
                            </li>
                        </ul>
                    </li> -->
                    @endif
                </ul>

                      
                   
        </div>
     
    </div>
</nav>

