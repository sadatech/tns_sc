@extends('layouts.app')
@section('title', $market == 1 ? "Route" : "Market")
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
  <h2 class="content-heading pt-10">{{ $market == 1 ? "Route" : "Market" }} <small>Manage</small></h2>
  <div class="block block-themed"> 
    <div class="block-header bg-primary pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#formModal" onclick="addModal()"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
          <div class="block-option">
            <a href="{{ route('route.export',['market'=>$market]) }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Download Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="subtable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th>{{ $market == 1 ? "Route" : "Market" }}</th>
          <th>SubArea</th>
          <th>Area</th>
          <th>Region</th>
          <th>Address</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add {{ $market == 1 ? "Route" : "Market" }}</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form method="post" action="{{ route('route.add',['market'=>$market]) }}">
        {!! csrf_field() !!}
        <div class="block-content">

          <div class="form-group">
            <label>Name {{ $market == 1 ? "Route" : "Market" }}</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Add new {{ $market == 1 ? 'Route' : 'Market' }}" required>
            <input type="hidden" id="id" name="id">
            <input type="hidden" id="type" name="type" value="{{ $market }}">
            <input type="hidden" id="update" name="update">
          </div>

          <div id="input-tree">
          </div>

          <div class="form-group">
              <label class="control-label">Address</label>
              <input type="text" class="form-control" name="address" id="us3-address"/>
          </div>
          <div class="form-group">
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
          <div class="form-group row">
            <div class="col-md-6 col-sm-6">
                <label>Latitude</label>
                <input type="text" class="form-control" readonly="readonly" id="latitude" name="latitude" required/>
            </div>
            <div class="col-md-6 col-sm-6">
                <label>Longitude</label>
                <input type="text" class="form-control" readonly="readonly" id="longitude" name="longitude" required/>
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
  .padding0{
    padding: 0;
  }
</style>
@endsection

@section('script')
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyCcAydgyjdaptJ3y8AyiSqgYYMQEU6z7Cg&amp;v=3&amp;libraries=places"></script>
<script src="{{ asset('js/locationpicker.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  });
    
  @include('utilities.select_tree', [ 'input' => ['sub_area','area','region'], 'selectorTree' => 'input-tree', 'treeId' => 'first' ])
  
  var selectSubArea = '', selectArea = '', selectRegion = '';
  var lat     = -6.2241031;
  var long    = 106.92347419999999;

  if( $('#latitude').val() != '') lat = $('#latitude').val();
  if( $('#longitude').val() != '') long = $('#longitude').val();
  initMap(lat, long);

  function initMap(latitude, longitude) {
    $('#us3-address').val('');
    $('#us3').locationpicker({
        location:{
            latitude:latitude,
            longitude:longitude
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
  }
  function addModal() {
    $('#name').val('');
    $('#update').val('');
    $('#id').val('');
    resetFirstTree();
    initMap(lat, long);
  }
  function editModal(json) {
    resetFirstTree();
    $('#name').val(json.name);
    $('#id').val(json.id);
    setTimeout(function() {
      if(json.latitude || json.longitude){
        $('#latitude').val(json.latitude);
        $('#longitude').val(json.longitude);
        initMap(json.latitude, json.longitude);
      }
      if(json.address){
        $('#us3-address').val(json.address);
      }
    }, 1000);
    setTimeout(function() {
      setFirstSelectTree(json.subarea, json.subarea_name);
      $('#update').val(1);
      setTimeout(function() {
        $('#name').focus();
      }, 200);
    }, 200);

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
    $('#subtable').dataTable({
      processing: true,
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
      ajax: '{!! route('route.data',[ 'market' => $market ]) !!}',
      // scrollY: "300px",
      order: [],
      columnDefs:[
        {"className": "text-center", "targets": 0}
      ],
      columns: [
      { data: 'id', name: 'routes.id' },
      { data: 'name', name: 'routes.name' },
      { data: 'subarea.name', name: 'sub_ares.name' },
      { data: 'subarea.area.name', name: 'sub_ares.areas.name' },
      { data: 'subarea.area.region.name', name: 'sub_areas.area.regions.name', "searchable": true },
      { data: 'address', name: 'address' },
      { data: 'latitude', name: 'latitude' },
      { data: 'longitude', name: 'longitude' },
      { data: 'action', name: 'action' },
      ]
    });
  });
</script>
@endsection