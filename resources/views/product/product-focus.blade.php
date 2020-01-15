@extends('layouts.app')
@section('title', "Product Focus Product")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Product Focus <small>Manage</small></h2>
  @if($errors->any())
    <div class="alert alert-danger">
      <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
      @foreach ($errors->all() as $error)
      <div> {{ $error }}</div>
      @endforeach
    </div>
  @endif
  <div class="block block-themed"> 
    <div class="block-header bg-primary pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#formModal" onclick="addModal()"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
          <div class="block-option">
            <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>
              Import Data
            </button>
            <a id="upload-status" class="btn btn-outline-info btn-square ml-10" data-toggle="modal" href="#trace-table">
              <i class="fa fa-check-square"></i> 
              View Job Status 
            </a>
            <a href="{{ route('focus.download') }}" class="btn btn-outline-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>In-direct Download</a>
            <a href="{{ route('focus.export') }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Direct Download</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="focusTable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th>Product</th>
          <th>Area</th> 
          <th>Start Month</th>
          <th>End Month</th>
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
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import Data Product Focus</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('focus.upload') }}">
                {{ csrf_field() }}
                <div class="block-content">
                    <div class="form-group">
                      <a href="{{ route('focus.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                    </div>
                    <div class="block-content">
                        <h5> Sample Data :</h5>
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <td><b>Product Name</b></td>
                                    <td><b>Area</b></td>
                                    <td><b>Start Month</b></td>
                                    <td><b>End Month</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SKU 1</td>
                                    <td>Area1, Area2</td>
                                    <td>11/2018</td>
                                    <td>12/2018</td>
                                </tr>
                                <tr>
                                    <td>SKU 3, SKU 4</td>
                                    <td></td>
                                    <td>1/2019</td>
                                    <td>2/2019</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Upload Your Data Product Focus:</label>
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

<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Product Focus</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('focus.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Product</label>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="update" id="update">
            <select class="form-control" style="width: 100%" id="productSelect" name="product[]" multiple required>
            </select>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="custom-control custom-checkbox custom-control-inline">
                <input class="custom-control-input" type="checkbox" id="area-checkbox" checked>
                <label class="custom-control-label" for="area-checkbox" style="cursor: pointer;">ALL Area</label>
              </div>
              <div id="areaDiv" style="margin-top: 5px;">
                <select id="areaSelect" class="form-control" style="width: 100%" name="area[]" multiple>
                </select>
              </div>
            </div>
          </div>
          <br/>
          <div class="row">
            <div class="col-md-6">
              <label>Start Month</label>
              <input class="form-control month-picker" type="text" placeholder="Start Month" data-date-format="mm/yyyy" id="startMonth" name="from" data-month-highlight="true" required>
            </div>
            <div class="col-md-6">
              <label>End Month</label>
              <input class="form-control month-picker" type="text" placeholder="End Month" data-date-format="mm/yyyy" id="endMonth" name="to" data-month-highlight="true" required>
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

@include('utilities.upload_trace_modal', ['trace' => 'product_focus', 'model' => 'App\ProductFocus'])
@include('utilities.explanation_modal')

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <style type="text/css">
    [data-notify="container"] 
    {
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    .pac-container {
        z-index: 99999;
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
    $(document).ready(function() {

      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      @if(session('type'))
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
      @endif

      $('#productSelect').select2(setOptions('{{ route("product-select2") }}', 'Select Product', function (params) {
        return filterData('name', params.term);
      }, function (data, params) {
        return {
          results: $.map(data, function (obj) {                                
            return {id: obj.id, text: obj.code + " | " + obj.name}
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

    });

      $('#areaDiv').hide();
      $(".month-picker").datepicker( {
        format: "mm/yyyy",
        viewMode: "months",
        autoclose: true,
        minViewMode: "months"
      });

      $("#area-checkbox").change(function() {
          if ($(this).removeAttr("checked")) {
            $('#areaDiv').show();
          }
      });

      $("#area-checkbox").change(function() {
          if ($(this).prop("checked")) {
            $('#areaDiv').hide();
            $('#coba').val(null).trigger('change');
          }
      });


      function editModal(json) {
        var area = 0;
          $('#id').val(json.id);
          $('#update').val(1);
          clearSelect();
          setSelect2IfPatch2($("#productSelect"), json.product.id, json.product.name);
          $.each(json.area, function(key, val){
            area++;
            setSelect2IfPatch2($("#areaSelect"), val.id_area, val.name);
          });
          if (area>0) {
            toggleOffSwitch();
          }
          $('#startMonth').val(json.from);
          $('#endMonth').val(json.to);
      }

      function addModal() {
        clearSelect();
        toggleOnSwitch();
        $('#update').val('');
        $('#startMonth').val('');
        $('#endMonth').val('');
      }

      function clearSelect() {
        select2Reset($("#productSelect"));
        select2Reset($("#areaSelect"));
      }

      function clearDate() {
        // body...
      }

      function toggleOffSwitch() {
        var checked = $('#area-checkbox:checkbox:checked').length > 0;
        if (checked) {
          $('#area-checkbox').click();
        }
      }

      function toggleOnSwitch() {
        var checked = $('#area-checkbox:checkbox:checked').length == 0;
        if (checked) {
          $('#area-checkbox').click();
        }
      }

      $(function() {
          $('#focusTable').DataTable({
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
              ajax: '{!! route('focus.data') !!}',
              order: [],
              columnDefs:[
                {"className": "text-center", "targets": 0},
                {"className": "text-right", "targets": 3},
                {"className": "text-right", "targets": 4}
              ],
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
  </script>
@endsection