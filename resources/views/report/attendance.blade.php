@extends('layouts.app')
@section('title', "Attendance Report")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Attendance Report <small>Manage</small></h2>
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

                <table class="table table-striped table-vcenter js-dataTable-full dataTable" id="faqtable">
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
    <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script type="text/javascript">
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
    $(function() {
        $('#faqtable').DataTable({
            processing: true,
            scrollX: true,
            drawCallback: function(){
              $('.popup-image').magnificPopup({
                    type: 'image',
                }); 
                $('.js-swal-delete').on('click', function(){
                    var url = $(this).data("url");
                    swal({
                        title: 'Are you sure?',
                        text: 'You will not be able to recover this data!',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d26a5c',
                        confirmButtonText: 'Yes, delete it!',
                        html: false,
                        preConfirm: function() {
                            return new Promise(function (resolve) {
                                setTimeout(function () {
                                    resolve();
                                }, 50);
                            });
                        }
                    }).then(function(result){
                        if (result.value) {
                            window.location = url;
                        } else if (result.dismiss === 'cancel') {
                            swal('Cancelled', 'Your data is safe :)', 'error');
                        }
                    });
                });
            },
            ajax: '{!! route('attendance.data') !!}',
            serverSide: true,
            scrollY: "300px",
            columns: [  
            { data: 'nik', name: 'nik' },
            { data: 'employee', name: 'employee' },
            { data: 'role', name: 'role' },
            { data: 'attendance', name: 'attendance' },
            { data: 'attendance_detail', name: 'attendance_detail' },

            ]
        });
    });
    </script>
@endsection
