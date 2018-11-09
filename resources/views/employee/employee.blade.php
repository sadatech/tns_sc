@extends('layouts.app')
@section('title', "Employee")
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
                        <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                    </h3>
                    <div class="block-option">
                        <a href="{{ route('employee.export') }}" class="btn btn-success btn-square float-right ml-10" title="Unduh Data"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </div>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full table-hover" id="employeetable">
                    <thead>
                        <th class="text-center" width="350px">Action</th>
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

<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import Data Employee SPG, MD, & TL MTC</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('employeess.import') }}">
                {{ csrf_field() }}
                <div class="block-content">
                    <div class="form-group">
                        <a href="{{ route('employee.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                    </div>
                    <div class="block-content">
                        <h5> Sample Data :</h5>
                        <table class="table table-bordered table-responsive table-vcenter">
                            <thead>
                                <tr>
                                    <td><b>NIK</b></td>
                                    <td><b>Name</b></td>
                                    <td><b>KTP</b></td>
                                    <td><b>Phone</b></td>
                                    <td><b>Email</b></td>
                                    <td><b>Sub Area</b></td>
                                    <td><b>Area</b></td>
                                    <td><b>Region</td>
                                    <td><b>Rekening</b></td>
                                    <td><b>Bank</b></td>    
                                    <td><b>Join Date</b></td>
                                    <td><b>Agency</b></td>
                                    <td><b>Gender</b></td>
                                    <td><b>Education</b></td>
                                    <td><b>Birth Date</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>NIK 1</td>
                                    <td>Name 1</td>
                                    <td>KTP 1</td>
                                    <td>Phone 1</td>
                                    <td>Email 1</td>
                                    <td>Sub Area 1</td>
                                    <td>Area 1</td>
                                    <td>Region 1</td>
                                    <td>Rekening 1</td>
                                    <td>Bank 1</td>
                                    <td>Join Date 1</td>
                                    <td>Agency 1</td>
                                    <td>Gender 1</td>
                                    <td>Education 1</td>
                                    <td>Birth Date 1</td>
                                </tr>
                                <tr>
                                    <td>NIK 2</td>
                                    <td>Name 2</td>
                                    <td>KTP 2</td>
                                    <td>Phone 2</td>
                                    <td>Email 2</td>
                                    <td>Sub Area 2</td>
                                    <td>Area 2</td>
                                    <td>Region 2</td>
                                    <td>Rekening 2</td>
                                    <td>Bank 2</td>
                                    <td>Join Date 2</td>
                                    <td>Agency 2</td>
                                    <td>Gender 2</td>
                                    <td>Education 2</td>
                                    <td>Birth Date 2</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Upload Your Data Employee DC:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                            <label class="custom-file-label">Choose file Excel</label>
                            <code> *Type File Excel</code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-alt-success">
                    <i class="fa fa-save"></i> Import
                  </button>
                  <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Store Information</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
                <input type="hidden" name="employee" id="employeeID">
                <div class="block-content">
                    <table class="table table-bordered">
                        <thead>
                            <th>Store</th>
                            <th>Account</th>
                            <th>Address</th>
                            <th>Coverage</th>
                        </thead>
                        <tbody id="storeGet">
                        </tbody>
                    </table>
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
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script type="text/javascript">
    function viewModal(json) {
        $('#viewModal').modal('show');
        $('#employeeID').val(json.id);
        $('#storeGet').html(json.store);
        
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
        $('#employeetable').DataTable({
            processing: true,
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