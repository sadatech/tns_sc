@extends('layouts.app')
@section('title', "Area")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Area <small>Manage</small></h2>
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
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="areatable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th class="text-center">Name</th>
          <th class="text-center">Region</th>
          <th class="text-center" style="width: 15%;"> Action</th>
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
          <h3 class="block-title"><i class="fa fa-edit"></i> Update Area</h3>
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
              <label>Area Name</label>
              <input type="text" class="form-control" name="name" id="nameInput" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12" style="padding: 0">
              <label class="col-md-12 col-sm-12" style="padding: 0">Region</label>
              <div class="input-group mb-3 col-sm-12 col-md-12" style="padding: 0">
                <div class="col-md-8 col-sm-12" style="padding: 0">
                  <select class="form-control" style="width: 100%" name="region" id="regionInput" required>
                  </select>
                </div>
                <div class="input-group-append col-md-4 col-sm-12" style="padding: 0">
                  <a href="{{ route('region') }}" target="_blank" class="btn btn-primary btn-square" style="width: 100%;"><i class="fa fa-plus mr-2"></i>Add Region</a>
                </div>
              </div>
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


<div class="modal fade" id="tambahModal" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Area</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('area.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Nama Area</label>
            <input type="text" class="form-control" name="name" placeholder="Add new area" required>
          </div>
          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Region</label>
            <div class="input-group mb-3 col-sm-12 col-md-12" style="padding: 0">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="region" id="regionSelect" required>
                </select>
              </div>
              <div class="input-group-append col-md-4 col-sm-12" style="padding: 0">
                <a href="{{ route('region') }}" target="_blank" class="btn btn-primary btn-square" style="width: 100%;"><i class="fa fa-plus mr-2"></i>Add Region</a>
              </div>
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
  <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
  <style type="text/css">
  [data-notify="container"] {
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
  }
  .padding0{
    padding: 0;
  }
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
  function editModal(json) {
    $('#editModal').modal('show');
    $('#editForm').attr('action', "{{ url('/store/area/update') }}/"+json.id);
    $('#nameInput').val(json.name);
    // $('#regionInput').val(json.region).trigger('change');
    setSelect2IfPatch2($("#regionInput"), json.region, json.region_name);
  }
  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('#regionInput').select2(setOptions('{{ route("region-select2") }}', 'Select Region', function (params) {
      return filterData('name', params.term);
    }, function (data, params) {
      return {
        results: $.map(data, function (obj) {                                
          return {id: obj.id, text: obj.name}
        })
      }
    }));
    $('#regionSelect').select2(setOptions('{{ route("region-select2") }}', 'Select Region', function (params) {
      return filterData('name', params.term);
    }, function (data, params) {
      return {
        results: $.map(data, function (obj) {                                
          return {id: obj.id, text: obj.name}
        })
      }
    }));
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
  $(function() {
    $('#areatable').DataTable({
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
      // scrollY: "300px",
      ajax: '{!! route('area.data') !!}',
      scrollY: "300px",
      columns: [
      { data: 'id', name: 'areas.id' },
      { data: 'name', name: 'areas.name' },
      { data: 'region.name', name: 'region.name' },
      { data: 'action', name: 'action' },
      ]
    });
  });
  $(".js-select2").select2({ 
    dropdownParent: $("#tambahModal")
  });
  $(".js-edit").select2({ 
    dropdownParent: $("#editModal")
  });
</script>
@endsection