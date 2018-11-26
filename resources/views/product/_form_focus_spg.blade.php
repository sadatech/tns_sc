@php
$action = $action ?? '';
$type = $type ?? '';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> {{ $type == '' ? 'Add' : ucfirst($type) }} Product Fokus Spg</h3>
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
            <label>Employee</label>
            <select class="{{$type}}-js-select2 form-control" style="width: 100%" id="{{$type}}Employee" name="id_employee" required>
              <option value="" disabled selected>Choose your Employee</option>
              @foreach(App\Employee::where('id_position','3')->get() as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <label>Product <span style="color: red;">*</span></label>
            </div>
            <div class="col-sm-3">
              <label>Month From <span style="color: red;">*</span></label>
            </div>
            <div class="col-sm-3">
              <label>Month Until <span style="color: red;">*</span></label>
            </div>
            <div class="col-sm-1">
              <label><span style="color: white;">-----</span></label>
            </div>
          </div>
          <div id="divRouteDocument">
            <div id="list-form">
              <div class="row form-group-product">
                  <div class="form-group col-md-5">
                    <select class="{{$type}}-js-select2 form-control" style="width: 100%" id="{{$type}}Product" name="id_product[]" required>
                      <option value="" disabled selected>Choose your Product</option>
                      @foreach(App\Product::where('id_brand',1)->get() as $data)
                      <option value="{{ $data->id }}">{{ $data->name }} </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <input class="js-datepicker form-control" type="text" placeholder="Month From" id="{{$type}}DateFrom" name="from[]" data-month-highlight="true" required>
                  </div>
                  <div class="form-group col-md-3">
                    <input class="js-datepicker form-control" type="text" placeholder="Month Until" id="{{$type}}DateTo" name="to[]" data-month-highlight="true" required>
                  </div>
                  <div class="form-group col-md-1">
                    <a id="buttonRemove" class="btn btn-default hidden" onclick="remLine($(this))"><i class="fa fa-minus"></i></a>
                  </div>
              </div>
            </div>
          </div>
          
          <div id="emptyDivRouteDocument"></div>

          <div class="form-group">
            <div class="col-md-offset-4" align="center">
              <a class="btn btn-primary" id="addButton" onclick="actAdd()"><i class="fa fa-plus"></i> Add new</a>
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

@push('additional-css')
<style type="text/css">
  .hidden{
    display: none;
  }
</style>
@endpush
@push('additional-js')
<script type="text/javascript">

  function actAdd()
  {
    $('#emptyDivRouteDocument').append($('#divRouteDocument').html())
    $('#buttonRemove').attr('class', 'btn btn-default')
    $('#emptyDivRouteDocument > #list-form').attr('id', 'list-form-' + $('#emptyDivRouteDocument > div').length)
  }


  function remLine(event)
  {
    event.closest('.form-group-product').remove()
  }

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
    $('#{{$type}}Form').attr('action', "{{ url('/product/fokusSpg/update') }}/"+json.id);
    $('#{{$type}}Employee').val(json.employee).trigger('change');
    $('#{{$type}}Product').val(json.product).trigger('change');
    $('#{{$type}}DateFrom').val(json.from);
    $('#{{$type}}DateTo').val(json.to);
    console.log(json);
  }
  @endif
</script>
@endpush