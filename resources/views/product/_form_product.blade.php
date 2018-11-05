@php
$type = $type ?? '';
$action = $action ?? '';
@endphp

<div class="modal fade" id="{{$id}}" tabindex="-1" role="dialog" aria-labelledby="{{$id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
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

                    {{ Form::select2Input('measure', [], App\MeasurementUnit::toDropDownData(), ['multiple' => true, 'id' => $type.'MeasurementUnit']) }}
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
            $('#{{$type}}MeasurementUnit').val(json.measure).trigger('change');
            console.log(json)
        }
    @endif
</script>
@endpush