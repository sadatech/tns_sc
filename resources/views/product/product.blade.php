@extends('layouts.app')
@section('title', "Data Product")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Product <small>Manage</small></h2>
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
            <button class="btn btn-primary btn-square" data-toggle="modal" onclick="addModalProduct()"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
          <div class="block-option">
            <a id="direct-download" class="btn btn-success btn-square float-right ml-10" onclick="directDownload('{{ route('product.export') }}', 'direct-download')">
              <i id="direct-download-icon" class="si si-cloud-download mr-2"></i>
              Direct Download
            </a>
            <a id="download-file" target="_blank" download="Product Focus.xlsx"></a>
            <button id="import-button" class="btn btn-outline-info btn-square float-right ml-10">
              <i class="si si-cloud-upload mr-2"></i>
              Import Data
            </button>
            <a id="in-direct-download" class="btn btn-outline-success btn-square float-right ml-10" onclick="inDirectDownload('{{ route('product.download') }}', 'in-direct-download')">
              <i id="in-direct-download-icon" class="si si-cloud-download mr-2"></i>
              In-direct Download
            </a>
            <a id="upload-status" class="btn btn-outline-warning btn-square float-right ml-10">
              <i class="fa fa-check-square-o"></i> 
              View Job Status 
            </a>
          </div>
        </div>
        <table class="table table-responsive table-striped table- table-vcenter js-dataTable-full table-hover table-bordered" id="mainTable">
        <thead>
          <th width="10px"></th>
          <th>Brand</th>
          <th>Category</th>
          <th>Sub Category</th>
          <th>Code</th>
          <th>Name</th>
          <th>Panel</th>
          <th>Carton</th>
          <th>Pack</th>
          <th>PCS</th>
          <th> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ADD PRODUCT MODAL --}}
<!-- include('product._form_product', ['action' => route('product.add'), 'id' => 'tambahModal', 'type' => 'product']) -->

<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import <i>Data Product</i></h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('product.import') }}">
        {{ csrf_field() }}
        <div class="block-content">
          <div class="form-group">
            <a href="{{ route('product.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <div class="block-content">
            <h5> Sample Data :</h5>
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <td><b>SubCategory</b></td>
                        <td><b>Category</b></td>
                        <td><b>Code</b></td>
                        <td><b>SKU</b></td>
                        <td><b>Panel</b></td>
                        <td><b>Carton</b></td>
                        <td><b>Pack</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SubCategory 1</td>
                        <td>Category 1</td>
                        <td>Code 1</td>
                        <td>SKU 1</td>
                        <td>Panel 1</td>
                        <td>Carton 1</td>
                        <td>Pack 1</td>
                    </tr>
                    <tr>
                        <td>SubCategory 2</td>
                        <td>Category 2</td>
                        <td>Code 2</td>
                        <td>SKU 2</td>
                        <td>Panel 2</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
          </div>
          <div class="form-group col-md-12">
            <label>Upload Your Data Product:</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                <label class="custom-file-label">Choose file Excel</label>
                <code> *Type File Excel</code>
            </div>
           </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-alt-success">
            <i class="fa fa-save"></i> Import
          </button>
          <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('utilities.import_function', ['button_id' => 'import-button','name' => 'product', 'form_url' => route('product.upload'), 'template_url' => route('product.download-template'), 'sample_data' => 
  [
      'sub_category' => [
          'Sambal Terasi 15ml', 'Bumbu Lumur Ayam Spesial'
      ],
      'category' => [
          'ALL', 'Jakarta'
      ],
      'code' => [
          '11/2019', '01/2020'
      ],
      'product' => [
          '11/2020', '01/2021'
      ]
  ]
])
@include('utilities.filter_modal', ['name' => 'product', 'table_id' => 'mainTable', 'model' => 'App\Product', 'width' => 3, 'adjust_display_timeout' => 0.1, 'filter' => 
    [
        [
            'name'          => 'brand',
            'type'          => 'select2',
            'multiple'      => 'true',
            'route'         => 'brand-select2',
        ],
        [
            'name'          => 'category',
            'type'          => 'select2',
            'multiple'      => 'true',
            'route'         => 'category-select2',
        ],
        [
            'name'          => 'sub_category',
            'type'          => 'select2',
            'multiple'      => 'true',
            'route'         => 'sub-category-select2',
        ],
        [
            'name'          => 'name',
            'type'          => 'text',
        ],
        [
            'name'          => 'code',
            'type'          => 'text',
        ],
        [
            'name'          => 'panel',
            'type'          => 'select',
            'item'          => [['value'=>'yes', 'text'=>'Yes'],['value'=>'no', 'text'=>'No']],
        ]
    ]
])
@include('utilities.trace_modal', ['name' => 'product', 'model' => 'App\Product', 'buttonId' => 'upload-status'])
@include('utilities.explanation_modal')
@include('utilities.export_function')
@include('utilities.form_modal', ['name' => 'product', 'url' => route('product.add'), 'width' => '12', 'with_label' => 'true', 'input' => [
    [
        'name'          => 'code',
        'type'          => 'text',
        'required'      => 'true',
        'edit_field'    => 'code',
        'width'         => '6',
    ],
    [
        'name'          => 'name',
        'type'          => 'text',
        'required'      => 'true',
        'edit_field'    => 'name',
        'width'         => '6',
    ],
    [
        'name'          => 'categorize',
        'type'          => 'select3',
        'width'         => '12',
        'width_tree'    => ['10','2'],
        'with_label'    => 'false',
        'edit_field'    => ['id_sub_category','sub_category_name'],
        'tree'          => ['sub_category','category','brand'],
    ],
    [
        'name'          => 'panel',
        'type'          => 'select',
        'item'          => [ ['value'=>'yes','text'=>'Yes'],['value'=>'no','text'=>'No'] ],
        'width'         => '6',
        'edit_field'    => 'panel'
    ],
    [
        'name'          => 'pcs',
        'type'          => 'text',
        'default'       => '1',
        'readonly'      => 'true',
        'required'      => 'true',
        'edit_field'    => 'pcs'
    ],
    [
        'name'          => 'pack',
        'type'          => 'text',
        'placeholder'   => 'Number of Pcs each Pack',
        'required'      => 'false',
        'edit_field'    => 'pack'
    ],
    [
        'name'          => 'carton',
        'type'          => 'text',
        'placeholder'   => 'Number of Pcs each Carton',
        'required'      => 'false',
        'edit_field'    => 'carton'
    ],
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
<script src="{{ asset('js/select2-handler.js') }}"></script>
<script type="text/javascript">
      var url   = '{!! route('product.data') !!}';
      var order = [];
      var columnDefs = [
        {"className": "text-center", "targets": 0},
        {"className": "text-center", "targets": 7},
        {"className": "text-center", "targets": 8},
        {"className": "text-center", "targets": 9},
        {"className": "text-center", "targets": 10}
      ];
              
      var tableColumns = [
        { data: 'id', name: 'id' },
        { data: 'brand', name: 'brand' },
        { data: 'category', name: 'category' },
        { data: 'sub_category.name', name: 'sub_category.name' },
        { data: 'code', name: 'code' },
        { data: 'name', name: 'name' },
        { data: 'panel', name: 'panel' },
        { data: 'carton', name: 'carton' },
        { data: 'pack', name: 'pack' },
        { data: 'pcs', name: 'pcs' },
        { data: 'action', name: 'action' },
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