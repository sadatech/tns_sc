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
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <span>
                    <i class="fa fa-calendar"></i> Periode
                  </span>
                  <input type="text" id="filterMonth" class="form-control" placeholder="Periode" name="periode">
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
                <table class="table table-striped table-vcenter js-dataTable-full table-responsive" id="cashDcTable">
                    <thead>
                        <tr>
                            <td rowspan="2" valign="middle" align="center">Tgl.</td>
                            <td rowspan="2" valign="middle" align="center">Keterangan</td>
                            <td colspan="3" align="center" valign="middle">KM pada saat pengisian BBM</td>
                            <td rowspan="2" valign="middle" align="center">TPD</td>
                            <td rowspan="2" valign="middle" align="center">Hotel / Kosan</td>
                            <td rowspan="2" valign="middle" align="center">BBM</td>
                            <td rowspan="2" valign="middle" align="center">Parkir/Tol</td>
                            <td rowspan="2" valign="middle" align="center">Pembelian Bahan Baku</td>
                            <td rowspan="2" valign="middle" align="center">Pembelian Property</td>
                            <td rowspan="2" valign="middle" align="center">Perijinan</td>
                            <td colspan="{{ route('spg.pasar.sales.summary.data') }}"" align="center" valign="middle">Angkutan</td>
                            <td colspan="2" align="center" valign="middle">Biaya lain-lain</td>
                            <td rowspan="2" valign="middle" align="center">Total Biaya</td>
                        </tr>
                        <tr>
                            <td valign="middle" align="center">KM Awal</td>
                            <td valign="middle" align="center">KM Akhir</td>
                            <td valign="middle" align="center">Jarak Tempuh</td>
                            <!--  -->
                            <td valign="middle" align="center">Bus</td>
                            <td valign="middle" align="center">SIPA</td>
                            <td valign="middle" align="center">Ojek</td>
                            <td valign="middle" align="center">Becak</td>
                            <td valign="middle" align="center">Taksi</td>
                            <!--  -->
                            <td valign="middle" align="center">Rp.</td>
                            <td valign="middle" align="center">Keterangan</td>
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
          <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import <i>Data Product</i></h3>
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
            <a href="{{ route('product.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
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
  min-width: 200px;
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
         * Filter Month
         */
        $('#filterMonth').datetimepicker({
            format: "MM yyyy",
            startView: "3",
            minView: "3",
            autoclose: true,
        });
        $('#filterMonth').val(moment().format("MMMM Y"));
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
          $('#filterMonth').val(moment().format("MMMM Y"));
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

            $('#panelData').removeAttr('style');

            var serial = $("#filterForm").serialize();

            if($.fn.dataTable.isDataTable('#cashDcTable'))
            {
                $('#cashDcTable').DataTable().clear();
                $('#cashDcTable').DataTable().destroy();
            }

            $('#cashDcTable').dataTable({
                "fnCreatedRow": function (nRow, data) {
                    $(nRow).attr('class', data.id);
                },
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('spg.pasar.sales.summary.data') }}" + "?" + $("#filterForm").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    error: function (data) {
                      swal("Error!", "Failed to load Data!", "error");
                    },
                },
                scrollX:        true,
                scrollCollapse: true,
                "bFilter": false,
                "rowId": "id",
                // "columns": tableColumns,
                // "columnDefs": columnDefs,
                "order": [[0, 'desc']],
                "ordering": false
            });

        });

    });
</script>

@endsection

