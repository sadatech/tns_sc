@extends('layouts.app')
@section('title', "Report Sales SMD Pasar")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Sales SMD Pasar <small>Report</small></h2>
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
          </h3>
          <div class="block-option">
              <a href="{{ route('export.sales.smd') }}" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-responsive" id="category">
          <thead>
            <tr>
              <th class="text-center" style="width: 70px;"></th>
              <th>Nama</th>
              <th>Pasar</th>
              <th>Tanggal</th>
              <th>Week</th>
              <th>Stockist</th>
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<style type="text/css">
[data-notify="container"] {
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
table.table thead tr th:first-child {
  min-width: 5px;
}
table.table thead tr th {
  min-width: 200px;
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
  $(function() {
    var table = $('#category').DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      paging: true,
      scrollX: true,
      scrollY: "300px",
      ajax: '{!! route('data.smd.stockist') !!}',
      columns: [
      { data: 'id' },
      { data: 'name' },
      { data: 'pasar' },
      { data: 'tanggal' },
      { data: 'week' },
      { data: 'stockist' },
      @foreach ($product as $pro)
      { data: 'product-{{ $pro->id }}' },
      @endforeach
      ]
    });
  });
</script>
@endsection