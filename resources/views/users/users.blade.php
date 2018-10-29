@extends('layouts.app')
@section('title', "User")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">User <small>Manage</small></h2>
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
        <table class="table table-striped table-vcenter js-dataTable-full" id="userTable">
        <thead>
          <th class="text-center">Name</th>
          <th class="text-center">Email</th>
          <th class="text-center">Role</th>
          <th class="text-center" style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="tambahModal" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Users</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('user.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Nama Users</label>
            <input type="text" class="form-control" name="name" placeholder="Add new User" required>
          </div>
          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label>Email</label>
            <input type="email" value="{{ old('email') }}" id="email"  class="form-control" name="email" placeholder="Email" required>  
              @if ($errors->has('email'))  
                <span class="help-block">
                  <strong>{{ $errors->first('email') }}</strong>
                </span>
              @endif
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" id="password" class="form-control" name="password" placeholder="Password" required>
          </div>
           <div class="form-group row">
            <div class="col-12">
                <label for="signup-password-confirm">Password Confirmation</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>
            </div>
          <div class="col-md-12 col-sm-12" style="padding: 0">
              <label class="col-md-12 col-sm-12" style="padding: 0">Role</label>
              <div class="input-group mb-3 col-sm-12 col-md-12" style="padding: 0">
                <div class="col-md-12 col-sm-12" style="padding: 0">
                  <select class="form-control" style="width: 100%" value="{{ old('role') }}" name="role" id="userInput" required>
                    @foreach($role as $row)
                    <option value="{{$row->id}}" >{{$row->level}}</option>
                    @endforeach
                  </select>
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

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="editModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-edit"></i> Update User</h3>
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
            <label>Nama Users</label>
            <input type="text" class="form-control" name="name" id="nameInput" placeholder="Add new User" required>
          </div>
          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label>Email</label>
            <input type="email" value="{{ old('email') }}" id="emailInput"  class="form-control" name="email" placeholder="Email" required>  
              @if ($errors->has('email'))  
                <span class="help-block">
                  <strong>{{ $errors->first('email') }}</strong>
                </span>
              @endif
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password"  class="form-control" name="password" placeholder="Password" >
          </div>
           <div class="form-group row">
            <div class="col-12">
                <label for="signup-password-confirm">Password Confirmation</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" >
            </div>
            </div>
          <div class="col-md-12 col-sm-12" style="padding: 0">
              <label class="col-md-12 col-sm-12" style="padding: 0">Role</label>
              <div class="input-group mb-3 col-sm-12 col-md-12" style="padding: 0">
                <div class="col-md-12 col-sm-12" style="padding: 0">
                  <select class="form-control" style="width: 100%" value="{{ old('role') }}" name="role" id="userInputs" required>
                    @foreach($role as $row)
                    <option value="{{$row->id}}" >{{$row->level}}</option>
                    @endforeach
                  </select>
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
    $('#editForm').attr('action', "{{ url('/user/update') }}/"+json.id);
    $('#nameInput').val(json.name);
    $('#emailInput').val(json.email);
    $('#userInputs').val(json.role_id);
  
    setSelect2IfPatch2($("#test"), json.user, json.level);
  }
  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
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
  $(function() {
    $('#userTable').DataTable({
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
      ajax: '{!! route('user.data') !!}',
      scrollY: "300px",
      columns: [
      { data: 'name', name: 'name' },
      { data: 'email', name: 'email' },
      { data: 'level', name: 'level' },
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

@section('script')
    <script src="{{ asset('assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/op_auth_signup.js') }}"></script>
@endsection