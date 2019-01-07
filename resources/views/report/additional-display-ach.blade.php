@extends('layouts.app')
@section('title', "Sales Report - Additional Display Ach")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Additional Display Ach <small>Report</small></h2>
  @if($errors->any())
    <div class="alert alert-danger">
      <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
      @foreach ($errors->all() as $error)
      <div> {{ $error }}</div>
      @endforeach
    </div>
  @endif
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <div class="block-option">
            <a id="btnDownloadXLS" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</a>
            <a id="btnDownloadXLSAll" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</a>
          </div>
        </div>
        <center><h3>Area/TL</h3></center>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTableArea">
        <thead>
          <tr>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">PERFORMANCE AREA/TL</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">JML. STORE COVERAGE</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">JLM. STORE PANEL</th>
            <th colspan="2" style="vertical-align: middle; text-align: center;">Additional Display</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 200px;">Area</th>
          </tr>
          <tr>
            <th style="vertical-align: middle; text-align: center;">Actual</th>
            <th style="vertical-align: middle; text-align: center;">% Ach.</th>
          </tr>

        </thead>
        </table>

        <center><h3>SPG</h3></center>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTableSpg">
        <thead>
          <tr>
                <th rowspan="2" style="vertical-align: middle; text-align: center;">PERFORMANCE SPG</th>
                <th rowspan="2" style="vertical-align: middle; text-align: center;">JML. STORE COVERAGE</th>
                <th rowspan="2" style="vertical-align: middle; text-align: center;">JLM. STORE PANEL</th>
                <th colspan="2" style="vertical-align: middle; text-align: center;">Additional Display</th>
                <th rowspan="2" style="vertical-align: middle; text-align: center; width: 200px;">NAMA STORE</th>
          </tr>
          <tr>
          <th style="vertical-align: middle; text-align: center;">Actual</th>
          <th style="vertical-align: middle; text-align: center;">% Ach.</th>
          </tr>
        </thead>
        </table>

        <center><h3>MD</h3></center>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTableMd">
        <thead>
          <tr>
                <th rowspan="2" style="vertical-align: middle; text-align: center;">PERFORMANCE MD</th>
                <th rowspan="2" style="vertical-align: middle; text-align: center;">JML. STORE COVERAGE</th>
                <th rowspan="2" style="vertical-align: middle; text-align: center;">JLM. STORE PANEL</th>
                <th colspan="2" style="vertical-align: middle; text-align: center;">Additional Display</th>
                <th rowspan="2" style="vertical-align: middle; text-align: center; width: 200px;">JML STORE</th>
          </tr>
          <tr>
          <th style="vertical-align: middle; text-align: center;">Actual</th>
          <th style="vertical-align: middle; text-align: center;">% Ach.</th>
          </tr>
        </thead>
        </table>

      </div>
    </div>
  </div>
  </div>
</div>


@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <style type="text/css">
    [data-notify="container"] 
    {
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    th, td {
        white-space: nowrap;
    }
    </style>
@endsection

@section('script')

  <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
  <script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
  <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('js/select2-handler.js') }}"></script>
  <script type="text/javascript">

      @if(session('type'))
      $(document).ready(function() {
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

      });
      @endif
</script>
<script type="text/javascript">
    $(document).ready(function() {

        /**
         * Ajax Setup
         */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /**
         * Download OnClick
         */
        $("#btnDownloadXLS, #btnDownloadXLSAll").on("click", function() {
            $.ajax({
                url: $(this).attr("target-url"),
                type: "post",
                success: function(e) {
                    swal("Success!", e.result, "success");
                },
                error: function() {
                    swal("Error!", e.result, "error");
                }
            });
        });

        $('#reportTableArea').DataTable({
            processing: true,
            serverSide: true,
            drawCallback: function() {
                $("#btnDownloadXLSAll").attr("target-url", "{{ route('additional_display.exportXLS') }}" + "?limitArea=&limitSPG=&limitMD=");
                $("#btnDownloadXLS").attr("target-url", "{{ route('additional_display.exportXLS') }}" + "?limitArea=" + $("#reportTableArea_length select").val() + "&limitSPG=" + $("#reportTableSpg_length select").val() + "&limitMD=" + $("#reportTableMd_length select").val());
            },
            ajax: '{!! route('additional_display.reportDataArea') !!}',
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'store_cover',
                    name: 'store_cover'
                },
                {
                    data: 'store_panel_cover',
                    name: 'store_panel_cover'
                },
                {
                    data: 'actual',
                    name: 'actual'
                },
                {
                    data: 'ach',
                    name: 'ach'
                },
                {
                    data: 'location',
                    name: 'location'
                },
            ],
            "scrollX": true,
            "scrollCollapse": true,
            "columnDefs": [{
                "className": "text-center",
                "targets": [1, 2, 3, 4, 5]
            }],
        });

        $('#reportTableSpg').DataTable({
            processing: true,
            serverSide: true,
            drawCallback: function() {
                $("#btnDownloadXLSAll").attr("target-url", "{{ route('additional_display.exportXLS') }}" + "?limitArea=&limitSPG=&limitMD=");
                $("#btnDownloadXLS").attr("target-url", "{{ route('additional_display.exportXLS') }}" + "?limitArea=" + $("#reportTableArea_length select").val() + "&limitSPG=" + $("#reportTableSpg_length select").val() + "&limitMD=" + $("#reportTableMd_length select").val());
            },
            ajax: '{!! route('additional_display.reportDataSpg') !!}',
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'store_cover',
                    name: 'store_cover'
                },
                {
                    data: 'store_panel_cover',
                    name: 'store_panel_cover'
                },
                {
                    data: 'actual',
                    name: 'actual'
                },
                {
                    data: 'ach',
                    name: 'ach'
                },
                {
                    data: 'location',
                    name: 'location'
                },
            ],
            "scrollX": true,
            "scrollCollapse": true,
            "columnDefs": [{
                "className": "text-center",
                "targets": [1, 2, 3, 4]
            }],
        });

        $('#reportTableMd').DataTable({
            processing: true,
            serverSide: true,
            drawCallback: function() {
                $("#btnDownloadXLSAll").attr("target-url", "{{ route('additional_display.exportXLS') }}" + "?limitArea=&limitSPG=&limitMD=");
                $("#btnDownloadXLS").attr("target-url", "{{ route('additional_display.exportXLS') }}" + "?limitArea=" + $("#reportTableArea_length select").val() + "&limitSPG=" + $("#reportTableSpg_length select").val() + "&limitMD=" + $("#reportTableMd_length select").val());
            },
            ajax: '{!! route('additional_display.reportDataMd') !!}',
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'store_cover',
                    name: 'store_cover'
                },
                {
                    data: 'store_panel_cover',
                    name: 'store_panel_cover'
                },
                {
                    data: 'actual',
                    name: 'actual'
                },
                {
                    data: 'ach',
                    name: 'ach'
                },
                {
                    data: 'location',
                    name: 'location'
                },
            ],
            "scrollX": true,
            "scrollCollapse": true,
            "columnDefs": [{
                "className": "text-center",
                "targets": [1, 2, 3, 4, 5]
            }],
        });
    });
  </script>
@endsection