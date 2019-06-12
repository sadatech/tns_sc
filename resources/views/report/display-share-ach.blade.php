@extends('layouts.app')
@section('title', "Sales Report - Display Share Ach")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Display Share Ach <small>Report</small></h2>
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

        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="reportTableArea">
          <thead>
            <tr>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">PERFORMANCE AREA/TL</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">JML. STORE COVERAGE</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">JLM. STORE PANEL</th>
              <th colspan="2" style="vertical-align: middle; text-align: center;">DISPLAY SHARE TB</th>
              <th colspan="2" style="vertical-align: middle; text-align: center;">DISPLAY SHARE FOKUS</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center; width: 200px;">Area</th>
            </tr>
            <tr>
              <th style="vertical-align: middle; text-align: center;">JML. STORE HIT TARGET</th>
              <th style="vertical-align: middle; text-align: center;">% Ach.</th>
              <th style="vertical-align: middle; text-align: center;">JML. STORE HIT TARGET</th>
              <th style="vertical-align: middle; text-align: center;">% Ach.</th>
            </tr>

          </thead>
        </table>

        <center><h3>SPG</h3></center>

        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="reportTableSpg">
          <thead>
            <tr>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">PERFORMANCE SPG</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">JML. STORE COVERAGE</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">JLM. STORE PANEL</th>
              <th colspan="2" style="vertical-align: middle; text-align: center;">DISPLAY SHARE TB</th>
              <th colspan="2" style="vertical-align: middle; text-align: center;">DISPLAY SHARE FOKUS</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center; width: 200px;">NAMA STORE</th>
            </tr>
            <tr>
              <th style="vertical-align: middle; text-align: center;">JML. STORE HIT TARGET</th>
              <th style="vertical-align: middle; text-align: center;">% Ach.</th>
              <th style="vertical-align: middle; text-align: center;">JML. STORE HIT TARGET</th>
              <th style="vertical-align: middle; text-align: center;">% Ach.</th>
            </tr>
          </thead>
        </table>

        <center><h3>MD</h3></center>

        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="reportTableMd">
          <thead>
            <tr>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">PERFORMANCE MD</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">JML. STORE COVERAGE</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">JLM. STORE PANEL</th>
              <th colspan="2" style="vertical-align: middle; text-align: center;">DISPLAY SHARE TB</th>
              <th colspan="2" style="vertical-align: middle; text-align: center;">DISPLAY SHARE FOKUS</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center; width: 200px;">JML STORE</th>
            </tr>
            <tr>
              <th style="vertical-align: middle; text-align: center;">JML. STORE HIT TARGET</th>
              <th style="vertical-align: middle; text-align: center;">% Ach.</th>
              <th style="vertical-align: middle; text-align: center;">JML. STORE HIT TARGET</th>
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/magnific-popup/magnific-popup.css') }}">
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
<script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
  $('#reset').click(function(){
    setTimeout(function() {
      $('.js-datepicker').val(null);
    }, 10);
  });
  $(".js-datepicker").datepicker( {
    format: "mm/yyyy",
    viewMode: "months",
    autoclose: true,
    minViewMode: "months"
  });
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

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

  $('#filter').submit(function(e) {
    Codebase.layout('header_loader_on');
    e.preventDefault();
    var table = null;

    var url = '{!! route('display_share.reportDataArea') !!}';
    table = $('#reportTableArea').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "400px",
      ajax: {
        url: url + "?" + $("#filter").serialize(),
        type: 'GET',
        dataType: 'json',
        dataSrc: function(res) {
          Codebase.layout('header_loader_off');
          if (res.data == 0) {
            $('#table-block').hide();
            swal("Error!", "Data is empty!", "error");
            return res.data;
          } else {
            $('#table-block').show();
            return res.data;
          }
        },
        error: function (data) {
          Codebase.layout('header_loader_off');
          swal("Error!", "Failed to load Data!", "error");
        },
      },
      drawCallback: function(){
        $('.popup-image').magnificPopup({
          type: 'image',
        });
      },
      columns: [
      { data: 'name', name: 'name'},
      { data: 'store_cover', name: 'store_cover'},
      { data: 'store_panel_cover', name: 'store_panel_cover'},
      { data: 'hitTargetTB', name: 'hitTargetTB'},
      { data: 'achTB', name: 'achTB'},
      { data: 'hitTargetPF', name: 'hitTargetPF'},
      { data: 'achPF', name: 'achPF'},
      { data: 'location', name: 'location'},
      ],
      bDestroy: true
    });

    url = '{!! route('display_share.reportDataSpg') !!}';
    table = $('#reportTableSpg').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "400px",
      ajax: {
        url: url + "?" + $("#filter").serialize(),
        type: 'GET',
        dataType: 'json',
        dataSrc: function(res) {
          Codebase.layout('header_loader_off');
          if (res.data == 0) {
            $('#table-block').hide();
            swal("Error!", "Data is empty!", "error");
            return res.data;
          } else {
            $('#table-block').show();
            return res.data;
          }
        },
        error: function (data) {
          Codebase.layout('header_loader_off');
          swal("Error!", "Failed to load Data!", "error");
        },
      },
      drawCallback: function(){
        $('.popup-image').magnificPopup({
          type: 'image',
        });
      },
      columns: [
      { data: 'name', name: 'name'},
      { data: 'store_cover', name: 'store_cover'},
      { data: 'store_panel_cover', name: 'store_panel_cover'},
      { data: 'hitTargetTB', name: 'hitTargetTB'},
      { data: 'achTB', name: 'achTB'},
      { data: 'hitTargetPF', name: 'hitTargetPF'},
      { data: 'achPF', name: 'achPF'},
      { data: 'location', name: 'location'},
      ],
      bDestroy: true
    });

    url = '{!! route('display_share.reportDataMd') !!}';
    table = $('#reportTableMd').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "400px",
      ajax: {
        url: url + "?" + $("#filter").serialize(),
        type: 'GET',
        dataType: 'json',
        dataSrc: function(res) {
          Codebase.layout('header_loader_off');
          if (res.data == 0) {
            $('#table-block').hide();
            swal("Error!", "Data is empty!", "error");
            return res.data;
          } else {
            $('#table-block').show();
            return res.data;
          }
        },
        error: function (data) {
          Codebase.layout('header_loader_off');
          swal("Error!", "Failed to load Data!", "error");
        },
      },
      drawCallback: function(){
        $('.popup-image').magnificPopup({
          type: 'image',
        });
      },
      columns: [
      { data: 'name', name: 'name'},
      { data: 'store_cover', name: 'store_cover'},
      { data: 'store_panel_cover', name: 'store_panel_cover'},
      { data: 'hitTargetTB', name: 'hitTargetTB'},
      { data: 'achTB', name: 'achTB'},
      { data: 'hitTargetPF', name: 'hitTargetPF'},
      { data: 'achPF', name: 'achPF'},
      { data: 'location', name: 'location'},
      ],
      bDestroy: true
    });
  });
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
            $("#btnDownloadXLSAll").attr("target-url", "{{ route('display_share.report.exportXLS') }}" + "?limitArea=&limitSPG=&limitMD=");
            $("#btnDownloadXLS").attr("target-url", "{{ route('display_share.report.exportXLS') }}" + "?limitArea=" + $("#reportTableArea_length select").val() + "&limitSPG=" + $("#reportTableSpg_length select").val() + "&limitMD=" + $("#reportTableMd_length select").val());
        },
        ajax: '{!! route('display_share.reportDataArea') !!}',
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
                data: 'hitTargetTB',
                name: 'hitTargetTB'
            },
            {
                data: 'achTB',
                name: 'achTB'
            },
            {
                data: 'hitTargetPF',
                name: 'hitTargetPF'
            },
            {
                data: 'achPF',
                name: 'achPF'
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
            "targets": [1, 2, 3, 4, 5, 6, 7]
        }],
    });

    $('#reportTableSpg').DataTable({
        processing: true,
        serverSide: true,
        drawCallback: function() {
            $("#btnDownloadXLSAll").attr("target-url", "{{ route('display_share.report.exportXLS') }}" + "?limitArea=&limitSPG=&limitMD=");
            $("#btnDownloadXLS").attr("target-url", "{{ route('display_share.report.exportXLS') }}" + "?limitArea=" + $("#reportTableArea_length select").val() + "&limitSPG=" + $("#reportTableSpg_length select").val() + "&limitMD=" + $("#reportTableMd_length select").val());
        },
        ajax: '{!! route('display_share.reportDataSpg') !!}',
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
                data: 'hitTargetTB',
                name: 'hitTargetTB'
            },
            {
                data: 'achTB',
                name: 'achTB'
            },
            {
                data: 'hitTargetPF',
                name: 'hitTargetPF'
            },
            {
                data: 'achPF',
                name: 'achPF'
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
            "targets": [1, 2, 3, 4, 5, 6]
        }],
    });

    $('#reportTableMd').DataTable({
        processing: true,
        serverSide: true,
        drawCallback: function() {
            $("#btnDownloadXLSAll").attr("target-url", "{{ route('display_share.report.exportXLS') }}" + "?limitArea=&limitSPG=&limitMD=");
            $("#btnDownloadXLS").attr("target-url", "{{ route('display_share.report.exportXLS') }}" + "?limitArea=" + $("#reportTableArea_length select").val() + "&limitSPG=" + $("#reportTableSpg_length select").val() + "&limitMD=" + $("#reportTableMd_length select").val());
        },
        ajax: '{!! route('display_share.reportDataMd') !!}',
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
                data: 'hitTargetTB',
                name: 'hitTargetTB'
            },
            {
                data: 'achTB',
                name: 'achTB'
            },
            {
                data: 'hitTargetPF',
                name: 'hitTargetPF'
            },
            {
                data: 'achPF',
                name: 'achPF'
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
            "targets": [1, 2, 3, 4, 5, 6, 7]
        }],
    });

});
</script>
@endsection