@extends('layouts.app')
@section('title', "Region")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Region <small>Manage</small></h2>
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

        <table class="table table-striped table-vcenter js-dataTable-full" id="regiontable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th class="text-center">Name</th>
          <th class="text-center" style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Update Region</h3>
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
                        <label>Region Name</label>
                        <input type="text" class="form-control" name="name" id="nameInput" required>
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

<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Region</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('region.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Nama Region</label>
            <input type="text" class="form-control" name="name" placeholder="Add new region" required>
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
@endsection

@section('script')
  <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script type="text/javascript">
    function editModal(id,name) {
              $('#editModal').modal('show');
              $('#editForm').attr('action', "{{ url('/store/region/update') }}/"+id);
              $('#nameInput').val(name);
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
      $('#regiontable').DataTable({
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
        ajax: '{!! route('region.data') !!}',
        scrollY: "300px",
        columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'action', name: 'action' },
        ]
      });
    });
  </script>
@endsection