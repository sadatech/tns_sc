@extends('layouts.app')
@section('title', "Sales Report - Price vs Competitor")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Price vs Competitor <small>Report</small></h2>
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
            <div class="col-md-4">
              <label>Store:</label>
              <select class="form-control" id="filterStore" name="store"></select>
            </div>
            <div class="col-md-4">
              <label>Periode:</label>
              <input class="js-datepicker form-control" type="text" placeholder="Select Periode" name="periode" data-month-highlight="true" value="{{ Carbon\Carbon::now()->format('m/Y') }}" required>
            </div>
          </div>
          <button type="submit" class="btn btn-outline-danger btn-square mt-10">Filter Data</button>
          <input type="reset" id="reset" class="btn btn-outline-secondary btn-square mt-10" value="Reset Filter"/>
        </form>
      </div>
    </div>
  </div>
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-cog mr-2"></i>Setting Main Competitor</button>
          </h3>
          <div class="block-option">
            <a id="btnDownloadXLS" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</a>
            <a id="btnDownloadXLSAll" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</a>
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="reportTable">
        <thead>
          <tr>
            <th style="vertical-align: middle; text-align: center;">CATEGORY</th>
            <th style="vertical-align: middle; text-align: center;">SASA</th>
            <th style="vertical-align: middle; text-align: center;">MAIN KOMPETITOR</th>
            <th style="vertical-align: middle; text-align: center;">BRAND KOMPETITOR</th>
            <th style="vertical-align: middle; text-align: center;">SASA Price</th>
            <th style="vertical-align: middle; text-align: center;">KOMPETITOR Price</th>
            <th style="vertical-align: middle; text-align: center;">INDEX</th>
          </tr>

        </thead>
        </table>

      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="tambahModal" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Price vs Competitor</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('priceData.store') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">

          <div class="row">
            <div class="form-group col-md-6">
              <label>Produk</label>
            </div>
            <div class="form-group col-md-6">
              <label>Main Competitor</label>
            </div>
          </div>
          @foreach($subCategories as $subcategory)
            @php
            $products = "products".$subcategory->id;
            $productCompetitors = "productCompetitors".$subcategory->id;
            @endphp
              @foreach($$products as $product)
                <div class="row">
                  <div class="form-group col-md-6">
                    <h5><small>{{ $subcategory->name }} -</small> {{ $product->name }} </h5>
                    <input type="text" name="products[]" id="products" class="form-control" value="{{ $product->id }}" hidden>
                  </div>
                  <div class="form-group col-md-6">
                    <select name="competitors[]" id="competitors" class="form-control js-select">
                      <option value="">(none)</option>
                      @foreach($$productCompetitors as $productCompetitor)
                      <option value="{{ $productCompetitor->id }}"@if($product->id_main_competitor == "$productCompetitor->id") selected @endif>{{ $productCompetitor->brand_name .'-'. $productCompetitor->name }}</option>
                      @endforeach
                    </select>
                  </div>

                </div>
              @endforeach
          @endforeach

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-alt-success">
            <i class="fa fa-save"></i> Save
          </button>
          <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
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
      $('#filterAccount,#filterStore,#filterArea').val(null).trigger('change');
    }, 10);
  });
  $('#filterAccount').select2(setOptions('{{ route("account-select2") }}', 'Choose your Account', function (params) {
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
  $('#filterStore').select2(setOptions('{{ route("store-select2") }}', '{{App\Store::first()->name1}}', function (params) {
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

  $("#btnDownloadXLS, #btnDownloadXLSAll").on("click", function(){
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
    var url = '{!! route('priceData.dataVs') !!}';
    table = $('#reportTable').DataTable({
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
            $('#table-block').show();
            return res.data;
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
        $("#btnDownloadXLS").attr("target-url","{{ route('priceData.dataVs.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val() + "&limit=" + $("#reportTable_length select").val());
        $("#btnDownloadXLSAll").attr("target-url","{{ route('priceData.dataVs.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val());
      },
      columns: [
        { data: 'category_name', name: 'category_name'},
        { data: 'name', name: 'name'},
        { data: 'competitor_name', name: 'competitor_name'},
        { data: 'competitor_brand', name: 'competitor_brand'},
        { data: 'price', name: 'price'},
        { data: 'price_competitor', name: 'price_competitor'},
        { data: 'index', name: 'index'},
      ],
      bDestroy: true
    });
  });

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
              ajax: {
                url: '{!! route('priceData.dataVs') !!}',
                type: 'POST',
              },
              drawCallback: function(){
                $("#btnDownloadXLS").attr("target-url","{{ route('priceData.dataVs.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val() + "&limit=" + $("#reportTable_length select").val());
                $("#btnDownloadXLSAll").attr("target-url","{{ route('priceData.dataVs.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val());
              },
              columns: [
                { data: 'category_name', name: 'category_name'},
                { data: 'name', name: 'name'},
                { data: 'competitor_name', name: 'competitor_name'},
                { data: 'competitor_brand', name: 'competitor_brand'},
                { data: 'price', name: 'price'},
                { data: 'price_competitor', name: 'price_competitor'},
                { data: 'index', name: 'index'},
              ],
              "scrollX":        true, 
              "scrollCollapse": true,
          });
      });

  </script>
@endsection