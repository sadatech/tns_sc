@extends('layouts.app')
@section('title', "Attendance Report")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Attendance Report <small>Manage</small></h2>
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
                      <div class="font-size-sm font-w600 text-uppercase text-muted">Region</div>
                      <select id="filterRegion" class="inputFilter" name="id_reg"></select>
                  </div>
                  <div class="col-4 col-sm-4 text-center text-sm-left">
                      <div class="font-size-sm font-w600 text-uppercase text-muted">Area</div>
                      <select id="filterArea" class="inputFilter" name="id_ar"></select>
                  </div>
                  <div class="col-4 col-sm-4 text-center text-sm-left">
                      <div class="font-size-sm font-w600 text-uppercase text-muted">Sub Area</div>
                      <select id="filterSubArea" class="inputFilter" name="id_sar"></select>
                  </div>
                  <div class="col-4 col-sm-4 text-center text-sm-left">
                      <div class="font-size-sm font-w600 text-uppercase text-muted">Store</div>
                      <select id="filterStore" class="inputFilter" name="id_str"></select>
                  </div>
                  <div class="col-4 col-sm-4 text-center text-sm-left">
                      <div class="font-size-sm font-w600 text-uppercase text-muted">Employee</div>
                      <select id="filterEmployee" class="inputFilter" name="id_emp"></select>
                  </div>
                  <div class="col-4 col-sm-4 text-center text-sm-left">
                      <div class="font-size-sm font-w600 text-uppercase text-muted">Role</div>
                      <select id="filterRole" class="inputFilter" name="id_role">
                        <option value="">-</option>
                        <option value="spgmtc">SPG</option>
                        <option value="mdmtc">MD</option>
                      </select>
                  </div>
              </div>
              <div class="row col-sm-12 col-md-12">
                <p class="btn btn-sm btn-primary" id="filterSearch" onclick="filteringReportWithoutSearch(paramFilter)"><i class="fa fa-search"></i> Search</p>
                <p class="btn btn-sm btn-danger" id="filterReset" onclick="triggerResetWithoutSearch(paramReset)"><i class="fa fa-refresh"></i> Clear</p>
              </div>
            </form>
          </div>
      </div>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <!-- <h3 class="block-title">
                        <a href="{{ route('tambah.faq') }}" class="btn btn-primary btn-square" title="Add Data Store"><i class="fa fa-plus mr-2"></i>Add Data</a>
                    </h3> -->
                    <div class="block-option">
                        <a href="{{route('attendance.exportXLS')}}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </div>
                </div>

                <table class="table table-striped table-vcenter js-dataTable-full dataTable" id="attendanceTable">
                    <thead>
                        <th width="70px">NIK</th>
                        <th width="100px">Name</th>
                        <th width="30px">Role</th>
                        <th width="30px">Attendance</th>
                        <th width="2600px">Attendance Detail</th>
                    </thead>
                </table>
            </div> 
        </div> 
    </div>


<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="modal-slideup" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-slideup" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="name"></h3>
                  <!--   <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div> -->
                </div>
                <div class="block-content">
                    <div class="block">
        
        <div class="block-content block-content-full">
            <ul class="list list-timeline list-timeline-modern pull-t" style="left: -100px !important;" id="attendance-detail">
            </ul>
        </div>
    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
  
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/magnific-popup/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
    <style type="text/css">
        [data-notify="container"] {
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .pac-container {
            z-index: 99999;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('js/select2-handler.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/datetimepicker-handler.js') }}"></script>
    <script type="text/javascript">

      var table = 'attendanceTable';
        var filterId = ['#filterRegion', '#filterArea', '#filterSubArea', '#filterStore', '#filterEmployee', '#filterRole'];
        var url = "{!! route('attendance.data') !!}";
        var order = [ [0, 'desc']];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [
                { data: 'nik', name: 'nik' },
                { data: 'employee', name: 'employee' },
                { data: 'role', name: 'role' },
                { data: 'attendance', name: 'attendance' },
                { data: 'attendance_detail', name: 'attendance_detail' }];

        var exportButton = '#export';

        var paramFilter = [table, $('#'+table), url, tableColumns, columnDefs, order, exportButton];

        var paramReset = [filterId, table, $('#'+table), url, tableColumns, columnDefs, order, exportButton, '#filterMonth'];


       function detailModal(json) {
        $('#detailModal').modal('show');
        $('#name').text(json.name);
        $('#attendanceDetail').text(json.attendanceDetail);
        $('#checkin').val(json.checkin);
        $('#checkout').val(json.checkout);
        $('#keterangan').val(json.keterangan);
        $('#date').val(json.date);

        console.log(json);
        $('#attendance-detail').html('');
        $.each(json.attandaceDetail, function(k, v){
          $('#attendance-detail').append(
              '<li>' +
                  '<i class="list-timeline-icon fa fa-building bg-info fa-2x"></i>' +
                  '<div class="list-timeline-content">' +
                      '<h4 class="font-w600" id="name"><b>'+  v.store.name1 +'</b></h4>' +
                      '<p">Place : '+  v.place.name +'</p>' +
                      '<p>Checkin  : '+ v.checkin +'</p>' +
                      '<p>Checkout :'+  v.checkout +'</p>' +
                  '</div>' +
              '</li>'
            )
        })
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

    $(document).ready(function() {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }                  

          });          

          $('#filterRegion').select2(setOptions('{{ route("region-select2") }}', 'Select Region', function (params) {
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          $('#filterArea').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          $('#filterSubArea').select2(setOptions('{{ route("sub-area-select2") }}', 'Select Sub Area', function (params) {
            return filterData('name', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          $('#filterStore').select2(setOptions('{{ route("store-select2") }}', 'Select Store', function (params) {
            return filterData('store', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name1}
              })
            }
          }));

          $('#filterEmployee').select2(setOptions('{{ route("employee-select2-for-report") }}', 'Select Employee', function (params) {
            filters['employeeMtc'] = 'test';
            return filterData('employee', params.term);
          }, function (data, params) {
            return {
              results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
              })
            }
          }));

          $('#filterRole').select2({
              width: '100%',
              placeholder: 'Select Role'
          });

          // TABLE RENDER
          $('#attendanceTable').dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url + "?" + $("#filterForm").serialize(),
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
            "ordering": false,
            "searching": false
          });

    });

    $("#filterReset").click(function () {

      $.each($('#filterForm select'), function(key, value) {
        $('#'+this.id).val(null).trigger('change')
      })

    })

    $("#filterSearch").click(function() {
      var serial = $("#filterForm").serialize()
      console.log(serial)
    })

    </script>
@endsection
