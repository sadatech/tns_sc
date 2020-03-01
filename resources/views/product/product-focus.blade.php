@extends('layouts.app')
@section('title', "Product Focus")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Product Focus <small>Manage</small></h2>
  @if($errors->any())
    <div class="alert alert-danger">
      <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
      @foreach ($errors->all() as $error)
      <div> {{ $error }}</div>
      @endforeach
    </div>
  @endif
  <div class="block block-themed"> 
    <div class="block-header bg-primary pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button id="add-button" class="btn btn-primary btn-square" data-toggle="modal" onclick="addModalProductFocus()"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
          <div class="block-option">
            <a id="direct-download" class="btn btn-success btn-square float-right ml-10" onclick="directDownload('{{ route('focus.export') }}', 'direct-download')">
              <i id="direct-download-icon" class="si si-cloud-download mr-2"></i>
              Direct Download
            </a>
            <a id="download-file" target="_blank" download="Product Focus.xlsx"></a>
            <button id="import-button" class="btn btn-outline-info btn-square float-right ml-10">
              <i class="si si-cloud-upload mr-2"></i>
              Import Data
            </button>
            <a id="in-direct-download" class="btn btn-outline-success btn-square float-right ml-10" onclick="inDirectDownload('{{ route('focus.download') }}', 'in-direct-download')">
              <i id="in-direct-download-icon" class="si si-cloud-download mr-2"></i>
              In-direct Download
            </a>
            <a id="upload-status" class="btn btn-outline-warning btn-square float-right ml-10">
              <i class="fa fa-check-square-o"></i> 
              View Job Status 
            </a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="mainTable">
        <thead>
          <th></th>
          <th>Product</th>
          <th>Area</th> 
          <th>Start Month</th>
          <th>End Month</th>
          <th>Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

@include('utilities.import_function', ['button_id' => 'import-button','name' => 'product_focus', 'form_url' => route('focus.upload'), 'template_url' => route('focus.download-template'), 'sample_data' => 
  [
      'product'     => [
          'Sambal Terasi 15ml', 'Bumbu Lumur Ayam Spesial'
      ],
      'area'        => [
          'ALL', 'Jakarta'
      ],
      'start_month' => [
          '11/2019', '01/2020'
      ],
      'end_month'   => [
          '11/2020', '01/2021'
      ]
  ]
])
@include('utilities.filter_modal', ['name' => 'product_focus', 'table_id' => 'mainTable', 'model' => 'App\ProductFocus', 'width' => 3, 'adjust_display_timeout' => 0.1, 'filter' => 
    [
        [
            'name'          => 'product',
            'type'          => 'select2',
            'multiple'      => 'true',
            'route'         => 'product-select2',
            'return_id'     => "obj.id",
            'return_text'   => "obj.code + ' | ' + obj.name"
        ],
        [
            'name'          => 'area',
            'type'          => 'select2',
            'multiple'      => 'true',
            'check_all'     => 'true',
            'route'         => 'area-select2',
        ],
        [
            'name'          => 'start_month',
            'type'          => 'date',
            'viewmode'      => 'months',
            'minviewmode'   => 'months',
            'format'        => 'mm/yyyy'
        ],
        [
            'name'          => 'end_month',
            'type'          => 'date',
            'viewmode'      => 'months',
            'minviewmode'   => 'months',
            'format'        => 'mm/yyyy'
        ]
    ]
])
@include('utilities.trace_modal', ['name' => 'product_focus', 'model' => 'App\ProductFocus', 'buttonId' => 'upload-status'])
@include('utilities.explanation_modal')
@include('utilities.export_function')
@include('utilities.form_modal', ['name' => 'product_focus', 'url' => route('focus.add'), 'width' => '12', 'with_label' => 'true', 'input' => [
    [
        'name'          => 'product',
        'type'          => 'select2',
        'required'      => 'true',
        'multiple'      => 'false',
        'route'         => 'product-select2',
        'return_text'   => "obj.code + ' | ' + obj.name",
        'edit_field'    => ['product.id','product.name']
    ],
    [
        'name'          => 'area',
        'type'          => 'select2',
        'multiple'      => 'true',
        'check_all'     => 'true',
        'route'         => 'area-select2',
        'width'         => ['11','1'],
        'edit_field'    => ['id_area','name']
    ],
    [
        'name'          => 'start_month',
        'type'          => 'date',
        'required'      => 'true',
        'viewmode'      => 'months',
        'minviewmode'   => 'months',
        'format'        => 'mm/yyyy',
        'width'         => '6',
        'edit_field'    => 'from'
    ],
    [
        'name'          => 'end_month',
        'type'          => 'date',
        'required'      => 'true',
        'viewmode'      => 'months',
        'minviewmode'   => 'months',
        'format'        => 'mm/yyyy',
        'width'         => '6',
        'edit_field'    => 'to'
    ]
  ], 'false_rules'  => [ 
    [
      'month', 'start_month', '>', 'end_month', 'Start Month tidak boleh lebih dari End Month'
    ]
  ]
])

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<style type="text/css">
  [data-notify="container"] 
  {
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
  <script src="{{ asset('js/select2-handler.js') }}"></script>
  <script type="text/javascript">
      var url   = '{!! route('focus.data') !!}';
      var order = [];
      var columnDefs = [
        {"className": "text-center", "targets": 0},
        {"className": "text-right", "targets": 3},
        {"className": "text-right", "targets": 4},
        {"className": "text-center", "targets": 5}
      ];
              
      var tableColumns = [
        { data: 'id', name: 'id' },
        { data: 'product.name', name: 'product.name'},
        { data: 'area', name: 'area' },
        { data: 'from', name: 'from' },
        { data: 'to', name: 'to' },
        { data: 'action', name: 'action' }
      ];

    $(document).ready(function() {

      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      @if(session('type'))
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
      @endif
    });
  </script>
@endsection