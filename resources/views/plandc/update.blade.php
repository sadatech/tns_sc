@extends('layouts.app')
@section('title', "Update Plan Demo Cooking")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Plan Demo Cooking <small>Update</small></h2>
    <div class="container">
        <div class="block">
            <div class="block-content">
                <form action="{{action('PlandcController@update', $plan->id) }}" method="post" enctype="multipart/form-data">
                {!! csrf_field() !!}
                {{ method_field('PUT')}}
                    <div class="block-content">
                        <div class="row">
                            <div class="form-group col-md-12" id="EmployeeSelected">
                                <label class="col-md-12" style="padding: 0">Employee</label>
                                <div class="input-group mb-3 col-md-12" style="padding: 0">
                                    <div style="width: 82%">
                                        <select id="employees" class="js-select2 form-control" style="width: 100%" data-placeholder="Choose Employee...">
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
                                <input class="js-datepicker form-control" value="{{ $plan->date }}" type="text" name="date" id="dateInput" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Stockist</label>
                                <input type="text" class="form-control" name="stocklist" value="{{ $plan->stocklist }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Channel</label>
                                <select class="form-control form-control-lg" id='channel' name="channel" required>
                                    <option value="" disabled selected>Choose your Channel</option>
                                    <option value="MTC" @if ($plan->channel == "MTC") {{ 'selected' }} @endif>MTC</option>
                                    <option value="GTC" @if ($plan->channel == "GTC") {{ 'selected' }} @endif>GTC</option>
                                    <option value="ITC" @if ($plan->channel == "ITC") {{ 'selected' }} @endif>ITC</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Plan</label>
                                <input type="text" class="form-control" name="plan" value="{{ $plan->plan }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Alamat</label>
                                <textarea type="text" class="form-control" name="alamat" required>{{ $plan->alamat }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-alt-success">
                                <i class="fa fa-save"></i> Save
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-alt-secondary" data-dismiss="modal">Back</a>
                        </div>
                    </div>
                </form>       
            </div>
        </div>     
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#employeesAdd').click(function () {
            var employees = $('#employees').val();
            if (employees != null) {
                $('#employees').val('');
                $('#select2-employees-container').html('');
                addItem2(employees);
            }else{                
                notif('Warning',': Please the select Employee first','warning');
            }
        });
    });
    
    var selectedEmployees = [], selectedEmployeesId = [], selectedEmployeesName = [], tabIndex = 0;
    $('#channel option[value="{{ $plan->channel }}"]').attr('selected','selected');
    var selected = {!! $employee_selected !!};
        $.each(selected, function( index, value ) {
          addItem2(value.employees_item);
      });

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
                "<button type='button' class='btn btn-sm btn-secondary js-tooltip-enabled' data-toggle='tooltip' title=' data-original-title='Delete' onclick='deleteItem2("+ employeeSplit[0] +")'>"+
                "<i class='fa fa-times'></i>"+
                "</button>"+
                "</div>"+
                "</td>"+
                "</tr>");
        }else{
            console.log("Data Already Exist! data: "+employeeSplit[1]);
            notif('Warning',': Please the select Employee DC first','warning');
        }
    }

    function deleteItem2(id) {
        var a = selectedEmployeesId.indexOf(''+id);
        if (a >= 0) {
            selectedEmployees.splice(a, 1);
            selectedEmployeesId.splice(a, 1);
            selectedEmployeesName.splice(a, 1);
            tableIndex = 0;
            $('#selectedEmployeeTableBody').html('');
            $.each(selectedEmployees, function( index, value ) {
              addItem2(value,'get2');
          });
        }else{
            console.log("Index Item Not Found!");
        }
    }

    function searchFunction() {
        var input, filter, table, tr, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("selectedEmployeeTableBody");
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
        });
    }

    $(".js-select2").select2();
</script>
@endsection