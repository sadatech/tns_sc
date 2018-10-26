@extends('layouts.app')
@section('title', "Sales Report - Sell In")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Sell In <small>Report</small></h2>
  @if($errors->any())
    <div class="alert alert-danger">
      <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
      @foreach ($errors->all() as $error)
      <div> {{ $error }}</div>
      @endforeach
    </div>
  @endif
  <div class="block block-themed block-mode-loading-refresh">
      <div class="block-header bg-primary">
          <h3 class="block-title">
              Filters
          </h3>
          <div class="block-options">
              <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-down"></i></button>
          </div>
      </div>
      <div class="block-content bg-white">
        <form id="filterForm" method="post" action="#">
          <div class="row items-push">
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Region</div>
                  <select id="filterRegion" class="inputFilter" name="id_reg"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Area</div>
                  <select id="filterArea" class="inputFilter" name="id_ar"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Sub Area</div>
                  <select id="filterSubArea" class="inputFilter" name="id_sar"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Store</div>
                  <select id="filterStore" class="inputFilter" name="id_str"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Employee</div>
                  <select id="filterEmployee" class="inputFilter" name="id_emp"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Date</div>
                  <button type="button" class="btn btn-default pull-right col-sm-12" id="daterange-btn">
                    <span>
                      <i class="fa fa-calendar"></i> Date Range picker
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                  <input type="hidden" id="inputDate" name="date_range">
              </div>
          </div>
          <div class="row col-sm-12 col-md-12">
            <p class="btn btn-sm btn-primary" id="filterSearch" onclick="filteringReportWithoutSearch(paramFilter)"><i class="fa fa-search"></i> Search</p>
            <p class="btn btn-sm btn-danger" id="filterReset" onclick="triggerResetWithoutSearch(paramReset)"><i class="fa fa-refresh"></i> Clear</p>
          </div>
        </form>
      </div>
  </div>
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sea pl-20 pr-20 pt-15 pb-15">
      <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus mr-2"></i>Add Sell In</button>
            <button class="btn btn-info btn-square"  data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
          </h3>
          <div class="block-option">
            <button id="export" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</button>
            <button id="exportAll" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</button>
          </div>
        </div>

                <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-popout" role="document">
                        <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary p-10">
                                    <h3 class="block-title"><i class="si si-cloud-upload"></i> Import Sell In</h3>
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
                                        <label>Import Sell In</label>
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
                    <h3 class="block-title"><i class="fa fa-edit"></i> Edit Quantity - Sell In</h3>
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
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Sell In</h3>
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
              <div class="offset-md-2 col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="employee" id="employeeSelect" >
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Store</label>
            <div class="input-group mb-3 col-sm-12 col-md-12">
              <div class="offset-md-2 col-md-8 col-sm-12" style="padding: 0">
                <select class="form-control" style="width: 100%" name="store" id="storeSelect" >
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">Date</label>
            <div class="input-group mb-3 col-sm-12 col-md-12">
              <div class="offset-md-2 col-md-8 col-sm-12" style="padding: 0">
                <input class="form-control" type="date" name="date" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" >
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12" style="padding: 0">
            <label class="col-md-12 col-sm-12" style="padding: 0">
              Product 
              <p class="btn btn-sm btn-primary" id="addProduct" style="float: right;"><i class="fa fa-plus"></i>More Product</p>
            </label>
              <div class="input-group mb-3 col-sm-12 col-md-12 row">
                <div class="col-md-6 col-sm-6">
                  <select class="form-control productSelect">
                  </select>
                  <input type="hidden" name="product[product_id][]">
                </div>
                <div class='col-md-6 col-sm-6 row' style='padding:0;'>
                  <div class='col-md-9 col-sm-9'>
                    <input type='text' class='form-control' name='product[qty][]' placeholder='Input quantity' >
                  </div>
                  <div class='col-md-3 col-sm-3'>
                  </div>
                </div>
              </div>
              <div class="otherProduct">
                
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
  <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
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
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <script src="{{ asset('js/daterangepicker.js') }}"></script>
  <script type="text/javascript">
    var index = 0;
    var productSelected = [];

      var filterId = ['#filterRegion', '#filterArea', '#filterSubArea', '#filterStore', '#filterEmployee'];
        var url = "{!! route('sellin.data') !!}";
        var order = [ [0, 'desc'] ];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [{ data: 'id', name: 'id', visible: false},
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
                { data: 'qty', name: 'qty'},
                { data: 'unit_price', name: 'unit_price'},
                { data: 'value', name: 'value'},
                { data: 'value_pf', name: 'value_pf'},
                { data: 'spv_name', name: 'spv_name'},
                { data: 'action', name: 'action' }];

        var exportButton = '#export';

        var paramFilter = ['reportTable', $('#reportTable'), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, 'reportTable', $('#reportTable'), url, tableColumns, columnDefs, order, exportButton, '#filterMonth'];

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

        $('.productSelect').select2(setOptions('{{ route("product-select2") }}', 'Select Product', function (params) {
          filters['productExcept'] = productSelected;
          return filterData('product', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name+' ('+obj.deskripsi+')'}
            })
          }
        }));
        $('.productSelect').on('change', function() {
          productSelected.push($('.productSelect').val());
          console.log(productSelected);
        });

        $('#filterRegion').select2(setOptions('{{ route("region-select2") }}', 'Select Region', function (params) {
          return filterData('name', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name}
            })
          }
        }));

        $('#filterArea').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
          return filterData('name', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name}
            })
          }
        }));

        $('#filterSubArea').select2(setOptions('{{ route("sub-area-select2") }}', 'Select Sub Area', function (params) {
          return filterData('name', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name}
            })
          }
        }));

        $('#filterStore').select2(setOptions('{{ route("store-select2") }}', 'Select Store', function (params) {
          return filterData('store', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name1}
            })
          }
        }));

        $('#filterEmployee').select2(setOptions('{{ route("employee-select2") }}', 'Select Employee', function (params) {
          return filterData('employee', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name}
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

      $("#addProduct").click(function () {
        index++;
        $(".otherProduct").append("<div class='input-group mb-3 col-sm-12 col-md-12 row'>"+
                "<div class='col-md-6 col-sm-6'>"+
                  "<select class='form-control productSelect productSelect"+index+"'>"+
                  "</select>"+
                  "<input type='text' name='product[product_id][]'>"+
                "</div>"+
                "<div class='col-md-6 col-sm-6 row' style='padding:0;'>"+
                  "<div class='col-md-9 col-sm-9'>"+
                    "<input type='text' class='form-control' name='product[qty][]' placeholder='Input quantity' >"+
                  "</div>"+
                  "<div class='col-md-3 col-sm-3'>"+
                    "<p class='btn btn-sm btn-danger deleteProduct'><i class='fa fa-trash'></i></p>"+
                  "</div>"+
                "</div>"+
              "</div>")
        $('.productSelect'+index).select2(setOptions('{{ route("product-select2") }}', 'Select Product', function (params) {
          filters['productExcept'] = productSelected;
          return filterData('product', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name+' ('+obj.deskripsi+')'}
            })
          }
        }));
        $('.productSelect'+index).on('change', function() {
          productSelected.push($('.productSelect'+index).val());
          console.log(productSelected);
        });
      })

      $("body").on('click','.deleteProduct',function(){
        $(this).parent().parent().parent().remove();
      })

      $("body").on('change','.productSelect',function(){
        $(this).nextAll('input').first().val(this.value);
      })

      //Date picker
    $(function () {
      $('#daterange-btn').daterangepicker(
        {
          ranges   : {
            'Today'       : [moment(), moment()],
            'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month'  : [moment().startOf('month'), moment().endOf('month')],
            'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate  : moment()
        },
        function (start, end) {
          $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
          $('#inputDate').val(start.format('YYYY-MM-DD')+'|'+end.format('YYYY-MM-DD'))
          filters['date_range'] = $('#inputDate').val();
        }
      )
    });

    $("#filterReset").click(function () {
      $('#inputDate').val('')
      $("#daterange-btn").html(
        "<span>"+
          "<i class='fa fa-calendar'></i> Date Range picker"+
        "</span>"+
        "<i class='fa fa-caret-down'></i>")
      $.each($('#filterForm select'), function(key, value) {
        $('#'+this.id).val(null).trigger('change')
      })
    })

    $("#filterSearch").click(function() {
      var serial = $("#filterForm").serialize()
      // $.each( $(".inputFilter"), function( key, value ) {
      //   alert( key + ": " + value.val() );
      // });
    })

    <!-- -->

    $('#filterRegion').on('select2:select', function () {
        self.selected('byRegion', $('#filterRegion').val());
    });
    $('#filterArea').on('select2:select', function () {
        self.selected('byArea', $('#filterArea').val());
    });
    $('#filterSubArea').on('select2:select', function () {
        self.selected('bySubArea', $('#filterSubArea').val());
    });
    $('#filterStore').on('select2:select', function () {
        self.selected('byStore', $('#filterStore').val());
    });
    $('#filterEmployee').on('select2:select', function () {
        self.selected('byEmployee', $('#filterEmployee').val());
    });

    $("#export").click( function(){

        var element = $("#export");
        var icon = $("#exportIcon");
        if (element.attr('disabled') != 'disabled') {
            var thisClass = icon.attr('class');

            // Export data
            exportFile = '';

            $.ajax({
                type: 'POST',
                url: '../../utilities/report-download/export',
                dataType: 'json',
                data: {
                        model: 1,
                        type: 'SELECTED',
                        data: JSON.stringify(data)
                      },
                global: false,
                async: false,
                beforeSend: function()
                {   
                    element.attr('disabled', 'disabled');
                    icon.attr('class', 'fa fa-spinner fa-spin');
                },
                success: function (data) {
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    console.log(data);
                },
                error: function(xhr, textStatus, errorThrown){
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    console.log(errorThrown);
                    alert('Export request failed');
                }
            });

        }

    });

    $("#exportAll").click( function(){

        var element = $("#exportAll");
        var icon = $("#exportAllIcon");
        if (element.attr('disabled') != 'disabled') {
            var thisClass = icon.attr('class');
            // Export data
            exportFile = '';

            $.ajax({
                type: 'POST',
                url: '../../utilities/report-download/export-all',
                dataType: 'json',
                data: filters,
                beforeSend: function()
                {   
                    element.attr('disabled', 'disabled');
                    icon.attr('class', 'fa fa-spinner fa-spin');
                },
                success: function (data) {
                    
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    console.log(data);

                },
                error: function(xhr, textStatus, errorThrown){
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    console.log(errorThrown);
                    alert('Export request failed');
                }
            });

        }


    });

  </script>
@endsection