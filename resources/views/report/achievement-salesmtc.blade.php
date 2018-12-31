@extends('layouts.app')
@section('title', "Sales Review MTC")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Sales Review <small> (MTC) </small> 
    <a id="btnDownloadXLS" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
    <!-- <button id="exportAll" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button> -->
  </h2>

  @if($errors->any())
    <div class="alert alert-danger">
      <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
      @foreach ($errors->all() as $error)
      <div> {{ $error }}</div>
      @endforeach
    </div>
  @endif
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sea pl-20 pr-20 pt-15 pb-15">
    <h3 class="block-title">Periode</h3>
      <div class="col-sm-3">
        <input type="text" id="filterMonth" class="form-control" placeholder="Periode" name="periode">
      </div>
      <button class="btn btn-md btn-primary" id="filterSearchPeriode"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">

        <br>

        <!-- TL -->
        <div class="row">
          <div class="col-4 col-sm-4 text-center text-sm-left">
            <h3>TL</h3>
          </div>
          
          <div class="col-8 col-sm-8 text-right">
            <div class="row">
              <div class="col-1 col-sm-1 text-center text-sm-left">
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <select id="filterEmployeeTl" class="inputFilter" name="id_emp_tl"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <select id="filterAreaTl" class="inputFilter" name="id_ar_tl"></select>
              </div>
              <div class="col-3 col-sm-3" style="vertical-align: middle;">
                  <button class="btn btn-sm btn-primary" id="filterSearchTl" ><i class="fa fa-search"></i> Search</button>
                  <button class="btn btn-sm btn-danger" id="filterResetTl" ><i class="fa fa-refresh"></i> Clear</button>
              </div>
            </div>
            
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="tlTable">
          <thead>          
            <th></th>            
            <th>Performance TL</th>
            <th class="text-center">Tahun Lalu</th>
            <th class="text-center">Bulan Ini</th>
            <th class="text-center">Target</th>
            <th class="text-center">% Ach</th>
            <th class="text-center">Growth</th>
            <th class="text-center">Achievement Fokus 1</th>
            <th class="text-center">Target Fokus 1</th>
            <th class="text-center">% Achievement</th>
            <th class="text-center">Achievement Fokus 2</th>
            <th class="text-center">Target Fokus 2</th>
            <th class="text-center">% Achievement</th>
            <th>Area</th>
          </thead>
        </table>

        <br><br>

        <!-- SPG -->
        <div class="row">
          <div class="col-4 col-sm-4 text-center text-sm-left">
            <h3>SPG</h3>
          </div>
          
          <div class="col-8 col-sm-8 text-right">
            <div class="row">
              <div class="col-1 col-sm-1 text-center text-sm-left">
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <select id="filterEmployeeSpg" class="inputFilter" name="id_emp_spg"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <select id="filterStoreSpg" class="inputFilter" name="id_str_spg"></select>
              </div>
              <div class="col-3 col-sm-3" style="vertical-align: middle;">
                  <button class="btn btn-sm btn-primary" id="filterSearchSpg"><i class="fa fa-search"></i> Search</button>
                  <button class="btn btn-sm btn-danger" id="filterResetSpg"><i class="fa fa-refresh"></i> Clear</button>
              </div>
            </div>
            
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="spgTable">
          <thead>          
            <th></th>            
            <th>Performance SPG</th>
            <th class="text-center">Tahun Lalu</th>
            <th class="text-center">Bulan Ini</th>
            <th class="text-center">Target</th>
            <th class="text-center">% Ach</th>
            <th class="text-center">Growth</th>
            <th class="text-center">Achievement Fokus 1</th>
            <th class="text-center">Target Fokus 1</th>
            <th class="text-center">% Achievement</th>
            <th class="text-center">Achievement Fokus 2</th>
            <th class="text-center">Target Fokus 2</th>
            <th class="text-center">% Achievement</th>
            <th>Nama Store</th>
          </thead>
        </table>

        <br><br>

        <!-- MD -->
        <div class="row">
          <div class="col-4 col-sm-4 text-center text-sm-left">
            <h3>MD</h3>
          </div>
          
          <div class="col-8 col-sm-8 text-right">
            <div class="row">
              <div class="col-1 col-sm-1 text-center text-sm-left">
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">                  
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <select id="filterEmployeeMd" class="inputFilter" name="id_emp_md"></select>
              </div>
              <div class="col-3 col-sm-3" style="vertical-align: middle;">
                  <button class="btn btn-sm btn-primary" id="filterSearchMd"><i class="fa fa-search"></i> Search</button>
                  <button class="btn btn-sm btn-danger" id="filterResetMd"><i class="fa fa-refresh"></i> Clear</button>
              </div>
            </div>
            
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="mdTable">
          <thead>          
            <th></th>            
            <th>Performance MD</th>
            <th class="text-center">Tahun Lalu</th>
            <th class="text-center">Bulan Ini</th>
            <th class="text-center">Target</th>
            <th class="text-center">% Ach</th>
            <th class="text-center">Growth</th>
            <th class="text-center">Achievement Fokus 1</th>
            <th class="text-center">Target Fokus 1</th>
            <th class="text-center">% Achievement</th>
            <th class="text-center">Achievement Fokus 2</th>
            <th class="text-center">Target Fokus 2</th>
            <th class="text-center">% Achievement</th>
            <th>Jumlah Store</th>
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

        var tl_table = 'tlTable';
        var tl_filterId = ['#filterAreaTl', '#filterEmployeeTl'];
        var tl_url = "{!! route('achievement-salesmtc-tl.data') !!}";
        var tl_order = [ [0, 'desc']];
        var tl_columnDefs = [{"className": "text-center", "targets": [2,3,4,5,6]}];
        var tl_tableColumns = [                
                { data: 'id', name: 'id', visible: false},
                { data: 'employee_name', name: 'employee_name'},
                { data: 'actual_previous', name: 'actual_previous'},
                { data: 'actual_current', name: 'actual_current'},
                { data: 'target', name: 'target'},
                { data: 'achievement', name: 'achievement'},
                { data: 'growth', name: 'growth'},
                { data: 'achievement_focus1', name: 'achievement_focus1'},
                { data: 'target_focus1', name: 'target_focus1'},
                { data: 'percentage_focus1', name: 'percentage_focus1'},
                { data: 'achievement_focus2', name: 'achievement_focus2'},
                { data: 'target_focus2', name: 'target_focus2'},
                { data: 'percentage_focus2', name: 'percentage_focus2'},
                { data: 'area', name: 'area'}];

        var tl_paramFilter = [tl_table, $('#'+tl_table), tl_url, tl_tableColumns, tl_columnDefs, tl_order];
        var tl_paramReset = [tl_filterId, tl_table, $('#'+tl_table), tl_url, tl_tableColumns, tl_columnDefs, tl_order];

        var spg_table = 'spgTable';
        var spg_filterId = ['#filterStoreSpg', '#filterEmployeeSpg'];
        var spg_url = "{!! route('achievement-salesmtc-spg.data') !!}";
        var spg_order = [ [0, 'desc']];
        var spg_columnDefs = [{"className": "text-center", "targets": [2,3,4,5,6]}];
        var spg_tableColumns = [                
                { data: 'id', name: 'id', visible: false},
                { data: 'employee_name', name: 'employee_name'},
                { data: 'actual_previous', name: 'actual_previous'},
                { data: 'actual_current', name: 'actual_current'},
                { data: 'target', name: 'target'},
                { data: 'achievement', name: 'achievement'},
                { data: 'growth', name: 'growth'},
                { data: 'achievement_focus1', name: 'achievement_focus1'},
                { data: 'target_focus1', name: 'target_focus1'},
                { data: 'percentage_focus1', name: 'percentage_focus1'},
                { data: 'achievement_focus2', name: 'achievement_focus2'},
                { data: 'target_focus2', name: 'target_focus2'},
                { data: 'percentage_focus2', name: 'percentage_focus2'},
                { data: 'store_name', name: 'store_name'}];

        var spg_paramFilter = [spg_table, $('#'+spg_table), spg_url, spg_tableColumns, spg_columnDefs, spg_order];
        var spg_paramReset = [spg_filterId, spg_table, $('#'+spg_table), spg_url, spg_tableColumns, spg_columnDefs, spg_order];

        var md_table = 'mdTable';
        var md_filterId = ['#filterEmployeeMd'];
        var md_url = "{!! route('achievement-salesmtc-md.data') !!}";
        var md_order = [ [0, 'desc']];
        var md_columnDefs = [{"className": "text-center", "targets": [2,3,4,5,6,7]}];
        var md_tableColumns = [                
                { data: 'id', name: 'id', visible: false},
                { data: 'employee_name', name: 'employee_name'},
                { data: 'actual_previous', name: 'actual_previous'},
                { data: 'actual_current', name: 'actual_current'},
                { data: 'target', name: 'target'},
                { data: 'achievement', name: 'achievement'},
                { data: 'growth', name: 'growth'},
                { data: 'achievement_focus1', name: 'achievement_focus1'},
                { data: 'target_focus1', name: 'target_focus1'},
                { data: 'percentage_focus1', name: 'percentage_focus1'},
                { data: 'achievement_focus2', name: 'achievement_focus2'},
                { data: 'target_focus2', name: 'target_focus2'},
                { data: 'percentage_focus2', name: 'percentage_focus2'},
                { data: 'jml_store', name: 'jml_store'}];

        var md_paramFilter = [md_table, $('#'+md_table), md_url, md_tableColumns, md_columnDefs, md_order];
        var md_paramReset = [md_filterId, md_table, $('#'+md_table), md_url, md_tableColumns, md_columnDefs, md_order];


      $(document).ready(function() {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }                  
          });

          // fix url download data undefined
          setTimeout(function(){
            $("#filterSearchPeriode").click();
          }, 100);

          $('#filterMonth').datetimepicker({
              format: "MM yyyy",
              startView: "3",
              minView: "3",
              autoclose: true,
          });

          $("#btnDownloadXLS").on("click", function(){
            $.ajax({
              url: $(this).attr("target-url"),
              type: "post",
              success: function(e){
                swal("Success!", e.result, "success");
              },
              error: function(e){
                swal("Error!", e.result, "error");
              }
            });
          });

          $('#filterMonth').val(moment().format("MMMM Y"));

          $('#filterAreaTl').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          $('#filterStoreSpg').select2(setOptions('{{ route("store-select2") }}', 'Select SPG Store', function (params) {
            return filterData('store', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name1}
              })
            }
          }));

          $('#filterEmployeeSpg').select2(setOptions('{{ route("employee-select2-for-report") }}', 'Select SPG', function (params) {
            filters['roleGroup'] = ['spgmtc'];
            return filterData('employee', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          $('#filterEmployeeMd').select2(setOptions('{{ route("employee-select2-for-report") }}', 'Select MD', function (params) {
            filters['roleGroup'] = ['mdmtc'];
            return filterData('employee', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          $('#filterEmployeeTl').select2(setOptions('{{ route("employee-select2-for-report") }}', 'Select TL', function (params) {
            filters['roleGroup'] = ['tlmtc'];
            return filterData('employee', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          // RENDER TABLE TL
          $('#tlTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: tl_url + "?periode=" +$("#filterMonth").val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter" : false,
            "rowId": "id",
            "columns": tl_tableColumns,
            "columnDefs": tl_columnDefs,
            "order": tl_order,
            "autoWidth": false
          }); 

          // RENDER TABLE SPG
          $('#spgTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: spg_url + "?periode=" +$("#filterMonth").val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter" : false,
            "rowId": "id",
            "columns": spg_tableColumns,
            "columnDefs": spg_columnDefs,
            "order": spg_order,
            "autoWidth": false
          });    

          // RENDER TABLE MD
          $('#mdTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: md_url + "?periode=" +$("#filterMonth").val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter" : false,
            "rowId": "id",
            "columns": md_tableColumns,
            "columnDefs": md_columnDefs,
            "order": md_order,
            "autoWidth": false
          });        

    });
      

    $("#filterResetTl").click(function () {
      
        $('#filterEmployeeTl').val(null).trigger('change')
        $('#filterAreaTl').val(null).trigger('change')

        if($.fn.dataTable.isDataTable('#tlTable')){
            $('#tlTable').DataTable().clear();
            $('#tlTable').DataTable().destroy();
        }

        // RENDER TABLE TL
        $('#tlTable').dataTable({
          "fnCreatedRow": function (nRow, data) {
              $(nRow).attr('class', data.id);
          },
          "processing": true,
          "serverSide": true,
          "ajax": {
              url: tl_url + "?periode=" +$("#filterMonth").val(),
              type: 'POST',
              dataType: 'json',
              error: function (data) {
                swal("Error!", "Failed to load Data!", "error");
              },
          },
          scrollX:        true,
          scrollCollapse: true,
          "bFilter" : false,
          "rowId": "id",
          "columns": tl_tableColumns,
          "columnDefs": tl_columnDefs,
          "order": tl_order,
          "autoWidth": false
        });

    })

    $("#filterResetSpg").click(function () {

        $('#filterEmployeeSpg').val(null).trigger('change')
        $('#filterStoreSpg').val(null).trigger('change')

        if($.fn.dataTable.isDataTable('#spgTable')){
            $('#spgTable').DataTable().clear();
            $('#spgTable').DataTable().destroy();
        }

        // RENDER TABLE SPG
          $('#spgTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: spg_url + "?periode=" +$("#filterMonth").val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter" : false,
            "rowId": "id",
            "columns": spg_tableColumns,
            "columnDefs": spg_columnDefs,
            "order": spg_order,
            "autoWidth": false
          }); 

    })

    $("#filterResetMd").click(function () {

        $('#filterEmployeeMd').val(null).trigger('change')

        if($.fn.dataTable.isDataTable('#mdTable')){
            $('#mdTable').DataTable().clear();
            $('#mdTable').DataTable().destroy();
        }

        // RENDER TABLE MD
          $('#mdTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: md_url + "?periode=" +$("#filterMonth").val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter" : false,
            "rowId": "id",
            "columns": md_tableColumns,
            "columnDefs": md_columnDefs,
            "order": md_order,
            "autoWidth": false
          });  

    })

    $("#filterSearchPeriode").click(function(){
      $("#btnDownloadXLS").attr("target-url", "{{ route('achievement-salesmtc.exportxls') }}" + "/" + $('#filterMonth').val());

        if($.fn.dataTable.isDataTable('#tlTable')){
            $('#tlTable').DataTable().clear();
            $('#tlTable').DataTable().destroy();
        }

        if($.fn.dataTable.isDataTable('#spgTable')){
            $('#spgTable').DataTable().clear();
            $('#spgTable').DataTable().destroy();
        }

        if($.fn.dataTable.isDataTable('#mdTable')){
            $('#mdTable').DataTable().clear();
            $('#mdTable').DataTable().destroy();
        }

        // RENDER TABLE TL
        $('#tlTable').dataTable({
          "fnCreatedRow": function (nRow, data) {
              $(nRow).attr('class', data.id);
          },
          "processing": true,
          "serverSide": true,
          "ajax": {
              url: tl_url + "?periode=" +$("#filterMonth").val(),
              type: 'POST',
              dataType: 'json',
              error: function (data) {
                swal("Error!", "Failed to load Data!", "error");
              },
          },
          scrollX:        true,
          scrollCollapse: true,
          "bFilter" : false,
          "rowId": "id",
          "columns": tl_tableColumns,
          "columnDefs": tl_columnDefs,
          "order": tl_order,
          "autoWidth": false
        });

        // RENDER TABLE SPG
          $('#spgTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: spg_url + "?periode=" +$("#filterMonth").val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter" : false,
            "rowId": "id",
            "columns": spg_tableColumns,
            "columnDefs": spg_columnDefs,
            "order": spg_order,
            "autoWidth": false
          });    

          // RENDER TABLE MD
          $('#mdTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: md_url + "?periode=" +$("#filterMonth").val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter" : false,
            "rowId": "id",
            "columns": md_tableColumns,
            "columnDefs": md_columnDefs,
            "order": md_order,
            "autoWidth": false
          });
    })

    $("#filterSearchTl").click(function() {
        console.log($("#filterMonth").val());

        if($.fn.dataTable.isDataTable('#tlTable')){
            $('#tlTable').DataTable().clear();
            $('#tlTable').DataTable().destroy();
        }

        // RENDER TABLE TL
        $('#tlTable').dataTable({
          "fnCreatedRow": function (nRow, data) {
              $(nRow).attr('class', data.id);
          },
          "processing": true,
          "serverSide": true,
          "ajax": {
              url: tl_url + "?periode=" + $("#filterMonth").val() + "&area=" + $("#filterAreaTl").val() + "&employee=" + $("#filterEmployeeTl").val(),
              type: 'POST',
              dataType: 'json',
              error: function (data) {
                swal("Error!", "Failed to load Data!", "error");
              },
          },
          scrollX:        true,
          scrollCollapse: true,
          "bFilter" : false,
          "rowId": "id",
          "columns": tl_tableColumns,
          "columnDefs": tl_columnDefs,
          "order": tl_order,
          "autoWidth": false
        });
    })

    $("#filterSearchSpg").click(function() {
        console.log($("#filterMonth").val());

        if($.fn.dataTable.isDataTable('#spgTable')){
            $('#spgTable').DataTable().clear();
            $('#spgTable').DataTable().destroy();
        }

        // RENDER TABLE SPG
        $('#spgTable').dataTable({
          "fnCreatedRow": function (nRow, data) {
              $(nRow).attr('class', data.id);
          },
          "processing": true,
          "serverSide": true,
          "ajax": {
              url: spg_url + "?periode=" + $("#filterMonth").val() + "&store=" + $("#filterStoreSpg").val() + "&employee=" + $("#filterEmployeeSpg").val(),
              type: 'POST',
              dataType: 'json',
              error: function (data) {
                swal("Error!", "Failed to load Data!", "error");
              },
          },
          scrollX:        true,
          scrollCollapse: true,
          "bFilter" : false,
          "rowId": "id",
          "columns": spg_tableColumns,
          "columnDefs": spg_columnDefs,
          "order": spg_order,
          "autoWidth": false
        });
    })

    $("#filterSearchMd").click(function() {
        console.log($("#filterMonth").val());

        if($.fn.dataTable.isDataTable('#mdTable')){
            $('#mdTable').DataTable().clear();
            $('#mdTable').DataTable().destroy();
        }

        // RENDER TABLE SPG
        $('#mdTable').dataTable({
          "fnCreatedRow": function (nRow, data) {
              $(nRow).attr('class', data.id);
          },
          "processing": true,
          "serverSide": true,
          "ajax": {
              url: md_url + "?periode=" + $("#filterMonth").val() + "&employee=" + $("#filterEmployeeMd").val(),
              type: 'POST',
              dataType: 'json',
              error: function (data) {
                swal("Error!", "Failed to load Data!", "error");
              },
          },
          scrollX:        true,
          scrollCollapse: true,
          "bFilter" : false,
          "rowId": "id",
          "columns": md_tableColumns,
          "columnDefs": md_columnDefs,
          "order": md_order,
          "autoWidth": false
        });
    })


  </script>
@endsection