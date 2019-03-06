@extends('layouts.app')
@section('title', "Sales Report - Price ")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Price <small>Report</small></h2>
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
              <label>Account:</label>
              <select class="form-control" id="filterAccount" name="account"></select>
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
          <div class="block-option">
            <a id="btnDownloadXLS" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</a>
            <a id="btnDownloadXLSAll" target="_blank" href="javascript:" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</a>
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTable">
        <thead>
          <!-- <tr>
            <th style="vertical-align: middle; text-align: center;">CATEGORY</th>
            <th style="vertical-align: middle; text-align: center;">PRODUCT</th>
            <th style="vertical-align: middle; text-align: center;">PACKAGING</th>
            @foreach ($stores as $store)
            <th style="vertical-align: middle; text-align: center;">{{ $store->name1 }}</th>
            @endforeach
            <th style="vertical-align: middle; text-align: center;">LOWEST</th>
            <th style="vertical-align: middle; text-align: center;">HIGHEST</th>
            <th style="vertical-align: middle; text-align: center;">HIGHEST VS LOWEST</th>
          </tr> -->

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
      $('#filterAccount,#filterStore,#filterArea').val(null).trigger('change');
    }, 10);
  });
  $('#filterAccount').select2(setOptions('{{ route("account-select2") }}', '{{App\Account::first()->name}}', function (params) {
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

    if($.fn.dataTable.isDataTable('#reportTable'))
    {
        $('#reportTable').DataTable().clear();
        $('#reportTable').DataTable().destroy();
    }

    var table = null;
    var url = '{!! route('priceData.dataRow') !!}';

    listHeader = [
          { title:'CATEGORY', data: 'category_name', name: 'category_name'},
          { title:'PRODUCT', data: 'brand_name', name: 'brand_name'},
          { title:'PACKAGING', data: 'name', name: 'name'},
      ];

      $.ajax({
        url: url + "?" + $("#filter").serialize() + "&storeList=yes",
        type: 'POST',
        dataType: 'json',
        success: function(e){
          console.log(e)
          e.forEach(function(store){
            listHeader.push({title:store.name1,  data: store.name1+"_price", name: store.name1+"_price"});
          })

          listHeader.push({title:'LOWEST',  data: 'lowest', name: 'lowest'});
          listHeader.push({title:'HIGHEST',  data: 'highest', name: 'highest'});
          listHeader.push({title:'HIGHEST VS LOWEST',  data: 'vs', name: 'vs'});

          table = $('#reportTable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: "300px",
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
        $("#btnDownloadXLS").attr("target-url","{{ route('priceData.row.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val() + "&account=" + $("#filterAccount").val() + "&limit=" + $("#reportTable_length select").val());
        $("#btnDownloadXLSAll").attr("target-url","{{ route('priceData.row.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val() + "&account=" + $("#filterAccount").val());
            },
            columns: listHeader,
            bDestroy: true
          });


        }
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
      $(function() {
          $('#reportTable').DataTable({
              processing: true,
              serverSide: true,
              ajax: {
                url: '{!! route('priceData.dataRow') !!}',
                type: 'POST',
              },
              drawCallback: function(){
        $('.popup-image').magnificPopup({
          type: 'image',
        });
        $("#btnDownloadXLS").attr("target-url","{{ route('priceData.row.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val() + "&account=" + $("#filterAccount").val() + "&limit=" + $("#reportTable_length select").val());
        $("#btnDownloadXLSAll").attr("target-url","{{ route('priceData.row.exportXLS') }}"+"?periode=" + $(".form-control[name=periode]").val() + "&account=" + $("#filterAccount").val());
      },
              columns: [
          { title:'CATEGORY', data: 'category_name', name: 'category_name'},
          { title:'PRODUCT', data: 'brand_name', name: 'brand_name'},
          { title:'PACKAGING', data: 'name', name: 'name'},
            @foreach($stores as $store)
              { title:'{{ $store->name1 }}', data: '{{ $store->name1 }}_price', name: '{{ $store->name1 }}_price', searchable: false, sortable: false},
            @endforeach
          { title:'LOWEST', data: 'lowest', name: 'lowest'},
          { title:'HIGHEST', data: 'highest', name: 'highest'},
          { title:'HIGHEST VS LOWEST', data: 'vs', name: 'vs'},
              ],
              "scrollX":        true, 
              "scrollCollapse": true,
          });
      });



  </script>
@endsection
