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

        <div class="block-header p-0 mb-20">
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTable">
          <thead>
            <tr>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">DATE</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">STORE NAME</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">ACCOUNT</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">AREA</th>
              <th rowspan="2" style="vertical-align: middle; text-align: center;">CEK/NO</th>
              @foreach ($categories as $category)
              <th colspan="{{ App\Product::join('sub_categories','products.id_subcategory','sub_categories.id')
              ->join('categories','sub_categories.id_category', 'categories.id')
              ->where('categories.id',$category->id)
              ->count() }}" style="vertical-align: middle; text-align: center;">{{ $category->name }}</th>
              @endforeach
            </tr>
            <tr>
              @foreach ($categories as $category)
              @foreach (App\Product::join('sub_categories','products.id_subcategory','sub_categories.id')
              ->join('categories','sub_categories.id_category', 'categories.id')
              ->where('categories.id',$category->id)->select('products.*')->get() as $product)
              <th style="vertical-align: middle; text-align: center;">{{ $product->name }}</th>
              @endforeach
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/magnific-popup/magnific-popup.css') }}">
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
<script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">

 $('#reset').click(function(){
  $('.js-datepicker').val(null);
  setTimeout(function() {
    $('#filterEmployee,#filterStore,#filterArea').val(null).trigger('change');
  }, 10);
});
 $('#filterEmployee').select2(setOptions('{{ route("employee-select2") }}', 'Choose your Employee', function (params) {
  return filterData('name', params.term);
}, function (data, params) {
  return {
    results: $.map(data, function (obj) {                                
      return {id: obj.id, text: obj.name}
    })
  }
}));
 $('#filterArea').select2(setOptions('{{ route("area-select2") }}', 'Choose your Area', function (params) {
  return filterData('name', params.term);
}, function (data, params) {
  return {
    results: $.map(data, function (obj) {                                
      return {id: obj.id, text: obj.name}
    })
  }
}));
 $('#filterStore').select2(setOptions('{{ route("store-select2") }}', 'Choose your Store', function (params) {
  return filterData('store', params.term);
}, function (data, params) {
  return {
    results: $.map(data, function (obj) {                                
      return {id: obj.id, text: obj.name1}
    })
  }
}));
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

   $('#filter').submit(function(e) {
    Codebase.layout('header_loader_on');
    e.preventDefault();
    var table = null;
    var url = '{!! route('availability.dataAccountRow') !!}';
    table = $('#reportTable').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: "300px",
      ajax: {
        url: url + "?" + $("#filter").serialize(),
        type: 'GET',
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
      drawCallback: function(){
        $('.popup-image').magnificPopup({
          type: 'image',
        });
        // $("#btnDownloadXLS").attr("target-url","{{ route('export.smd.new-cbd') }}"+"/"+$(".js-datepicker").val()+"/"+$("#filterEmployee").val()+"/"+$("#filterStore").val()+"/new");
      },
      columns: [
      { d{ data: 'avai_date', name: 'avai_date'},
      { data: 'name1', name: 'name1'},
      { data: 'account_name', name: 'account_name'},
      { data: 'area_name', name: 'area_name'},
      { data: 'cek', name: 'cek'},
      @foreach($categories as $category)
      @foreach(App\Product::join('sub_categories','products.id_subcategory','sub_categories.id')
        ->join('categories','sub_categories.id_category', 'categories.id')
        ->where('categories.id',$category->id)->select('products.*')->get() as $product)
      { data: '{{ $category->id }}_{{ $product->id }}', name: '{{ $category->id }}_{{ $product->id }}', searchable: false, sortable: false},
      @endforeach
      @endforeach
      ],
      bDestroy: true
    });
  });
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

 </script>
 @endsection