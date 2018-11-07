@extends('layouts.app')
@section('title', "Sales Report - Availability")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Availability <small>Report</small></h2>
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

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTableArea">
        <thead>
          <th class="text-center" style="width: 70px;">no</th>
          <th>AREA</th>
          @foreach ($categories as $category)
          <th>{{ $category->name }}</th>
          @endforeach
        </thead>
        </table>

      </div>
    </div>
  </div>

  <div class="block block-themed"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
          <div class="block-option">
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</button>
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</button>
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTableAccount">
        <thead>
          <th class="text-center" style="width: 70px;">no</th>
          <th>ACCOUNT</th>
          @foreach ($categories as $category)
          <th>{{ $category->name }}</th>
          @endforeach
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
          $('#reportTableArea').DataTable({
              processing: true,
              serverSide: true,
              ajax: '{!! route('availability.dataArea') !!}',
              columns: [
                { data: 'test0', name: 'test0'},
                { data: 'test1', name: 'test1'},
                { data: 'test2', name: 'test2'},
                { data: 'test3', name: 'test3'},
              ],
              "scrollX":        true, 
              "scrollCollapse": true,
          });
      });

      $(function() {
          $('#reportTableAccount').DataTable({
              processing: true,
              serverSide: true,
              ajax: '{!! route('availability.dataAccount') !!}',
              columns: [
                { data: 'test0', name: 'test0'},
                { data: 'test1', name: 'test1'},
                { data: 'test2', name: 'test2'},
                { data: 'test3', name: 'test3'},
              ],
              "scrollX":        true, 
              "scrollCollapse": true,
          });
      });

  </script>
@endsection