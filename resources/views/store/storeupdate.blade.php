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
                        <label>Name</label> {{$str->id_timezone}}
                        <input type="text" class="form-control" value="{{ $str->name1 }}" name="name1" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Optional Name</label>
                        <input type="text" class="form-control" value="{{ $str->name2 }}" name="name2">
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
                <h5><b>Account & Area</b></h5>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Is Jawa</label>
                        <select class="js-select2 custom-select" name="is_jawa" id="jawa">
                            <option value="" disabled selected>Choose Jawa / Non Jawa</option>
                            <option value="Jawa">JAWA</option>
                            <option value="Non Jawa">NON JAWA</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                         <label>Sales</label>
                        <select class="js-select2 custom-select" name="sales" id="sales" required>
                            @foreach($sales as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
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
                        <label>Is Vito</label>
                        <select class="js-select2 custom-select" name="is_vito" id="is_vito" required>
                            <option value="" disabled selected>Choose Vito / Non Vito</option>
                            <option value="Vito">VITO</option>
                            <option value="Non Vito">NON VITO</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Delivery</label>
                        <select class="js-select2 form-control" name="delivery" id="delivery" required>
                            <option value="" disabled selected>Choose Delivery</option>
                                <option value="Direct">Direct</option>
                                <option value="DC">DC</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Store Panel</label>
                        <select class="js-select2 custom-select" name="store_panel" id="store_panel" required>
                            <option value="" disabled selected>Choose Store Panel</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Coverage</label>
                        <select class="js-select2 form-control" name="coverage" id="coverage" required>
                            <option value="" disabled selected>Choose Coverage</option>
                                <option value="Direct">Direct</option>
                                <option value="In Direct">In Direct</option>
                        </select>
                    </div>
                </div>
                <div class="row">
              <div class="form-group col-md-6">
                        <label>Timezones</label>
                        <select class="js-select2 custom-select" name="timezone" id="timezone" required>
                            @foreach($timezone as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                        </select>
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
        $('#account option[value="{{ $str->account->id }}"]').attr('selected','selected');
        $('#subbarea option[value="{{ $str->subarea->id }}"]').attr('selected','selected');
        $('#timezone option[value="{{ $str->id_timezone }}"]').attr('selected','selected');
        $('#sales option[value="{{ $str->id_salestier }}"]').attr('selected','selected');
        $('#is_vito option[value="{{ $str->is_vito }}"]').attr('selected','selected');
        $('#jawa option[value="{{ $str->is_jawa }}"]').attr('selected','selected');
        $('#coverage option[value="{{ $str->coverage }}"]').attr('selected','selected');
        $('#store_panel option[value="{{ $str->store_panel }}"]').attr('selected','selected');
        $('#delivery option[value="{{ $str->delivery }}"]').attr('selected','selected');
        $('#account').trigger('change');
        $('#timezone').trigger('change');
        $('#subbarea').trigger('change');
        $('#sales').trigger('change');
        $('#is_vito').trigger('change');
        $('#jawa').trigger('change');
        $('#coverage').trigger('change');
        $('#store_panel').trigger('change');
        $('#delivery').trigger('change');
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
