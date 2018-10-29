@extends('layouts.app')
@section('title', "Plan Demo Cooking")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Plan Demo Cooking <small>Manage</small></h2>
    @if($errors->any())
        <div class="alert alert-danger">
            <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
            @foreach ($errors->all() as $error)
            <div> {{ $error }}</div>
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
                        <button class="btn btn-info btn-square" data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                        <a href="{{ route('plan.export') }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </h3>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full" id="planTable">
                <thead>
                    <th class="text-center" style="width: 70px;"></th>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Lokasi</th>
                    <th>Stocklist</th>
                    <th class="text-center" style="width: 15%;"> Action</th>
                </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editModal" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Update Plan DemoCooking</h3>
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
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-md-12" style="padding: 0">Employee</label>
                            <div class="input-group mb-3 col-md-12" style="padding: 0">
                                <div style="width: 82%">
                                    <select id="employees" class="js-edit form-control" style="width: 100%" data-placeholder="Choose Employee...">
                                        @foreach($employee as $data)
                                        <option value="{{ $data->id.'|'.$data->name }}">{{ $data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group-append" style="width: 18%">
                                    <button id="employeesAdd" class="btn btn-outline-secondary" type="button" style="width: 100%">Add</button>
                                </div>
                            </div>
                            <!-- Block’s content.. -->
                            <div class="block block-themed block-rounded">
                                <div class="block-header bg-gd-lake" style="padding: 5px">
                                    <h3 class="block-title">Selected Employee</h3>
                                    <span id="selectedEmployeeDefault" style="color: #ffeb5e;padding-top: 5px;">Please Add Employee</span>
                                    <div class="block-options">
                                        <input type="text" id="myEmployee" class="form-control" placeholder="Search for Employee..">
                                    </div>
                                </div>
                                <div class="block-content" style="padding: 0; width: 100%;">
                                    <table id="selectedEmployeeTable" class="table table-striped table-vcenter" style="display: none;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">#</th>
                                                <th>Employee</th>
                                                <th class="text-center" style="width: 100px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedEmployeeTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Date</label>
                            <input class="js-datepicker form-control" type="text" name="date" id="dateInput" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Stocklist</label>
                            <input type="text" class="form-control" name="stocklist" id="stocklistInput" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Lokasi</label>
                            <textarea class="form-control" name="lokasi" id="lokasiInput" required></textarea>
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


<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import Data Plan Demo Cooking</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form action="{{ route('plan.import') }}" method="post" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <div class="block-content">
                    <div class="form-group">
                        <a href="{{ route('plan.download-template') }}" class="btn btn-sm btn-info" title="Sample Data" style="float: right;">Download Import Format</a>
                    </div>
                        <div class="form-group col-md-12">
                            <label class="col-md-12" style="padding: 0">Employee</label>
                            <div class="input-group mb-3 col-md-12" style="padding: 0">
                                <div style="width: 82%">
                                    <select id="stores" class="js-select2 form-control" style="width: 100%" data-placeholder="Choose Employee...">
                                        @foreach($employee as $data)
                                            <option value="{{ $data->id.'|'.$data->name}}">{{$data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group-append" style="width: 18%">
                                    <button id="storesAdd" class="btn btn-outline-secondary" type="button" style="width: 100%">Add</button>
                                </div>
                            </div>
                            <!-- Block’s content.. -->
                            <div class="block block-themed block-rounded">
                                <div class="block-header bg-gd-lake" style="padding: 5px">
                                    <h3 class="block-title">Selected Employee DC</h3>
                                    <span id="selectedStoreDefault" style="color: #ffeb5e;padding-top: 5px;">Please Add Employee</span>
                                    <div class="block-options">
                                        <input type="text" id="myInput" class="form-control" onkeyup="searchFunction()" placeholder="Search for Employee..">
                                    </div>
                                </div>
                                <div class="block-content" style="padding: 0; width: 100%;">
                                    <table id="selectedStoreTable" class="table table-striped table-vcenter" style="display: none;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">#</th>
                                                <th>Employee</th>
                                                <th class="text-center" style="width: 100px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedStoreTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Upload Your Data Plan DC:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                <label class="custom-file-label">Choose file Excel</label>
                                <code> *Type File Excel</code>
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

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
<style type="text/css">
[data-notify="container"] {
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
    $('#editForm').attr('action', "{{ url('/planDc/update') }}/"+json.id);
    $('#dateInput').val(json.date);
    $('#stocklistInput').val(json.stocklist);
    $('#lokasiInput').val(json.lokasi);
    console.log(json);
     $('#employeesAdd').click(function () {
            var employees = $('#employees').val();
            if (employees != null) {
                clearStores();
                addItem2(employees);
            }
        });
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
    $('#planTable').DataTable({
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
      ajax: '{!! route('plan.data') !!}',
      scrollY: "300px",
      columns: [
      { data: 'id', name: 'plan_dcs.id' },
      { data: 'planEmployee', name: 'planEmployee' },
      { data: 'date', name: 'plan_dcs.date'},
      { data: 'lokasi', name: 'plan_dcs.lokasi'},
      { data: 'stocklist', name: 'plan_dcs.stocklist'},
      { data: 'action', name: 'action' }
      ]
    });
  });
  
  var selectedStores = [], selectedStoresId = [], selectedStoresName = [], tableIndex = 0;
  var selectedEmployees = [], selectedEmployeesId = [], selectedEmployeesName = [], tabIndex = 0;
  $(".js-select2").select2({ 
    tags: true,
    dropdownParent: $("#importModal")
  });
  $(".js-edit").select2({ 
    tags: true,
    dropdownParent: $("#editModal")
  });
  
    $('#storesAdd').click(function () {
        var stores = $('#stores').val();
        if (stores != null) {
            $('#stores').val('');
            $('#select2-stores-container').html('');
            addItem(stores);
        }
    });
    function addItem(stores, get = '') {
        var storeSplit = stores.split("|");
        var a = selectedStoresId.indexOf(''+storeSplit[0]);

        if (get != 'get') {
            selectedStores.push(stores);
            selectedStoresId.push(storeSplit[0]);
            selectedStoresName.push(storeSplit[1]);
        }

        if (a < 0 || get == 'get') {
            tableIndex++;
            $('#selectedStoreTable').removeAttr('style');
            $('#selectedStoreDefault').css('display','none');
            $('#selectedStoreTableBody').append("<tr>"+
                "<th class='text-center' scope='row'>"+ tableIndex +"</th>"+
                "<td><span>"+ storeSplit[1] +"</span>"+
                "<input type='hidden' name='employee[]' value='"+ storeSplit[0] +
                "'></td>"+
                "<td class='text-center'>"+
                "<div class='btn-group'>"+
                "<button type='button' class='btn btn-sm btn-secondary js-tooltip-enabled' data-toggle='tooltip' title=' data-original-title='Delete' onclick='deleteItem("+ storeSplit[0] +")'>"+
                "<i class='fa fa-times'></i>"+
                "</button>"+
                "</div>"+
                "</td>"+
                "</tr>");
        }else{
            var msg = " : Data Already Exist! data: "+storeSplit[1];
            notif('Warning',msg,'warning');
        }
    }
    function deleteItem(id) {
        var a = selectedStoresId.indexOf(''+id);
        if (a >= 0) {
            console.log(selectedStores)
            selectedStores.splice(a, 1);
            selectedStoresId.splice(a, 1);
            selectedStoresName.splice(a, 1);
            console.log(selectedStores)
            tableIndex = 0;
            $('#selectedStoreTableBody').html('');
            $.each(selectedStores, function( index, value ) {
                console.log(value)
                addItem(value,'get');
            });
        }else{
            console.log("Index Item Not Found!");
        }
    }
    function searchFunction() {
        var input, filter, table, tr, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("selectedStoreTableBody");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            console.log(a)
            a = tr[i].getElementsByTagName("span")[0];
            if(a != null){
                a= a.innerHTML;
                if (a.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    function notif(title = 'default title<br/>', message = '<i class="em em-confetti_ball mr-2"></i>default message', type = 'success') {
        $.notify(
        {
            title: '<strong>'+title+'</strong>',
            message: message
        }, 
        {
            type: type,
            animate: {
                enter: 'animated zoomInDown',
                exit: 'animated zoomOutUp'
            },
            placement: {
                from: 'top',
                align: 'center'
            }
        }
        );
    }

    function clearStores() {
        $('#employees').val('');
        $('#select2-employees-container').html('<span class="select2-selection__placeholder">Choose your Employee</span>');
    }
    function addItem2(employees, get2 = '') {
        var employeeSplit = employees.split("|");
        var a = selectedEmployeesId.indexOf(''+employeeSplit[0]);
        
        if (get2 != 'get2') {
            selectedEmployees.push(employees);
            selectedEmployeesId.push(employeeSplit[0]);
            selectedEmployeesName.push(employeeSplit[1]);
        }

        if (a < 0 || get2 == 'get2') {
            tabIndex++;
            $('#selectedEmployeeTable').removeAttr('style');
            $('#selectedEmployeeTableBody').append("<tr>"+
                "<th class='text-center' scope='row'>"+ tabIndex +"</th>"+
                "<td><span>"+ employeeSplit[1] +"</span>"+
                "<input type='hidden' name='employee[]' value='"+ employeeSplit[0] +
                "'></td>"+
                "<td class='text-center'>"+
                "<div class='btn-group'>"+
                "<button type='button' class='btn btn-sm btn-secondary js-tooltip-enabled' data-toggle='tooltip' title=' data-original-title='Delete' onclick='deleteItem("+ employeeSplit[0] +")'>"+
                "<i class='fa fa-times'></i>"+
                "</button>"+
                "</div>"+
                "</td>"+
                "</tr>");
        }else{
            console.log("Data Already Exist! data: "+employeeSplit[1]);
            notif('Warning',': Please the select Store first','warning');
        }
    }
</script>
@endsection