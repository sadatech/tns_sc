@php
$type = $type ?? '';
$action = $action ?? '';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> {{ $type == '' ? 'Add' : ucfirst($type) }} Price Product</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ $action }}" method="post" id="{{ $type }}Form">
        @if ($type == 'edit')
        {!! method_field('PUT') !!}
        @endif
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Category & Name Product</label>
            <select class="{{$type}}-js-select2 form-control" style="width: 100%" name="product" required id="{{ $type }}Product">
              <option value="" disabled selected>Choose your Product</option>
              @foreach($product as $data)
              <option value="{{ $data->id }}">{{ $data->subCategory->name }} - {{ $data->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Price</label>
              <input type="text" class="form-control" name="price" placeholder="Input Price" id="{{ $type }}Price" required>
            </div>
            <div class="form-group col-md-6">
              <label>Rilis Date</label>
              <input class="js-datepicker form-control" type="text" id="{{ $type }}Rilis" name="rilis" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-alt-success">
            <i class="fa fa-save"></i> Save
          </button>
          <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('additional-js')
<script type="text/javascript">
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