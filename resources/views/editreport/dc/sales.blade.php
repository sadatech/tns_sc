@extends('layouts.app')
@section('title', "Report Sales Demo Cooking")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Sales Demo Cooking <small>Report</small></h2>
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
              <input class="js-datepicker form-control" value="{{ Carbon\Carbon::now()->format('m/Y') }}" type="text" placeholder="Select Periode" name="periode" data-month-highlight="true" required>
            </div>
            <div class="col-md-4">
              <label>Area:</label>
              <select class="form-control" id="filterArea" name="area"></select>
            </div>
            <div class="col-md-4">
              <label>Employee:</label>
              <select class="form-control" id="filterEmployee" name="employee"></select>
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
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered table-responsive" id="category">
          <thead>
            <tr>
              <th class="text-center" style="width: 70px;"></th>
              <th>Nama DC</th>
              <th>Place</th>
              <th>Tanggal</th>
              <th>Icip Icip</th>
              <th>Effective Contact</th>
              <th>Product</th>
              <th>Quantity</th>
              <th>Satuan</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="editModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-edit"></i> Update Sales DC</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="editForm" method="post">
        {!! method_field('PUT') !!}
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="row">
            <div class="form-group col-md-12">
              <label>DC Name</label>
              <input type="text" class="form-control" name="name" id="nameInput" readonly required>
            </div>
            <div class="form-group col-md-12">
              <label>Product</label>
                <select class="form-control" style="width: 100%" name="product" id="productInput" required>
                </select>
            </div>
            <div class="form-group col-md-12">
              <label>Quantity</label>
              <input type="text" class="form-control" name="qty_actual" id="qtyInput" required>
            </div>
            <div class="form-group col-md-12">
              <label>Satuan</label>
              <input type="text" class="form-control" name="satuan" id="satuanInput" readonly required>
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
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
  function editModal(json) {
    $('#editModal').modal('show');
    $('#editForm').attr('action', "{{ url('/edit/gtc/dc/sales/update') }}/"+json.id_detail);
    $('#nameInput').val(json.nama);
    setSelect2IfPatch2($("#productInput"), json.id_product, json.product);
    $('#qtyInput').val(json.qty_actual);
    $('#satuanInput').val(json.satuan).trigger('change');
  }
  $('#productInput').select2(setOptions('{{ route("product-select2") }}', 'Choose your Area', function (params) {
    return filterData('name', params.term);
  }, function (data, params) {
    return {
      results: $.map(data, function (obj) {                                
        return {id: obj.id, text: obj.name}
      })
    }
  }));

  $(".js-select2").select2({ 
  });

 $('#reset').click(function(){
    $('.js-datepicker').val(null);
    setTimeout(function() {
      $('#filterEmployee,#filterArea').val(null).trigger('change');
    }, 10);
  });
  $('#filterArea').select2(setOptions('{{ route("area-select2") }}', 'Choose your Area', function (params) {
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


  $('#filter').submit(function(e) {
    Codebase.layout('header_loader_on');
    e.preventDefault();
    var table = null;
    var url = '{!! route('dataedit.gtc.dc.sales') !!}';
    table = $('#category').DataTable({
      processing: true,
      serverSide: true,
      drawCallback: function(){
          $('.js-swal-delete').on('click', function(){
              var url = $(this).data("url");
              swal({
                  title: 'Are you sure?',
                  text: 'You will not be able to recover this data!',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d26a5c',
                  confirmButtonText: 'Yes, delete it!',
                  html: false,
                  preConfirm: function() {
                      return new Promise(function (resolve) {
                          setTimeout(function () {
                              resolve();
                          }, 50);
                      });
                  }
              }).then(function(result){
                  if (result.value) {
                      window.location = url;
                  } else if (result.dismiss === 'cancel') {
                      swal('Cancelled', 'Your data is safe :)', 'error');
                  }
              });
          });
      },
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
      columns: [
      { data: 'id' },
      { data: 'nama' },
      { data: 'place' },
      { data: 'tanggal' },
      { data: 'icip_icip' },
      { data: 'effective_contact' },
      { data: 'product' },
      { data: 'qty_actual' },
      { data: 'satuan' },
      { data: 'action' },
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