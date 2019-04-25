@extends('dashboard')
@section('konten')
<div class="row invisible" data-toggle="appear">
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-briefcase fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600" id="product"><i class="fa fa-spin fa-spinner"></i></div>
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
                <div class="font-size-h3 font-w600" id="store"><i class="fa fa-spin fa-spinner"></i></div>
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
                <div class="font-size-h3 font-w600" id="pasar"><i class="fa fa-spin fa-spinner"></i></div>
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
                <div class="font-size-h3 font-w600" id="employee"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Employee</div>
            </div>
        </a>
    </div>
</div>

    @if(Auth::user()->role->level == 'AdminGtc' || Auth::user()->role->level == 'MasterAdmin' || Auth::user()->role->level == 'Administrator' || Auth::user()->role->level == 'ViewAll')
<!-- 
    <div class="block block-themed">
        <div class="block">
          <div class="block-content block-content-full">
            <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="vdo">
            <thead>
              <th style="width: 70px;"></th>
              <th>NAMA AREA</th>
              <th>NAMA VDO</th>
              <th>JABATAN</th>
              <th>TOTAL HK BULAN INI</th>
              <th>HARI KERJA ACTUAL</th>
              <th>KONTRAK DISPLAY Target</th>
              <th>KONTRAK DISPLAY Actual</th>
              <th>KONTRAK DISPLAY Ach</th>
             <tfoot>
             <tr>
                <th colspan="4" style="text-align:right">total amount:</th>
                <th>{{ $hk_target }}</th>
                <th>{{ $hk_actual }}</th>
                <th>{{ $cbd_target }}</th>
                <th>{{ $cbd_actual }}</th>
                <th>{{ $ach }}</th>
             </tr>
             </tfoot>
            </thead>
            </table>
          </div>
        </div>
    </div>
    <div class="block block-themed">
        <div class="block">
          <div class="block-content block-content-full">
            <div class="block-header p-0 mb-20">
            </div>
            <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="vdoAch">
            <thead>
              <th style="width: 70px;"></th>
              <th>NAMA VDO</th>
              <th>NAMA AREA</th>
              <th>KONTRAK DISPLAY Target</th>
            </thead>
            </table>
          </div>
        </div>
    </div> -->
    <div class="row" id="content">
      <div class="col-md-5">
        <div class="block block-bordered block-themed">
          <div class="block-header p-5 pl-10">
            <h3 class="block-title" id="pie"></h3>
          </div>
          <div class="block-content">
            <canvas id="myChartPie"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="block block-bordered block-themed">
          <div class="block-header p-5 pl-10">
            <h3 class="block-title" id="bar"></h3>
          </div>
          <div class="block-content">
            <canvas id="AreaChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    @endif
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
</style>
@endsection
@section('js')
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route('data.dashboard') }}',
            type: 'GET',
            success: function (data) {
                // console.log(data);
                if (data.success){
                    $('#employee').html(data.count.employee);
                    $('#store').html(data.count.store);
                    $('#product').html(data.count.product);
                    $('#pasar').html(data.count.pasar);
                    $('#pie').html(data.pie);
                    $('#bar').html(data.bar);
                }
            }
        });
    })
    $(function() {
        $('#vdo').DataTable({
            processing: true,
            scrollY: "300px",
            ajax: '{!! route('data.dashboard.achSmd') !!}',
            columns: [
            { data: 'id', name: 'id' },
            { data: 'area', name: 'area' },
            { data: 'name', name: 'name' },
            { data: 'status', name: 'status' },
            { data: 'hk_target', name: 'hk_target' },
            { data: 'hk_actual', name: 'hk_actual' },
            { data: 'cbd_target', name: 'cbd_target' },
            { data: 'sum_of_cbd', name: 'sum_of_cbd' },
            { data: 'ach', name: 'ach' },
            ]
        });
    });
    $(function() {
        $('#vdoAch').DataTable({
            processing: true,
            scrollY: "300px",
            ajax: '{!! route('data.dashboard.achSmdArea') !!}',
            columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'area', name: 'area' },
            { data: 'sum_of_cbd', name: 'sum_of_cbd' },
            ],
            order: [[ 3, "desc" ]]
        });
    });
</script>
@endsection

@section('chartjs')
<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var urlPie = "{{ route('data.dashboard.chartPieNational') }}";
        var cbdStatus = new Array();
        var cbdPersen = new Array();
        $(document).ready(function(){
          $.get(urlPie, function(response){
            response.forEach(function(data){
                cbdStatus.push(data.name);
                cbdPersen.push(data.poin);
            });
            var pie = document.getElementById('myChartPie').getContext('2d');
            var myChartPie = new Chart(pie, {
                type: 'doughnut',
                data: {
                    labels: cbdStatus,
                    datasets: [{
                        label: 'CBD',
                        data: cbdPersen,
                        backgroundColor: [
                            'rgba(65, 200, 68, 0.9)',
                            'rgba(244, 66, 66, 0.9)',
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 69, 132, 1)',
                        ],
                        borderWidth: 1
                    }]
                }
            });
          });
        });
        var url = "{{ route('data.dashboard.chartArea') }}";
        var Area = new Array();
        var cbdArea = new Array();
        var Color = new Array();
        $(document).ready(function(){
          $.get(url, function(response){
            response.forEach(function(data){
                Area.push(data.name);
                cbdArea.push(data.persen);
                Color.push(data.color);
            });
            var arx = document.getElementById("AreaChart").getContext('2d');
            var AreaChart = new Chart(arx, {
              type: 'horizontalBar',
              data: {
                  labels:Area,
                  datasets: [{
                      label: 'CBD Actual',
                      backgroundColor: Color,
                      data: cbdArea,
                      borderWidth: 1
                  }]
              },
              options: {
                  responsive: true,
                  title: {
                      display: true,
                      text: 'Kontrak Display Area'
                  },
                  scales: {
                      xAxes: [{
                          ticks: {
                              beginAtZero:true
                          }
                      }]
                  }
              }
            });
          });
        });
    });
</script>
@endsection
