@php
$type = $type ?? '';
$action = $action ?? '';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> {{ $type == '' ? 'Add' : ucfirst($type) }} Product Target</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ $action }}" method="post" id="{{$id}}Form">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Employee</label>
            <select class="{{$type}}-js-select2 form-control" style="width: 100%" name="id_employee" id="{{$type}}Employee">
              <option value="" disabled selected>Choose your employee</option>
              @foreach($employee as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Store</label>
              <select class="{{ $type }}-js-select2 form-control" style="width: 100%" name="id_store" id="{{$type}}Store">
                <option disabled selected>Choose your Store</option>
                @foreach($store as $data)
                <option value="{{ $data->id }}">{{ $data->name1 }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Release</label>
              <input class="js-datepicker form-control" type="text" name="rilis" id="{{$type}}Rilis" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label>Value</label>
              <input type="number" name="value" id="{{$type}}Value" class="form-control" required="">
            </div>
            <div class="form-group col-md-6">
              <label>Value PF</label>
              <input type="number" name="value_pf" id="{{$type}}ValuePf" class="form-control" required="">
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
  @if ($type == 'edit')
    function editModal(json) {
      $('#{{$id}}').modal('show');
      $('#{{$id}}Form').attr('action', "{{ url('/product/target/update') }}/"+json.id);
      $('#{{$type}}Employee').val(json.employee).trigger('change');
      $('#{{$type}}Store').val(json.store).trigger('change');
      $('#{{$type}}type').val(json.type);
      $('#{{$type}}rilis').val(json.rilis);
      console.log(json);
    }
  @endif

  $(".{{$type}}-js-select2").select2({ 
    dropdownParent: $("#{{$id}}")
  });
</script>
@endpush