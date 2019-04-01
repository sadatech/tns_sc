@extends('layouts.app')
@section('title', "Edit CBD VDO Pasar")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">CBD VDO Pasar<small> Edit</small></h2>
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
              <th>Pasar</th>
              <th>Date</th>
              <th>Approval</th>
              <th>Total Hanger</th>
              <th>Outlet Type</th>
              <th>CBD Position</th>
              <th>CBD Competitor</th>
              <th>POSM Shop Sign</th>
              <th>POSM Hangering Mobile</th>
              <th>POSM Poster</th>
              <th>POSM Other</th>
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
          <h3 class="block-title"><i class="fa fa-edit"></i> Update Sales VDO</h3>
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
              <label>VDO Name</label>
              <input type="text" class="form-control" name="name" id="nameInput" readonly required>
            </div>
            <div class="form-group col-md-12">
              <label>Outlet</label>
                <select class="form-control" style="width: 100%" name="outlate" id="outletInput" required>
                </select>
            </div>
            <div class="form-group col-md-12">
              <label>Total Hanger</label>
              <input type="text" class="form-control" name="total_hanger" id="total_hangerInput" required>
            </div>
            <div class="form-group col-md-12">
              <label>CBD Position</label>
              <select class="js-select2 form-control" style="width: 100%" id="cbd_positionInput" name="cbd_position" required>
                <option value="" disabled selected>Choose</option>
                <option value="ETC">ETC </option>
                <option value="Depan">Depan </option>
                <option value="Belakang">Belakang </option>
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>Outlet Type</label>
              <select class="js-select2 form-control" style="width: 100%" id="outlet_typeInput" name="outlet_type" required>
                <option value="" disabled selected>Choose</option>
                <option value="Permanen">Permanen </option>
                <option value="Tidak">Tidak </option>
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>Competitor</label>
              <input type="text" class="form-control" name="cbd_competitor" id="cbd_competitorInput">
            </div>
            <div class="form-group col-md-12">
              <label>POSM Shop Sign</label>
              <select class="js-select2 form-control" style="width: 100%" id="posm_shop_signInput" name="posm_shop_sign" required>
                <option value="" disabled selected>Choose</option>
                <option value="1">Yes </option>
                <option value="0">No </option>
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>POSM Hangering Mobile</label>
              <select class="js-select2 form-control" style="width: 100%" id="posm_hangering_mobileInput" name="posm_hangering_mobile" required>
                <option value="" disabled selected>Choose</option>
                <option value="1">Yes </option>
                <option value="0">No </option>
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>POSM Poster</label>
              <select class="js-select2 form-control" style="width: 100%" id="posm_posterInput" name="posm_poster" required>
                <option value="" disabled selected>Choose</option>
                <option value="1">Yes </option>
                <option value="0">No </option>
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>POSM Other</label>
              <input type="text" class="form-control" name="posm_others" id="posm_othersInput" required>
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
  function editModal(json) {
    $('#editModal').modal('show');
    $('#editForm').attr('action', "{{ url('/edit/gtc/smd/new-cbd/update') }}/"+json.id_cbd);
    $('#nameInput').val(json.employee);
    setSelect2IfPatch2($("#productInput"), json.id_product, json.product);
    setSelect2IfPatch2($("#outletInput"), json.id_outlet, json.outlet);
    $('#posm_shop_signInput').val(json.posm_shop_sign).trigger('change');
    $('#posm_hangering_mobileInput').val(json.posm_hangering_mobile).trigger('change');
    $('#posm_posterInput').val(json.posm_poster).trigger('change');
    $('#posm_othersInput').val(json.posm_others);
    $('#total_hangerInput').val(json.total_hanger);
    $('#cbd_competitorInput').val(json.cbd_competitor);
    $('#cbd_positionInput').val(json.cbd_position).trigger('change');
    $('#outlet_typeInput').val(json.outlet_type).trigger('change');
  }
  $('#outletInput').select2(setOptions('{{ route("outlet-select2") }}', 'Choose your Outlet', function (params) {
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
  

  $('#filter').submit(function(e) {
    Codebase.layout('header_loader_on');
    e.preventDefault();
    var table = null;
    var url = '{!! route('dataedit.gtc.smd.new-cbd') !!}';
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
      columns: [
      { data: 'id' },
      { data: 'employee' },
      { data: 'outlet' },
      { data: 'region' },
      { data: 'area' },
      { data: 'subarea' },
      { data: 'pasar' },
      { data: 'date' },
      { data: 'status' },
      { data: 'total_hanger' },
      { data: 'outlet_type' },
      { data: 'cbd_position' },
      { data: 'cbd_competitor' },
      { data: 'posm_shop_sign_display' },
      { data: 'posm_hangering_mobile_display' },
      { data: 'posm_poster_display' },
      { data: 'posm_others' },
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