@php
$action = $action ?? '';
$type = $type ?? '';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> {{ $type == '' ? 'Add' : ucfirst($type) }} Product Fokus</h3>
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
            <label>Product</label>
            <select class="{{$type}}-js-select2 form-control" style="width: 100%" id="{{$type}}Product" name="id_product" required>
              <option value="" disabled selected>Choose your Product</option>
              @foreach(App\Product::get() as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-md-12">
              {{ Form::select2Input('channel', [], App\Channel::toDropDownData(), ['multiple' => true, 'id' => $type.'Channel']) }}
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12">
            {{ Form::select2Input('area', [], App\Area::toDropDownData(), ['multiple' => true, 'id' => $type.'Area']) }}
            </div>
          
            <div class="form-group col-md-6">
              <label>Month From</label>
              <input class="js-datepicker form-control" type="text" placeholder="Month From" id="{{$type}}DateFrom" name="from" data-month-highlight="true" required>
            </div>
            <div class="form-group col-md-6">
              <label>Month Until</label>
              <input class="js-datepicker form-control" type="text" placeholder="Month Until" id="{{$type}}DateTo" name="to" data-month-highlight="true" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-alt-success">
              <i class="fa fa-save"></i> Save
            </button>
            <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@push('additional-js')
<script type="text/javascript">
  $(".js-datepicker").datepicker( {
    format: "mm/yyyy",
    viewMode: "months",
    autoclose: true,
    minViewMode: "months"
  });

  $(".{{$type}}-js-select2").select2({ 
    dropdownParent: $("#{{$id}}")
  });

  @if ($type == 'edit')
  function editModal(json) {
    $('#editModal').modal('show');
    $('#{{$type}}Form').attr('action', "{{ url('/product/fokus/update') }}/"+json.id);
    $('#{{$type}}Product').val(json.product).trigger('change');
    $('#{{$type}}Area').val(json.area).trigger('change');
    $('#{{$type}}DateFrom').val(json.from);
    $('#{{$type}}DateTo').val(json.to);
    $('#{{$type}}Channel').val(json.channel).trigger('change');
    // console.log(json);
  }
  @endif
</script>
@endpush