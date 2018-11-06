@php
$type = $type ?? '';
$action = $action ?? '';
@endphp

<div class="modal fade" id="{{$id}}" tabindex="-1" role="dialog" aria-labelledby="{{$id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
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
                    {{ 
                        Form::select2Input('id_subcategory', old('id_subcategory'), App\SubCategory::toDropDownData(), [
                            'labelText' => 'Sub Category Product',
                            'required' => '',
                            'id' => $type . 'SubCategory'
                        ])
                    }}

                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::textInput('code', old('code'), ['labelText' => 'Code SKU Product', 'id' => $type.'Code',  'required' => '']) }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::textInput('name', old('name'), ['labelText' => 'SKU Product', 'required' => '', 'id' => $type.'Name']) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::select2Input('panel', old('panel'), App\Product::getPanelOptions(), ['labelText' => 'Panel', 'required' => '', 'id' => $type.'Panel']) }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::select2Input('stock_type_id', old('stock_type_id'), App\ProductStockType::toDropDownData(), ['labelText' => 'Stock Type', 'required' => '', 'id' => $type.'StockType']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox custom-control-inline col-md-6">
                                <input class="custom-control-input" type="checkbox" id="example-inline-checkbox1" checked>
                                <label class="custom-control-label" for="example-inline-checkbox1">Carton</label>
                            </div>
                           
                            <div class="custom-control custom-checkbox custom-control-inline col-md6">
                                <input class="custom-control-input" type="checkbox" id="example-inline-checkbox2" checked>
                                <label class="custom-control-label" for="example-inline-checkbox2">Pack</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="carton" id="Input1">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="pack" id="Input2">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>PCS</label>
                        <input type="text" class="form-control" name="pcs" value="1" readOnly="">
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
$("#example-inline-checkbox1").change(function() {
    if ($(this).removeAttr("checked")) {
        $("#Input1").hide();
    }
});

$("#example-inline-checkbox2").change(function() {
    if ($(this).removeAttr("checked")) {
        $("#Input2").hide();
    }
});

$("#example-inline-checkbox1").change(function() {
    if ($(this).prop("checked")) {
        $("#Input1").show();
    }
});

$("#example-inline-checkbox2").change(function() {
    if ($(this).prop("checked")) {
        $("#Input2").show();
    }
});
    @if ($type == 'edit')
        function editModal(json) {
            $('#{{$id}}').modal('show');
            $('#{{$type}}Form').attr('action', "{{ url('/product/summary/update') }}/"+json.id);
            $('#{{$type}}Name').val(json.name);
            $('#{{$type}}Code').val(json.code);
            $('#{{$type}}SubCategory').val(json.subcategory).trigger('change');
            $('#{{$type}}Product').val(json.product).trigger('change');
            $('#{{$type}}Panel').val(json.panel).trigger('change');
            $('#{{$type}}StockType').val(json.stock_type_id).trigger('change');
            $('#Input1').val(json.carton);
            $('#Input2').val(json.pack);
            $('#{{$type}}Pcs').val(json.pcs);
            // $('#{{$type}}MeasurementUnit').val(json.measure).trigger('change');
            console.log(json)
        }
    @endif
</script>
@endpush