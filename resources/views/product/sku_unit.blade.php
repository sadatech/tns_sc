@extends('layouts.app')
@section('title', "SKU Unit Product")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">SKU Unit <small>Manage</small></h2>
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
          <div class="block-option">
            <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
            <a href="{{ route('sku-unit.export') }}" class="btn btn-success btn-square float-right ml-10" title="Unduh Data"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="sku-unit">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th>Sku Unit</th>
          <th>Conversion Value</th>
          <th class="text-center" style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import <i>SKU Unit</i></h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('sku-unit.import') }}">
        {{ csrf_field() }}
        <div class="block-content">
          <div class="form-group">
            <a href="{{ route('sku-unit.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <div class="block-content">
            <h5> Sample Data :</h5>
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <td><b>SKU Name</b></td>
                        <td><b>Conversion Value </b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SKU Name 1</td>
                        <td>Conversion Value 1</td>
                    </tr>
                    <tr>
                        <td>SKU Name 2</td>
                        <td>Conversion Value 2</td>
                    </tr>
                </tbody>
            </table>
          </div>
          <div class="form-group col-md-12">
            <label>Upload Your Data Sku Unit:</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
              <label class="custom-file-label">Choose file Excel</label>
              <code> *Type File Excel</code>
            </div>
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

{{-- SKU UNIT ADD FORM MODAL--}}
@include('product._form_sku_unit', ['action' => route('sku-unit.add'), 'id' => 'tambahModal'])

{{-- SKU UNIT EDIT FORM MODAL --}}
@include('product._form_sku_unit', ['id' => 'editModal', 'type' => 'edit'])

@endsection

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
  <style type="text/css">
  [data-notify="container"] {
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
  }
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
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
    $('#sku-unit').DataTable({
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
      scrollY: "300px",
      ajax: '{!! route('sku-unit.data') !!}',
      columns: [
      { data: 'id', name: 'id' },
      { data: 'name', name: 'name' },
      { data: 'size', name: 'size' },
      { data: 'action', name: 'action' },
      ]
    });
  });
</script>
@endsection