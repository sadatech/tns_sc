@extends('layouts.app')
@section('title', "VDO Target")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Target<small class="ml-2">VDO Pasar</small></h2>
  @if($errors->any())
  <div class="alert alert-danger">
    <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
    @foreach ($errors->all() as $error)
    <div>> {{ $error }}</div>
    @endforeach
  </div>
  @endif
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
      <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
          <div class="block-option">
          <button class="btn btn-info btn-square"  data-toggle="modal" data-target="#importModal" title="Unduh Data"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
            <a href="{{ route('target.smd.export') }}" class="btn btn-success btn-square float-right ml-10" title="Unduh Data"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="promoTable">
          <thead>
            <th class="text-center" style="width: 70px;"></th>
            <th>Employee Name</th>
            <th>Hk</th>
            <th>Release Date</th>
            <th>Sales Value</th>
            <th>EC PF</th>
            <th>CBD</th>
            <th class="text-center" style="width: 15%;"> Action</th>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Import Data Target VDO Pasar</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('target.smd.import') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
              <a href="{{ route('targetsmd.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <h5> Sample Data :</h5>
          <table class="table table-bordered table-vcenter">
            <thead>
              <tr>
                  <td><b>Employee</b></td>
                  <td><b>HK</b></td>
                  <td><b>Value</b></td>
                  <td><b>ECPF</b></td>
                  <td><b>CBD</b></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                  <td>Employee 1</td>
                  <td>HK 1</td>
                  <td>Value 1</td>
                  <td>ECPF 1</td>
                  <td>CBD 1</td>
              </tr>
              <tr>
                  <td>Employee 2</td>
                  <td>HK 2</td>
                  <td>Value 2</td>
                  <td>ECPF 2</td>
                  <td>CBD 2</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="block-content">
          <div class="form-group">
          <label>Upload Your Data Target VDO Pasar:</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                <label class="custom-file-label">Choose file Excel</label>
                <code> *Type File Excel</code>
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="fa fa-edit"></i> Update Target VDO</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="editForm" method="post">
        {!! method_field('PUT') !!}
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Employee</label>
            <select class="js-edit form-control" style="width: 100%" id="EmployeeInput" name="employee" >
              @foreach($employee as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Release Date</label>
              <input class="js-datepicker form-control" type="text" id="rilisInput" name="rilis" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
            </div>
            <div class="form-group col-md-6">
              <label>HK</label>
              <input class="JustAngka form-control" type="text" id="hkInput" name="hk" required>
            </div>
            <div class="form-group col-md-6">
              <label >Sales Value</label>
              <div class="input-group-append">
                <span class="input-group-text">Rp</span>
                <div class="input-group">
                  <input type="text" class="JustAngka form-control" name="value" id="valueInput" required>
                  <div class="input-group-append">
                    <span class="input-group-text">.00</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-md-6">
              <label>EC</label>
              <input class="JustAngka form-control" type="text" id="ecInput" name="ec" required>
            </div>
            <div class="form-group col-md-6">
              <label>CBD</label>
              <input class="JustAngka form-control" type="text" id="cbdInput" name="cbd" required>
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

<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-gd-sun p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Target VDO</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('target.smd.add') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">
          <div class="form-group">
            <label>Employee</label>
            <select class="js-select2 form-control" style="width: 100%" name="employee" required>
              <option value="" disabled selected>Choose your employee</option>
              @foreach($employee as $data)
              <option value="{{ $data->id }}">{{ $data->name }} </option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Release</label>
              <input class="js-datepicker form-control" type="text" name="rilis" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
            </div>
            <div class="form-group col-md-6">
              <label>HK</label>
              <input class="JustAngka form-control" type="text" name="hk" placeholder="Input HK" required>
            </div>
            <div class="form-group col-md-6">
              <label >Sales Value</label>
              <div class="input-group-append">
                <span class="input-group-text">Rp</span>
                <div class="input-group">
                  <input type="text" class="JustAngka form-control" name="value" placeholder="" required>
                  <div class="input-group-append">
                    <span class="input-group-text">.00</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-md-6">
              <label>EC</label>
              <input class="JustAngka form-control" type="text" name="ec" placeholder="Input EC"required>
            </div>
            <div class="form-group col-md-6">
              <label>CBD</label>
              <input class="JustAngka form-control" type="text" name="cbd" placeholder="Input CBD" required>
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

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<style type="text/css">
[data-notify="container"] 
{
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
</style>
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
  function editModal(json) {
    $('#editModal').modal('show');
    $('#editForm').attr('action', "{{ url('/target/smd/update') }}/"+json.id);
    $('#EmployeeInput').val(json.employee).trigger('change');
    $('#rilisInput').val(json.rilis);
    $('#valueInput').val(json.value);
    $('#hkInput').val(json.hk);
    $('#ecInput').val(json.ec);
    $('#cbdInput').val(json.cbd);
        }
        @if(session('type'))
        $(document).ready(function() {
          $.notify({
            title: '<strong>{!! session('title') !!}</strong>',
            message: '{!! session('message') !!}'
          }, {
            type: '{!! session('type') !!}',
            animate: {
              enter: 'animated zoomInDown',
              exit: 'animated zoomOutUp'
            },
            placement: {
              from: 'top',
              align: 'center'
            }
          });
        });
        @endif
        $(function() {
          $('#promoTable').DataTable({
            processing: true,
            drawCallback: function(){
              $('.js-swal-delete').on('click', function(){
                var url = $(this).data("url");
                swal({
                  title: 'Are you sure?',
                  text: 'You will not be able to recover this data!',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d26a5c',
                  confirmButtonText: 'Yes, delete it!',
                  html: false,
                  preConfirm: function() {
                    return new Promise(function (resolve) {
                      setTimeout(function () {
                        resolve();
                      }, 50);
                    });
                  }
                }).then(function(result){
                  if (result.value) {
                    window.location = url;
                  } else if (result.dismiss === 'cancel') {
                    swal('Cancelled', 'Your data is safe :)', 'error');
                  }
                });
              });
            },
            ajax: '{!! route('target.smd.data') !!}',
            scrollY: "300px",
            columns: [
            { data: 'id', name: 'id' },
            { data: 'employee.name', name: 'employee.name'},
            { data: 'hk', name: 'hk' },
            { data: 'rilis', name: 'rilis' },
            { data: 'values', name: 'value_sales' },
            { data: 'ec', name: 'ec' },
            { data: 'cbd', name: 'cbd' },
            { data: 'action', name: 'action' }
            ],
            order: [[ 0, "desc" ]]
          });
        });
        $(".JustAngka").keydown(function (e) {
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
        $(".js-select2").select2({ 
          dropdownParent: $("#tambahModal")
        });
        $(".js-edit").select2({ 
          dropdownParent: $("#editModal")
        });
      </script>
      @endsection