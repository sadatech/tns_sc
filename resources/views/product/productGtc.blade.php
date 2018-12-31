@extends('layouts.app')
@section('title', "Product Fokus Product")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Product Fokus <small>Manage</small></h2>
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
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="promoTable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th>Product</th>
          <th>Area</th> 
          <th>Month From</th>
          <th>Month Until</th>
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
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import Data Product Fokus</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('fokusGTC.import') }}">
                {{ csrf_field() }}
                <div class="block-content">
                    <div class="form-group">
                      <a href="{{ route('fokusGTC.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                    </div>
                    <div class="block-content">
                        <h5> Sample Data :</h5>
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <td><b>SKU</b></td>
                                    <!-- <td><b>Channel</b></td> -->
                                    <td><b>Area</b></td>
                                    <td><b>From</b></td>
                                    <td><b>Until</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SKU 1</td>
                                    <!-- <td>Channel1, Channel2, Channel3</td> -->
                                    <td>Area1, Area2</td>
                                    <td>11-2018</td>
                                    <td>12-2018</td>
                                </tr>
                                <tr>
                                    <td>SKU 3, SKU 4</td>
                                    <!-- <td>Channel1, Channel3</td> -->
                                    <td></td>
                                    <td>1-2019</td>
                                    <td>2-2019</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Upload Your Data Product Fokus:</label>
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Update Product Fokus</h3>
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
                  <label>Product</label>
                  <select class="js-edit form-control" style="width: 100%" id="productInput" name="id_product" >
                    @foreach($product as $data)
                      <option value="{{ $data->id }}">{{ $data->name }} </option>
                    @endforeach
                  </select>
                </div>
                <div class="row">
                  <div class="form-group col-md-6">
                      <label>Month From</label>
                      <input class="form-control date1" type="text" id="fromInput" name="from" data-month-highlight="true" data-date-format="mm/yyyy" placeholder="dd/yyyy" required>
                  </div>
                  <div class="form-group col-md-6">
                      <label>Month Until</label>
                      <input class="form-control date1" type="text" id="toInput" name="to" data-month-highlight="true" data-date-format="mm/yyyy" placeholder="dd/yyyy">
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
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Product Fokus</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('fokusGTC.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Product</label>
            <select class="js-select2 form-control" style="width: 100%" name="product[]" multiple required>
              @foreach($product as $data)
                <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="custom-control custom-checkbox custom-control-inline">
                  <input class="custom-control-input" type="checkbox" id="example-inline-checkbox2" checked>
                  <label class="custom-control-label" for="example-inline-checkbox2">ALL</label>
              </div>
              <div id="test">
                <select id="coba" class="js-select2 form-control" style="width: 100%" name="area">
                  <option value="">Choose Area</option>
                    @foreach ($area as $dis)
                    <option value="{{ $dis->id }}">{{ $dis->name }}</option>
                    @endforeach
                </select>
              </div>
            </div>
          </div>
          <br/>
          <div class="row">
            <div class="col-md-6">
              <label>Month From</label>
              <input class="form-control date1" type="text" placeholder="Month From" data-date-format="mm/yyyy" name="from" data-month-highlight="true" required>
            </div>
            <div class="col-md-6">
              <label>Month Until</label>
              <input class="form-control date1" type="text" placeholder="Month Until" data-date-format="mm/yyyy" name="to" data-month-highlight="true" required>
            </div>
          </div>
        </div>
        <br/>
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
      $('#test').hide();
      $("#example-inline-checkbox2").change(function() {
          if ($(this).removeAttr("checked")) {
            $('#test').show();
          }
      });

      $("#example-inline-checkbox2").change(function() {
          if ($(this).prop("checked")) {
            $('#test').hide();
            $('#coba').val(null).trigger('change');
          }
      });
  
      $(".date1").datepicker( {
        format: "mm/yyyy",
        viewMode: "months",
        autoclose: true,
        minViewMode: "months"
      });

      function editModal(json) {
          $('#editModal').modal('show');
          $('#editForm').attr('action', "{{ url('/product/fokusGTC/update') }}/"+json.id);
          $('#productInput').val(json.product).trigger('change');
          $('#areaInput').val(json.area).trigger('change');
          $('#fromInput').val(json.from);
          $('#toInput').val(json.to);
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
              ajax: '{!! route('fokusGTC.data') !!}',
              columns: [
	              { data: 'id', name: 'id' },
                { data: 'product.name', name: 'product.name'},
	              { data: 'area', name: 'area' },
                { data: 'from', name: 'from' },
                { data: 'to', name: 'to' },
	              { data: 'action', name: 'action' }
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