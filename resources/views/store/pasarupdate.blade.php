@extends('layouts.app')
@section('title', "Update Market")
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
            <div class="block-header bg-gd-sun p-10">
                <h3 class="block-title"><i class="fa fa-edit"></i> Update Market</h3>
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
                        <input type="text" class="form-control" value="{{ $str->name }}" name="name" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Sub-area / Area</label>
                        <select class="js-select2 custom-select" name="subarea" id="subarea" required>
                            @foreach($subarea as $data)
                                <option value="{{ $data->id }}" {{$data->id==$str->id_subarea ? 'selected' : ''}}>{{ $data->name }} - {{ $data->area->name }}</option>
                            @endforeach
                        </select>
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
                <input type="hidden" id="latitudeInput" name="latitude" required/>
                <input type="hidden" id="longitudeInput" name="longitude" required/>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-alt-success">
                    <i class="fa fa-save"></i> Save
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-alt-secondary" data-dismiss="modal">Back</a>
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
        $('#subbarea option[value="{{ $str->subarea->id }}"]').attr('selected','selected').trigger('change');

    });
    var lat     = -6.21;
    var long    = 106.85;
    console.log(long);
    $('#us3Input').locationpicker({
        location: {
            @if ($str->latitude)
                @if ($str->latitude == '-' or $str->longitude == '-')
                    latitude: lat,
                    longitude: long
                @else
                    latitude: {{ $str->latitude }},
                    longitude: {{ $str->longitude }}
                @endif
            @else
            latitude: lat,
            longitude: long
            @endif
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
