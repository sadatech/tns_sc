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
      <h3 class="block-title">Filter</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <form method="post" id="filter">
          <div class="row">
            <div class="col-md-4">
              <label>Periode:</label>
              <input class="js-datepicker form-control" type="text" placeholder="Select Periode" name="periode" data-month-highlight="true" value="{{ Carbon\Carbon::now()->format('m/Y') }}">
            </div>
          </div>
          <button type="submit" class="btn btn-outline-danger btn-square mt-10">Filter Data</button>
          <input type="reset" id="reset" class="btn btn-outline-secondary btn-square mt-10" value="Reset Filter"/>
        </form>
      </div>
    </div>
  </div>
  <div class="block block-themed" id="table-block" style="display: none">
    <!-- <div class="block block-themed">  -->
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

          <center><h3>AREA</h3></center>
          <table class="table table-striped table-vcenter js-dataTable-full" id="reportTableArea">
            <thead>
              <th class="text-center" style="width: 70px;">no</th>
              <th>AREA</th>
              @foreach ($categories as $category)
              <th>{{ $category->name }}</th>
              @endforeach
            </thead>
          </table>

          <div class="block-header p-0 mb-20">
          </div>
          <center><h3>ACCOUNT</h3></center>
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
    setTimeout(function() {
      $('.js-datepicker').val(null);
    }, 10);
  });
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
</script>
<script>
  $('#filter').submit(function(e) {
    Codebase.layout('header_loader_on');
    e.preventDefault();
    var table = null;
    var url = '{!! route('availability.dataArea') !!}';
    table = $('#reportTableArea').DataTable({
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
      },
      columns: [
      { data: 'id', name: 'id'},
      { data: 'area', name: 'area'},
      @foreach($categories as $category)
      {data: 'item_{{ $category->name }}', name: 'item_{{ $category->name }}', searchable: false, sortable: false},
      @endforeach
      ],
      bDestroy: true
    });

    url = '{!! route('availability.dataAccount') !!}';
    table = $('#reportTableAccount').DataTable({
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
      },
      columns: [
      { data: 'id', name: 'id'},
      { data: 'area', name: 'area'},
      @foreach($categories as $category)
      {data: 'item_{{ $category->name }}', name: 'item_{{ $category->name }}', searchable: false, sortable: false},
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
<script>
        $('#reportTableArea').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('availability.dataArea') !!}',
            drawCallback: function(){
              $("#btnDownloadXLSAll").attr("target-url","{{ route('availability.exportXLS') }}");
              $("#btnDownloadXLS").attr("target-url","{{ route('availability.exportXLS') }}?limitArea="+$("#reportTableArea_length select").val()+"&limitAccount="+$("#reportTableAccount_length select").val());
            },
            columns: [
              { data: 'id', name: 'id'},
              { data: 'area', name: 'area'},
              @foreach($categories as $category)
              {data: 'item_{{ $category->name }}', name: 'item_{{ $category->name }}', searchable: false, sortable: false},
              @endforeach
            ],
            "scrollX":        true, 
            "scrollCollapse": true,
        });

        $('#reportTableAccount').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('availability.dataAccount') !!}',
            drawCallback: function(){
              $("#btnDownloadXLSAll").attr("target-url","{{ route('availability.exportXLS') }}");
              $("#btnDownloadXLS").attr("target-url","{{ route('availability.exportXLS') }}?limitArea="+$("#reportTableArea_length select").val()+"&limitAccount="+$("#reportTableAccount_length select").val());
            },
            columns: [
              { data: 'id', name: 'id'},
              { data: 'area', name: 'area'},
              @foreach($categories as $category)
              {data: 'item_{{ $category->name }}', name: 'item_{{ $category->name }}', searchable: false, sortable: false},
              @endforeach
            ],
            "scrollX":        true, 
            "scrollCollapse": true,
        });

      });
  </script>
@endsection