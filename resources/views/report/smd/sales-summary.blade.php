@extends('layouts.app')
@section('title', "Report Sales SPG")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Sales Summary<small>Report</small></h2>
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
            <div class="col-md-4">
                <span>
                  <i class="fa fa-map"></i> Area:
                </span>
              <select class="form-control" id="filterArea" name="area"></select>
            </div>
          </div>
          <div class="row col-sm-12 col-md-12">
            <p class="btn btn-sm btn-primary" id="filterSearch"><i class="fa fa-search"></i> Search</p>
            <p class="btn btn-sm btn-danger" id="filterReset"><i class="fa fa-refresh"></i> Clear</p>
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
            <tr id="is_header_before"></tr>
            <tr id="is_header"></tr>
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

        var thead_before = '<th colspan="9" style="text-align: center;">INFORMATION</th>';
        var thead = '<th class="text-center" style="width: 70px;">No.</th><th>Area</th><th>Nama SMD</th><th style="text-align: center;">Jabatan</th><th>Nama Pasar</th><th>Nama Stokist</th><th style="text-align: center;">Tanggal</th><th style="text-align: center;">Call</th><th style="text-align: center;">RO</th>';
        var table = 'summaryTable';
        var filterId = ['#filterSubCategory'];
        var url = "{!! route('smd.pasar.sales.summary.data') !!}";
        var order = [ [0, 'desc']];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumnsDefault = [
                { data: 'new_id', name:'new_id', visible: false },
                { data: 'area', name:'area' },
                { data: 'nama_smd', name:'nama_smd' },
                { data: 'jabatan', name:'jabatan' },
                { data: 'nama_pasar', name:'nama_pasar' },
                { data: 'nama_stokist', name:'nama_stokist' },
                { data: 'tanggal', name:'tanggal' },
                { data: 'call', name:'call' },
                { data: 'ro', name:'ro' },
                ];
        var tableColumns = [];

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

        $('#filterArea').select2(setOptions('{{ route("area-select2") }}', 'Choose your Area', function (params) {
          return filterData('name', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name}
            })
          }
        }));

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
      
      setTimeout(function() {
        $('#filterEmployee,#filterOutlet,#filterArea').val(null).trigger('change');
      }, 10);
    })

    $("#filterSearch").click(function() {

      var serial = $("#filterForm").serialize()
      console.log(serial)

      $('#panelData').removeAttr('style');

      $.ajax({
        url:"{!! route('product-fokus-gtc-data') !!}",
        method:"POST", //First change type to method here

        data:{
          periode: $('#filterMonth').val(),
        },
        success:function(response) {
          console.log(response);

          // filteringReportWithoutSearch([table, $('#'+table), url, tableColumns, columnDefs, order, exportButton]);

          if($.fn.dataTable.isDataTable('#summaryTable')){
              $('#summaryTable').DataTable().clear();
              $('#summaryTable').DataTable().destroy();
          }

          document.getElementById("is_header_before").innerHTML = thead_before;
          document.getElementById("is_header").innerHTML = thead;

          document.getElementById("is_header_before").innerHTML += response.th_before;
          document.getElementById("is_header").innerHTML += response.th;

          // tableColumns = tableColumnsDefault;
          tableColumns = tableColumnsDefault.concat(response.columns);

          console.log(document.getElementById("is_header").innerHTML);
          console.log(tableColumns);
          // return

          $("#btnDownloadXLS").attr("target-url", "{{ route('smd.pasar.sales.summary.exportXLS') }}" + "/" + $('#filterMonth').val()+"/"+$("#filterArea").val());

          $('#summaryTable').dataTable({
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
          
        },
        error:function(){
          swal("Error!", "Failed to request!", "error");
        }

      });

      
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