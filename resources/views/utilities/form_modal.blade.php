@php
//################################
// name -> purpose of main view, for example menu name, use underscore, example: product_focus
// url  -> form action url, example: route('focus.add')
// input -> input that want to appear, in array, example: 
//    [
//        'name'          => 'product',
//        'type'          => 'select2',
//        'required'      => 'true',
//        'multiple'      => 'true',
//        'route'         => 'product-select2',
//        'return_text'   => "obj.code + ' | ' + obj.name",
//        'edit_field'    => ['area_id','name']
//    ],
//    [
//        'name'          => 'area',
//        'type'          => 'select2',
//        'multiple'      => 'true',
//        'check_all'     => 'true',
//        'route'         => 'area-select2',
//    ],
//    [
//        'name'          => 'start_month',
//        'type'          => 'date',
//        'required'      => 'true',
//        'viewmode'      => 'months',
//        'minviewmode'   => 'months',
//        'format'        => 'mm/yyyy',
//        'width'         => '6'
//    ],
//    [
//        'name'          => 'end_month',
//        'type'          => 'date',
//        'required'      => 'true',
//        'viewmode'      => 'months',
//        'minviewmode'   => 'months',
//        'format'        => 'mm/yyyy',
//        'width'         => '6'
//    ]
// false_rules -> example:
//    [
//      [
//          'month', 'start_month', '>', 'end_month', 'Start Month tidak boleh lebih dari End Month'
//      ]
//    ]
//################################

$title        = ucwords(str_replace('_',' ',$name));
$thisId       = str_replace(' ','',$title);
$modalId      = "inputModal".$thisId;
$validate     = [];
$onEdit       = [];
$inputCollect = [];

foreach ( $input as $key => $value ) {
    $updateString               = '';
    $id                         = ucwords(str_replace('_',' ',$value['name']));
    $id                         = str_replace(' ','',$id);
    $editField                  = isset($value['edit_field']) ? $value['edit_field'] : ['id','name'];
    $inputCollect[$key]['id']   = $thisId."Input".$id;
    $inputCollect[$key]['name'] = is_array($editField) ? $value['name'] : $editField;

    if ($value['type'] == 'select2') {
        $multiple   = isset($value['multiple']) ? ($value['multiple'] == 'true' ? 'true' : 'false') : 'false';
        if ( isset($value['check_all']) ) {
            if($value['check_all'] == 'true'){
                $updateString = "onEdit".$thisId."Input('select2','$value[name]','".$thisId."Input".$id."',json.".$value['name'].",['$editField[0]','$editField[1]'],true,$multiple);";
            }
        }
        $updateString = !empty($updateString) ? $updateString : "onEdit".$thisId."Input('select2','$value[name]','".$thisId."Input".$id."',{'id':json.$editField[0],'name':json.$editField[1]});";
        $onEdit[] = $updateString;
    } elseif ( $value['type'] == 'select3' ) {
        $onEdit[] = "onEdit".$thisId."Input('$value[type]','$value[name]','".$thisId."Input".$id."',{'id':json.$editField[0],'name':json.$editField[1]});";
    } elseif ( $value['type'] == 'location' ) {
        $onEdit[] = "onEdit".$thisId."Input('$value[type]','$value[name]','".$thisId."Input".$id."',{'latitude':json.$editField[0],'longitude':json.$editField[1]});";
    } elseif ( $value['type'] == 'password' ) {
        
    } else {
        $onEdit[] = "onEdit".$thisId."Input('$value[type]','$value[name]','".$thisId."Input".$id."',json.$editField);";
    }
}

if ( isset($false_rules) ) {
    foreach ( $false_rules as $key => $value ) {
        if ( $value[0] == "month" ) {
            $id         = $thisId."Input".str_replace(' ','',ucwords(str_replace('_',' ',$value['1'])));
            $id2        = $thisId."Input".str_replace(' ','',ucwords(str_replace('_',' ',$value['3'])));
            $validate[] = "
                var startMonth$key  = $('#$id').val();
                var startDate$key   = startMonth$key.split('/');
                var startDate$key   = new Date(startDate".$key."[1], (startDate".$key."[0]-1), '1');
                
                var endMonth$key  = $('#$id2').val();
                var endDate$key   = endMonth$key.split('/');
                var endDate$key   = new Date(endDate".$key."[1], (endDate".$key."[0]-1), '1');

                if (startDate$key $value[2] endDate$key) {
                    swal('Gagal melakukan request', '$value[4]', 'error');
                    return;
                }
            ";
        }
    }
}

$onEdit     = implode(" \n ", $onEdit);
$validate   = implode(" \n ", $validate);
@endphp


<div class="modal fade" id="{{$modalId}}" tabindex="-1" role="dialog" aria-labelledby="{{$modalId}}" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add {{ $title }}</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="{{$thisId}}Form" method="post" action="{{ $url }}">
        {!! csrf_field() !!}
        <input type="hidden" name="id" id="idInput{{$thisId}}">
        <input type="hidden" name="update" id="updateInput{{$thisId}}">
        <div id="inputGen" class="block-content row">
        </div>
        <div class="modal-footer">
          <button id="{{$thisId}}Form-submit" data-form="{{$thisId}}Form" class="btn btn-alt-success">
            <i class="fa fa-save"></i> Save
          </button>
          <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include( 'utilities.input_generator', ['name' => $name.'_input', 'input' => $input, 'formId' => 'inputGen', 'modal' => $modalId] )

@push('additional-css')
<style type="text/css">
    .no-padding {
        padding: 0px !important;
    }
    .m-bot-10{
        margin-bottom: 10px;
    }
</style>
@endpush

@push('additional-js')
<script type="text/javascript">
    $(document).ready(function() {

        $('#{{$thisId}}Form').submit(function(event) {
            // stop the form from submitting the normal way and refreshing the page
            event.preventDefault();

            validate{{$thisId}}();

            var formData = {
                'id'      : $('#idInput{{$thisId}}').val(),
                'update'  : $('#updateInput{{$thisId}}').val(),
                @foreach($inputCollect as $value)
                '{{$value['name']}}' : $('#{{$value['id']}}').val(),
                @endforeach
            };
            var url = $('#{{$thisId}}Form').attr('action');

            $.ajax({
                url   : url,
                type  : 'POST',
                data  : formData,
                success: function (data) {
                    console.log(data)
                    swal('data.title', 'data.message', 'success');
                    // swal(data.title, data.message, data.type);
                },
                error: function(xhr, textStatus, errorThrown){
                    swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
                }
            });

            $('#{{$modalId}}').modal('toggle');

        });

    }); //END READY

    function editModal{{$thisId}}(json) {
        $('#{{$modalId}}').modal('toggle');
        var area = 0;
        $('#idInput{{$thisId}}').val(json.id);
        $('#updateInput{{$thisId}}').val(1);
        clear{{$thisId}}Input();
        {!!$onEdit!!}
    }

    function addModal{{$thisId}}() {
        $('#{{$modalId}}').modal('toggle');
        $('#update').val('');
        clear{{$thisId}}Input();
    }

    function validate{{$thisId}}() {
        {!!$validate!!}
    }
</script>
@endpush