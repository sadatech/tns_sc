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

                <h5><b>Account & Area</b></h5>
                <div class="row">
                    <div class="row col-md-6">
                        <div class="col-md-8 col-sm-12">
                            {{ 
                                Form::select2Input('account', null, route('account-select2'), [
                                    'useLabel'  => false,
                                    'elOptions' => [
                                        'placeholder' => 'Choose your Account',
                                    ]
                                ]) 
                            }}
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="css-control css-control-primary css-switch">
                                <input type="checkbox" class="css-control-input" id="newAccountCheckbox" name="newAccountCheckbox">
                                <span class="css-control-indicator"></span> New
                            </label>
                        </div>
                    </div>
                    <div class="row col-md-6">
                        <div class="col-md-8 col-sm-12">
                            {{ 
                                Form::select2Input('subarea', null, route('subarea-select2'), [
                                    'useLabel'  => false,
                                    'labelText' => 'Sub Area',
                                    'text' => 'obj.area_name + " - " + obj.name',
                                    'elOptions' => [
                                        'required' => 'required',
                                        'placeholder' => 'Choose your Subarea',
                                    ]
                                ]) 
                            }}
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="css-control css-control-primary css-switch">
                                <input type="checkbox" class="css-control-input" id="newSubAreaCheckbox" name="newSubAreaCheckbox">
                                <span class="css-control-indicator"></span> New
                            </label>
                        </div>
                    </div>
                </div>

                <h5><b>Type</b></h5>
                <div class="row">
                    <div class="col-md-6">
                        {{
                            Form::select2Input('is_jawa', null, ["Jawa"=>"JAWA","Non Jawa"=>"NON JAWA"], [
                                'elOptions' => [
                                    'required' => 'required',
                                    'placeholder' => 'Choose Java / Non Java',
                                ]
                            ]) 
                        }}
                        {{-- <label>Is Jawa</label>
                        <select class="js-select2 custom-select" name="is_jawa" required>
                            <option value="" disabled selected>Choose Jawa / Non Jawa</option>
                            <option value="Jawa">JAWA</option>
                            <option value="Non Jawa">NON JAWA</option>
                        </select> --}}
                    </div>
                    <div class="col-md-6">
                        {{-- isset($data->sales->id) ? [$data->sales->id, $data->sales->name] :  --}}
                        {{ 
                            Form::select2Input('sales', null, route('sales-tier-select2'), [
                                'elOptions' => [
                                    'required' => 'required',
                                    'placeholder' => 'Choose your Sales',
                                ]
                            ]) 
                        }}
                        {{-- <label>Sales</label>
                        <select class="form-control form-control-lg" name="sales" required>
                        <option value="" disabled selected>Choose your Sales</option>
                            @foreach($sales as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                        </select> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {{
                            Form::select2Input('is_vito', null, ["Vito"=>"VITO","Non Vito"=>"NON VITO"], [
                                'elOptions' => [
                                    'required' => 'required',
                                    'placeholder' => 'Choose Vito / Non Vito',
                                ]
                            ]) 
                        }}
                        {{-- <label>Is Vito</label>
                        <select class="js-select2 custom-select" name="is_vito" required>
                            <option value="" disabled selected>Choose Vito / Non Vito</option>
                            <option value="Vito">VITO</option>
                            <option value="Non Vito">NON VITO</option>
                        </select> --}}
                    </div>
                    <div class="col-md-6">
                        {{
                            Form::select2Input('delivery', null, ["Direct"=>"DIRECT","DC"=>"DC"], [
                                'elOptions' => [
                                    'required' => 'required',
                                    'placeholder' => 'Choose Delivery',
                                ]
                            ]) 
                        }}
                        {{-- <label>Delivery</label>
                        <select class="js-select2 form-control" name="delivery" required>
                            <option value="" disabled selected>Choose Delivery</option>
                                <option value="Direct">Direct</option>
                                <option value="DC">DC</option>
                        </select> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {{
                            Form::select2Input('store_panel', null, ["YES"=>"YES","NO"=>"NO"], [
                                'elOptions' => [
                                    'required' => 'required',
                                    'placeholder' => 'Choose Store Panel',
                                ]
                            ]) 
                        }}
                        {{-- <label>Store Panel</label>
                        <select class="js-select2 custom-select" name="store_panel" required>
                            <option value="" disabled selected>Choose Store Panel</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select> --}}
                    </div>
                    <div class="col-md-6">
                        {{
                            Form::select2Input('coverage', null, ["Direct"=>"DIRECT","In Direct"=>"IN DIRECT"], [
                                'elOptions' => [
                                    'required' => 'required',
                                    'placeholder' => 'Choose Coverage',
                                ]
                            ]) 
                        }}
                        {{-- <label>Coverage</label>
                        <select class="js-select2 form-control" name="coverage" required>
                            <option value="" disabled selected>Choose Coverage</option>
                                <option value="Direct">Direct</option>
                                <option value="In Direct">In Direct</option>
                        </select> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {{ 
                            Form::select2Input('timezone', null, route('timezone-select2'), [
                                'elOptions' => [
                                    'required' => 'required',
                                    'placeholder' => 'Choose your Timezone',
                                ]
                            ]) 
                        }}
                        {{-- <label>Timezones</label>
                        <select class="form-control form-control-lg" name="timezone" required>
                        <option value="" disabled selected>Choose your Timezone</option>
                            @foreach($timezone as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                        </select> --}}
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
        markerIcon: "{{ asset('img/Map-Marker-PNG-File-70x70.png') }}"
    });
    $('#us3').locationpicker('autosize');
    
    $(".js-select2").select2();
</script>
@endsection
