@extends('layouts.app')
@section('title', "Data Product")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Product <small>Manage</small></h2>
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
            <a href="{{ route('product.export') }}" class="btn btn-success btn-square float-right ml-10" title="Unduh Data"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="product">
        <thead>
          <th style="width: 70px;"></th>
          <th>Brand</th>
          <th>Sub Category</th>
          <th>Code</th>
          <th>SKU</th>
          <th>Stock Type</th>
          <th>Panel</th>
          <th style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ADD PRODUCT MODAL --}}
@include('product._form_product', ['action' => route('product.add'), 'id' => 'tambahModal'])

@include('product._form_product', ['id' => 'editModal', 'type' => 'edit'])
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Update Product</h3>
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
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>SKU Name</label>
                            <input type="text" class="form-control" name="name" id="nameInput" required>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="deskripsiInput"></textarea>
                    </div> -->
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Panel</label>
                            <select class="js-edit form-control" id="panelinput" style="width: 100%" name="panel" >
                                <option value="yes"> Yes </option>
                                <option value="no"> No </option>
                            </select>
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
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import <i>Data Product</i></h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('product.import') }}">
        {{ csrf_field() }}
        <div class="block-content">
          <div class="form-group">
            <a href="{{ route('product.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <div class="block-content">
            <h5> Sample Data :</h5>
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <td><b>SubCategory</b></td>
                        <td><b>Category</b></td>
                        <td><b>Code</b></td>
                        <td><b>SKU</b></td>
                        <td><b>Type</b></td>
                        <td><b>Panel</b></td>
                        <td><b>SkuUnit</b></td>
                        <td><b>Value</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SubCategory 1</td>
                        <td>Category 1</td>
                        <td>Code 1</td>
                        <td>SKU 1</td>
                        <td>Type 1</td>
                        <td>Panel 1</td>
                        <td>SkuUnit 1</td>
                        <td>Value 1</td>
                    </tr>
                    <tr>
                        <td>SubCategory 2</td>
                        <td>Category 2</td>
                        <td>Code 2</td>
                        <td>SKU 2</td>
                        <td>Type 2</td>
                        <td>Panel 2</td>
                        <td>SkuUnit 2</td>
                        <td>Value 2</td>
                    </tr>
                </tbody>
            </table>
          </div>
          <div class="form-group col-md-12">
            <label>Upload Your Data Product:</label>
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
        $('#product').DataTable({
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
            ajax: '{!! route('product.data') !!}',
            columns: [
            { data: 'id', name: 'id' },
            { data: 'brand', name: 'brand' },
            { data: 'subcategory.name', name: 'subcategory.name' },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'stockType', name: 'stockType' },
            { data: 'panel', name: 'panel' },
            { data: 'action', name: 'action' },
            ]
        });
    });
</script>
@endsection