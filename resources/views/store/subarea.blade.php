@extends('layouts.app')
@section('title', "Sub Area")
@section('content')
<div class="content">
  @if($errors->any())
    <div class="alert alert-danger">
        <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
        @foreach ($errors->all() as $error)
        <div> {{ $error }}</div>
        @endforeach
    </div>
  @endif
  <h2 class="content-heading pt-10">Sub Area <small>Manage</small></h2>
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus mr-2"></i>Add Data</button>
            <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
          </h3>
          <div class="block-option">
            <a href="{{ route('subarea.export') }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="subtable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th>SubArea</th>
          <th>Area</th>
          <th>Region</th>          
          <th style="width: 15%;"> Action</th>
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
          <h3 class="block-title"><i class="fa fa-edit"></i> Update Sub Area</h3>
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
          <div class="form-group">
            <label>Sub Area Name</label>
            <input type="text" class="form-control" name="name" id="nameInput" required>
          </div>
          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Area</label>
            <div class="input-group mb-3 col-sm-12 col-md-12" style="padding: 0">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="area" id="areaInput" required>
                </select>
              </div>
              <div class="input-group-append col-md-4 col-sm-12" style="padding: 0">
                <a href="{{ route('area') }}" target="_blank" class="btn btn-primary btn-square" style="width: 100%;"><i class="fa fa-plus mr-2"></i>Add Area</a>
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
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Sub Area</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('subarea.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Name Sub Area</label>
            <input type="text" class="form-control" name="name" placeholder="Add new Sub Area" required>
          </div>
          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Area</label>
            <div class="input-group mb-3 col-sm-12 col-md-12" style="padding: 0">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="area" id="areaSelect" required>
                </select>
              </div>
              <div class="input-group-append col-md-4 col-sm-12" style="padding: 0">
                <a href="{{ route('area') }}" target="_blank" class="btn btn-primary btn-square" style="width: 100%;"><i class="fa fa-plus mr-2"></i>Add Area</a>
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


<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import <i>Sub Area - Area - Region</i></h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="import-form" method="post" enctype="multipart/form-data" action="{{ url('store/subarea/import') }}">
        {{ csrf_field() }}
        <div class="block-content">
          <div class="form-group">
            <a href="{{ route('subarea.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <div class="block-content">
            <h5> Sample Data :</h5>
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <td><b>Sub Area</b></td>
                        <td><b>Area</b></td>
                        <td><b>Region</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sub Area Name 1</td>
                        <td>Area Name 1</td>
                        <td>Region Name 1</td>
                    </tr>
                    <tr>
                        <td>Sub Area Name 2</td>
                        <td>Area Name 2</td>
                        <td>Region Name 2</td>
                    </tr>
                </tbody>
            </table>
          </div>
          <div class="form-group">
            <label>Select File</label>
            <input type="file" name="file" class="form-control">
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
    $('#editForm').attr('action', "{{ url('/store/subarea/update') }}/"+json.id);
    $('#nameInput').val(json.name);
    // $('#areaInput').val(json.area).trigger('change');
    setSelect2IfPatch2($("#areaInput"), json.area, json.area_name);
  }
  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $('#regionSelect').select2(setOptions('{{ route("region-select2") }}', 'Select Region', function (params) {
      return filterData('name', params.term);
    }, function (data, params) {
      return {
        results: $.map(data, function (obj) {                                
          return {id: obj.id, text: obj.name}
        })
      }
    }));
    $('#areaSelect').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
      return filterData('name', params.term);
    }, function (data, params) {
      return {
        results: $.map(data, function (obj) {                                
          return {id: obj.id, text: obj.name}
        })
      }
    }));
    $('#areaInput').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
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
    $('#subtable').DataTable({
      processing: true,
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
      ajax: '{!! route('subarea.data') !!}',
      scrollY: "300px",
      columns: [
      { data: 'id', name: 'sub_areas.id' },
      { data: 'name', name: 'sub_areas.name' },
      { data: 'area.name', name: 'area.name' },
      { data: 'area.region.name', name: 'area.regions.name', "searchable": true },
      { data: 'action', name: 'action' },
      ]
    });
  });
  $(".js-select2").select2({ 
      dropdownParent: $("#tambahModal")
  });
  $(".js-modal").select2({ 
      dropdownParent: $("#editModal")
  });
</script>
@endsection