@extends('layouts.app')
@section('title', "Report Distributor PF Motorik")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Distributor PF Motorik <small>Report</small></h2>
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
      <h3 class="block-title">Filter</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <form method="post" id="filter">
          <div class="row">
            <div class="col-md-6">
              <label>Periode:</label>
              <input class="js-datepicker form-control" type="text" placeholder="Select Periode" name="periode" data-month-highlight="true" required>
            </div>
          </div>
          <button type="submit" class="btn btn-outline-danger btn-square mt-10">Filter Data</button>
        </form>
      </div>
    </div>
  </div>
  <div class="block block-themed" id="table-block" style="display: none"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
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
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered table-responsive" id="category">
          <thead>
            <tr>
              <th class="text-center" style="width: 70px;"></th>
              <th>Nama Motorik</th>
              <th>Block</th>
              <th>Tanggal</th>
              @foreach ($product as $pro)
              <th>{{ $pro->name }}</th>
              @endforeach
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<style type="text/css">
[data-notify="container"] {
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
table.table thead tr th:first-child {
  min-width: 5px;
}

table.table thead tr th {
  min-width: 150px;
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
  $(".js-datepicker").datepicker( {
    format: "mm/yyyy",
    viewMode: "months",
    autoclose: true,
    minViewMode: "months"
  });
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $("#btnDownloadXLS").on("click", function(){
      var url= "{{ route('report.motorik.distPF.export') }}"+"?periode="+$(".js-datepicker").val();
      window.location.href=url;
  });

  $('#filter').submit(function(e) {
    Codebase.layout('header_loader_on');
    e.preventDefault();
    var table = null;
    var url = '{!! route('report.motorik.distPF.data') !!}';
    table = $('#category').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "400px",
      ajax: {
        url: url + "?" + $("#filter").serialize(),
        type: 'POST',
        dataType: 'json',
        dataSrc: function(res) {
          Codebase.layout('header_loader_off');
          if (res.data == 0) {
            $('#table-block').hide();
            swal("Error!", "Data is empty!", "error");
            return res.data;
          } else {
            $('#table-block').show();
            return res.data;
          }
        },
        error: function (data) {
          Codebase.layout('header_loader_off');
          swal("Error!", "Failed to load Data!", "error");
        },
      },
      columns: [
        { data: 'id', name:'' },
      { data: 'nama', name:'Nama' },
      { data: 'block', name:'Block' },
      { data: 'tanggal', name:'Tanggal' },
      @foreach ($product as $pro)
      { data: 'product-{{ $pro->id }}' },
      @endforeach
      ],
      bDestroy: true
    });
  });
</script>
@endsection