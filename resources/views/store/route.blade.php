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
    {{
       Form::generateInput('form_test12', 
     [
         [
         'name'  => 'text_input',
         'type'  => 'text',
         ],
         [
         'name'     => 'year_input',
         'type'     => 'date',
         'min_view' => 'years',
         'view'     => 'years',
         'format'   => 'yyyy'
         ],
         [
         'name'     => 'month_input',
         'type'     => 'date',
         'min_view' => 'months',
         'view'     => 'months',
         'format'   => 'MM'
         ],
         [
         'name'  => 'date_input',
         'type'  => 'date',
         ],
         [
         'name'  => 'file_input',
         'type'  => 'file',
         ],
         [
         'name'      => 'location_input',
         'type'      => 'location',
         'use_label' => false,
         ],
         [
         'name'  => 'select_multi_input',
         'type'  => 'select-multi',
         'route' => 'product-select2',
         ],
         [
         'name'  => 'select2_input',
         'type'  => 'select2',
         'route' => 'category-select2',
         'multiple' => true,
         ],
         [
         'name'        => 'select2_check_input',
         'type'        => 'select2',
         'multiple'    => true,
         'check_all'   => true,
         'route'       => 'product-select2',
         'return_id'   => "obj.id",
         'return_text' => "obj.code + ' | ' + obj.name",
         'width'       => ['11','1'],
         'edit_id'     => 'id',
         'edit_text'   => 'name',
         ],
         [
         'name'  => 'radio_input',
         'type'  => 'radio',
         'value' => ['satu'=>'1','dua'=>'2'],
         ],
         [
         'name'  => 'checkbox_input',
         'type'  => 'checkbox',
         'value' => ['satu'=>'1','dua'=>'2'],
         ],
         [
         'name'       => 'select_tree_input',
         'type'       => 'select3',
         'width'      => '12',
         'width_tree' => ['9','3'],
         'use_label'  => false,
         'edit_field' => ['id_category','category_name'],
         'tree'       => ['category','brand'],
         'route'      => ['category-select2','brand-select2'],
         ],
         [
         'name' => 'id',
         'type' => 'hidden',
         ],
         [
         'name' => 'update',
         'type' => 'hidden',
         ],
         [
         'name'    => 'type',
         'type'    => 'hidden',
         'default' => $market,
         ],
         [
         'name' => 'email_input',
         'type' => 'email',
         ],
         [
         'name' => 'number_input',
         'type' => 'number',
         ],
         [
         'name' => 'password_input',
         'type' => 'password',
         ]
     ], ['filter'=>false,'width'=>'4','use_label'=>true]
 )
    }}        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#formModal" onclick="addModalRoute()"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
          <div class="block-option">
            <a href="{{ route('route.export',['market'=>$market]) }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Download Data</a>
          </div>
        </div>
        <table class="table table-responsive table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="subtable">
        <thead>
          <th></th>
          <th>{{ $market == 1 ? "Route" : "Market" }}</th>
          <th>SubArea</th>
          <th>Area</th>
          <th>Region</th>
          <th>Address</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{
  Form::formModalInput('route', route('route.add',['market'=>$market]),
    [
      [
        'name' => 'id',
        'type' => 'hidden',
      ],
      [
        'name' => 'update',
        'type' => 'hidden',
      ],
      [
        'name'    => 'type',
        'type'    => 'hidden',
        'default' => $market,
      ],
      [
        'name'     => 'route',
        'type'     => 'text',
        'required' => true,

      ],
      [
        'name'       => 'sub_area',
        'type'       => 'select3',
        'width'      => '12',
        'width_tree' => ['9','3'],
        'use_label'  => false,
        'edit_field' => ['sub_area_id','sub_area_name'],
        'tree'       => ['sub_area','area','brand'],
        'route'      => ['sub-area-select2','area-select2','region-select2'],
      ],
      [
        'name'      => 'location',
        'type'      => 'location',
        //'edit_field' => ['latitude','longitude'],
        'use_label' => false,
      ],
    ]
  ) 
}}

{{
  Form::filterInput( 'route', 'subtable',
    [
      [
        'name'     => 'route',
        'type'     => 'select2',
        'route'    => 'route-select2',
        'multiple' => true,
      ],
      [
        'name'  => 'sub_area',
        'type'  => 'select2',
        'route' => 'sub-area-select2',
      ],
      [
        'name'  => 'area',
        'type'  => 'select2',
        'route' => 'area-select2',
      ],
      [
        'name'  => 'region',
        'type'  => 'select2',
        'route' => 'region-select2',
      ]
    ], 
    [
      'use_label'  => true,
      'width'      => '4',
      'filter'     => true,
      'url'        => route('route.data',[ 'market' => $market ]),
      'order'      => "",
      'columnDefs' => "{'className': 'text-center', 'targets': 8}",
      'colums'     => "
        { data: 'id', name: 'routes.id' },
        { data: 'name', name: 'routes.name' },
        { data: 'subarea.name', name: 'sub_ares.name' },
        { data: 'subarea.area.name', name: 'sub_ares.areas.name' },
        { data: 'subarea.area.region.name', name: 'sub_areas.area.regions.name' },
        { data: 'address', name: 'address' },
        { data: 'latitude', name: 'latitude' },
        { data: 'longitude', name: 'longitude' },
        { data: 'action', name: 'action' },
      "
    ]
  )
}}

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
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  });
  
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
</script>
@endsection

@prepend('additional-js')
<script src="{{ asset('js/select2-handler.js') }}"></script>
@endprepend