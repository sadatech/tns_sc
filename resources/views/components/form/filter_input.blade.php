@php
//################################
// table -> table id tat want to affect
// name  -> purpose of main view, for example menu name, use underscore, example: product_focus
// width -> width of filter input for col-md-?, default 4, example: 4
// timeout -> timeout to adjust table width, in second, example: 4
// filter-> input filter that want to appear, in array, example: 
    // [
    //     [
    //         'name'          => 'product',
    //         'type'          => 'select2',
    //         'multiple'      => 'true',
    //         'check_all'     => 'true',
    //         'route'         => 'product-select2',
    //         'return_id'     => "obj.id",
    //         'return_text'   => "obj.code + ' | ' + obj.name"
    //     ],
    //     [
    //         'name'          => 'start_month',
    //         'type'          => 'date',
    //         'viewmode'      => 'months',
    //         'minviewmode'   => 'months',
    //         'format'        => 'mm/yyyy'
    //     ],
    //     [
    //         'name' => 'area',
    //         'type' => 'text',
    //     ],
    //     [
    //         'name' => 'gender',
    //         'type' => 'select',
    //         'item' => [
    //             [
    //                 'text'  => 'Laki-laki',
    //                 'value' => 1
    //             ],
    //             [
    //                 'text'  => 'Perempuan',
    //                 'value' => 2
    //             ]
    //         ]
    //     ]
    // ]
//################################
$inputId   = [];
$title     = ucwords(str_replace('_',' ',$name));
$thisId    = str_replace(' ','',$title);

foreach ( $input as $key => $value ) {
    $label     = ucwords(str_replace('_',' ',$value['name']));
    $label     = isset($value['label']) ? $value['label'] : $label;
    $currentId = $thisId.str_replace(' ','',$label);
    $inputId[] = '#'.$currentId;
}

$inputId = array_map(function($val) { return "'$val'"; }, $inputId);

$timeout = isset($options['timeout']) ? $options['timeout'] : '1.5';

@endphp

<button type="button" data-toggle="modal" data-target="#filter-modal" class="btn btn-warning btn-sm act-btn display-hide"><i class="fa fa-filter"></i> Filter</button>

<div class="modal fade" id="filter-modal" role="dialog" aria-labelledby="filter-modal" aria-hidden="true" tabindex="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Filter {{ @$title }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="block-content row" style="padding-bottom: 10px;">
                <form id="filterForm" class="row">
                    {!! csrf_field() !!}
                    {{
                        Form::generateInput( $name.'Filter', $input, 
                            array_merge([ 'use_label'=>false, 'width'=>$options['width'], 'timeout'=>'1' ], $options)
                        ) 
                    }}
                </form>
            </div>
            <div class="modal-footer" style="justify-content: space-between;">
                <div style="padding-left: 10px;">
                    <button type="button" id="submitFilterButton" class="btn btn-primary" data-dismiss="modal" onclick="filteringReportWithActionCallback(paramFilter,{{ $timeout ?? '1.5' }})"><i class="fa fa-filter"></i> Submit Filter</button>
                    <button type="button" id="resetFilterButton" class="btn btn-outline-danger" data-dismiss="modal" onclick="triggerResetWithActionCallback(paramReset)" style="margin-left: 5px;"><i class="fa fa-refresh"></i> Reset Filter</button>
                </div>
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal" style="margin-right: 10px;">Close</button>
            </div>
        </div>
    </div>
</div>

@push('additional-css')
<style type="text/css">
    .act-btn{
        transform: rotate(-90deg);
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        display: block;
        font-size: 15px;
        font-weight: bold;
        text-decoration: none;
        transition: ease all 0.3s;
        position: fixed;
        top: 50%;
        right: -20px;
        z-index: 9999;
    }
    .select2-container{
        width: 100% !important;
    }
</style>
@endpush

@push('function-js')
{{-- <script type="text/javascript"> --}}
    var url   = '{!! $options['url'] !!}';
    var order = [
        {!! $options['order'] !!}
    ];
    var columnDefs = [
        {!! $options['columnDefs'] !!}
    ];
    var tableColumns = [
        {!! $options['colums'] !!}
    ];
    var tmpFilter   = [];
    var filterId    = [{!! implode(', ',$inputId) !!}];
    var paramFilter = ['{{ $table }}', $('#{{ $table }}'), url, tableColumns, columnDefs, order, '#export'];
    var paramReset  = [filterId, '{{ $table }}', $('#{{ $table }}'), url, tableColumns, columnDefs, order, '#export'];
    $(document).ready(function() {
        $('#submitFilterButton').click();
        setTimeout(function() {
            $('#submitFilterButton').click();
    }, 500);
    });
{{-- </script> --}}
@endpush