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
$inputId = [];
$title   = ucwords(str_replace('_',' ',$name));
$thisId  = str_replace(' ','',$title);

foreach ( $input as $key => $value ) {
    $label     = ucwords(str_replace('_',' ',$value['name']));
    $label     = isset($value['label']) ? $value['label'] : $label;
    $currentId = $thisId.str_replace(' ','',$label);
    $inputId[] = '#'.$currentId;
}

$inputId = array_map(function($val) { return "'$val'"; }, $inputId);

$timeout = isset($options['timeout']) ? $options['timeout'] : '1.5';

$tableActionLeft  = [];
$tableActionRight = [];
$right    = 0;
$left     = 0;
$download = [];

if ( isset($options['add']) ) {
    if ($options['add']) {
        $left++;
        $tableActionLeft[] = "
            <div class='m-bot-10 float-left'>
                <button id='add-button' class='btn btn-primary btn-square' data-toggle='modal' onclick='addModalProductFocus()'>
                    <i class='fa fa-plus mr-2'></i>
                    Add Data
                </button>
            </div>
        ";
    }
}

if ( isset($options['direct-download']) ) {
    if ($options['direct-download']) {
        $right++;
        $buttonId = "direct-download";
        $tableActionRight[] = "
            <button id='$buttonId' class='btn btn-success btn-square float-right ml-10' onclick=\\\"directDownload('" . route('focus.export') . "', '$buttonId')\\\">
                <i id='$buttonId-icon' class='si si-cloud-download mr-2'></i>
                Direct Download
            </button>
        ";
        $download['direct'] = true;
    }
}

if ( isset($options['import']) ) {
    if ($options['import']) {
        $right++;
        $buttonId = "import-button";
        $tableActionRight[] = "
            <button id='$buttonId' class='btn btn-outline-info btn-square float-right ml-10' onclick=\\\"importModal('$buttonId')\\\">
                <i class='si si-cloud-upload mr-2'></i>
                Import Data
            </button>
        ";
        $formRoute     = isset( $options['import-form-route'] ) ? $options['import-form-route'] : $name.'.import';
        $templateRoute = isset( $options['import-template-route'] ) ? $options['import-template-route'] : $name.'.download-template';
        echo Form::importModal('route', $formRoute, $templateRoute);
    }
}

if ( isset($options['in-direct-download']) ) {
    if ($options['in-direct-download']) {
        $right++;
        $buttonId = "in-direct-download";
        $tableActionRight[] = "
            <button id='$buttonId' class='btn btn-outline-success btn-square float-right ml-10' onclick=\\\"inDirectDownload('" . route('focus.download') . "', '$buttonId')\\\">
                <i id='$buttonId-icon' class='si si-cloud-download mr-2'></i>
                In-direct Download
            </button>
        ";
        $download['in-direct'] = true;
    }
}

echo count($download) > 0 ? Form::exportFunction($download) : "";

if ( isset($options['job-status']) ) {
    if ($options['job-status']) {
        $right++;
        $tableActionRight[] = "
            <button id='upload-status' class='btn btn-outline-warning btn-square float-right ml-10'>
                <i class='fa fa-check-square-o'></i>
                View Job Status
            </button>
        ";
        $model = isset($options['model']) ? $options['model'] : 'App\\'.str_replace(' ', '', ucwords($title) );
        echo Form::jobStatusModal('route',$model);
    }
}
$tableAction = "";
$tableAction .= $left > 0 ? implode("", $tableActionLeft) : "";
$tableAction .= $right > 0 ? "<div class='m-bot-10 float-right'>".implode("", $tableActionRight)."</div>" : "";
$tableAction = str_replace(array("\n","\r"), "", $tableAction);
$tableAction = "<div style='width:100%;'>". $tableAction ."</div>";

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

    $('#{{ $table }}').prev().append("{!! $tableAction !!}");

    // Filtering data with action callback
    function filteringReportWithActionCallback(arrayOfData, timeout = '') {
        var table        = arrayOfData[0];
        var newElement   = $('#'+table);
        var element      = arrayOfData[1];
        var url          = arrayOfData[2];
        var tableColumns = arrayOfData[3];
        var columnDefs   = arrayOfData[4];
        var order        = arrayOfData[5];

        $(document).ready(function () {
            setupTable(table, newElement, order, columnDefs, tableColumns, url);
            adjustTableDisplay(timeout);
        });
    }

    function triggerResetWithActionCallback (arrayOfData) {
        var data         = arrayOfData[0];
        var table        = arrayOfData[1];
        var element      = arrayOfData[2];
        var newElement   = $('#'+arrayOfData[1]);
        var url          = arrayOfData[3];
        var tableColumns = arrayOfData[4];
        var columnDefs   = arrayOfData[5];
        var order        = arrayOfData[6];

        data.map((id) => {
            $(id).prop('disabled', false);
            if ( $(id).is(':checkbox') ) {
                $(id).prop('checked',false);
            }else{
                $(id).val('').trigger('change');
                if($(id).hasClass('default-select')){$(id).prop("selectedIndex", 0).val();}
            }
        });

        this.filters = {};

        setupTable(table, newElement, order, columnDefs, tableColumns, url);
    }

    function setupTable(table, newElement, order, columnDefs, tableColumns, url) {
        if($.fn.dataTable.isDataTable('#'+table)){
            newElement.DataTable().clear();
            newElement.DataTable().destroy();
        }

        newElement.DataTable({
            processing:     true,
            serverSide:     true,
            scrollX:        true,
            scrollCollapse: true,
            bFilter:        false,
            rowId:          "row_id",
            ordering:       false,
            order:          order,
            columnDefs:     columnDefs,
            columns:        tableColumns,
            ajax: {
                url: url,
                type: 'POST',
                data: filters,
                dataType: 'json',
                error: function (data) {
                    swal("Error!", "Failed to load Data!", "error");
                },
                dataSrc: function(result){
                    this.data = result.data;
                    return result.data;
                },
            },
            drawCallback: function(){
                $('.js-swal-delete').on('click', function(){
                    var id = $(this).data("id");
                    var deleteUrl = $(this).data("url");
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
                            $.ajax({
                                url: deleteUrl,
                                type: 'GET',
                                success: function (data) {
                                    $("#"+id).remove();
                                },
                                error: function(xhr, textStatus, errorThrown){
                                    swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
                                }
                            });
                        } else if (result.dismiss === 'cancel') {
                            swal('Cancelled', 'Your data is safe :)', 'error');
                        }
                    });
                });
            },
        });
    }
{{-- </script> --}}
@endpush