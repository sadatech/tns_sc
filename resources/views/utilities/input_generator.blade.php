@php
//################################
// name  -> the purpose of form input, example: product_focus_crud
// filter-> is this filter function, example: true
// formId-> id that want to implement input
// width -> width of filter input for col-md-?, default 4, example: 4
// modal -> modal id, if the input is show on the modal
// with_label -> define of using label for the input, example: true
// input -> input that want to appear, in array, example: 
    // [
    //     [
    //         'name'          => 'product',
    //         'type'          => 'select2',
    //         'multiple'      => 'true',
    //         'check_all'     => 'true',
    //         'route'         => 'product-select2',
    //         'return_id'     => "obj.id",
    //         'return_text'   => "obj.code + ' | ' + obj.name"
    //         'div_class'     => 'm-bot-10',
    //         'input_class'   => 'm-bot-10',
    //         'width'         => ['10','2'],
    //         'edit_id'       => 'id',
    //         'edit_text'     => 'name',
    //     ],
    //     [
    //         'name'          => 'start_month',
    //         'label'         => 'Choose the Start of Month',
    //         'type'          => 'date',
    //         'viewmode'      => 'months',
    //         'minviewmode'   => 'months',
    //         'format'        => 'mm/yyyy'
    //         'width'         => '6',
    //     ],
    //     [
    //         'name'           => 'area',
    //         'type'           => 'text',
    //         'placeholder'    => 'text',
    //         'default'        => '1',
    //         'readonly'       => 'true',
    //         'width'          => '12',
    //     ],
    //     [
    //         'name' => 'gender',
    //         'type' => 'select',
    //         'width'=> '12',
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
    //  ]
//################################

$inputs       = [];
$select3      = [];
$script       = [];
$inputId      = [];
$clearInput   = [];
$defaultInput = [];
$thisTitle    = ucwords(str_replace('_',' ',$name));
$thisId       = str_replace(' ','',$thisTitle);
$filter       = isset($filter) ? ($filter == 'true' ? true : false) : false;
$thisInputId  = ($filter ? 'finput_' : 'input_') . $thisId;

    foreach($input as $key => $value){
        $content          = '';
        $js               = '';
        $check_all        = '';
        $inputJsWithCHeck = '';
        $label            = ucwords(str_replace('_',' ',$value['name']));
        $label            = isset($value['label']) ? $value['label'] : $label;
        $currentId        = $thisId.str_replace(' ','',$label);
        $divClass         = isset($value['div_class']) ? $value['div_class'] : '';
        $inputClass       = isset($value['input_class']) ? $value['input_class'] : '';
        $width            = isset($value['width']) ? $value['width'] : $width;
        $currentClear     = "$('#$currentId').val('');";

        $content .= "<div class='col-md-".(is_array($width) ? "12" : $width)." filter-div row-no-margin $divClass".($value["type"] == "select3" ? "padding-r-0" : "")."'>".
                    ( isset($with_label) ? 
                        ( $with_label == 'true' ? 
                            ( !isset($value['with_label']) ? 
                                "<label>$label</label>" 
                                : ( $value['with_label'] == 'true' ? "<label>$label</label>" : "" )
                            )
                            : ""
                        ) 
                        : "" 
                    );
        
        if($value['type'] == 'select2'){
            $multi        = (isset($value["multiple"]) ? ($value["multiple"] == "true" ? "multiple='multiple'" : "") : "" );
            $select2      = "";
            $enableSelect = "";

            $select2returnValue = (isset($value['return_id']) ? $value['return_id'] : 'obj.id');

            if(isset($value['check_all']))
                if($value['check_all'] == 'true'){
                    $inputId[] = "#".$currentId."_all";
                    $check_all = 'true';
                    $width     = count($width) > 1 ? $width : ['9','3'];
                    $textName  = '';
                    if($multi != ''){
                        $textName = '[]';
                        $inputJsWithCHeck = "
                            var tmpSelectWithCheck = [];
                            $.each($('#$currentId').val(), function( index, value ) {
                                var splitted = value.split('`^');
                                tmpSelectWithCheck.push(splitted[0]);
                            });
                            self.selected('".$currentId."', tmpSelectWithCheck);
                        ";
                    }else{
                        $inputJsWithCHeck = "
                            var splitted = $('#$currentId').val().split('`^');
                            self.selected('".$currentId."', splitted[0]);
                        ";
                    }

                    $select2returnValue = "obj.id+'`^'+obj.name";
                    $select2 =
                    "<div class='row-no-margin' style='width: 100%;'>
                        <div class='col-md-$width[0]' style='padding: 0;'>
                            <select id='$currentId' name='$value[name]". ($multi != "" ? "[]" : "") ."' $multi class='$inputClass'></select>
                        </div>
                        <div class='col-md-$width[1]' style='padding: 4px 0px 0px 5px;text-align: right;'>
                            <label class='css-control css-control-primary css-checkbox' style='font-size: 7pt;color: #227d82'>
                                <input type='checkbox' class='css-control-input' id='".$currentId."_all' name='".$currentId."_all'>
                                <span class='css-control-indicator'></span> All
                            </label>
                        </div>
                    </div>";

                        $jsCheck = "
                                if('".$currentId."' in tmpCheck){";
                    if($multi != ""){
                        $jsCheck .= "
                                    if (Array.isArray(tmpCheck['".$currentId."']) && tmpCheck['".$currentId."'].length){
                                        $.each(tmpCheck['".$currentId."'], function( index, value ) {
                                            var splitted = value.split('`^');
                                            setSelect2IfPatch2($('#$currentId'), splitted[0], splitted[1]);
                                        });
                                    }
                        ";
                    }else{
                        $jsCheck .= "
                                    if(tmpCheck['".$currentId."'] != ''){
                                        var splitted = tmpCheck['".$currentId."'].split('`^');
                                        setSelect2IfPatch2($('#$currentId'), splitted[0], splitted[1]);
                                    }
                        ";
                    }
                        $jsCheck .= "
                                }
                        ";

                    $js .= "
                        $('#".$currentId."_all').change(function(){
                            var atLeastOneIsChecked = $('#".$currentId."_all:checkbox:checked').length > 0;
                            if (atLeastOneIsChecked) {"
                                .($filter ? "filters['filter_check_all_$value[name]'] = 'true';" : "").
                                "tmpCheck['".$currentId."'] = $('#$currentId').val();
                                select2Reset($('#$currentId'));
                                $('#$currentId').prop('disabled', true);
                            }else{"
                                .($filter ? "filters['filter_check_all_$value[name]'] = 'false';" : "").
                                "$('#$currentId').prop('disabled', false);
                                $jsCheck
                            }
                        });

                        function enable$currentId(){
                            $('#$currentId').prop('disabled', false);
                            var atLeastOneIsChecked = $('#".$currentId."_all:checkbox:checked').length > 0;
                            if (atLeastOneIsChecked) {
                                $('#".$currentId."_all').click();
                            }
                        }
                    ";
                    $enableSelect = "enable".$currentId."()\n";
                }

                $select2    = $select2 == "" ? "<select id='$currentId' name='$value[name]". ($multi != "" ? "[]" : "") ."' $multi class='$inputClass'></select>" : $select2;

                $content    .= $select2;

                $js     .= "
                    $('#$currentId').select2(setOptions('".route($value['route'])."', 'Select $label', function (params) {
                        return filterData('name', params.term);
                    }, function (data, params) {
                        return {
                            results: $.map(data, function (obj) {                                
                                return {id: ". $select2returnValue .", text: ". (isset($value['return_text']) ? $value['return_text'] : 'obj.name') ."}
                            })
                        }
                    }));
                ";

            $currentClear = $enableSelect."select2Reset($('#$currentId'));";
        }else if($value['type'] == 'select3'){
            $content    .= "<div id='div$currentId' style='width: 100%;'></div>";
            $left   = isset($value['width_tree']) ? $value['width_tree'][0] : '10';
            $right  = isset($value['width_tree']) ? $value['width_tree'][1] : '2';

            $select3[] = [ 
                'input'         => $value['tree'], 
                'selectorTree'  => "div$currentId", 
                'treeId'        => "tree$currentId", 
                'left'          => $left, 
                'right'         => $right 
            ];

            $currentClear = "reset".ucfirst("tree$currentId")."Tree()";
        }else if($value['type'] == 'select'){
            $content    .= "<select id='".$currentId."' name='$value[name]' class='form-control $inputClass'>";
                $content .= "<option value='' selected hidden disabled> Select $label</option>";
                foreach($value['item'] as $selectItem){
                    $content .= "<option value='$selectItem[value]'>$selectItem[text]</option>";
                }
            $content    .= "</select>";

            $currentClear = "$('#$currentId').prop('selectedIndex', 0).val()";
        }else if($value['type'] == 'text'){
            $placeholder = isset($value['placeholder']) ? $value['placeholder'] : "Input $label (text)";
            $content .= "<input type='text' id='".$currentId."' name='$value[name]' class='form-control $inputClass' placeholder='$placeholder'".
            (isset($value["readonly"]) ? ( $value["readonly"] == "true" ? " readonly" : "") : "" ).
            ">";

            if(isset($value["default"])){

                $defaultInput[] = "$('#$currentId').val('$value[default]')";
            }

        }else if($value['type'] == 'date'){
            $content    .= "<input type='text' id='".$currentId."' name='$value[name]' class='form-control $inputClass' placeholder='Select $label'>";
            $js         .= "
                $('#".$currentId."').datepicker( {
                    format: ". (isset($value['format']) ? "'$value[format]'" : 'dd/mm/yyyy') .",
                    viewMode: ". (isset($value['viewmode']) ? (strlen($value['viewmode']) > 1 ? "'$value[viewmode]'" : $value['viewmode']) : "0") .",
                    autoclose: true,
                    minViewMode: ". (isset($value['minviewmode']) ? (strlen($value['minviewmode']) > 1 ? "'$value[minviewmode]'" : $value['minviewmode']) : "0") ."
                });
            ";
        }

        $content .= "</div>";
        
        if($filter){
            $script[] = $js."
                $('#".$currentId."').on('change', function () {
                    ". (!empty($inputJsWithCHeck) ? $inputJsWithCHeck : "self.selected('filter_".$value['name']."', $('#".$currentId."').val());" ) ."
                });
            ";
        }else{
            $script[] = $js;
        }

        $inputId[]      = '#'.$currentId;
        $clearInput[]   = $currentClear;

        if(isset($value['index'])){
            $inputs[$value['index']] = $content;
        }else{
            $inputs[] = $content;
        }
    }

    ksort($inputs);

    $defaultInput = implode("\n", $defaultInput);
    
    $inputId      = array_map(function($val) { return "'$val'"; }, $inputId);
    
    $inputHtml    = str_replace(array("\n","\r"), '', implode('',$inputs));
@endphp

@foreach($select3 as $value)
    @include('utilities.select_tree', $value)
@endforeach

@push('additional-css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<style type="text/css">
    .m-bot-10{
        margin-bottom: 10px;
    }
</style>
@endpush

@prepend('additional-js')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script type="text/javascript">
    var tmpCheck = [];
    var inputId = [{!! implode(', ',$inputId) !!}];
    {!! $filter ? "var filterId = [".implode(', ',$inputId)."]" : "" !!}
    $("#{{$formId}}").html("{!! $inputHtml !!}");
    {!! implode('',$script) !!}
    function clear{{$thisId}}(argument) {
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
                    }else{

                    }
                }
            }else{
                setSelect2IfPatch2($("#"+elementId), value['id'], value['name']);
            }
        }else if (type == 'select3') {
            setSelect2IfPatch2($("#"+$('#div'+elementId).find('select')[1].id), value['id'], value['name']);
        }else{
            $("#"+elementId).val(value);
        }
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
</script>
@endprepend