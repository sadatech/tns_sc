@php
$action = $action ?? '';
$type = $type ?? '';
@endphp

<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
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

          <div class="divAddProdukFokus hidden">
            <div id="addList">
              <div class="row form-group-product">
                  <div class="form-group col-md-3">
                    <select class="form-control addCategory" style="width: 100%" id="addCategorySelect" name="id_category[]" required>
                      <option value="" disabled selected>Choose your Category</option>
                      @foreach(App\Category::get() as $data)
                      <option value="{{ $data->id }}">{{ $data->name }} </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-4">
                    <select class="form-control" style="width: 100%" id="addProductSelect" name="id_product[]" required>
                      <option value="" disabled selected>Choose your Product</option>
                      <option value="all">All Products</option>
                      @foreach(App\Product::where('id_brand',1)->get() as $data)
                      <option value="{{ $data->id }}">{{ $data->name }} </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <input class="js-datepicker form-control" type="text" placeholder="Month From" id="addDateFrom" name="from[]" data-month-highlight="true" autocomplete="off" required>
                  </div>
                  <div class="form-group col-md-2">
                    <input class="js-datepicker form-control" type="text" placeholder="Month Until" id="addDateTo" name="to[]" data-month-highlight="true" autocomplete="off" required>
                  </div>
                  <div class="form-group col-md-1">
                    <button id="buttonRemove" class="btn btn-default" onclick="remLineAdd($(this))"><i class="fa fa-minus"></i></button>
                  </div>
              </div>
            </div>
          </div>

      <form action="{{ $action }}" method="post" id="AddForm">
        @if ($type == 'edit')
        {!! method_field('PUT') !!}
        @endif
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Employee <span style="color: red;">*</span></label>
            <select class="js-select form-control" style="width: 100%" id="addEmployee" name="id_employee" required>
              <option value="" disabled selected>Choose your Employee</option>
              @foreach(App\Employee::where('id_position','3')->get() as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-sm-3">
              <label>Category <span style="color: red;">*</span></label>
            </div>

            <div class="col-sm-4">
              <label>Product <span style="color: red;">*</span></label>
            </div>
            <div class="col-sm-2">
              <label>Month From <span style="color: red;">*</span></label>
            </div>
            <div class="col-sm-2">
              <label>Month Until <span style="color: red;">*</span></label>
            </div>
            <div class="col-sm-1">
              <label><span style="color: white;">-----</span></label>
            </div>
          </div>
          
          <div class="emptyDivAddProdukFokus"></div>

          <div class="form-group">
            <div class="col-md-offset-4" align="center">
              <a class="btn btn-info btn-square" id="addButton" onclick="actAdd()"><i class="fa fa-plus"></i> Add new</a>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" onclick="$('#AddForm').submit();" class="btn btn-alt-success">
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

  $(".js-datepicker").datepicker( {
    format: "mm/yyyy",
    viewMode: "months",
    autoclose: true,
    minViewMode: "months"
  });

  $('.addCategory').on('change', function(){
    initSelect2($(this).attr('id'));
  });
  
  $(".js-select").select2({
    dropdownParent: $('#tambahModal')
  });

  function initSelect(){
  $(".js-datepicker").datepicker( {
    format: "mm/yyyy",
    viewMode: "months",
    autoclose: true,
    minViewMode: "months"
  });

  $('.addCategory').on('change', function(){
    initSelect2($(this).attr('id'));
  });
  
  $(".js-select").select2();
  }


  function actAdd()
  {
    $('.emptyDivAddProdukFokus').append($('.divAddProdukFokus').html())
    $('#buttonRemove').attr('class', 'btn btn-default')
    $('.emptyDivAddProdukFokus > #addList').attr('id', 'addList-' + $('.emptyDivAddProdukFokus > div').length)

    //
    $('#' + 'addList-' + $('.emptyDivAddProdukFokus > div').length);

    //
    $('#' + 'addList-' + $('.emptyDivAddProdukFokus > div').length + ' > div > .form-group > #addCategorySelect').attr('id', 'addCategorySelect_' + $('.emptyDivAddProdukFokus > div').length);
    $('#' + 'addList-' + $('.emptyDivAddProdukFokus > div').length + ' > div > .form-group > #addProductSelect').attr('id', 'addProductSelect_' + $('.emptyDivAddProdukFokus > div').length);

    console.log();
    initSelect();
  }


  function remLineAdd(event)
  {
    event.closest('.form-group-product').remove()
  }


  function initSelect2(divArea){
    $("#" + divArea.replace("addCategorySelect", "addProductSelect")).empty();

      var getDataUrl = "{{ url('select2/product-byCategory-select2') }}";

      $.get(getDataUrl + '/' + $("#" + divArea).val(), function (data) {
      if(data){
        console.log(data);
        document.getElementById("" + divArea.replace("addCategorySelect", "addProductSelect")).innerHTML = "<option selected disabled>Choose your Product</option><option value='all'>All Products</option>";

                var element = document.getElementById("" + divArea.replace("addCategorySelect", "addProductSelect"));
                $.each(data, function(index, item) {
                  // console.log(item);

                  var option = document.createElement("option");
                  option.value = item.id;
            option.text = item.name;
            element.add(option);
          
        });


          } 

      })

  }




</script>
@endpush