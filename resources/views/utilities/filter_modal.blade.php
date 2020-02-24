@php
//################################
// table_id -> table id tat want to affect
// name -> purpose of main view, for example menu name, use underscore, example: product_focus
// width -> width of filter input for col-md-?, default 4, example: 4
// adjust_display_timeout -> timeout to adjust table width, in second, example: 4
// filter -> input filter that want to appear, in array, example: 
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

$title = ucwords(str_replace('_',' ',$name));
$width = isset($width) ? $width : '4';
$filters    = [];
$script     = [];
$filterId   = [];

    foreach($filter as $key => $value){
        $content    = '';
        $js         = '';
        $label      = ucwords(str_replace('_',' ',$value['name']));
        $check_all  = '';
        $inputJsWithCHeck = "";

        $content .= "<div class='col-md-$width filter-div row-no-margin'>";
        
        if($value['type'] == 'select2'){
            $multi      = (isset($value["multiple"]) ? ($value["multiple"] == "true" ? "multiple='multiple'" : "") : "" );
            $select2Id  = "filter_".$value['name'];
            $select2    = "";

            $select2returnValue = (isset($value['return_id']) ? $value['return_id'] : 'obj.id');


            if(isset($value['check_all']))
                if($value['check_all'] == 'true'){
                    $filterId[] = "'#filter_all_$value[name]'";
                    $check_all = 'true';
                    $textName = '';
                    if($multi != ''){
                        $textName = '[]';
                        $inputJsWithCHeck = "
                            var tmpSelectWithCheck = [];
                            $.each($('#$select2Id').val(), function( index, value ) {
                                var splitted = value.split('`^');
                                tmpSelectWithCheck.push(splitted[0]);
                            });
                            self.selected('filter_$value[name]', tmpSelectWithCheck);
                        ";
                    }else{
                        $inputJsWithCHeck = "
                            var splitted = $('#$select2Id').val().split('`^');
                            self.selected('filter_$value[name]', splitted[0]);
                        ";

                    }

                    $select2returnValue = "obj.id+'`^'+obj.name";
                    $select2 =
                    "
                        <div class='col-md-9' style='padding: 0;'>
                            <select id='$select2Id' name='$value[name]". ($multi != "" ? "[]" : "") ."' $multi></select>
                        </div>
                        <div class='col-md-3' style='padding: 4px 0px 0px 5px;'>
                            <label class='css-control css-control-primary css-checkbox' style='font-size: 7pt;color: #227d82'>
                                <input type='checkbox' class='css-control-input' id='filter_all_$value[name]' name='filter_all_$value[name]'>
                                <span class='css-control-indicator'></span> All
                            </label>
                        </div>
                    ";

                        $jsCheck = "
                                if('filter_$value[name]' in tmpFilter){";
                    if($multi != ""){
                        $jsCheck .= "
                                    if (Array.isArray(tmpFilter['filter_$value[name]']) && tmpFilter['filter_$value[name]'].length){
                                        $.each(tmpFilter['filter_$value[name]'], function( index, value ) {
                                            var splitted = value.split('`^');
                                            setSelect2IfPatch2($('#$select2Id'), splitted[0], splitted[1]);
                                        });
                                    }
                        ";
                    }else{
                        $jsCheck .= "
                                    if(tmpFilter['filter_$value[name]'] != ''){
                                        var splitted = tmpFilter['filter_$value[name]'].split('`^');
                                        setSelect2IfPatch2($('#$select2Id'), splitted[0], splitted[1]);
                                    }
                        ";
                    }
                        $jsCheck .= "
                                }
                        ";

                    $js .= "
                        $('#filter_all_$value[name]').change(function(){
                            var atLeastOneIsChecked = $('#filter_all_$value[name]:checkbox:checked').length > 0;
                            if (atLeastOneIsChecked) {
                                filters['filter_check_all_$value[name]'] = 'true';
                                tmpFilter['filter_$value[name]'] = $('#$select2Id').val();
                                select2Reset($('#$select2Id'));
                                $('#$select2Id').prop('disabled', true);
                            }else{
                                filters['filter_check_all_$value[name]'] = 'false';
                                $('#$select2Id').prop('disabled', false);
                                $jsCheck
                            }
                        });
                    ";
                }

            $select2    = $select2 == "" ? "<select id='$select2Id' name='$value[name]". ($multi != "" ? "[]" : "") ."' $multi></select>" : $select2;

            $content    .= $select2;

            $js     .= "
                $('#$select2Id').select2(setOptions('".route($value['route'])."', 'Select $label', function (params) {
                    return filterData('name', params.term);
                }, function (data, params) {
                    return {
                        results: $.map(data, function (obj) {                                
                            return {id: ". $select2returnValue .", text: ". (isset($value['return_text']) ? $value['return_text'] : 'obj.name') ."}
                        })
                    }
                }));
            ";
        }else if($value['type'] == 'select'){
            $content    .= "<select id='filter_$value[name]' name='$value[name]' class='form-control default-select'>";
                $content .= "<option selected='true' disabled='disabled'> Select $label</option>";
                foreach($value['item'] as $selectItem){
                    $content .= "<option value='$selectItem[value]'>$selectItem[text]</option>";
                }
            $content    .= "</select>";
        }else if($value['type'] == 'text'){
            $content .= "<input type='text' id='filter_$value[name]' name='$value[name]' class='form-control' placeholder='Input $label (text)'>";
        }else if($value['type'] == 'date'){
            $content    .= "<input type='text' id='filter_$value[name]' name='$value[name]' class='form-control' placeholder='Select $label'>";
            $js         .= "
                $('#filter_$value[name]').datepicker( {
                    format: ". (isset($value['format']) ? "'$value[format]'" : 'dd/mm/yyyy') .",
                    viewMode: ". (isset($value['viewmode']) ? (strlen($value['viewmode']) > 1 ? "'$value[viewmode]'" : $value['viewmode']) : "0") .",
                    autoclose: true,
                    minViewMode: ". (isset($value['minviewmode']) ? (strlen($value['minviewmode']) > 1 ? "'$value[minviewmode]'" : $value['minviewmode']) : "0") ."
                });
            ";
        }

        $content .= "</div>";
        
        $script[] = $js."
                $('#filter_$value[name]').on('change', function () {
                    ". (!empty($inputJsWithCHeck) ? $inputJsWithCHeck : "self.selected('filter_$value[name]', $('#filter_$value[name]').val());" ) ."
                });
            ";

        $filterId[] = "'#filter_$value[name]'";

        if(isset($value['index'])){
            $filters[$value['index']] = $content;
        }else{
            $filters[] = $content;
        }
    }

    ksort($filters);
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
                {!! implode('',$filters) !!}
                </form>
            </div>
            <div class="modal-footer" style="justify-content: space-between;">
                <div style="padding-left: 10px;">
                    <button type="button" id="submitFilterButton" class="btn btn-primary" data-dismiss="modal" onclick="filteringReportWithActionCallback(paramFilter,{{ $adjust_display_timeout ?? '1.5' }})"><i class="fa fa-filter"></i> Submit Filter</button>
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
    .filter-div{
        margin-bottom: 10px;
    }
</style>
@endpush

@push('additional-js')
<script type="text/javascript">
    var tmpFilter = [];
    var filterId = [{!! implode(', ',$filterId) !!}];
    var paramFilter = ['{{ $table_id }}', $('#{{ $table_id }}'), url, tableColumns, columnDefs, order, '#export'];
    var paramReset  = [filterId, '{{ $table_id }}', $('#{{ $table_id }}'), url, tableColumns, columnDefs, order, '#export'];
    {!! implode('',$script) !!}
    $(document).ready(function() {
        $('#submitFilterButton').click();
        setTimeout(function() {
            $('#submitFilterButton').click();
    }, 500);
    });
</script>
@endpush