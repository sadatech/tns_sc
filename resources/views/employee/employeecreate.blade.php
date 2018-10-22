@extends('layouts.app')
@section('title', "Add Employee")
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
        <form action="{{ route('employee.add') }}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>NIK</label>
                        <input type="text" class="form-control" name="nik" placeholder="Add new employee" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Add new employee" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>No. KTP</label>
                        <input type="text" id="numKtp" onblur="checkLength(this)" class="form-control" name="ktp" placeholder="Add new employee"  minlength="16" maxlength="16" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text" id="numPhone" class="form-control" name="phone" placeholder="Add new employee" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Add new employee" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Add new password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <select class="form-control form-control-lg" name="gender" required>
                            <option value="" disabled selected>Choose your Gender</option>
                            <option value="Laki-laki">Laki - Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Education</label>
                        <select class="form-control form-control-lg" name="education" required>
                            <option value="" disabled selected>Choose your Education</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SLTA">SLTA</option>
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
                        <input class="js-datepicker form-control" type="date" name="birthdate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Foto KTP</label>
                        <div class="custom-file">
                            <input type="file" name="foto_ktp" class="custom-file-input" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp" required>
                            <label class="custom-file-label">Pilih Foto</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Timezones</label>
                        <select class="form-control form-control-lg" name="timezone" required>
                        <option value="" disabled selected>Choose your Timezone</option>
                            @foreach($timezone as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-user mr-2"></i>Bank Account</h3>
                </div>
            </div>
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nomor Rekening</label>
                        <input type="text" id="txtboxToFilter" class="form-control" name="rekening" placeholder="Add new employee">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name Bank</label>
                        <input type="text" class="form-control" name="bank" placeholder="Add new employee">
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
                            <option value="" disabled selected>Choose your Position</option>
                            @foreach($position as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Agency</label>
                        <select class="js-select form-control form-control-lg" style="width: 100%" name="agency" required>
                            <option value="" disabled selected>Choose your Agency</option>
                            @foreach($agency as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6" id="subarea" >
                        <label>Sub Area / Area</label>
                        <select class="js-select form-control form-control-lg" style="width: 100%" name="subarea" id="subareaInput">
                            <option disabled selected>Choose your Subarea</option>
                            @foreach($subarea as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6" id="status">
                        <label>Status</label>
                        <select class="form-control form-control-lg" id="statusInput" name="status">
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
                                <span id="selectedStoreDefault" style="color: #ffeb5e;padding-top: 5px;">Please Add the Store</span>
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
                        <select class="js-select form-control" style="width: 100%" data-placeholder="Choose store..." name="store" id="stayInput">
                            @foreach($store as $data)
                            <option value="{{ $data->id }}">{{ $data->name1." - ".$data->city->name.", ".$data->province->name }}</option>
                            @endforeach
                        </select>
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
        tags: true
    });
    $(".js-select").select2();
    $(document).ready(function() {
        $('input[type=email]').bind('change', function () {
          var arr = []
          $siblings = $(this).siblings();
          $.each($siblings, function (i, key) {
             arr.push($(key).val()); 
          });
          if ($.inArray($(this).val(), arr) !== -1)
          {
              alert("duplicate has been found");
          }
      });    
        $("#txtboxToFilter").keydown(function (e) {
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
        $('#subarea').hide();
        $('#status').hide();
        $('#storeMobile').hide();
        $('#storeStay').hide();
        $('#spv').hide();

        // add Store to Selected Store
        $('#storesAdd').click(function () {
            var stores = $('#stores').val();
            if (stores != null) {
                $('#stores').val('');
                $('#select2-stores-container').html('');
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
            $('#subarea').hide();
            $('#subareaInput').val(null);
        } else if (select == {{ App\Position::where(['level' => 'level 3'])->first()->id }}) {
            $('#subarea').show();
            $('#status').hide();
            $('#spv').hide();
            $('#storeStay').hide();
            $('#storeMobile').hide();
            $('#status').val(null);
        } else {
            $('#subarea').hide();
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
    $(".js-select2").select2();

    
    function ForNumbers(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode;

        if (
            //0~9
            charCode >= 48 && charCode <= 57
        )
        {
            //make sure the new value below 20
            if(parseInt(this.value+String.fromCharCode(charCode), 10) <= 10000000000000000000000000) 
                return true;
        }
        
        evt.preventDefault();
        evt.stopPropagation();
        
        return false;
    }
    document.getElementById('numKtp').addEventListener('keypress', ForNumbers, false);
    document.getElementById('numPhone').addEventListener('keypress', ForNumbers, false);


    function checkLength(el) {
      if (el.value.length != 16) {
        alert("length must be exactly 16 numbers")
      }
    }


</script>
@endsection