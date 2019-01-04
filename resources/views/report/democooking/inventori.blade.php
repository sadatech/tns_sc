@extends('layouts.app')
@section('title', "Properti DC")
@section('content')
<div class="content">
    @if($errors->any())
    <div class="alert alert-danger">
        <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
        @foreach ($errors->all() as $error)
        <div> {{ $error }}</div>
        @endforeach
    </div>
    @endif
    <h2 class="content-heading pt-10">Inventori <small>Report</small></h2>

    <div class="block block-themed block-mode-loading-refresh">
      <div class="block-header bg-primary">
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
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Employee</div>
                  <select id="filterEmployee" class="inputFilter" name="id_area"></select>
              </div>
          </div>
          <div class="row col-sm-12 col-md-12">
            <p class="btn btn-sm btn-primary" id="filterSearch"><i class="fa fa-search"></i> Search</p>
            <p class="btn btn-sm btn-success" id="filterSearch" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus"></i> Add Data</p>
            <p class="btn btn-sm btn-danger" id="filterReset"><i class="fa fa-refresh"></i> Clear</p>
        </div>
    </form>
</div>
</div>

<div class="block block-themed" style="display: none;" id="panelTable"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title" id="titleDatatable">Datatables</h3>
    </div>
    <div class="block">        
        <div class="block-content block-content-full">
            <div class="block-header p-0 mb-20">
                <h3 class="block-title">
                </h3>
                <div class="block-option">
                    <!-- <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button> -->
                    <a target-url="{{ route('dc.inventori.data.exportXLS') }}" id="btnDownloadXLS" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                </div>
            </div>
            <table class="table table-striped table-vcenter js-dataTable-full" id="reportTable">
                <thead>
                    <th class="text-center" style="width: 6px;">#</th>
                    <th>Employee</th>
                    <th style="width: 25%;">Item</th>
                    <th style="width: 5px;">Quantity</th>
                    <th style="width: 5px;">Actual</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Description</th>
                    <th>Dokumentasi</th>
                    <!-- <th style="width: 15%;"> Action</th> -->
                </thead>
            </table>
        </div>  
    </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-plus"></i> Add Inventory</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form action="{{ route('dc.inventori.data.add') }}" method="post">
                {!! csrf_field() !!}
                <div class="block-content">
                    <div class="form-group">
                        <label>No Polisi</label>
                        <input type="text" class="form-control" name="no_polisi" placeholder="Add new No Polisi" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Employee</label>
                        <select id="filterEmployeeAdd" class="form-control inputFilter" name="id_employee"></select>
                    </div>
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

</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<style type="text/css">
[data-notify="container"] {
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
    function editModal(json) {
        $('#editModal').modal('show');
        $('#editForm').attr('action', "{{ url('/propertiDc/update') }}/"+json.id);
        $('#nameInput').val(json.item);
    }
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

    });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
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

    $('#filterEmployee').select2(setOptions('{{ route("employee-is-tl-select2") }}', 'Select Employee', function (params) {
        return filterData('name', params.term);
    }, function (data, params) {
        return {
          results: $.map(data, function (obj){
            return {id: obj.id, text: obj.name}
        })
      }
  }));
    $('#filterEmployeeAdd').select2(setOptions('{{ route("employee-is-tl-select2") }}', 'Select Employee', function (params) {
        return filterData('name', params.term);
    }, function (data, params) {
        return {
          results: $.map(data, function (obj){
            return {id: obj.id, text: obj.name}
        })
      }
  }));

    $("#filterReset").click(function (){
      $.each($('#filterForm select'), function(key, value) {
        $('#'+this.id).val(null).trigger('change');
    });
      $('#panelTable').slideUp();
  })


    $("#filterSearch").click(function(){
        if($('#filterEmployee').val() == null){
            swal("Warning!", "Please select Employee First!", "warning");
            return;
        }

        $("#filterEmployeeAdd").val($("#filterEmployee").val());

        if($.fn.dataTable.isDataTable('#reportTable'))
        {
            $('#reportTable').DataTable().clear();
            $('#reportTable').DataTable().destroy();
        }

        $('#panelTable').slideDown();

        $('#reportTable').DataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            // event scroll after datatable loaded
            "drawCallback": function(){
                setTimeout(function(){
                    $('html, body').animate({scrollTop: ($('#panelTable').offset().top - 75)}, 500);
                }, 10);
            },
            "filter": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '{!! route('dc.inventori.data') !!}/' + $('#filterEmployee').val(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
              },
          },
          "columns": [
          { data: 'id', name: 'id' },
          { data: 'employee', name: 'employee'},
          { data: 'item', name: 'item'},
          { data: 'quantity', name: 'quantity'},
          { data: 'actual', name: 'actual'},
          { data: 'status', name: 'status'},
          { data: 'description', name: 'description'},
          { data: 'dokumentasi', name: 'dokumentasi'}
          ]
      });

        $.each($('#filterForm select'), function(key, value) {
            $('#'+this.id).val(null).trigger('change');
        });
    });

})
</script>
@endsection