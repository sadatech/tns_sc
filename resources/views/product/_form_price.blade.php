@php
$type = $type ?? '';
$action = $action ?? '';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> {{ $type == '' ? 'Add' : ucfirst($type) }} Price Product</h3>
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
          <div class="form-group">
            <label>Category & Name Product</label>
            <select class="{{$type}}-js-select2 form-control" style="width: 100%" name="id_product" required id="{{ $type }}Product">
              <option value="" disabled selected>Choose your Product</option>
              @foreach($product as $data)
              <option value="{{ $data->id }}">{{ $data->subCategory->name }} - {{ $data->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label >Price</label>
              <div class="input-group-append">
                <span class="input-group-text">Rp</span>
                <div class="input-group">
                  <input type="text" class="currency form-control" id="{{$type}}Price" name="price" placeholder="" required>
                  <div class="input-group-append">
                    <span class="input-group-text">.00</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              {{ Form::textInput('rilis', old('rilis'), ['labelText' => 'Rilis Date', 'id' => $type.'Rilis', 'class' => 'js-datepicker form-control', 'data-week-start' => '1', 'data-autoclose' => 'true', 'data-today-highlight' => 'true', 'data-date-format' => 'yyyy-mm-dd', 'placeholder' => 'yyyy-mm-dd', 'required' => '']) }}
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
  $(".currency").keydown(function (e) {
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
  $(".{{$type}}-js-select2").select2({ 
    dropdownParent: $("#{{$id}}")
  });
  @if ($type == 'edit')
  function editModal(json) {
    $('#editModal').modal('show');
    $('#{{$type}}Form').attr('action', "{{ url('/product/price/update') }}/"+json.id);
    $('#{{$type}}Product').val(json.product).trigger('change');
    $('#{{$type}}Price').val(json.price);
    $('#{{$type}}Rilis').val(json.rilis);
      // console.log(json);
  }
  @endif
</script>
@endpush