@extends('layouts.app')
@section('title', "Report Cash Advance")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Cash Advance <small>Report</small></h2>
    @if($errors->any())
    <div class="alert alert-danger">
        <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
        @foreach ($errors->all() as $error)
        <div> {{ $error }}</div>
        @endforeach
    </div>
    @endif
    <div class="block block-themed block-mode-loading-refresh">
      <div class="block-header bg-primary">
          <h3 class="block-title">
              Filters
          </h3>
          <div class="block-options">
              <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-down"></i></button>
          </div>
      </div>
      <div class="block-content bg-white">
        <form id="filterForm" method="post" action="#">
          <div class="row items-push">
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Area</div>
                  <select id="filterArea" class="inputFilter" name="id_area"></select>
              </div>
              <div class="col-2 col-sm-2 text-center text-sm-left">
                  <span>
                    <i class="fa fa-calendar"></i> From :
                  </span>
                  <input type="text" id="filterMonthFrom" class="form-control" placeholder="Periode" name="periodeFrom">
              </div>
              <p>
                <br> -
              </p>
              <div class="col-2 col-sm-2 text-center text-sm-left">
                  <span>
                     <i class="fa fa-calendar"></i> To :
                  </span>
                  <input type="text" id="filterMonthTo" class="form-control" placeholder="Periode" name="periodeTo">
              </div>
          </div>
          <div class="row col-sm-12 col-md-12">
            <p class="btn btn-sm btn-primary" id="filterSearch"><i class="fa fa-search"></i> Search</p>
            <p class="btn btn-sm btn-danger" id="filterReset"><i class="fa fa-refresh"></i> Clear</p>
          </div>
        </form>
      </div>
    </div>

    <div class="block block-themed " id="panelData" style="display: none;"> 
        <div class="block-header bg-gd-sun">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">        
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <h3 class="block-title">
                    </h3>
                    <div class="block-option">
                        <a id="btnDownloadXLS" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                        <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                    </div>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered table-responsive" id="cashDcTable">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle; width: 20px !important;">Tgl.</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Employee</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Keterangan</th>
                            <th colspan="3" style="vertical-align: middle; text-align: center; width: 200px !important;">KM pada saat pengisian BBM</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">TPD</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Hotel / Kosan</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">BBM</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Parkir/Tol</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Pembelian Bahan Baku</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Pembelian Property</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Perijinan</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 200px !important;">Angkutan</th>
                            <th colspan="2" style="vertical-align: middle; text-align: center; width: 200px !important;">Biaya lain-lain</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Total Biaya</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Profit</th>
                            <th rowspan="2" style="vertical-align: middle; width: 200px !important;">Subsidi Sasa</th>
                        </tr>
                        <tr>
                            <th style="vertical-align: middle; width: 120px !important;">KM Awal</th>
                            <th style="vertical-align: middle; width: 120px !important;">KM Akhir</th>
                            <th style="vertical-align: middle; width: 120px !important;">Jarak Tempuh</th>
                            <!--  --><!-- 
                            <th style="vertical-align: middle; width: 120px !important;">Bus</th>
                            <th style="vertical-align: middle; width: 120px !important;">SIPA</th>
                            <th style="vertical-align: middle; width: 120px !important;">Ojek</th>
                            <th style="vertical-align: middle; width: 120px !important;">Becak</th>
                            <th style="vertical-align: middle; width: 120px !important;">Taksi</th> -->
                            <!--  -->
                            <th style="vertical-align: middle; width: 120px !important;">Rp.</th>
                            <th style="vertical-align: middle; width: 120px !important;">Keterangan</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import <i>Cash Advance Data</i></h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('report.demo.import') }}">
        {{ csrf_field() }}
        <div class="block-content">
          <div class="form-group">
            <a href="{{ route('report.dc.cash.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
            <div class="text-center form-group col-md-12 text-sm-left">
                <label>Area:</label>
                <select id="areaModal" class="inputFilter" name="id_area"></select>
            </div>
            <div class="text-center form-group col-md-12 text-sm-left">
                <label>Employee:</label>
                <select id="employeeModal" class="inputFilter" name="id_employee"></select>
            </div>
            <div class="text-center form-group col-md-12 text-sm-left monthModal">
                <label>Period:</label>
                <input type="text" id="monthModal" class="form-control" placeholder="Periode">
                <input type="hidden" id="period" name="periode">
            </div>
          <div class="form-group col-md-12">
            <label>Upload Your Data Cash Advance:</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                <label class="custom-file-label">Choose file Excel</label>
                <code> *Type File Excel</code>
            </div>
           </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-alt-success">
            <i class="fa fa-save"></i> Import
          </button>
          <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
<style type="text/css">
[data-notify="container"] {
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
/*table.table thead tr th:first-child {
  min-width: 5px;
}*/
table.table thead tr th {
  /*width: 300px !important;*/
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.js') }}"></script>
<script src="{{ asset('js/datetimepicker-handler.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
      @if(session('type'))
        $.notify({
            title: '<strong>{!! session('title') !!}</strong>',
            message: '{!! session('message') !!}'
        }, {
            type: '{!! session('type') !!}',
            animate: {
                enter: 'animated zoomInDown',
                exit: 'animated zoomOutUp'
            },
            placement: {
                from: 'top',
                align: 'center'
            }
        });
      @endif
        /**
         * Ajax CSRF
         */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /**
         * Download OnClick
         */
        $("#btnDownloadXLS").on("click", function(){
          $.ajax({
            url: $(this).attr("target-url"),
            type: "post",
            success: function(e){
              swal("Success!", e.result, "success");
            },
            error: function(){
              swal("Error!", e.result, "error");
            }
          });
        });

        /**
         * Filter Month
         */
        $('#filterMonthFrom').datetimepicker({
            format: "d MM yyyy",
            startView: "2",
            minView: "2",
            autoclose: true,
        });
        $('#filterMonthTo').datetimepicker({
            format: "d MM yyyy",
            startView: "2",
            minView: "2",
            autoclose: true,
        });
        $('#filterMonthFrom').val(moment().format("D MMMM Y"));
        $('#filterMonthTo').val(moment().format("D MMMM Y"));
        $('#monthModal').datetimepicker({
            format: "MM yyyy",
            startView: "3",
            minView: "3",
            autoclose: true,
        }).on('changeDate', function(ev){
          $('#period').val(moment(ev.date).format('Y-MM-DD'));
        });
        

        /**
         * Filter Area Select2
         */
        $('#filterArea').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj){
                return {id: obj.id, text: obj.name}
              })
            }
          }));

        $('#areaModal').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj){
                return {id: obj.id, text: obj.name}
              })
            }
          }));

        $('#employeeModal').select2(setOptions('{{ route("employee-select2") }}', 'Select Employee', function (params) {
          filters['roleGroup'] = ['dc'];
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj){
                return {id: obj.id, text: obj.name}
              })
            }
          }));

        /**
         * Filter Reset Click
         */
        $("#filterReset").click(function (){
          $.each($('#filterForm select'), function(key, value) {
            $('#'+this.id).val(null).trigger('change');
          });
          $('#filterMonthFrom').val(moment().format("D MMMM Y"));
          $('#filterMonthTo').val(moment().format("D MMMM Y"));
          $('#panelData').attr("style", "display:none;");
        })

        /**
         * Filter Search Click
         */
        $("#filterSearch").click(function() {

            if($('#filterArea').val() == null){
                swal("Warning!", "Please select Area First!", "warning");
                return;
            }
            if($('#filterMonthFrom').val() == null){
                swal("Warning!", "Please select Periode From First!", "warning");
                return;
            }
            if($('#filterMonthTo').val() == null){
                swal("Warning!", "Please select Periode To First!", "warning");
                return;
            }

            $('#panelData').removeAttr('style');

            var serial = $("#filterForm").serialize();

            if($.fn.dataTable.isDataTable('#cashDcTable'))
            {
                $('#cashDcTable').DataTable().clear();
                $('#cashDcTable').DataTable().destroy();
            }

            $("#btnDownloadXLS").attr("target-url", "{{ route('report.demo.cashAdvance.exportXLS') }}" + "/" + $('#filterArea').val() + "/" + $('#filterMonthFrom').val() + "/" + $('#filterMonthTo').val());

            $('#cashDcTable').dataTable({
                "fnCreatedRow": function (nRow, data) {
                    $(nRow).attr('class', data.id);
                },
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('report.demo.cashAdvance.data') }}" + "?" + $("#filterForm").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    error: function (data) {
                      swal("Error!", "Failed to load Data!", "error");
                    },
                },
                scrollX:        true,
                scrollCollapse: true,
                // "bFilter": false,
                // "rowId": "id",
                "columns": [
                  { data: 'tgl', name: 'tgl' },
                  { data: 'employee', name: 'employee' },
                  { data: 'description', name: 'description'},
                  { data: 'km_begin', name: 'km_begin'},
                  { data: 'km_end', name: 'km_end'},
                  { data: 'km_distance', name: 'km_distance'},
                  { data: 'tpd', name: 'tpd'},
                  { data: 'hotel', name: 'hotel'},
                  { data: 'bbm', name: 'bbm'},
                  { data: 'parking_and_toll', name: 'parking_and_toll'},
                  { data: 'raw_material', name: 'raw_material'},
                  { data: 'property', name: 'property'},
                  { data: 'permission', name: 'permission'},
                  // { data: 'bus', name: 'bus'},
                  // { data: 'sipa', name: 'sipa'},
                  // { data: 'taxibike', name: 'taxibike'},
                  // { data: 'rickshaw', name: 'rickshaw'},
                  // { data: 'taxi', name: 'taxi'},
                  { data: 'trasnport', name: 'trasnport'},
                  { data: 'other_cost', name: 'other_cost'},
                  { data: 'other_description', name: 'other_description'},
                  { data: 'total_cost', name: 'total_cost'},
                  { data: 'price_profit', name: 'price_profit'},
                  { data: 'subsidi_sasa', name: 'subsidi_sasa'},
                ],
                // "columnDefs": columnDefs,
                "order": [[0, 'desc']],
                "ordering": false
            });

        });

    });
</script>

@endsection

