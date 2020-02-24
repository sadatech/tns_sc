  {{--
  //################################
  // selectorTree -> div selector that want to include select tree
  // treeId       -> unique id that needed if you want to add multiple tree in single page
  // input        -> array tree, lowercase, use underscore [example: sub_area for Sub Area input]
  // left         -> number, weight of the select wide, in 1 until 11 of bootstrap width (default: 8)
  // right        -> number, weight of the checkbox wide, in 1 until 11 of bootstrap width (default: 4)
  // this will generate 2 js function to reset the tree input: 1. reset{{$treeId}}Tree(), 2. set{{$treeId}}SelectTree(id, name)
  //################################;
  --}}
@php

  $closure  = '';
  $script   = '';
  $padding  = '';
  $prevValue    = '';
  $prevTitle    = '';
  $prevSelector = '';
  $nextValue    = '';
  $nextTitle    = '';
  $nextSelector = '';

  $content = 
    "
      <div style='display: none;'>
        <select>
          <option value=''>trigger for required popup</option>
        </select>
      </div>
    ";

  $left   = !empty(@$left) ? $left : '8';
  $right  = !empty(@$right) ? $right : '4';
  $treeId = !empty(@$treeId) ? ucfirst($treeId) : '';
  $firstSelector = $treeId.str_replace(' ','', ucwords(str_replace('_',' ',$input[0])));

  foreach($input as $key => $value){
    $title    =  ucwords(str_replace('_',' ',$value));
    $routes   =  str_replace('_','-',$value);
    $selector =  $treeId.str_replace(' ','',$title);
    $nextValue    = !empty($input[$key + 1]) ? $input[$key + 1] : '';
    $nextTitle    = !empty($nextValue) ? ucwords(str_replace('_',' ',$nextValue)) : '';
    $nextSelector = $treeId.str_replace(' ','',$nextTitle);

    $last     = ( count($input) == ($key+1) ? 1 : 0 );
    $script   .= "
      var select".$selector." = '';
      $('#".$selector."Select').select2(setOptions('". route($routes.'-select2') ."', 'Select ".$title."', function (params) {
        return filterData('name', params.term);
      }, function (data, params) {
        return {
          results: $.map(data, function (obj) {                                
            return {id: obj.id+'`^'+obj.name, text: obj.name}
          })
        }
      }));
      $('#new".$selector."Checkbox').change(function(){
        var atLeastOneIsChecked = $('#new".$selector."Checkbox:checkbox:checked').length > 0;
        if (atLeastOneIsChecked) {
          select".$selector." = $('#".$selector."Select').val();
          select2Reset($('#".$selector."Select'));
          $('#".$selector."Select').prop('disabled', true);
          $('#new_".$value."').prop('required',true);
          $('#".$selector."Select').prop('required',false);
          $('#new".$selector."Content').css('display','block');
          ".
          (!empty($nextValue) ? "$('#".$nextSelector."Select').prop('required',true);":"")
          ."
        }else{
          $('#new".$selector."Content').css('display','none');
          $('#".$selector."Select').prop('disabled', false);
          $('#".$selector."Select').prop('required',true);
          $('#new_".$value."').prop('required',false);
          ".
          (!empty($nextValue) ? "$('#".$nextSelector."Select').prop('required',false);":"")
          ."
          if (select".$selector.") {
            var splitted = select".$selector.".split('`^');
            setSelect2IfPatch2($('#".$selector."Select'), splitted[0], splitted[1]);
          }
        }
      });
      ";
    $closure  .= '</div>';
    $padding = ($key > 0) ? 'padding-left: 10px;display: none;' : '';
    $content .= 
      "
        <div id='new".$prevSelector."Content' class='col-md-12 col-sm-12' style='padding: 0;".$padding."'>".
          ( ($key == 0) ? "<label class='col-md-12 col-sm-12' style='padding: 0'>$title</label>" : '')
          .
          ( ($prevValue != '') ? "<div class='col-md-12 col-sm-12' style='padding-right: 0;padding-left: 0;margin-bottom: 5px;'>
                  <input type='text' class='form-control' id='new_".$prevValue."' name='new_".$prevValue."' placeholder='New ".$prevTitle."'>
                </div>" : '')
          ."
          <div class='input-group mb-3 col-sm-12 col-md-12' style='padding: 0;margin-bottom: 5px !important;'>
            <div class='col-md-". $left ." col-sm-12' style='padding: 0'>
              <select class='form-control' style='width: 100%' name='".$value."' id='".$selector."Select' ".( ($key == 0) ? 'required' : '' ).">
              </select>
            </div>
            <div class='input-group-append col-md-". $right ." col-sm-12 padding-r-0'>
              <label class='css-control css-control-primary css-switch pos-abs-r'>
                  <input type='checkbox' class='css-control-input' id='new".$selector."Checkbox' name='new".$selector."Checkbox'>
                  <span class='css-control-indicator'></span> New
              </label>
            </div>
          </div>
      ";
    if($last == 1){
      $content .= 
        "
          <div id='new".$selector."Content' class='input-group col-sm-12 col-md-12' style='padding-right: 0;".$padding."'>
            <div class='col-md-12 col-sm-12' style='padding-right: 0;padding-left: 0;'>
              <input type='text' class='form-control' id='new_".$value."' name='new_".$value."' placeholder='New ".$title."'>
            </div>
        ";
    }
    $prevValue    = $value;
    $prevTitle    = $title;
    $prevSelector = $selector;
  }
  $content .= $closure;
  $content = str_replace(array("\n","\r"), '', $content);
@endphp

@push('additional-js')
<script type="text/javascript">
  $('#{{$selectorTree}}').html("{!!$content!!}");
  $('#{{$selectorTree}}').css('padding','15px');
  $('#{{$selectorTree}}').css('padding-top','0');
  $('#{{$selectorTree}}').css('padding-right','0');
  $('#{{$selectorTree}}').addClass('row');

  {!!$script!!}

  function reset{{$treeId}}Tree() {
    @php
    foreach($input as $key => $value){
      $selector    =  $treeId.str_replace(' ','',ucwords(str_replace('_',' ',$value)));
      echo
        "
        $('#new_".$value."').val('');
        select2Reset($('#".$selector."Select'));
        
        var checked = $('#new".$selector."Checkbox:checkbox:checked').length > 0;
        if (checked) {
          $('#new".$selector."Checkbox').click();
        }
        ";
    }
    @endphp
  }

  function set{{$treeId}}SelectTree(id, name){
    setSelect2IfPatch2($("#{{$firstSelector}}Select"), id+'`^'+name, name);
  }
</script>
@endpush