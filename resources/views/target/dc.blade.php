@extends('layouts.app')
@section('title', "Product Target")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Product Target<small class="ml-2">Demo Cooking</small></h2>
  @if($errors->any())
  <div class="alert alert-danger">
    <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
    @foreach ($errors->all() as $error)
    <div>> {{ $error }}</div>
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
          <div class="block-option">
            <button class="btn btn-info btn-square"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="promoTable">
          <thead>
            <th class="text-center" style="width: 70px;"></th>
            <th>Employee Name</th>
            <th>Pasar</th>
            <th>Type</th>
            <th>Release Month</th>
            <th class="text-center" style="width: 15%;"> Action</th>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-edit"></i> Update Product Target</h3>
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
            <label>Employee</label>
            <select class="js-edit form-control" style="width: 100%" id="EmployeeInput" name="employee" >
              @foreach($employee as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Pasar</label>
              <select class= "js-edit form-control" id="PasarInput" style="width: 100%"  name="pasar" >
                @foreach($pasar as $data)
                <option value="{{ $data->id }}">{{ $data->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Release Date</label>
              <input class="js-datepicker form-control" type="date" id="rilisInput" name="rilis" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
            </div>
            <div class="form-group col-md-6">
              <label>Value</label>
              <input class="form-control" type="text" id="valueInput" name="value" required>
            </div>
            <div class="form-group col-md-6">
              <label>Value Focus</label>
              <input class="form-control" type="text" id="valuepfInput" name="valuepf" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-alt-success">
              <i class="fa fa-save"></i> Save
            </button>
            <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Product Fokus</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('target.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Employee</label>
            <select class="js-select2 form-control" style="width: 100%" name="employee" required>
              <option value="" disabled selected>Choose your employee</option>
              @foreach($employee as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Pasar</label>
              <select class="js-select2 form-control" style="width: 100%" name="pasar">
                <option disabled selected>Choose your Pasar</option>
                @foreach($pasar as $data)
                <option value="{{ $data->id }}">{{ $data->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Release</label>
              <input class="js-datepicker form-control" type="date" id="releaseInput" name="rilis" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
            </div>
            <div class="form-group col-md-6">
              <label>Value</label>
              <input class="form-control" type="text" id="valueInput" name="value" required>
            </div>
            <div class="form-group col-md-6">
              <label>Value Focus</label>
              <input class="form-control" type="text" id="valuepfInput" name="valuepf" required>
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
[data-notify="container"] 
{
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
  function editModal(json) {
    $('#editModal').modal('show');
    $('#editForm').attr('action', "{{ url('/product/target/update') }}/"+json.id);
    $('#EmployeeInput').val(json.employee).trigger('change');
    $('#PasarInput').val(json.pasar).trigger('change');
    $('#rilisInput').val(json.rilis);
    $('#valueInput').val(json.value);
    $('#valuepfInput').val(json.valuepf);
          // console.log(json);
        }
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
          $('#promoTable').DataTable({
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
            ajax: '{!! route('target.data') !!}',
            columns: [
            { data: 'id', name: 'id' },
            { data: 'employee.name', name: 'employee.name'},
            { data: 'pasar.name', name: 'pasar.name' },
            { data: 'type', name: 'type' },
            { data: 'rilis', name: 'rilis' },
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