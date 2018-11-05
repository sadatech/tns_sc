@extends('layouts.app')
@section('title', "Market")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Market <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <h3 class="block-title">
                        <a href="{{ route('tambah.pasar') }}" class="btn btn-primary btn-square" title="Add Data Store"><i class="fa fa-plus mr-2"></i>Add Data</a>
                    </h3>
                    <div class="block-option">
                        <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                        <a href="{{ route('pasar.export') }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </div>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full dataTable" id="storetable">
                    <thead>
                        <th class="text-center" style="width: 150px;">Action</th>
                        <th width="200px">Pasar</th>
                        <th width="200px">Address</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th width="200px">Subarea</th>
                        <th width="200px">Area</th>
                        <th width="200px">Region</th>
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
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import Data Pasar</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="import-form" method="post" enctype="multipart/form-data" action="{{ url('store/pasar/import') }}">
                {{ csrf_field() }}
                <div class="block-content">
                    <div class="form-group">
                      <a href="{{ route('pasar.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                    </div>
                    <div class="block-content">
                        <h5> Sample Data :</h5>
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <td><b>Market</b></td>
                                    <td><b>Address</b></td>
                                    <td><b>Longitude</b></td>
                                    <td><b>Latitude</b></td>
                                    <td><b>Subarea</b></td>
                                    <td><b>Area</b></td>
                                    <td><b>Region</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Market 1</td>
                                    <td>Address 1</td>
                                    <td>Longitude 1</td>
                                    <td>Latitude 1</td>
                                    <td>Subarea 1</td>
                                    <td>Area 1</td>
                                    <td>Region 1</td>
                                </tr>
                                <tr>
                                    <td>Market 2</td>
                                    <td>Address 2</td>
                                    <td>Longitude 2</td>
                                    <td>Latitude 2</td>
                                    <td>Subarea 2</td>
                                    <td>Area 2</td>
                                    <td>Region 2</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Upload Your Data Plan DC:</label>
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
        $('#storetable').DataTable({
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
            ajax: '{!! route('pasar.data') !!}',
            serverSide: true,
            scrollY: "300px",
            columns: [  
            { data: 'action', name: 'action' },
            { data: 'name', name: 'pasars.name' },
            { data: 'address', name: 'pasars.address' },
            { data: 'longitude', name: 'pasars.longitude' },
            { data: 'latitude', name: 'pasars.latitude' },
            { data: 'subarea.name', name: 'subarea.name' },
            { data: 'subarea.area.name', name: 'subarea.area.name' },
            { data: 'subarea.area.region.name', name: 'subarea.area.region.name' },
            ]
        });
    });
    </script>
@endsection
