@extends('layouts.app')
@section('title', "Sales Report - Availability")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Display Share <small>Report</small></h2>
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
          <div class="block-option">
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</button>
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</button>
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTable">
        <thead>
          <tr>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">REGION</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">AREA</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">TL</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">NAMA SPG</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">JABATAN</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">OUTLET</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">ACCOUNT</th>
            @foreach ($categories as $category)
            <th colspan="{{ ($jml_brand + 1)*2 }}" style="vertical-align: middle; text-align: center;">{{ $category->name }}</th>
            @endforeach
          </tr>
          <tr>
            @foreach ($categories as $category)
              @foreach ($brands as $brand)
                <th colspan="2" style="vertical-align: middle; text-align: center;">{{ $brand->name }}</th>
              @endforeach
                <th colspan="2" style="vertical-align: middle; text-align: center;">Total</th>
            @endforeach
          </tr>
          <tr>
            @foreach ($categories as $category)
              @foreach ($brands as $brand)
                <th style="vertical-align: middle; text-align: center;">tier</th>
                <th style="vertical-align: middle; text-align: center;">depth</th>
              @endforeach
              <th style="vertical-align: middle; text-align: center;">tier</th>
              <th style="vertical-align: middle; text-align: center;">depth</th>
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

      //   $(document).ready(function() {
      //   $.ajaxSetup({
      //     headers: {
      //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      //     }
      //   });

      //   $('#employeeSelect').select2(setOptions('{{ route("employee-select2") }}', 'Select Employee', function (params) {
      //     return filterData('employee', params.term);
      //   }, function (data, params) {
      //     return {
      //       results: $.map(data, function (obj) {                                
      //         return {id: obj.id, text: obj.nik+' - '+obj.name}
      //       })
      //     }
      //   }));

      //   $('#storeSelect').select2(setOptions('{{ route("store-select2") }}', 'Select Store', function (params) {
      //     return filterData('store', params.term);
      //   }, function (data, params) {
      //     return {
      //       results: $.map(data, function (obj) {                                
      //         return {id: obj.id, text: obj.name1+' - '+obj.name2}
      //       })
      //     }
      //   }));


      //   $('#productSelect').select2(setOptions('{{ route("product-select2") }}', 'Select Product', function (params) {
      //     return filterData('product', params.term);
      //   }, function (data, params) {
      //     return {
      //       results: $.map(data, function (obj) {                                
      //         return {id: obj.id, text: obj.name+' ('+obj.deskripsi+')'}
      //       })
      //     }
      //   }));

      // });

//   $("#datepicker").datepicker( {
//     format: "mm-yyyy",
//     viewMode: "months", 
//     minViewMode: "months"
// });
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
              ajax: '{!! route('display_share.dataSpg') !!}',
              columns: [
                { data: 'region_name', name: 'region_name'},
                { data: 'area_name', name: 'area_name'},
                { data: 'tl_name', name: 'tl_name'},
                { data: 'emp_name', name: 'emp_name'},
                { data: 'jabatan', name: 'jabatan'},
                { data: 'store_name', name: 'store_name'},
                { data: 'account_name', name: 'account_name'},
                @foreach($categories as $category)
                  @foreach($brands as $brand)
                    {data: '{{ $category->id }}_{{ $brand->id }}_tier', name: '{{ $category->id }}_{{ $brand->id }}_tier', searchable: false, sortable: false},
                    {data: '{{ $category->id }}_{{ $brand->id }}_depth', name: '{{ $category->id }}_{{ $brand->id }}_depth', searchable: false, sortable: false},
                  @endforeach
                  {data: '{{ $category->id }}_total_tier', name: '{{ $category->id }}_total_tier', searchable: false, sortable: false},
                  {data: '{{ $category->id }}_total_depth', name: '{{ $category->id }}_total_depth', searchable: false, sortable: false},
                @endforeach
              ],
              "scrollX":        true, 
              "scrollCollapse": true,
          });
      });


  </script>
@endsection
