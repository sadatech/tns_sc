@extends('layouts.app')
@section('title', "Sales Report - Sell In")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Sales Summary (MTC) <small> Report </small></h2>
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
                  <span>
                    <i class="fa fa-calendar"></i> Periode
                  </span>
                  <input type="text" id="filterMonth" class="form-control" placeholder="Periode" name="periode">
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
          </h3>
          <div class="block-option">            
            <button id="exportAll" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button>
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="onTargetTable">
          <thead>          
            <th>Periode</th>
            <th>Region</th>
            <th>Jawa / Non Jawa</th>
            <th>Jabatan</th>
            <th>Nama</th>
            <th>Area</th>
            <th>Sub Area</th>
            <th>Outlet</th>
            <th>Account</th>
            <th>Category</th>
            <th>Product Line</th>
            <th>Product Name</th>
            <th>Actual Out Qty</th>
            <th>Actual In Qty</th>
            <th>Price</th>
            <th>Actual Out Value</th>
            <th>Actual In Value</th>
            <th>Total Actual</th>
            <th>Target Qty</th>
            <th>Target Value</th>
          </thead>
        </table>

      </div>
    </div>
  </div>
</div>


@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">

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
  <script src="{{ asset('assets/js/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
  <script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
  <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('js/select2-handler.js') }}"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <script src="{{ asset('js/daterangepicker.js') }}"></script>
  <script src="{{ asset('js/datetimepicker-handler.js') }}"></script>
  <script type="text/javascript">

        var table = 'onTargetTable';
        var filterId = ['#filterRegion', '#filterArea', '#filterSubArea', '#filterStore', '#filterEmployee'];
        var url = "{!! route('salesmtc.data') !!}";
        var order = [ [0, 'desc']];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [
                { data: 'periode', name: 'periode'},
                { data: 'region', name: 'region'},
                { data: 'is_jawa', name: 'is_jawa'},
                { data: 'jabatan', name: 'jabatan'},
                { data: 'employee_name', name: 'employee_name'},
                { data: 'area', name: 'area'},
                { data: 'sub_area', name: 'sub_area'},
                { data: 'store_name', name: 'store_name'},
                { data: 'account', name: 'account'},
                { data: 'category', name: 'category'},
                { data: 'product_line', name: 'product_line'},
                { data: 'product_name', name: 'product_name'},
                { data: 'actual_out_qty', name: 'actual_out_qty'},
                { data: 'actual_in_qty', name: 'actual_in_qty'},
                { data: 'price', name: 'price'},
                { data: 'actual_out_value', name: 'actual_out_value'},
                { data: 'actual_in_value', name: 'actual_in_value'},
                { data: 'total_actual', name: 'total_actual'},
                { data: 'target_qty', name: 'target_qty'},
                { data: 'target_value', name: 'target_value'}];

        var exportButton = '#export';

        var paramFilter = [table, $('#'+table), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, table, $('#'+table), url, tableColumns, columnDefs, order, exportButton, '#filterMonth'];


      $(document).ready(function() {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }                  

          });

          $('#filterMonth').datetimepicker({
              format: "MM yyyy",
              startView: "3",
              minView: "3",
              autoclose: true,
          });

          $('#filterMonth').val(moment().format("MMMM Y"));

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

          // TABLE BY SALES
          $('#onTargetTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url + "?" + $("#filterForm").serialize(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter": false,
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
            "ordering": false
          });

          // TABLE BY TARGET
          // $('#offTargetTable').dataTable({
          //   "fnCreatedRow": function (nRow, data) {
          //       $(nRow).attr('class', data.id);
          //   },
          //   "processing": true,
          //   "serverSide": true,
          //   "ajax": {
          //       url: url_target + "?" + $("#filterForm").serialize(),
          //       type: 'POST',
          //       dataType: 'json',
          //       error: function (data) {
          //         swal("Error!", "Failed to load Data!", "error");
          //       },
          //   },
          //   scrollX:        true,
          //   scrollCollapse: true,
          //   "bFilter": false,
          //   "rowId": "id",
          //   "columns": tableColumns,
          //   "columnDefs": columnDefs,
          //   "order": order,
          // });

    });
      

    $("#filterReset").click(function () {

      $.each($('#filterForm select'), function(key, value) {
        $('#'+this.id).val(null).trigger('change')
      })

      $('#filterMonth').val(moment().format("MMMM Y"));
    })

    $("#filterSearch").click(function() {
      var serial = $("#filterForm").serialize()
      console.log(serial)
    })


    $("#exportAll").click( function(){

        var element = $("#exportAll");
        var icon = $("#exportAllIcon");
        if (element.attr('disabled') != 'disabled') {
            var thisClass = icon.attr('class');
            // Export data
            exportFile = '';

            $.ajax({
                type: 'POST',
                url: 'export' + "?" + $("#filterForm").serialize() + "&model=Sales MTC",
                dataType: 'json',
                beforeSend: function()
                {   
                    console.log($("#filterForm").serialize());
                    element.attr('disabled', 'disabled');
                    icon.attr('class', 'fa fa-spinner fa-spin');
                },
                success: function (data) {
                    
                    console.log(data);
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    
                    if(data.result){
                      swal("Berhasil melakukan request", "Silahkan cek di halaman 'Download Export File'", "success");
                    }else{
                      swal("Gagal melakukan request", "Silahkan dicoba kembali", "error");
                    }
                    
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