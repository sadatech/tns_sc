@extends('layouts.app')
@section('title', "Report CBD SMD Pasar")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">CBD SMD Pasar<small>Report</small></h2>
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
              <label>Periode:</label>
              <input class="js-datepicker form-control" type="text" placeholder="Select Periode" name="periode" data-month-highlight="true" value="{{ Carbon\Carbon::now()->format('m/Y') }}">
            </div>
            <div class="col-md-4">
                <label>Date:</label>
                <input type="text" id="filterDate" class="form-control" placeholder="Date" name="date" autocomplete="off">
            </div>
            <div class="col-md-4">
              <label>Employee:</label>
              <select class="form-control" id="filterEmployee" name="employee"></select>
            </div>
            <div class="col-md-4">
              <label>Area:</label>
              <select class="form-control" id="filterArea" name="area"></select>
            </div>
            <div class="col-md-4">
              <label>Outlet:</label>
              <select class="form-control" id="filterOutlet" name="outlet"></select>
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
          <h3 class="block-title">
          </h3>
          <div class="block-option">
            <a id="btnDownloadXLS" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>

          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered table-responsive" id="category">
          <thead>
            <tr>
              <th class="text-center" style="width: 70px;"></th>
              <th>Employee</th>
              <th>Outlet</th>
              <th>Region</th>
              <th>Area</th>
              <th>Sub Area</th>
              <th>Date</th>
              <th>Photo Before</th>
              <th>Photo After</th>
              <th>Total Hanger</th>
              <th>Outlet Type</th>
              <th>CBD Position</th>
              <th>CBD Competitor</th>
              <th>POSM Shop Sign</th>
              <th>POSM Hangering Mobile</th>
              <th>POSM Poster</th>
              <th>POSM Other</th>
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
[data-notify="container"] {
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
table.table thead tr th:first-child {
  min-width: 5px;
}
table.table thead tr th {
  min-width: 200px;
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {

      $('.js-datepicker').change(function(){
          console.log(filters);
          $('#filterDate').val('');
      });

      $('#filterDate').change(function(){
          console.log(filters);
          $('.js-datepicker').val('');
      });

  });
  $('#reset').click(function(){
    $('.js-datepicker').val(null);
    $('#filterEmployee,#filterOutlet,#filterArea,#filterDate').val(null).trigger('change');
    setTimeout(function() {
      $('#filterEmployee,#filterOutlet,#filterArea,#filterDate').val(null).trigger('change');
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
  $('#filterOutlet').select2(setOptions('{{ route("outlet-select2") }}', 'Choose your Outlet', function (params) {
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
  $('#filterDate').datepicker({
      format: "mm/yyyy/dd",
      startView: "0",
      minView: "0",
      autoclose: true,
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
    var url = '{!! route('data.smd.new-cbd') !!}';
    table = $('#category').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "300px",
      ajax: {
        url: url + "?" + $("#filter").serialize(),
        type: 'POST',
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
        if ($("#filterDate").val()=='') {
          $("#btnDownloadXLS").attr("target-url","{{ route('export.smd.new-cbd') }}"+"/"+$(".js-datepicker").val()+"/null/"+$("#filterEmployee").val()+"/"+$("#filterOutlet").val()+"/"+$("#filterArea").val()+"/new");
        }else{
          $("#btnDownloadXLS").attr("target-url","{{ route('export.smd.new-cbd') }}"+"/"+$("#filterDate").val()+"/"+$("#filterEmployee").val()+"/"+$("#filterOutlet").val()+"/"+$("#filterArea").val()+"/new");
        };
      },
      columns: [
      { data: 'id' },
      { data: 'employee' },
      { data: 'outlet' },
      { data: 'region' },
      { data: 'area' },
      { data: 'subarea' },
      { data: 'date' },
      { data: 'photo' },
      { data: 'photo2' },
      { data: 'total_hanger' },
      { data: 'outlet_type' },
      { data: 'cbd_position' },
      { data: 'cbd_competitor' },
      { data: 'posm_shop_sign' },
      { data: 'posm_hangering_mobile' },
      { data: 'posm_poster' },
      { data: 'posm_others' },
      ],
      bDestroy: true
    });
  });
</script>
@endsection