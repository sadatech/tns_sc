@extends('layouts.app')
@section('title', "Employee")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Employee <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">        
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <h3 class="block-title">
                        <a href="{{ route('tambah.employee') }}" title="Add Data Employee" class="btn btn-primary btn-square"><i class="fa fa-plus mr-2"></i>Add Data</a>
                        <button class="btn btn-info btn-square"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                    </h3>
                    <div class="block-option">
                        <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button>
                        <button class="btn btn-danger btn-square float-right" type="submit"><i class="si si-trash mr-2"></i>Delete Data</button>
                    </div>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full table-hover" id="employeetable">
                    <thead>
                        <th class="text-center" width="245px">Action</th>
                        <th width="200px">NIK</th>
                        <th width="200px">Name</th>
                        <th width="200px">No. KTP</th>
                        <th width="200px">Store</th>
                        <th width="200px">Phone</th>
                        <th width="200px">Email</th>
                        <th width="200px">No. Rekening</th>
                        <th width="200px">Bank</th>
                        <th width="60px">Status</th>
                        <th width="150px">Join Date</th>
                        <th width="200px">Agency</th>
                        <th width="120px">Gender</th>
                        <th width="60px">Education</th>
                        <th width="200px">Birth Date</th>
                        <th width="200px">Name Position</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/magnific-popup/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
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
    $(function() {
        $('#employeetable').DataTable({
            processing: true,
            scrollX: true,
            scrollY: "300px",
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
            ajax: '{!! route('employee.data') !!}',
            serverSide: true,
            columns: [
            { data: 'action', name: 'action' },
            { data: 'nik', name: 'nik' },
            { data: 'name', name: 'name' },
            { data: 'ktp', name: 'ktp' },
            { data: 'employeeStore', name: 'employeeStore' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'rekening', name: 'rekening' },
            { data: 'bank', name: 'bank' },
            { data: 'status', name: 'status' },
            { data: 'joinAt', name: 'joinAt' },
            { data: 'agency', name: 'agency' },
            { data: 'gender', name: 'gender' },
            { data: 'education', name: 'education' },
            { data: 'birthdate', name: 'birthdate' },
            { data: 'position', name: 'position' },
            ]
        });
    });
</script>
@endsection