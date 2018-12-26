@extends('layouts.app')
@section('title', "Report Sales SPG")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Target SMD <small>Based on KPI</small></h2>
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

  <div class="block block-themed " id="panelData" style="display: none;"> 
    <div class="block-header bg-primary">
      <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
      <div class="block-header p-0 mb-20">
          <h3 class="block-title">
          </h3>
          <div class="block-option">
              <a id="btnDownloadXLS" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-responsive" id="summaryTable">
          <thead>
            <tr>
              <th colspan="4" style="text-align: center;">INFORMATION</th>
              <th colspan="3" style="text-align: center;">TARGET/BULAN</th>
              <th colspan="3" style="text-align: center;">ACHIEVEMENT</th>
            </tr>
            <tr>
              <th class="text-center" style="width: 70px;"></th>              
              <th>Area</th>
              <th>Nama SMD</th>
              <th>HK Target</th>
              <th>Sales Value</th>
              <th>EC Produk Fokus</th>
              <th>CBD</th>
              <th>Sales Value</th>
              <th>EC Produk Fokus</th>
              <th>CBD</th>
            </tr>
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
[data-notify="container"] {
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
/*table.table thead tr th:first-child {
  min-width: 5px;
}*/
table.table thead tr th {
  min-width: 200px;
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
<!-- <script type="text/javascript">
  $(function() {
    var table = $('#category').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "300px",
      ajax: '{!! route('spg.pasar.sales.summary.data') !!}',
      columns: [
      { data: 'id', name:'id', visible: false },
      { data: 'area', name:'area' },
      { data: 'nama_spg', name:'nama_spg' },
      { data: 'tanggal', name:'tanggal' },
      { data: 'nama_pasar', name:'nama_pasar' },
      { data: 'nama_stokies', name:'nama_stokies' },
      { data: 'jumlah_beli', name:'jumlah_beli' },
      { data: 'detail', name:'detail' },
      ]
    });
  });
</script> -->
<script type="text/javascript">

        var table = 'summaryTable';
        var filterId = ['#filterSubCategory'];
        var url = "{!! route('smd.pasar.target.kpi.data') !!}";
        var order = [ [0, 'desc']];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [
                { data: 'id', name:'id', visible: false },                                
                { data: 'area', name:'area' },
                { data: 'name', name:'name' },
                { data: 'hk_target', name:'hk_target' },
                { data: 'target_sales_value', name:'target_sales_value' },
                { data: 'target_ec_pf', name:'target_ec_pf' },
                { data: 'target_cbd', name:'target_cbd' },
                { data: 'ach_sales_value', name:'ach_sales_value' },
                { data: 'ach_ec_pf', name:'ach_ec_pf' },
                { data: 'ach_cbd', name:'ach_cbd' },
                ];

        var exportButton = '#export';

        var paramFilter = [table, $('#'+table), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, table, $('#'+table), url, tableColumns, columnDefs, order, exportButton, '#filterMonth'];
        


      $(document).ready(function() {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }                  

          });

         /**
           * Download OnClick
           */
          $("#btnDownloadXLS").on("click", function(){
            $.ajax({
              url: $(this).attr("target-url"),
              type: "post",
              success: function(e){
                swal("Success!", e.result, "success");
              },
              error: function(){
                swal("Error!", e.result, "error");
              }
            });
          });

          $('#filterMonth').datetimepicker({
              format: "MM yyyy",
              startView: "3",
              minView: "3",
              autoclose: true,
          });

          $('#filterMonth').val(moment().format("MMMM Y"));  

          // TABLE BY SALES
          // $('#summaryTable').dataTable({
          //   "fnCreatedRow": function (nRow, data) {
          //       $(nRow).attr('class', data.id);
          //   },
          //   "processing": true,
          //   "serverSide": true,
          //   "ajax": {
          //       url: url + "?" + $("#filterForm").serialize(),
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
          //   "ordering": false
          // });

    });
      

    $("#filterReset").click(function () {

      $.each($('#filterForm select'), function(key, value) {
        $('#'+this.id).val(null).trigger('change')
      })

      $('#filterMonth').val(moment().format("MMMM Y"));

      $('#panelData').attr("style", "display:none;");
    })

    $("#filterSearch").click(function() {

      var serial = $("#filterForm").serialize()
      console.log(serial)

      $("#btnDownloadXLS").attr("target-url", "{{ route('smd.pasar.target.kpi.exportXLS') }}" + "/" + $('#filterMonth').val());
      $('#panelData').removeAttr('style');
      
    })


    $("#exportAll").click( function(){

        // var element = $("#exportAll");
        // var icon = $("#exportAllIcon");
        // if (element.attr('disabled') != 'disabled') {
        //     var thisClass = icon.attr('class');
        //     // Export data
        //     exportFile = '';

        //     $.ajax({
        //         type: 'POST',
        //         url: 'export' + "?" + $("#filterForm").serialize() + "&model=Sales MTC",
        //         dataType: 'json',
        //         beforeSend: function()
        //         {   
        //             console.log($("#filterForm").serialize());
        //             element.attr('disabled', 'disabled');
        //             icon.attr('class', 'fa fa-spinner fa-spin');
        //         },
        //         success: function (data) {
                    
        //             console.log(data);
        //             element.removeAttr('disabled');
        //             icon.attr('class', thisClass);
                    
        //             if(data.result){
        //               swal("Berhasil melakukan request", "Silahkan cek di halaman 'Download Export File'", "success");
        //             }else{
        //               swal("Gagal melakukan request", "Silahkan dicoba kembali", "error");
        //             }
                    
        //         },
        //         error: function(xhr, textStatus, errorThrown){
        //             element.removeAttr('disabled');
        //             icon.attr('class', thisClass);
        //             console.log(errorThrown);
        //             alert('Export request failed');
        //         }
        //     });

        // }


    });

  </script>
@endsection