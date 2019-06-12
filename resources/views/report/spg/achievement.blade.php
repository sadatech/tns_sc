@extends('layouts.app')
@section('title', "Report Sales SPG")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10">Sales Achievement<small>Report</small></h2>
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
              <a id="btnExportAchievement" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered table-responsive" id="category">
          <thead>
            <tr>
              <th class="text-center" style="width: 70px;">No.</th>
              <th>Periode</th>
              <th>Area</th>
              <th>Nama SPG</th>
              <th>HK</th>
              <th>Sum Of Jumlah</th>
              <th>Sum Of PF Value</th>
              <th>Sum Of Total Value</th>
              <th>Eff. Kontak</th>
              <th>Value</th>
              <th>Sales/Kontak</th>
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
/*table.table thead tr th:first-child {
  min-width: 5px;
}*/

table.table thead tr th {
  min-width: 150px;
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
      scrollX: true,
      scrollY: "400px",
      ajax: '{!! route('spg.pasar.sales.achievement.data') !!}',
      columns: [
      { data: 'id', name:'id', visible: false },
      { data: 'periode', name:'periode' },
      { data: 'area', name:'area' },
      { data: 'nama_spg', name:'nama_spg' },
      { data: 'hk', name:'hk' },
      { data: 'sum_of_jumlah', name:'sum_of_jumlah' },
      { data: 'sum_of_pf_value', name:'sum_of_pf_value' },
      { data: 'sum_of_total_value', name:'sum_of_total_value' },
      { data: 'eff_kontak', name:'eff_kontak' },
      { data: 'act_value', name:'act_value' },
      { data: 'sales_per_kontak', name:'sales_per_kontak' },
      ]
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $("#btnExportAchievement").on("click", function(){
      $.ajax({
        url: "{{ route('spg.pasar.sales.achievement.data.exportXLS') }}",
        type: "post",
        success: function(e){
          swal("Success!", e.result, "success");
        },
        error: function(){
          swal("Error!", e.result, "error");
        }
      });
    })
  });
</script>
@endsection