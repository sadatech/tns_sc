@php
$type = $type ?? '';
$action = $action ?? '';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> {{ $type == '' ? 'Add' : ucfirst($type) }} SKU Unit</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      {{ Form::open(['url' => $action, 'id' => $type . 'Form']) }}
        @if ($type == 'edit')
        {!! method_field('PUT') !!}
        @endif
        <div class="block-content">
          {{ Form::textInput('name', old('name'), ['id' => $type.'Name', 'required' => '']) }}
          {{ Form::numberInput('conversion_value', old('conversion_value'), ['id' => $type.'ConversionValue', 'required' => '', 'min' => '1']) }}
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
    $('#editModal').modal('show');
    $('#{{$type}}Form').attr('action', "{{ url('/product/sku-unit/update') }}/"+json.id);
    $('#{{$type}}Name').val(json.name);
    $('#{{$type}}ConversionValue').val(json.conversion_value);
  }
  @endif
</script>
@endpush