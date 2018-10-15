@extends('layouts.app')
@section('title', "Update Employee")
@section('content')
<div class="content">
    @if($errors->any())
    <div class="alert alert-danger">
        <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
        @foreach ($errors->all() as $error)
        <div> {{ $error }}</div>
        @endforeach
    </div>
    @endif
    <div class="block block-fx-shadow">
        <div class="block block-themed block-transparent mb-0">
            <div class="block-header bg-primary p-10">
                <h3 class="block-title"><i class="fa fa-user mr-2"></i>Employee Profile</h3>
            </div>
        </div>
        <form action="{{action('EmployeeController@update', $emp->id) }}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            {{ method_field('PUT')}}
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>NIK</label>
                        <input type="text" class="form-control" name="nik" value="{{$emp->nik}}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{$emp->name}}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>No. KTP</label>
                        <input type="number" class="form-control" name="ktp" value="{{$emp->ktp}}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="number" class="form-control" name="phone" value="{{$emp->phone}}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{$emp->email}}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Timezones</label>
                        <select class="form-control form-control-lg" name="timezone" id="timezone" required>
                            @foreach($timezone as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <select class="form-control form-control-lg" name="gender" id="gender" required>
                            <option disabled selected>Choose your Gender</option>
                            <option value="Laki-laki">Laki - Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" >
                        <label>Education</label>
                        <select class="form-control form-control-lg" id="education" name="education" required>
                            <option disabled selected>Choose your Education</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA/SLTA/Sederajat">SMA/sederajat</option>
                            <option value="D1">D1</option>
                            <option value="D2">D2</option>
                            <option value="D3">D3</option>
                            <option value="S1/D4">S1/D4</option>
                            <option value="S2">S2</option>
                        </select>
                    </diV>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Tanggal Lahir</label>
                        <input class="js-datepicker form-control" type="date" value="{{$emp->birthdate}}" name="birthdate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Foto KTP</label>
                        <div class="custom-file">
                            <input type="file" name="foto_ktp"  class="custom-file-input" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp">
                            <label class="custom-file-label">Pilih Foto</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-user mr-2"></i>Required Information</h3>
                </div>
            </div>
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nomor Rekening</label>
                        <input type="text" class="form-control" name="rekening" value="{{$emp->rekening}}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name Bank</label>
                        <input type="text" class="form-control" name="bank" value="{{$emp->bank}}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Foto Rekening</label>
                            <div class="custom-file">
                                <input type="file" name="foto_tabungan" class="custom-file-input" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp">
                                <label class="custom-file-label">Pilih Foto</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-user mr-2"></i>Employee Positioning</h3>
                </div>
            </div>
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Position</label>
                        <select class="form-control form-control-lg" name="position" id="position" required>
                            <option disabled selected>Choose your Position</option>
                            @foreach($position as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="agency">
                        <label>Agency</label>
                        <select class="js-select2 form-control form-control-lg" name="agency" required>
                            @foreach($agency as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Brand</label>
                        <select class="form-control form-control-lg" id="brand" name="brand" required>
                            @foreach($brand as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6" id="status">
                        <label>Status</label>
                        <select class="form-control form-control-lg" name="status" required>
                            <option disabled selected>Choose your Status</option>
                            <option value="Stay">Stay</option>
                            <option value="Mobile">Mobile</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="storeMobile">
                        <label class="col-md-12" style="padding: 0">Store</label>
                        <div class="input-group mb-3 col-md-12" style="padding: 0">
                            <div style="width: 82%">
                                <select id="stores" class="js-select2 form-control" style="width: 100%" data-placeholder="Choose store...">
                                    <option disabled selected>Choose your Status</option>
                                    @foreach($store as $data)
                                    <option value="{{ $data->id.'|'.$data->name1.' - '.$data->city->name.', ' .$data->province->name }}">{{ $data->name1." - ".$data->city->name.", ".$data->province->name }}</option>
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
                                <h3 class="block-title">Selected Store</h3>
                                <div class="block-options">
                                    <input type="text" id="myInput" class="form-control" onkeyup="searchFunction()" placeholder="Search for Store..">
                                </div>
                            </div>
                            <div class="block-content" style="padding: 0; width: 100%;">
                                <table id="selectedStoreTable" class="table table-striped table-vcenter" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th>Store</th>
                                            <th class="text-center" style="width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedStoreTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6" id="storeStay">
                        <label>Store</label>
                        <select class="js-select form-control" style="width: 100%" data-placeholder="Choose store..." name="store">
                            <option disabled selected>Choose your Status</option>
                            @foreach($store as $data)
                            <option value="{{ $data->id }}">{{ $data->name1." - ".$data->city->name.", ".$data->province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12" id="spv">
                        @if($spv->count() < 1)
                        <b class="text-danger">*Kamu tidak memiliki supervisor, harap tambahkan supervisor terlebih dahulu.</b>
                        @else
                        <label>Supervisor</label>
                        <select class="form-control form-control-lg" name="status" required>
                            <option disabled selected>Choose your Status</option>
                            @foreach($spv as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                        @endif
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
@endsection
@section('script')
<script type="text/javascript">
    var selectedStores = [], selectedStoresId = [], selectedStoresName = [], tableIndex = 0;
    $(".js-select2").select2({
        placeholder: 'Select...',
        allowClear: true,
        tags: true
    });
    
    $(document).ready(function() {
        $('#gender option[value="{{ $emp->gender }}"]').attr('selected','selected');
        $('#education option[value="{{ $emp->education }}"]').attr('selected','selected');
        $('#agency option[value="{{ $emp->agency->id }}"]').attr('selected','selected');
        $('#brand option[value="{{ $emp->brand->id }}"]').attr('selected','selected');
        $('#position option[value="{{ $emp->position->id }}"]').attr('selected','selected');
        $('#timezone option[value="{{ $emp->timezone->id }}"]').attr('selected','selected');
        $('#spv option[value="{{ $emp->id_position }}"]').attr('selected','selected');
        var position = $('#position').find(":selected").val();
        if (position == {{$emp->position->id}}) { 
            $('#status').show();
            $('#spv').show();
            $('#status option[value="{{ $emp->status }}"]').attr('selected','selected');
            var status = $('#status').find(":selected").val();
            var select = $('#position').find(":selected").val();
            if (status == 'Mobile') {
                var selected = {!! $store_selected !!};
                $('#storeStay').hide();
                $('#storeMobile').show();
                clearStores()
                $.each(selected, function( index, value ) {
                  addItem(value.stores_item);
              });
            } else {
                $('#storeStay').val('{{ $store_selected }}');
                $('#storeMobile').hide();
                $('#storeStay').show();
            }
            if (select == {{ App\Position::where(['level' => 'level 1'])->first()->id }}) {
                $('#status').show();
                $('#spv').show();
            } else {
                $('#status').hide();
                $('#spv').hide();
                $('#storeStay').hide();
                $('#storeMobile').hide();
            }   
        }

        // add Store to Selected Store
        $('#storesAdd').click(function () {
            var stores = $('#stores').val();
            if (stores != null) {
                clearStores();
                addItem(stores);
            }else{                
                notif('Warning',': Please the select Store first','warning');
            }
        });
    });
    $('#position').on('change', e => {
        var select = $('#position').find(":selected").val()
        var status = $('#status').find(":selected").val()
        if (select == {{ App\Position::where(['level' => 'level 1'])->first()->id }}) {
            $('#status').show();
            $('#spv').show();
            $('#storeStay').show();
        } else {
            $('#status').hide();
            $('#spv').hide();
            $('#storeStay').hide();
            $('#storeMobile').hide();
        }
    })
    $('#status').on('change', e => {
        var select = $('#position').find(":selected").val()
        var status = $('#status').find(":selected").val()
        if (status == 'Stay') {
            $('#storeMobile').hide();
            $('#storeStay').show();
        } else {
            $('#storeStay').hide();
            $('#storeMobile').show();
        }
    })

    function clearStores() {
        $('#stores').val('');
        $('#select2-stores-container').html('<span class="select2-selection__placeholder">Choose your Store</span>');
    }
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
            $('#selectedStoreTableBody').append("<tr>"+
                "<th class='text-center' scope='row'>"+ tableIndex +"</th>"+
                "<td><span>"+ storeSplit[1] +"</span>"+
                "<input type='hidden' name='stores[]' value='"+ storeSplit[0] +
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
            console.log("Data Already Exist! data: "+storeSplit[1]);
            notif('Warning',': Please the select Store first','warning');
        }
    }
    function deleteItem(id) {
        var a = selectedStoresId.indexOf(''+id);
        if (a >= 0) {
            selectedStores.splice(a, 1);
            selectedStoresId.splice(a, 1);
            selectedStoresName.splice(a, 1);
            tableIndex = 0;
            $('#selectedStoreTableBody').html('');
            $.each(selectedStores, function( index, value ) {
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
    $(".js-select2").select2();
</script>
@endsection