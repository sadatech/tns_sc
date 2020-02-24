
<div class="modal fade" id="inputModal" tabindex="-1" role="dialog" aria-labelledby="inputModal" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Product Focus</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="focus-form" method="post" action="{{ route('focus.add') }}">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Product</label>
            <input type="hidden" name="id" id="idInput">
            <input type="hidden" name="update" id="updateInput">
            <select class="form-control" style="width: 100%" id="productInput" name="product[]" multiple required>
            </select>
          </div>
          <div class="row" style="margin-bottom: 15px">
            <div class="col-md-12">
              <div class="custom-control custom-checkbox custom-control-inline">
                <input class="custom-control-input" type="checkbox" id="area-checkbox" checked>
                <label class="custom-control-label" for="area-checkbox" style="cursor: pointer;">ALL Area</label>
              </div>
              <div id="areaInputDiv" style="margin-top: 5px;">
                <select id="areaInput" class="form-control" style="width: 100%" name="area[]" multiple>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label>Start Month</label>
              <input class="form-control month-picker" type="text" placeholder="Start Month" data-date-format="mm/yyyy" id="fromInput" name="from" data-month-highlight="true" required>
            </div>
            <div class="col-md-6">
              <label>End Month</label>
              <input class="form-control month-picker" type="text" placeholder="End Month" data-date-format="mm/yyyy" id="toInput" name="to" data-month-highlight="true" required>
            </div>
          </div>
        </div>
        <br/>
        <div class="modal-footer">
          <button id="focus-form-submit" data-form="focus-form" class="btn btn-alt-success">
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
    $(document).ready(function() {

        $('#productInput').select2(setOptions('{{ route("product-select2") }}', 'Select Product', function (params) {
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {                                
                    return {id: obj.id, text: obj.code + " | " + obj.name}
                })
            }
        }));

        $('#areaInput').select2(setOptions('{{ route("area-select2") }}', 'Select Area', function (params) {
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {                                
                    return {id: obj.id, text: obj.name}
                })
            }
        }));

        $('#focus-form').submit(function(event) {
            // stop the form from submitting the normal way and refreshing the page
            event.preventDefault();

            var startMonth  = $('#fromInput').val();
            var startDate   = startMonth.split('/');
            var startDate   = new Date(startDate[1], (startDate[0]-1), '1');

            var endMonth  = $('#toInput').val();
            var endDate   = endMonth.split('/');
            var endDate   = new Date(endDate[1], (endDate[0]-1), '1');

            if (startDate <= endDate) {
                var formData = {
                    'id'      : $('#idInput').val(),
                    'update'  : $('#updateInput').val(),
                    'product' : $('#productInput').val(),
                    'area'    : $('#areaInput').val(),
                    'from'    : $('#fromInput').val(),
                    'to'      : $('#toInput').val()
                };
                var url = $('#focus-form').attr('action');

                $.ajax({
                    url   : url,
                    type  : 'POST',
                    data  : formData,
                    success: function (data) {
                        swal(data.title, data.message, data.type);
                    },
                    error: function(xhr, textStatus, errorThrown){
                        swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
                    }
                });
            }else{
                swal("Gagal melakukan request", "Start Month tidak boleh lebih dari End Month", "error");
                return;
            }

            $('#inputModal').modal('toggle');

        });

    }); //END READY

    $('#areaInputDiv').hide();
    $(".month-picker").datepicker( {
        format: "mm/yyyy",
        viewMode: "months",
        autoclose: true,
        minViewMode: "months"
    });

    $("#area-checkbox").change(function() {
        if ($(this).removeAttr("checked")) {
            $('#areaInputDiv').show();
        }
    });

    $("#area-checkbox").change(function() {
        if ($(this).prop("checked")) {
            $('#areaInputDiv').hide();
            $('#coba').val(null).trigger('change');
        }
    });

    function editModal(json) {
        $('#inputModal').modal('toggle');
        var area = 0;
        $('#idInput').val(json.id);
        $('#updateInput').val(1);
        clearSelect();
        setSelect2IfPatch2($("#productInput"), json.product.id, json.product.name);
        $.each(json.area, function(key, val){
          area++;
            setSelect2IfPatch2($("#areaInput"), val.id_area, val.name);
        });
        if (area>0) {
            toggleOffSwitch();
        }else{
            toggleOnSwitch();
        }
        $('#fromInput').val(json.from);
        $('#toInput').val(json.to);
    }

    function addModal() {
        $('#inputModal').modal('toggle');
        clearSelect();
        clearDate();
        toggleOnSwitch();
        $('#update').val('');
    }

    function clearSelect() {
        select2Reset($("#productInput"));
        select2Reset($("#areaInput"));
    }

    function clearDate() {
        $('#fromInput').val('');
        $('#toInput').val('');
    }

    function toggleOffSwitch() {
        var checked = $('#area-checkbox:checkbox:checked').length > 0;
        if (checked) {
            $('#area-checkbox').click();
        }
    }

    function toggleOnSwitch() {
        var checked = $('#area-checkbox:checkbox:checked').length == 0;
        if (checked) {
            $('#area-checkbox').click();
        }
    }
</script>
@endpush