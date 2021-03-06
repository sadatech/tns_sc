@extends('layouts.app')
@section('title', "Add Market")
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
                <h3 class="block-title"><i class="fa fa-plus"></i> Add Market</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                        <i class="si si-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <form action="{{ route('pasar.add') }}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Add new name" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="col-md-12 col-sm-12" style="padding: 0">Sub Area</label>
                        <div class="input-group mb-3 col-sm-12 col-md-12" style="padding: 0">
                            <div class="col-sm-12" style="padding: 0">
                                <select class="form-control" style="width: 100%" name="subarea" id="subSelect" required>
                                </select>
                            </div>
                        </div>
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
                <input type="hidden" id="latitude" name="latitude" required/>
                <input type="hidden" id="longitude" name="longitude" required/>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-alt-success">
                    <i class="fa fa-save"></i> Save
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-alt-secondary" data-dismiss="modal">Back</a>
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
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#subSelect').select2(setOptions('{{ route("sub-area-select2") }}', 'Choose your SubArea', function (params) {
        return filterData('name', params.term);
    }, function (data, params) {
        return {
            results: $.map(data, function (obj) {                                
                return {id: obj.id, text: obj.name}
            })
        }
    }));
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
