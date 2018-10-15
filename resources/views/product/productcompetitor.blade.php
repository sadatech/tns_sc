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
            <button class="btn btn-info btn-square"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="product">
        <thead>
          <th style="width: 70px;"></th>
          <th>Brand</th>
          <th>Sub Category</th>
          <th>SKU</th>
          <th>Panel</th>
          <th style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-plus"></i> Add Product</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form action="{{ route('product-competitor.add') }}" method="post">
                {!! csrf_field() !!}
                <div class="block-content">
                    <div class="form-group">
                      <label>Brand Product</label>
                      <select class="js-select2 form-control" style="width: 100%" name="brand">
                      <option disabled selected>Choose your Brand</option>
                        @foreach($brand as $data)
                        @if($data->id != 1)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                        @endif
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Sub Category Product</label>
                      <select class="js-select2 form-control" style="width: 100%" name="subcategory">
                      <option disabled selected>Choose your Sub Category</option>
                        @foreach($subcategory as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label>SKU Product</label>
                      <input type="text" class="form-control" name="name" placeholder="Add new product" required>
                    </div>
                    <!-- <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="deskripsi"></textarea>
                    </div>
 -->                    <div class="form-group">
                      <label>Panel</label>
                      <select class="js-select2 form-control" style="width: 100%" name="panel">
                      <option disabled selected>Choose your Panel</option>
                            <option value="yes"> Yes </option>
                            <option value="no"> No </option>
                      </select>
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
                        <div class="form-group col-md-12">
                            <label>Brand Product</label>
                            <select class="js-edit form-control" id="brandinput" style="width: 100%" name="brand" >
                                @foreach($brand as $data)
                                @if($data->id != 1)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Sub Category Product</label>
                            <select class="js-edit form-control" id="subcategoryinput" style="width: 100%" name="subcategory" >
                                @foreach($subcategory as $data)
                                    <option value="{{ $data->id }}">{{ $data->name}}</option>
                                @endforeach
                            </select>
                        </div>
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
    function editModal(json) {
        $('#editModal').modal('show');
        $('#editForm').attr('action', "{{ url('/product/product-competitor/update') }}/"+json.id);
        $('#nameInput').val(json.name);
        $('#deskripsiInput').val(json.deskrispi);
        $('#brandinput').val(json.brand).trigger('change');
        $('#subcategoryinput').val(json.subcategory).trigger('change');
        $('#panelinput').val(json.panel).trigger('change');
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
        $('#product').DataTable({
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
            ajax: '{!! route('product-competitor.data') !!}',
            columns: [
            { data: 'id', name: 'id' },
            { data: 'brand', name: 'brand' },
            { data: 'subcategory', name: 'subcategory' },
            { data: 'name', name: 'name' },
            { data: 'panel', name: 'panel' },
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