@php
$type = $type ?? '';
$action = $action ?? '';
@endphp

<div class="modal fade" id="{{$id}}" tabindex="-1" role="dialog" aria-labelledby="{{$id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-plus"></i> {{ $type == 'edit' ? "Edit" : "Add" }} Product</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::open(['url' => $action, 'id' => $type . 'Form']) }}
                {!! $type == 'edit' ? method_field('PUT') : "" !!}
                <div class="block-content">

                    <div class="row">
                        <div class="form-group col-md-6">
                          <label>Product Code</label>
                            <input type="text" class="form-control" name="code" id="{{$type}}Code" placeholder="Product Code">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Product Name</label>
                            <input type="text" class="form-control" name="name" id="{{$type}}Name" placeholder="Product Name" required="required">
                        </div>
                    </div>

                    <div id="input-tree">
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Panel</label>
                            <select class="{{$type}}-js-select2 form-control" style="width: 100%" id="{{$type}}Panel" name="panel" required>
                              <option value="" disabled selected>Choose your Panel</option>
                              <option value="yes">Yes </option>
                              <option value="no">No </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Pcs</label>
                            <input type="text" class="form-control" name="pcs" value="1" readOnly="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox custom-control-inline col-md-6">
                                <input class="custom-control-input" type="checkbox" id="inlineCheckboxCarton" checked>
                                <label class="custom-control-label" for="inlineCheckboxCarton"><span style="font-style: italic;font-size: 10pt;color: lightslategrey;font-weight: lighter;">Pcs each</span> Carton</label>
                            </div>
                           
                            <div class="custom-control custom-checkbox custom-control-inline col-md6">
                                <input class="custom-control-input" type="checkbox" id="inlineCheckboxPack" checked>
                                <label class="custom-control-label" for="inlineCheckboxPack"><span style="font-style: italic;font-size: 10pt;color: lightslategrey;font-weight: lighter;">Pcs each</span> Pack</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="number" max="1000" class="number form-control" name="carton" id="Input1" placeholder="example: 12">
                        </div>
                        <div class="col-md-6">
                            <input type="number" max="1000" class="number form-control" name="pack" id="Input2" placeholder="example: 6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-alt-success">
                        <i class="fa fa-save"></i> Save
                    </button>
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@push('additional-js')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

  @include('utilities.select_tree', [ 'input' => ['sub_category','category','brand'], 'selectorTree' => 'input-tree', 'treeId' => 'first', 'left' => '10', 'right' => '2' ])

    $("#inlineCheckboxCarton").change(function() {
      var checked = $('#inlineCheckboxCarton:checkbox:checked').length > 0;
        if (checked) {
            $("#Input1").show();
        }else{
            $("#Input1").hide();
        }
    });

    $("#inlineCheckboxPack").change(function() {
        var checked = $('#inlineCheckboxPack:checkbox:checked').length > 0;
        if (checked) {
            $("#Input2").show();
        }else{
            $("#Input2").hide();
        }
    });

    $(".number").keydown(function (e) {
      if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
          (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
          (e.keyCode >= 35 && e.keyCode <= 40)) 
      {
          return;
      }
      if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
          e.preventDefault();
      }
    });

    function editModal(json) {
        resetFirstTree();
        $('#{{$id}}').modal('show');
        $('#id').val(json.id);
        $('#update').val(1);
        $('#{{$type}}Name').val(json.name);
        $('#{{$type}}Code').val(json.code);
        setFirstSelectTree(json.subcategory.id, json.subcategory.name);
        $('#{{$type}}Product').val(json.product).trigger('change');
        $('#{{$type}}Panel').val(json.panel).trigger('change');
        if (json.carton != null) {
          toggleOnCheckId('inlineCheckboxCarton');
          $('#Input1').val(json.carton);
        }else{
          toggleOffCheckId('inlineCheckboxCarton');
        }
        if (json.pack != null) {
          toggleOnCheckId('inlineCheckboxPack');
          $('#Input2').val(json.pack);
        }else{
          toggleOffCheckId('inlineCheckboxPack');
        }
        $('#{{$type}}Pcs').val(json.pcs);
        // $('#{{$type}}MeasurementUnit').val(json.measure).trigger('change');
        // console.log(json.carton)
    }

    function addModal() {
      resetInput();
    }

    function resetInput() {
        resetFirstTree();
        toggleOnCheck();
        $('#update').val('');
        $('#id').val('');
        $('#{{$type}}Name').val('');
        $('#{{$type}}Code').val('');
        $('#Input1').val('');
        $('#Input2').val('');
        select2Reset($('#{{$type}}Product'));
        $('#{{$type}}Panel').val('').trigger('change');
    }

    function toggleOffCheck() {
      var checked = $('#inlineCheckboxCarton:checkbox:checked').length > 0;
      if (checked) {
        $('#inlineCheckboxCarton').click();
      }
      var checked2 = $('#inlineCheckboxPack:checkbox:checked').length > 0;
      if (checked2) {
        $('#inlineCheckboxPack').click();
      }
    }

    function toggleOffCheckId(id) {
      var checked = $('#'+id+':checkbox:checked').length > 0;
      if (checked) {
        $('#'+id).click();
      }
    }

    function toggleOnCheck() {
      var checked = $('#inlineCheckboxCarton:checkbox:checked').length == 0;
      if (checked) {
        $('#inlineCheckboxCarton').click();
      }
      var checked2 = $('#inlineCheckboxPack:checkbox:checked').length == 0;
      if (checked2) {
        $('#inlineCheckboxPack').click();
      }
    }

    function toggleOnCheckId(id) {
      var checked = $('#'+id+':checkbox:checked').length == 0;
      if (checked) {
        $('#'+id).click();
      }
    }    
    
</script>
@endpush