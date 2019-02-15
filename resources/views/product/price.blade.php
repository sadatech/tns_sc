@extends('layouts.app')
@section('title', "Price Product")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Price Product <small>Manage</small></h2>
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
            <a href="{{ route('price.export') }}" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="price">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th class="text-center">Product</th>
          <th class="text-center">Category</th>
          <th class="text-center">Price</th>
          <th class="text-center">Date Rilis</th>
          <th class="text-center" style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import Data Price Product</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('price.import') }}">
                {{ csrf_field() }}
                <div class="block-content">
                    <div class="form-group">
                      <a href="{{ route('price.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                    </div>
                    <div class="block-content">
                        <h5> Sample Data :</h5>
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <td><b>Product</b></td>
                                    <td><b>Price</b></td>
                                    <td><b>Rilis</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Product 1 (sesuai dengan data product)</td>
                                    <td>10000</td>
                                    <td>2018/12/5</td>
                                </tr>
                                <tr>
                                    <td>Product 3 (sesuai dengan data product)</td>
                                    <td>20000</td>
                                    <td>2018/12/20</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Upload Your Data Product Price:</label>
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

{{-- MODAL ADD PRICE --}}
@include('product._form_price', ['id' => 'tambahModal', 'action' => route('price.add')])

{{-- MODAL UPDATE PRICE --}}
@include('product._form_price', ['id' => 'editModal', 'type' => 'edit'])

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
  <style type="text/css">
  [data-notify="container"] {
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
  }
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
$('#currency').on('change', function() {
    window.location.href = $(this).val();
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
    $('#price').DataTable({
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
      ajax: '{!! route('price.data') !!}',
      columns: [
      { data: 'id', name: 'id' },
      { data: 'product', name: 'product' },
      { data: 'category', name: 'category' },
      { data: 'price', name: 'price' },
      { data: 'rilis', name: 'rilis' },
      { data: 'action', name: 'action' },
      ]
    });
  });
</script>
@endsection