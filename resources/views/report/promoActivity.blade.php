@extends('layouts.app')
@section('title', "Promo Activity")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Promo Activity <small>Manage</small></h2>
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
              <label>Product:</label>
              <select class="form-control" id="filterProduct" name="product"></select>
            </div>
            <div class="col-md-4">
              <label>Begin Periode:</label>
              <input class="js-datepicker form-control" type="text" placeholder="Select Begin Periode" name="begin_periode" data-month-highlight="true" value="{{ Carbon\Carbon::now()->format('m/Y') }}">
            </div>
            <div class="col-md-4">
              <label>End Periode:</label>
              <input class="js-datepicker form-control" type="text" placeholder="Select End Periode" name="end_periode" data-month-highlight="true" value="{{ Carbon\Carbon::now()->format('m/Y') }}">
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
           <a href="{{route('pa.exportXLS')}}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
         </div>
       </div>

       <table class="table table-striped table-vcenter js-dataTable-full dataTable" id="reportTable">
        <thead>
          <th class="text-center" style="width: 150px;">Action</th>
          <th width="200px">Store Name</th>
          <th width="200px">Employee Name</th>
          <th width="200px">Product Name</th>
          <th width="200px">Brand Name</th>
          <th width="200px">Type</th>
          <th width="200px">Description</th>
          <th width="200px">Start Promo</th>
          <th width="200px">End Promo</th>
          <th width="300px">Images</th>
        </thead>
      </table>
    </div> 
  </div> 
</div>


<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Import Data Account</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('pa.importXLS') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
       <!--  <div class="block-content">
          <div class="form-group">
              <a href="{{ route('account.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <h5> Sample Data :</h5>
          <table class="table table-bordered table-vcenter">
            <thead>
              <tr>
                  <td><b>account</b></td>
                  <td><b>channel</b></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                  <td>Name Account 1</td>
                  <td>Name Channel 1</td>
              </tr>
              <tr>
                  <td>Name Account 1</td>
                  <td>Name Channel 1</td>
              </tr>
            </tbody>
          </table>
        </div> -->
        <div class="block-content">
          <div class="form-group">
            <label>Upload Your Data Account:</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
              <label class="custom-file-label">Choose file Excel</label>
              <code> *Type File Excel</code>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-alt-success">
            <i class="fa fa-save"></i> Save
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/magnific-popup/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<style type="text/css">
[data-notify="container"] {
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
.pac-container {
  z-index: 99999;
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
  $('#reset').click(function(){
    setTimeout(function() {
      $('.js-datepicker').val(null);
      $('#filterEmployee,#filterStore,#filterArea,#filterProduct').val(null).trigger('change');
    }, 10);
  });
  $('#filterProduct').select2(setOptions('{{ route("product-select2") }}', 'Choose your Product', function (params) {
    return filterData('name', params.term);
  }, function (data, params) {
    return {
      results: $.map(data, function (obj) {                                
        return {id: obj.id, text: obj.name}
      })
    }
  }));
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
    var url = '{!! route('pa.data') !!}';
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
      { data: 'action', name: 'action' },
      { data: 'store', name: 'store' },
      { data: 'employee', name: 'employee' },
      { data: 'product', name: 'product' },
      { data: 'brand', name: 'brand' },
      { data: 'type', name: 'type' },
      { data: 'description', name: 'description' },
      { data: 'start_date', name: 'start_date' },
      { data: 'end_date', name: 'end_date' },
      { data: 'images', name: 'images' },
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
