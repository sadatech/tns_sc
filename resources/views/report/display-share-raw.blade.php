@extends('layouts.app')
@section('title', "Sales Report - Availability")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Display Share <small>Report</small></h2>
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
      <h3 class="block-title">Filter</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <form method="post" id="filter">
          <div class="row">
            <div class="col-md-4">
              <label>Employee:</label>
              <select class="form-control" id="filterEmployee" name="employee"></select>
            </div>
            <div class="col-md-4">
              <label>Store:</label>
              <select class="form-control" id="filterStore" name="store"></select>
            </div>
            <div class="col-md-4">
              <label>Area:</label>
              <select class="form-control" id="filterArea" name="area"></select>
            </div>
            <div class="col-md-4">
              <label>Periode:</label>
              <input class="js-datepicker form-control" type="text" placeholder="Select Periode" name="periode" data-month-highlight="true" value="{{ Carbon\Carbon::now()->format('m/Y') }}" required>
            </div>
          </div>
          <button type="submit" class="btn btn-outline-danger btn-square mt-10">Filter Data</button>
          <input type="reset" id="reset" class="btn btn-outline-secondary btn-square mt-10" value="Reset Filter"/>
        </form>
      </div>
    </div>
  </div>
  <div class="block block-themed" id="table-block" style="display: none">
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
      <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <div class="block-option">
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</button>
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</button>
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTable">
          <thead>
            <tr>
              <th rowspan="3" style="vertical-align: middle; text-align: center;">REGION</th>
              <th rowspan="3" style="vertical-align: middle; text-align: center;">AREA</th>
              <th rowspan="3" style="vertical-align: middle; text-align: center;">TL</th>
              <th rowspan="3" style="vertical-align: middle; text-align: center;">NAMA SPG</th>
              <th rowspan="3" style="vertical-align: middle; text-align: center;">JABATAN</th>
              <th rowspan="3" style="vertical-align: middle; text-align: center;">STORE</th>
              <th rowspan="3" style="vertical-align: middle; text-align: center;">ACCOUNT</th>
              @foreach ($categories as $category)
              <th colspan="{{ ($jml_brand + 1)*2 }}" style="vertical-align: middle; text-align: center;">{{ $category->name }}</th>
              @endforeach
            </tr>
            <tr>
              @foreach ($categories as $category)
              @foreach ($brands as $brand)
              <th colspan="2" style="vertical-align: middle; text-align: center;">{{ $brand->name }}</th>
              @endforeach
              <th colspan="2" style="vertical-align: middle; text-align: center;">Total</th>
              @endforeach
            </tr>
            <tr>
              @foreach ($categories as $category)
              @foreach ($brands as $brand)
              <th style="vertical-align: middle; text-align: center;">tier</th>
              <th style="vertical-align: middle; text-align: center;">depth</th>
              @endforeach
              <th style="vertical-align: middle; text-align: center;">tier</th>
              <th style="vertical-align: middle; text-align: center;">depth</th>
              @endforeach
            </tr>

          </thead>
        </table>

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
    $('.js-datepicker').val(null);
    setTimeout(function() {
      $('#filterEmployee,#filterStore,#filterArea').val(null).trigger('change');
    }, 10);
  });
  $('#filterEmployee').select2(setOptions('{{ route("employee-select2") }}', 'Choose your Employee', function (params) {
    return filterData('name', params.term);
  }, function (data, params) {
    return {
      results: $.map(data, function (obj) {                                
        return {id: obj.id, text: obj.name}
      })
    }
  }));
  $('#filterArea').select2(setOptions('{{ route("area-select2") }}', 'Choose your Area', function (params) {
    return filterData('name', params.term);
  }, function (data, params) {
    return {
      results: $.map(data, function (obj) {                                
        return {id: obj.id, text: obj.name}
      })
    }
  }));
  $('#filterStore').select2(setOptions('{{ route("store-select2") }}', 'Choose your Store', function (params) {
    return filterData('store', params.term);
  }, function (data, params) {
    return {
      results: $.map(data, function (obj) {                                
        return {id: obj.id, text: obj.name1}
      })
    }
  }));
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

   $('#filter').submit(function(e) {
    Codebase.layout('header_loader_on');
    e.preventDefault();
    var table = null;
    var url = '{!! route('display_share.dataSpg') !!}';
    table = $('#reportTable').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "300px",
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
        // $("#btnDownloadXLS").attr("target-url","{{ route('export.smd.new-cbd') }}"+"/"+$(".js-datepicker").val()+"/"+$("#filterEmployee").val()+"/"+$("#filterStore").val()+"/new");
      },
      columns: [
      { data: 'region_name', name: 'region_name'},
      { data: 'area_name', name: 'area_name'},
      { data: 'tl_name', name: 'tl_name'},
      { data: 'emp_name', name: 'emp_name'},
      { data: 'jabatan', name: 'jabatan'},
      { data: 'store_name', name: 'store_name'},
      { data: 'account_name', name: 'account_name'},
      @foreach($categories as $category)
      @foreach($brands as $brand)
      {data: '{{ $category->id }}_{{ $brand->id }}_tier', name: '{{ $category->id }}_{{ $brand->id }}_tier', searchable: false, sortable: false},
      {data: '{{ $category->id }}_{{ $brand->id }}_depth', name: '{{ $category->id }}_{{ $brand->id }}_depth', searchable: false, sortable: false},
      @endforeach
      {data: '{{ $category->id }}_total_tier', name: '{{ $category->id }}_total_tier', searchable: false, sortable: false},
      {data: '{{ $category->id }}_total_depth', name: '{{ $category->id }}_total_depth', searchable: false, sortable: false},
      @endforeach
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
 @endsection
