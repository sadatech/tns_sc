@extends('layouts.app')
@section('title', "Report Sales Motorik")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Sales Motorik<small>Report</small></h2>
  @if($errors->any())
  <div class="alert alert-danger">
    <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
    @foreach ($errors->all() as $error)
    <div> {{ $error }}</div>
    @endforeach
  </div>
  @endif
  <div class="block block-themed block-mode-loading-refresh">
      <div class="block-header bg-gd-sun">
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
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Block</div>
                  <select id="filterBlock" class="inputFilter" name="id_block"></select>
              </div>
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <span>
                    <i class="fa fa-calendar"></i> Periode
                  </span>
                  <input type="text" id="filterMonth" class="form-control" placeholder="Periode" name="periode">
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
    <div class="block-header bg-gd-sun">
      <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
      <div class="block-header p-0 mb-20">
          <h3 class="block-title">
          </h3>
          <div class="block-option">
              <a href="{{ route('export.distpf.smd') }}" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-responsive" id="summaryTable">
          <thead>
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
<script type="text/javascript">

        var thead = '<th class="text-center" style="width: 70px;">No.</th><th></th><th>Nama Motorik</th><th>Block</th><th>Date</th>';
        var table = 'summaryTable';
        var filterId = ['#filterBlock'];
        var url = "{!! route('report.motorik.sales.data') !!}";
        var order = [ [0, 'desc']];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumnsDefault = [
          { data: 'id', name:'' },
          { data: 'nama', name:'Nama' },
          { data: 'block', name:'Block' },
          { data: 'date', name:'date' },
          @foreach ($product as $pro)
          { data: 'product-{{ $pro->id }}' },
          @endforeach
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

        

          $('#filterBlock').select2(setOptions('{{ route("block-select2") }}', 'Select Block', function (params) {
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

    });
    $('#filterMonth').datetimepicker({
              format: "MM yyyy",
              startView: "3",
              minView: "3",
              autoclose: true,
          });

          $('#filterMonth').val(moment().format("MMMM Y"));  
      

    $("#filterReset").click(function () {

      $.each($('#filterForm select'), function(key, value) {
        $('#'+this.id).val(null).trigger('change')
      })

      $('#filterMonth').val(moment().format("MMMM Y"));

      $('#panelData').attr("style", "display:none;");
    })

    $("#filterSearch").click(function() {

      if($('#filterBlock').val() == null){
        swal("Warning!", "Please select Block First!", "warning")
        return
      }

      var serial = $("#filterForm").serialize()
      console.log(serial)

      $('#panelData').removeAttr('style');

      $.ajax({
        url:"{!! route('motorik-product-data') !!}",
        method:"POST", 

        data:{
          id_block: $('#filterBlock').val(), 
          periode: $('#filterMonth').val(),
        },
        success:function(response) {
          console.log(response);
          if($.fn.dataTable.isDataTable('#summaryTable')){
              $('#summaryTable').DataTable().clear();
              $('#summaryTable').DataTable().destroy();
          }

          document.getElementById("is_header").innerHTML = thead;
          document.getElementById("is_header").innerHTML += response.th;

          tableColumns = tableColumnsDefault.concat(response.columns);

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
  </script>
@endsection