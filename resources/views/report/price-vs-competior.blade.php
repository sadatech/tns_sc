@extends('layouts.app')
@section('title', "Sales Report - Price vs Competitor")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Price vs Competitor <small>Report</small></h2>
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
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus mr-2"></i>Add Price vs Competitor</button>
            <button class="btn btn-info btn-square"  data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
          </h3>
          <div class="block-option">
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</button>
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</button>
          </div>
        </div>

                <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-popout" role="document">
                        <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary p-10">
                                    <h3 class="block-title"><i class="si si-cloud-upload"></i> Import Price vs Competitor</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="si si-close"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('sellin.import') }}" method="post" enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                <div class="block-content">
                                    <div class="form-group">
                                        <a href="{{ route('account.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                                    </div>
                                    <div class="form-group">
                                        <label>Import Price vs Competitor</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                            <label class="custom-file-label">Choose file Excel</label>
                                            <code> *Type File Excel</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-alt-success">
                                        <i class="fa fa-save"></i> Import File
                                    </button>
                                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th>Week</th>
          <th>Distributor Code</th>
          <th>Distributor Name</th>
          <th>Region</th>
          <th>Area</th>
          <th>Sub Area</th>
          <th>Account</th>
          <th>Channel</th>
          <th>Store Name 1</th>
          <th>Store Name 2</th>
          <th>NIK</th>
          <th>Employee Name</th>
          <th>Date</th>
          <th>Product</th>
          <th>Category</th>
          <th>Quantity</th>
          <th>Unit Price</th>
          <th>Value</th>
          <th>Value PF</th>
          <th>SPV Name</th>
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
                    <h3 class="block-title"><i class="fa fa-edit"></i> Edit Quantity - Price vs Competitor</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="editForm" method="post">
                {!! csrf_field() !!}
                <div class="block-content">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Quantity</label>
                            <input type="text" class="form-control" name="qty" id="qtyInput" required>
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
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Price vs Competitor</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('sellin.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Employee</label>
            <div class="input-group mb-3 col-sm-12 col-md-12">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="employee" id="employeeSelect" >
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Store</label>
            <div class="input-group mb-3 col-sm-12 col-md-12">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="store" id="storeSelect" >
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Date</label>
            <div class="input-group mb-3 col-sm-12 col-md-12">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <input class="form-control" type="date" name="date" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" >
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Product</label>
            <div class="input-group mb-3 col-sm-12 col-md-12">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="product" id="productSelect" >
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Quantity</label>
            <div class="input-group mb-3 col-sm-12 col-md-12">
              <div class="col-md-8 col-sm-12" style="padding: 0">
                <input type="text" class="form-control" name="qty" placeholder="Input quantity" >
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <style type="text/css">
    [data-notify="container"] 
    {
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    th, td {
        white-space: nowrap;
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

        $('#employeeSelect').select2(setOptions('{{ route("employee-select2") }}', 'Select Employee', function (params) {
          return filterData('employee', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.nik+' - '+obj.name}
            })
          }
        }));

        $('#storeSelect').select2(setOptions('{{ route("store-select2") }}', 'Select Store', function (params) {
          return filterData('store', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name1+' - '+obj.name2}
            })
          }
        }));


        $('#productSelect').select2(setOptions('{{ route("product-select2") }}', 'Select Product', function (params) {
          return filterData('product', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name+' ('+obj.deskripsi+')'}
            })
          }
        }));

      });

//   $("#datepicker").datepicker( {
//     format: "mm-yyyy",
//     viewMode: "months", 
//     minViewMode: "months"
// });
      function editModal(json) {
          $('#editModal').modal('show');
          $('#editForm').attr('action', "{{ url('/report/sales/sellin/edit') }}/"+json.id);
          $('#qtyInput').val(json.qty);
          console.log(json);
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
          $('#reportTable').DataTable({
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
              ajax: '{!! route('sellin.data') !!}',
              columns: [
	              { data: 'id', name: 'id', visible: false},
                { data: 'week', name: 'week'},
                { data: 'distributor_code', name: 'distributor_code'},
                { data: 'distributor_name', name: 'distributor_name'},
                { data: 'region', name: 'region'},
                { data: 'area', name: 'area'},
                { data: 'sub_area', name: 'sub_area'},
                { data: 'account', name: 'account'},
                { data: 'channel', name: 'channel'},
                { data: 'store_name_1', name: 'store_name_1'},
                { data: 'store_name_2', name: 'store_name_2'},
                { data: 'nik', name: 'nik'},
                { data: 'employee_name', name: 'employee_name'},
                { data: 'date', name: 'date'},
                { data: 'product_name', name: 'product_name'},
                { data: 'category', name: 'category'},
                { data: 'quantity', name: 'quantity'},
                { data: 'unit_price', name: 'unit_price'},
                { data: 'value', name: 'value'},
                { data: 'value_pf', name: 'value_pf'},
                { data: 'spv_name', name: 'spv_name'},
                { data: 'action', name: 'action' },
              ],
              "scrollX":        true, 
              "scrollCollapse": true,
          });
      });

  </script>
@endsection