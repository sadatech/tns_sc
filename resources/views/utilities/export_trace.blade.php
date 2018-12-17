@extends('layouts.app')
@section('title', "Sales Report - Sell In")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Download Exported File(s) </h2>
  @if($errors->any())
    <div class="alert alert-danger">
      <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
      @foreach ($errors->all() as $error)
      <div> {{ $error }}</div>
      @endforeach
    </div>
  @endif
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
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Title</div>
                  <input type="text" id="filterTitle" class="inputFilter form-control" name="title" placeholder="Title">
              </div>    
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <div class="font-size-sm font-w600 text-uppercase text-muted">Status</div>
                  <select id="filterStatus" class="inputFilter form-control" name="status">
                    <option value="PROCESSING">PROCESSING</option>
                    <option value="DONE">DONE</option>
                    <option value="FAILED">FAILED</option>
                  </select>
              </div>          
              <div class="col-4 col-sm-4 text-center text-sm-left">
                  <span>
                    <i class="fa fa-calendar"></i> Date
                  </span>
                  <input type="text" id="filterDate" class="form-control" placeholder="Date" name="date">
              </div>
              
          </div>
          <div class="row col-sm-12 col-md-12">
            <p class="btn btn-sm btn-primary" id="filterSearch" onclick="filteringReportWithoutWidth(paramFilter)"><i class="fa fa-search"></i> Search</p>
            <p class="btn btn-sm btn-danger" id="filterReset" onclick="triggerResetWithoutWidth(paramReset)"><i class="fa fa-refresh"></i> Clear</p>
          </div>
        </form>
      </div>
  </div>
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sea pl-20 pr-20 pt-15 pb-15">
      <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">


        <table class="table table-striped table-vcenter js-dataTable-full" id="traceTable">
          <thead> 
            <th class="text-center" style="width: 70px;"></th>    
            <th>Date</th>
            <th>Title</th>
            <th>Status</th>
            <th>REQUEST BY</th>
            <th>Action</th>
          </thead>
        </table>

      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="si si-speech"></i>  Explanation for this request</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="editForm" method="post">
                {!! csrf_field() !!}
                <div class="block-content">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label style="margin-bottom: 10px; "><b>Explanation</b></label>
                            <textarea class="form-control" name="explanation" id="explanation"></textarea>
                            <!-- <input type="text" class="form-control" name="explanation" id="explanation" required> -->
                            <div id="explanationText"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-alt-success" id="saveModal">
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
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
  <script src="{{ asset('assets/js/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
  <script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
  <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('js/select2-handler.js') }}"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <script src="{{ asset('js/daterangepicker.js') }}"></script>
  <script src="{{ asset('js/datetimepicker-handler.js') }}"></script>
  <script type="text/javascript">

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

        var table = 'traceTable';
        var filterId = ['#filterRegion', '#filterArea', '#filterSubArea', '#filterStore', '#filterEmployee'];
        var url = "{!! route('export-download.data') !!}";
        var order = [ [0, 'desc']];
        var columnDefs = [{"className": "dt-center", "targets": [0, 4]}];
        var tableColumns = [
                { data: 'id', name: 'id', visible: false},
                { data: 'date', name: 'date'},
                { data: 'title', name: 'title'},
                { data: 'status', name: 'status'},
                { data: 'request_by', name: 'request_by'},
                { data: 'action', name: 'action'}];

        var exportButton = '#export';

        var paramFilter = [table, $('#'+table), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, table, $('#'+table), url, tableColumns, columnDefs, order, exportButton, '#filterMonth'];


      $(document).ready(function() {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }                  

          });

          $('#filterDate').datetimepicker({
              format: "dd MM yyyy",
              startView: "2",
              minView: "2",
              autoclose: true,
          });

          $('#filterDate').val(null);
          $('#filterTitle').val(null);

          $('#filterStatus').select2({
            placeholder: "Select Status",
          });

          $.each($('#filterForm select'), function(key, value) {
            $('#'+this.id).val(null).trigger('change')
          })

          // console.log(getParam());

          // RENDER TABLE
          $('#'+table).dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url + "?" + getParam(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter": false,
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
            "autoWidth": false,
            colReorder: {
                realtime: true
            }
          });

    });
      

    $("#filterReset").click(function () {

      $.each($('#filterForm select'), function(key, value) {
        $('#'+this.id).val(null).trigger('change')
      })

      $('#filterDate').val(null);

      $('#filterTitle').val(null);
    })

    $("#filterSearch").click(function() {
      console.log(getParam())
    })


    function editModal(json) {
        
        $('#editModal').modal('show');
        $('#editForm').attr('action', "{{ url('/utility/export-download/explain') }}/"+json.id);
        $('#explanation').val(json.text);

        if(json.type == 'edit'){
          document.getElementById("saveModal").style.display = "";
          document.getElementById("explanation").style.display = "";
          document.getElementById("explanationText").innerHTML = "";
        }

        if(json.type == 'show'){
          document.getElementById("saveModal").style.display = "none";
          document.getElementById("explanation").style.display = "none";
          document.getElementById("explanationText").innerHTML = json.text;
        }
    }

    function getParam(){
      console.log('TEST : '+$('#filterTitle').val()+'-'+$('#filterStatus').val()+'-'+$('#filterDate').val())
      var res = '';

      if($('#filterTitle').val() != null && $('#filterTitle').val() != ''){
        if(res == ''){
          res += 'title='+$('#filterTitle').val()
        }else{
          res += '&title='+$('#filterTitle').val()
        }
      }else{

      }

      if($('#filterStatus').val() != null && $('#filterStatus').val() != ''){
        if(res == ''){
          res += 'status='+$('#filterStatus').val()
        }else{
          res += '&status='+$('#filterStatus').val()
        }
      }

      if($('#filterDate').val() != null && $('#filterDate').val() != ''){
        if(res == ''){
          res += 'date='+$('#filterDate').val()
        }else{
          res += '&date='+$('#filterDate').val()
        }
      }

      return res;
    }

  </script>
@endsection