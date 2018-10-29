@extends('layouts.app')
@section('title', "Add Store")
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
                <h3 class="block-title"><i class="fa fa-plus"></i> Add Store</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                        <i class="si si-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <form action="{{ route('store.add') }}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name1" placeholder="Add new name" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Optional Name</label>
                        <input type="text" class="form-control" name="name2" placeholder="Add new otional name">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="control-label">Address</label>
                        <input type="text" class="form-control" name="address" id="us3-address"/>
                    </div>
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
                        <label>Latitude</label>
                        <input type="text" class="form-control" readonly="readonly" id="latitude" name="latitude" required/>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Longitude</label>
                        <input type="text" class="form-control" readonly="readonly" id="longitude" name="longitude" required/>
                    </div>
                </div>
            </div>
            <div class="block-content">
                <h5><b>Distributor, Account, Area, and Type</b></h5>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Distributor</label>
                        <select class="js-select2 custom-select" name="distributor[]" multiple required>
                            @foreach ($distributor as $dis)
                            <option value="{{ $dis->id }}">{{ $dis->name }}</option>
                            @endforeach
                        </select>
                    </div>
                     <div class="form-group col-md-6">
                        <label>Sales</label>
                        <select class="form-control form-control-lg" name="sales" required>
                        <option value="" disabled selected>Choose your Sales</option>
                            @foreach($sales as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Account</label>
                        <select class="js-select2 custom-select" name="account" required>
                            <option value="" disabled selected>Choose your Account</option>
                            @foreach ($account as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->channel->name }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Sub Area/ Area</label>
                        <select class="js-select2 form-control" name="subarea" required>
                            <option value="" disabled selected>Choose your Subarea</option>
                            @foreach($subarea as $data)
                                <option value="{{ $data->id }}">{{ $data->area->name }} - {{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Is Vito</label>
                        <select class="js-select2 custom-select" name="is_vito" required>
                            <option value="" disabled selected>Choose Vito / Non Vito</option>
                            <option value="Vito">VITO</option>
                            <option value="Non Vito">NON VITO</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Delivery</label>
                        <select class="js-select2 form-control" name="delivery" required>
                            <option value="" disabled selected>Choose Delivery</option>
                                <option value="Direct">Direct</option>
                                <option value="DC">DC</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Store Panel</label>
                        <select class="js-select2 custom-select" name="store_panel" required>
                            <option value="" disabled selected>Choose Store Panel</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Coverage</label>
                        <select class="js-select2 form-control" name="coverage" required>
                            <option value="" disabled selected>Choose Coverage</option>
                                <option value="Direct">Direct</option>
                                <option value="In Direct">In Direct</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Timezones</label>
                        <select class="form-control form-control-lg" name="timezone" required>
                        <option value="" disabled selected>Choose your Timezone</option>
                            @foreach($timezone as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Photo</label>
                        <div class="custom-file">
                            <input type="file" name="photo" class="custom-file-input" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp">
                            <label class="custom-file-label">Choose Photo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-alt-success">
                        <i class="fa fa-save"></i> Save
                    </button>
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('css')
    <style type="text/css">
        .pac-container {
            /*z-index: 99999;*/
        }
    </style>
@endsection

@section('script')
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyCcAydgyjdaptJ3y8AyiSqgYYMQEU6z7Cg&amp;v=3&amp;libraries=places"></script>
<script src="{{ asset('js/locationpicker.jquery.min.js') }}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var lat     = -6.2241031;
    var long    = 106.9212855;
    if( $('#latitude').val() != '') lat = $('#latitude').val();
    if( $('#longitude').val() != '') long = $('#longitude').val();
    $('#us3-address').val('');
    $('#us3').locationpicker({
        location:{
            latitude:lat,
            longitude:long
        },
        radius:5,
        inputBinding:{
            latitudeInput:$('#latitude'),
            longitudeInput:$('#longitude'),
            radiusInput:$('#us3-radius'),
            locationNameInput:$('#us3-address')
        },
        enableAutocomplete:true,
        markerIcon:"https://www.fratekindoapp.com/public/img/Map-Marker-PNG-File-70x70.png"
    });
    $('#us3').locationpicker('autosize');
    
    $(".js-select2").select2();
</script>
@endsection
