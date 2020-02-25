@php
//################################
// name  -> the purpose of form input, example: product_focus_crud
// #OPTION
// filter-> is this filter function, example: true
// width -> width of filter input for col-md-?, default 4, example: 4
// modal -> modal id, if the input is show on the modal
// use_label -> define of using label for the input, example: true
// #OPTION
// input -> input that want to appear, in array
// example: 
// Form::generateInput('form_test12', 
//     [
//         [
//         'name'  => 'text_input',
//         'type'  => 'text',
//         ],
//         [
//         'name'     => 'year_input',
//         'type'     => 'date',
//         'min_view' => 'years',
//         'view'     => 'years',
//         'format'   => 'yyyy'
//         ],
//         [
//         'name'     => 'month_input',
//         'type'     => 'date',
//         'min_view' => 'months',
//         'view'     => 'months',
//         'format'   => 'MM'
//         ],
//         [
//         'name'  => 'date_input',
//         'type'  => 'date',
//         ],
//         [
//         'name'  => 'file_input',
//         'type'  => 'file',
//         ],
//         [
//         'name'      => 'location_input',
//         'type'      => 'location',

//         'use_label' => false,
//         ],
//         [
//         'name'  => 'select_multi_input',
//         'type'  => 'select-multi',
//         'route' => 'product-select2',
//         ],
//         [
//         'name'  => 'select2_input',
//         'type'  => 'select2',
//         'route' => 'category-select2',
//         ],
//         [
//         'name'        => 'select2_check_input',
//         'type'        => 'select2',
//         'multiple'    => true,
//         'check_all'   => true,
//         'route'       => 'product-select2',
//         'return_id'   => "obj.id",
//         'return_text' => "obj.code + ' | ' + obj.name",
//         'width'       => ['11','1'],
//         'edit_id'     => 'id',
//         'edit_text'   => 'name',
//         ],
//         [
//         'name'  => 'radio_input',
//         'type'  => 'radio',
//         'value' => ['satu'=>'1','dua'=>'2'],
//         ],
//         [
//         'name'  => 'checkbox_input',
//         'type'  => 'checkbox',
//         'value' => ['satu'=>'1','dua'=>'2'],
//         ],
//         [
//         'name'       => 'select_tree_input',
//         'type'       => 'select3',
//         'width'      => '12',
//         'width_tree' => ['9','3'],
//         'use_label'  => false,
//         'edit_field' => ['id_sub_category','sub_category_name'],
//         'tree'       => ['sub_category','category','brand'],
//         'route'      => ['sub-category-select2','category-select2','brand-select2'],
//         ],
//         [
//         'name' => 'id',
//         'type' => 'hidden',
//         ],
//         [
//         'name' => 'update',
//         'type' => 'hidden',
//         ],
//         [
//         'name'    => 'type',
//         'type'    => 'hidden',
//         'default' => $market,
//         ],
//         [
//         'name' => 'email_input',
//         'type' => 'email',
//         ],
//         [
//         'name' => 'number_input',
//         'type' => 'number',
//         ],
//         [
//         'name' => 'password_input',
//         'type' => 'password',
//         ]
//     ], ['filter'=>false,'width'=>'4','use_label'=>true]
// )
//################################


$includeLoc   = 0;
$includeDate  = 0;
$select3      = [];
$script       = [];
$inputId      = [];
$clearInput   = [];
$defaultInput = [];
$defaultWidth = isset($options['width']) ? $options['width'] : '12';
$modal        = isset($options['modal']) ? $options['modal'] : null;
$use_label    = isset($options['use_label']) ? $options['use_label'] : false;
$thisTitle    = ucwords(str_replace('_',' ',$name));
$thisId       = str_replace(' ','',$thisTitle);
$filter       = isset($options['filter']) ? $options['filter'] : false;
$thisInputId  = ($filter ? 'finput_' : 'input_') . $thisId;

    foreach ( $input as $key => $value ) {
        $content            = '';
        $js                 = '';
        $check_all          = '';
        $inputJsWithCHeck   = '';
        $label              = ucwords(str_replace('_',' ',$value['name']));
        $label              = isset($value['label']) ? $value['label'] : $label;
        $currentId          = $thisId.str_replace(' ','',$label);
        $inputClass         = isset($input[$key]['input_class']) ? $input[$key]['input_class'] : '';
        $divClass           = isset($value['div_class']) ? $value['div_class'] : '';
        $width              = isset($value['width']) ? $value['width'] : $defaultWidth;
        $currentClear       = "$('#$currentId').val('');";
        $defLlabel          = isset($use_label) ? ( $use_label ? ( !isset($value['use_label']) ? "<label>$label</label>" : ( $value['use_label'] ? "<label>$label</label>" : "" ) ) : "" ) : "" ;
        $required           = isset($value['required']) ? ( $value['required'] ? ['required'=>'required'] : [] ) : [];
        $divClass           .= empty($label) ? 'm-top-15' : '';
        $value['elOptions'] = array_merge( $required, (isset($value['elOptions']) ? $value['elOptions'] : [] ) );
        $placeholder        = isset($value['placeholder']) ? $value['placeholder'] : "Please enter ".ucwords($label)." here";

        echo ($value['type'] != 'hidden') ?
                    "<div class='col-md-".(is_array($width) ? "12" : $width)." row-no-margin $divClass'>".
                    $defLlabel
                    : "";
        
        if ( $value['type'] == 'select2' ) {
            $multi        = (isset($value["multiple"]) ? ($value["multiple"] ? "multiple='multiple'" : "") : "" );
            $select2      = "";
            $enableSelect = "";
            $checkAll     = isset($value['check_all']) ? ( $value['check_all'] ? true : false ) : false;

            $select2returnValue = (isset($value['return_id']) ? $value['return_id'] : 'obj.id');

            if( $checkAll ) {
                echo Form::select2CheckboxInput($value['name'], $currentId, $value['route'], ['elOptions'=>['id'=>$currentId], 'useLabel'=>false ]);
            } else {
                echo Form::select2Input($value['name'], null, $value['route'], ['elOptions'=>['id'=>$currentId], 'useLabel'=>false ]);
            }

            $enableSelect = $checkAll ? "enable".$currentId."()\n" : '';
            $currentClear = $enableSelect."select2Reset($('#$currentId'));";
        } elseif ( $value['type'] == 'select3' ) {
            echo "<div id='div$currentId' style='width: 100%;'></div>";
            $left    = isset($value['width_tree']) ? $value['width_tree'][0] : '10';
            $right   = isset($value['width_tree']) ? $value['width_tree'][1] : '2';
            $treeId  = "tree$currentId";

            echo Form::selectTreeInput($value['name'], $value['tree'], $value['route'], 
                [
                    'treeId'   => $treeId, 
                    'route'    => $value['route'], 
                    'left'     => $left, 
                    'right'    => $right
                ]);

            $currentClear = "reset".ucfirst($treeId)."Tree()";
        } elseif ( $value['type'] == 'select-multi' ) {
            echo  Form::select2MultipleInput($value['name'], null, $value['route'], [ 'elOptions' => [ 'id' => $currentId ], 'useLabel' => false , 'width100' => true ]);

            $currentClear = "select2val_$currentId = [];generateTable_$currentId();";
        } elseif ( $value['type'] == 'select' ) {
            echo Form::select($value['name'], [null], null, ['class'=> "form-control $inputClass", 'id'=>$currentId, 'placeholder'=>$placeholder]);

            $currentClear = "$('#$currentId').prop('selectedIndex', 0).val();";
        } elseif ( $value['type'] == 'location' ) {
            $includeLoc++;
            echo Form::locationInput($value['name'], null, ['useLabel'=>false,'elOptions'=>['id'=>$currentId]]);

            $currentClear = "initMap$currentId([])";
        } elseif ( $value['type'] == 'text' ) {
            echo Form::textInput($value['name'], ( isset($value['value']) ? $value['value'] : null ),
                [
                  'elOptions'  => array_merge( ['class'=>'form-control '.$inputClass, 'id'=>$currentId], ( isset($value['elOptions']) ? $value['elOptions'] : [] ) ),
                  'useLabel'   => false,
                  'groupClass' => 'm-lr-0'
                ]
            );

            if ( isset($value["default"]) && isset($modal) ) {
                $defaultInput[] = "$('#$currentId').val('$value[default]')";
            }
        } elseif ( $value['type'] == 'date' ) {
            $includeDate = 1;
            $minView = isset( $value['min_view'] ) ? $value['min_view'] : 'days';
            $option = [
                'elOptions'  => array_merge( ['class'=>$inputClass, 'id'=>$currentId], ( isset($value['elOptions']) ? $value['elOptions'] : [] ) ),
                'useLabel'   => false,
                'groupClass' => 'm-lr-0',
                'min_view'   => $minView,
                'view'       => isset( $value['view'] ) ? $value['view'] : $minView,
                'format'     => isset( $value['format'] ) ? $value['format'] : ( $minView == 'years' ? 'yyyy' : ( $minView == 'months' ? 'mm-yyyy' : 'dd-mm-yyyy' ) ),
            ];
              
            echo Form::dateInput($value['name'], ( isset($value['value']) ? $value['value'] : null ), $option );
        } elseif ( $value['type'] == 'number' ) {
            echo Form::number($value['name'], ( isset($value['value']) ? $value['value'] : null ),
                array_merge( 
                    ['class'=>'form-control m-bot-15 '.$inputClass, 'id'=>$currentId, 'placeholder'=>$placeholder], 
                    ( isset($value['elOptions']) ? $value['elOptions'] : [] ) 
                )
            );

            if ( isset($value["default"]) && isset($modal) ) {
                $defaultInput[] = "$('#$currentId').val('$value[default]')";
            }
        } elseif ( $value['type'] == 'email' ) {
            echo Form::email($value['name'], ( isset($value['value']) ? $value['value'] : null ),
                array_merge( 
                    ['class'=>'form-control m-bot-15 '.$inputClass, 'id'=>$currentId, 'placeholder'=>$placeholder], 
                    ( isset($value['elOptions']) ? $value['elOptions'] : [] ) 
                )
            );

            if ( isset($value["default"]) && isset($modal) ) {
                $defaultInput[] = "$('#$currentId').val('$value[default]')";
            }
        } elseif ( $value['type'] == 'password' ) {
            echo Form::password($value['name'],
                array_merge( 
                    ['class'=>'form-control m-bot-15 '.$inputClass, 'id'=>$currentId, 'placeholder'=>$placeholder], 
                    ( isset($value['elOptions']) ? $value['elOptions'] : [] ) 
                )
            );
        } elseif ( $value['type'] == 'hidden' ) {
            echo "<input type='hidden' id='".$currentId."' name='$value[name]'".
                                (isset($value["default"]) ? "value='".$value["default"]."'" : "" ).
                                ">";

            if ( isset($value["default"]) && isset($modal) ) {
                $defaultInput[] = "$('#$currentId').val('$value[default]')";
            }
        } elseif ( $value['type'] == 'file' ) {
            echo Form::fileInput($value['name'],
                [
                  'elOptions'  => array_merge( ['class'=>$inputClass, 'id'=>$currentId], ( isset($value['elOptions']) ? $value['elOptions'] : [] ) ),
                  'useLabel'   => false,
                  'groupClass' =>'m-lr-0'
                ]
            );
        } elseif ( $value['type'] == 'radio' ) {
            echo "<div class='col-md-12'>";
            echo Form::radioInput($value['name'], null, $value['value'],
                [
                  'elOptions'   => array_merge( ['class'=>'custom-control-input', 'id'=>$currentId], ( isset($value['elOptions']) ? $value['elOptions'] : [] ) ),
                  'orientation' => ( isset($value['orientation']) ? $value['orientation'] : 'horizontal' ),
                  'useLabel'    => false
                ]
            );
            echo "</div>";
            $currentClear = "$(\"input[name='".$value['name']."']\").prop('checked', false);";
        } elseif ( $value['type'] == 'checkbox' ) {
            echo "<div class='col-md-12'>";
            echo Form::checkboxInput($value['name'], null, $value['value'],
                [
                  'elOptions'   => array_merge( ['class'=>'custom-control-input '.$currentId, 'id'=>$currentId], ( isset($value['elOptions']) ? $value['elOptions'] : [] ) ),
                  'orientation' => ( isset($value['orientation']) ? $value['orientation'] : 'horizontal' ),
                  'useLabel'    => false
                ]
            );
            echo "</div>";
            $currentClear = "$(\"input[name='".$value['name']."[]']\").prop('checked', false);";
        }

        echo ($value['type'] != 'hidden') ? "</div>" : "";
        
        if ( $filter ) {
            $script[] = $js."
                $('#".$currentId."').on('change', function () {
                    ". (!empty($inputJsWithCHeck) ? $inputJsWithCHeck : "self.selected('filter_".$value['name']."', $('#".$currentId."').val());" ) ."
                });
            ";
        } else {
            $script[] = $js;
        }

        $inputId[]      = '#'.$currentId;
        $clearInput[]   = $currentClear;

    }

    $defaultInput = implode("\n", $defaultInput);
    
    $inputId      = array_map(function($val) { return "'$val'"; }, $inputId);
@endphp

@push('additional-css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<style type="text/css">
    [data-notify="container"] 
    {
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    .pac-container {
        z-index: 99999;
    }
    .m-bot-10{
        margin-bottom: 10px;
    }
    .m-bot-15{
        margin-bottom: 15px;
    }
    .r-15{
        padding-right: 15px;
    }
    .width-100{
        width: 100%;
    }
    .width-50{
        width: 50%;
    }
    .m-left-0{
        margin-left: 0;
    }
    .m-right-0{
        margin-right: 0;
    }
    .m-lr-0{
        margin-left: 0;
        margin-right: 0;
    }
    .m-top-15{
        margin-top: 15px;
    }
    .m-bot-0{
        margin-bottom: 0;
    }
</style>
@endpush
@prepend('additional-css')
<link rel="stylesheet" href="http://localhost/tns_sc/public/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
@endprepend
@prepend('additional-js')
<script src="{{ asset('js/select2-handler.js') }}"></script>
@if($includeDate > 0)
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
@endif
@if($includeLoc > 0)
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyCcAydgyjdaptJ3y8AyiSqgYYMQEU6z7Cg&amp;v=3&amp;libraries=places"></script>
<script src="{{ asset('js/locationpicker.jquery.min.js') }}"></script>
@endif
@endprepend
@prepend('function-js')
{{-- <script type="text/javascript"> --}}
    
    {!! $filter ? "var tmpFilter = [];\nvar inputId = [". implode(', ',$inputId) ."];" : "" !!}
    {!! implode('',$script) !!}
    
    function clear{{$thisId}}() {
        {!! implode("\n",$clearInput) !!}
    }

    function onEdit{{$thisId}}(type, name, elementId, value, valueIndex = [], check = false, multiple = false) {
        var countingCheck = 0;

        if (type == 'select2') {
            if (multiple == true) {
                $.each(value, function(key, val){
                    countingCheck++;
                    setSelect2IfPatch2($("#"+elementId), val[valueIndex[0]], val[valueIndex[1]]);
                });
                if (check == true && countingCheck == 0) {
                    var checkId = "#"+elementId+"_all:checkbox:checked";
                    var atLeastOneIsChecked = $(checkId).length > 0;
                    if (!atLeastOneIsChecked) {
                        $("#"+elementId+"_all").click();
                    }
                }
            }else{
                setSelect2IfPatch2($("#"+elementId), value['id'], value['name']);
            }
        } else if (type == 'location') {
            var fnName = "initMap" + elementId;
            var params = [ parseFloat(value['latitude']) , parseFloat(value['longitude']) ];

            window[fnName](params);
        } else if (type == 'checkbox') {
            $.each(value, function(i, val){
               $("input[name='" + name + "[]'][value='" + val + "']").prop('checked', true);
            });
        } else if (type == 'radio') {
            $("input[name='" + name + "[]'][value='" + value + "']").prop('checked', true);
        } else if (type == 'select-multi') {
            var varName = "select2val_"+ elementId;
            window[varName] = [];
            $.each(value, function( i, v ) {
                window[varName].push({id:"'"+v['id']+"'",name:"'"+v['name']+"'"});
            });

            window["generateTable_"+ elementId]();
        } else if (type == 'select3') {
            setSelect2IfPatch2( $("#"+$('#div'+elementId).next().find('select')[1].id), value['id'], value['name'] );
        } else {
            $("#"+elementId).val(value);
        }
    }

    function cek(){
        clear{{$thisId}}();
    }

    @if(isset($modal))
    var firstShow{{$thisId}} = 0;
    $('#{{$modal}}').on('shown.bs.modal', function(){
        if (firstShow{{$thisId}} == 0) {
            {!!$defaultInput!!}
            firstShow{{$thisId}} = 1;
        }
    });
    @endif
{{-- </script> --}}
@endprepend