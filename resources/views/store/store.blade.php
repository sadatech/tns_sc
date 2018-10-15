@extends('layouts.app')
@section('title', "Store")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Store <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <h3 class="block-title">
                        <a href="{{ route('tambah.store') }}" class="btn btn-primary btn-square" title="Add Data Store"><i class="fa fa-plus mr-2"></i>Add Data</a>
                    </h3>
                    <div class="block-option">
                        <button class="btn btn-info btn-square"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                        <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button>
                    </div>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full dataTable" id="storetable">
                    <thead>
                        <th class="text-center" style="width: 150px;">Action</th>
                        <th width="200px">Name</th>
                        <th width="200px">Optional Name</th>
                        <th width="200px">Type Store</th>
                        <th width="200px">Sub Area</th>
                        <th width="200px">Account</th>
                        <th>Distributor</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th width="200px">Address</th>
                    </thead>
                </table>
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
            ajax: '{!! route('store.data') !!}',
            serverSide: true,
            scrollY: "300px",
            columns: [  
            { data: 'action', name: 'action' },
            { data: 'name1', name: 'name1' },
            { data: 'name2', name: 'name2' },
            { data: 'type', name: 'type' },
            { data: 'subarea', name: 'subarea' },
            { data: 'account', name: 'account' },
            { data: 'distributor', name: 'distributor' },
            { data: 'longitude', name: 'longitude' },
            { data: 'latitude', name: 'latitude' },
            { data: 'address', name: 'address' },
            ]
        });
    });
    </script>
@endsection
