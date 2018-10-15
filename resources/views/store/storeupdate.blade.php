@extends('layouts.app')
@section('title', "Update Store")
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
    <div class="modal-content">
        <div class="block block-themed block-transparent mb-0">
            <div class="block-header bg-primary p-10">
                <h3 class="block-title"><i class="fa fa-edit"></i> Update Store</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                        <i class="si si-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <form id="editForm" method="post" enctype="multipart/form-data">
            {!! method_field('PUT') !!}
            {!! csrf_field() !!}
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" value="{{ $str->name1 }}" name="name1" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Optional Name</label>
                        <input type="text" class="form-control" value="{{ $str->name2 }}" name="name2">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Store Phone Number</label>
                        <input type="number" class="form-control" value="{{ $str->store_phone }}" name="store_phone" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Owner Phone Number</label>
                        <input type="number" class="form-control" value="{{ $str->owner_phone }}" name="owner_phone" required>
                    </div>
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
                            <div class="m-t-small">
                            </div>
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
            </div>
            <div class="block-content">
                <div class="form-group row">
                    <div class="col-md-6">
                        <label>Province</label>
                        <select class="form-control form-control-lg" id="province" name="province" required>
                            @foreach($province as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>City</label>
                        <select class="form-control form-control-lg" id="city" name="city" required>
                            <option disabled selected>Choose your City</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="block-content">
                <h5><b>Distributor, Account, Classification, Area</b></h5>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Classification</label>
                        <select class="js-select2 custom-select" name="classification" id="classification" required>
                            @foreach ($classification as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Distributor</label>
                        <select class="js-select2 custom-select" name="distributor[]" id="distributor" multiple required>
                            @foreach ($distributor as $dis)
                            <option value="{{ $dis->id }}">{{ $dis->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Account</label>
                        <select class="js-select2 custom-select" name="account" id="account" required>
                            @foreach ($account as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->channel->name }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Area / SubArea</label>
                        <select class="js-select2 custom-select" name="subarea" id="subarea" required>
                            @foreach ($subarea as $data)
                                <option value="{{ $data->id }}">{{ $data->area->name }} - {{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                <div class="form-group col-md-6">
                        <label>Type Store</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="" disabled selected>Choose your Type Store</option>
                            <option value="TR"> TR </option>
                            <option value="MR"> MR </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Photo</label>
                            <div class="custom-file">
                                <input type="file" name="photo" class="custom-file-input" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp">
                                <label class="custom-file-label">Pilih Foto</label>
                            </div>
                        </div>
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
@endsection

@section('css')
    <style type="text/css">
        .pac-container {
            z-index: 99999;
        }
    </style>
@endsection

@section('script')
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyCcAydgyjdaptJ3y8AyiSqgYYMQEU6z7Cg&amp;v=3&amp;libraries=places"></script>
<script src="{{ asset('js/locationpicker.jquery.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#classification option[value="{{ $str->classification->id }}"]').attr('selected','selected').trigger('change');
        $('#account option[value="{{ $str->account->id }}"]').attr('selected','selected');
        $('#subbarea option[value="{{ $str->subarea->id }}"]').attr('selected','selected');
        $('#type option[value="{{ $str->type }}"]').attr('selected','selected');
        $('#province option[value="{{ $str->province->id }}"]').attr('selected','selected');
        var dist = {{ $dist }};
        $('#distributor').val(dist).trigger('change');
        $('#province').on('change', e => {
            var id = $('#province').find(":selected").val()
            $('#city').empty()
            $.ajax({
                type: "GET",
                url: "{{ route('getCity') }}/?id="+json.province,
                success: data => {
                    // console.log(data);
                    data.forEach(city =>
                        $('#city').append(`<option value="${city.id}">${city.name}</option>`)
                    )

                    $('#cityInput').val(json.city);
                }
            })
        })
        $.ajax({
            type: "GET", 
            url: "{{ route('getCity') }}/?id={{ $str->province->id }}",
            success: data => {
                data.forEach(city =>
                    $('#city').append(`<option value="${city.id}">${city.name}</option>`)
                    )
                $('#city').val({{ $str->city->id }});
            }  
        })
    });


    $('#us3Input').locationpicker({
        location: {
            latitude: {{ $str->latitude }},
            longitude: {{ $str->longitude }}
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

    $(".js-select2").select2();
</script>
@endsection
