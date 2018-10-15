@extends('layouts.app')
@section('title', "Place")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Place <small>Manage</small></h2>
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
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">        
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <h3 class="block-title">
                        <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal" title="Add Data Place"><i class="fa fa-plus mr-2"></i>Add Data</button>
                    </h3>
                    <div class="block-option">
                        <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal" title="Import Data Place"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                        <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</button>
                       
                    </div>
                </div>

                <table class="table table-striped table-vcenter js-dataTable-full table-responsive" id="placetable">
                    <thead>
                        <th style="width: 70px;">Action</th>
                        <th width="120px">Code</th>
                        <th width="200px">Name</th>
                        <th>email</th>
                        <th>phone</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th width="200px">Address</th>
                        <th width="200px">Description</th>
                    </thead>
                </table>
            </div>  
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-plus"></i> Import Data Place</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form action="{{ route('place.import') }}" method="post" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <div class="block-content">
                    <div class="form-group">
                        <a href="{{ route('place.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                    </div>
                    <h5> Sample Data :</h5>
                    <table class="table table-bordered table-vcenter">
                        <thead>
                            <tr>
                                <td><b>Code</b></td>
                                <td><b>Name</b></td>
                                <td><b>Email</b></td>
                                <td><b>Phone</b></td>
                                <td><b>Longitude</b></td>
                                <td><b>Latitude</b></td>
                                <td><b>Address</b></td>
                                <td><b>Description</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Code 1</td>
                                <td>Name 1</td>
                                <td>Email 1</td>
                                <td>Phone 1</td>
                                <td>Longitude 1</td>
                                <td>Latitude 1</td>
                                <td>Address 1</td>
                                <td>Description 1</td>
                            </tr>
                            <tr>
                                <td>Code 2</td>
                                <td>Name 2</td>
                                <td>Email 2</td>
                                <td>Phone 2</td>
                                <td>Longitude 2</td>
                                <td>Latitude 2</td>
                                <td>Address 2</td>
                                <td>Description 2</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="block-content">
                  <div class="form-group">
                  <label>Upload Your Data Distributor:</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                        <label class="custom-file-label">Choose file Excel</label>
                        <code> *Type File Excel</code>
                    </div>
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

    <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary p-10">
                        <h3 class="block-title"><i class="fa fa-plus"></i> Add Place</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <form action="{{ route('place.add') }}" method="post">
                    {!! csrf_field() !!}
                    <div class="block-content">
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" class="form-control" name="code" placeholder="Add new Code" required>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Add new place" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Add new email">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="number" class="form-control" name="phone" placeholder="Add new phone number">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input class="form-control" id="us3-address" name="address" placeholder="Add new addrerss" required>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="form-horizontal">
                                    <div class="form-group" style="display: none">
                                        <label class="col-sm-2 control-label">Radius:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="us3-radius" />
                                        </div>
                                    </div>
                                    <div id="us3" style="width: 100%; height: 400px;"></div>
                                    <div class="clearfix">&nbsp;</div>
                                    <div class="m-t-small">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="longitude" readonly="readonly" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" readonly="readonly" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" placeholder="Add new description" required></textarea>
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

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary p-10">
                        <h3 class="block-title"><i class="fa fa-edit"></i> Update Place</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <form id="editForm" method="post">
                    {!! method_field('PUT') !!}
                    {!! csrf_field() !!}
                    <div class="block-content">
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" class="form-control" id="codeInput" name="code" required>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="nameInput" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="emailInput" name="email">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="number" class="form-control" id="phoneInput" name="phone">
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="control-label">Address</label>
                                <input class="form-control" name="address" id="us3Input-address"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="form-horizontal">
                                    <div class="form-group" style="display: none">
                                        <label class="col-sm-2 control-label">Radius:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="us3Input-radius" />
                                        </div>
                                    </div>
                                    <div id="us3Input" style="width: 100%; height: 400px;"></div>
                                    <div class="clearfix">&nbsp;</div>
                                    <div class="m-t-small"></div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Latitude</label>
                                <input type="text" class="form-control" readonly="readonly" id="latitudeInput" name="latitude" required/>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Longitude</label>
                                <input type="text" class="form-control" readonly="readonly" id="longitudeInput" name="longitude" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="descriptionInput" required></textarea>
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
@endsection

@section('css')
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
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyCcAydgyjdaptJ3y8AyiSqgYYMQEU6z7Cg&amp;v=3&amp;libraries=places"></script>
<script src="{{ asset('js/locationpicker.jquery.min.js') }}"></script>
<script type="text/javascript">
    function editModal(json) {
        $('#editModal').modal('show');
        $('#editForm').attr('action', "{{ url('/store/place/update') }}/"+json.id);
        $('#nameInput').val(json.name);
        $('#codeInput').val(json.code); 
        $('#emailInput').val(json.email);
        $('#phoneInput').val(json.phone);
        $('#latitudeInput').val(json.latitude);
        $('#longitudeInput').val(json.longitude);
        $('#us3Input-address').val(json.address);
        $('#descriptionInput').val(json.description);
        // $('#provinceInput').on('change', e => {
        //     var id = $('#provinceInput').find(":selected").val()
        //     $('#cityInput').empty()
        //     $.ajax({
        //         type: "GET", 
        //         url: "{{ route('getCity') }}/?id="+id,
        //         success: data => {
        //             // console.log(data);
        //             data.forEach(city =>
        //                 $('#cityInput').append(`<option value="${city.id}">${city.name}</option>`)
        //                 )
        //             }
        //         })
        // })
        // $.ajax({
        //     type: "GET", 
        //     url: "{{ route('getCity') }}/?id="+json.province,
        //     success: data => {
        //         data.forEach(city =>
        //             $('#cityInput').append(`<option value="${city.id}">${city.name}</option>`)
        //             )
        //         $('#cityInput').val(json.city);
        //     }   
        // })
        $('#editModal').on('shown.bs.modal', function () {
            var lat     = -6.2241031;
            var long    = 106.9212855;

            if( $('#latitudeInput').val() != '') lat = $('#latitudeInput').val();
            if( $('#longitudeInput').val() != '') long = $('#longitudeInput').val();
            
            $('#us3Input').locationpicker({
                location: {
                    latitude: lat,
                    longitude: long
                },
                radius: 5,
                inputBinding: {
                    latitudeInput: $('#latitudeInput'),
                    longitudeInput: $('#longitudeInput'),
                    radiusInput: $('#us3Input-radius'),
                    locationNameInput: $('#us3Input-address')
                },
                enableAutocomplete: true,
                markerIcon: "{{ asset('img/Map-Marker-PNG-File-70x70.png') }}"
            });
            $('#us3Input').locationpicker('autosize');
        });
        console.log(json);
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
        $('#placetable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: "300px",
            drawCallback: function(){
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
            ajax: '{!! route('place.data') !!}',
            columns: [
            { data: 'action', name: 'action' },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'longitude', name: 'longitude' },
            { data: 'latitude', name: 'latitude' },
            { data: 'address', name: 'address' },
            { data: 'description', name: 'description' }
            ]
        });
    });
    $('#tambahModal').on('shown.bs.modal', function () {
        var lat     = -6.2241031;
        var long    = 106.9212855;
        $('#us3-address').val('');

        $('#us3').locationpicker({
            location: {
                latitude: lat,
                longitude: long
            },
            radius: 5,
            inputBinding: {
                latitudeInput: $('#latitude'),
                longitudeInput: $('#longitude'),
                radiusInput: $('#us3-radius'),
                locationNameInput: $('#us3-address')
            },
            enableAutocomplete: true,
            markerIcon: "{{ asset('img/Map-Marker-PNG-File-70x70.png') }}"
        });

        $('#us3').locationpicker('autosize');
    });
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // $('#province').on('change', e => {
    //     var id = $('#province').find(":selected").val()
    //     $('#city').empty()
    //     $.ajax({
    //         type: "GET", 
    //         url: "{{ route('getCity') }}/?id="+id,
    //         success: data => {
    //                 // console.log(data);
    //                 data.forEach(city =>
    //                     $('#city').append(`<option value="${city.id}">${city.name}</option>`)
    //                     )
    //             }
    //         })
    // })
</script>
@endsection
