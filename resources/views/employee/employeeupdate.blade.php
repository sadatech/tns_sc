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
            <div class="block-header bg-gd-sun p-10">
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
                        <input type="hidden" name="globalPosition" id="globalPosition">
                        <input type="text" class="form-control" name="nik" value="{{$emp->nik}}" placeholder="Add new nik" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{$emp->name}}" placeholder="Add new employee" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>No. KTP</label>
                        <input type="text" id="numKtp" onblur="checkLength(this)" value="{{$emp->ktp}}" class="form-control" name="ktp" placeholder="Add new KTP"  minlength="16" maxlength="16" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text" id="numPhone" class="form-control" name="phone" value="{{$emp->phone}}" placeholder="Add new phone" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{$emp->email}}" placeholder="Add new email" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" value="{{ old('password') }}" placeholder="Add new password">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <select class="form-control form-control-lg" name="gender" required>
                            <option value="" disabled selected>Choose your Gender</option>
                            <option value="Laki-laki" @if ($emp->gender == "Laki-laki") {{ 'selected' }} @endif>Male</option>
                            <option value="Perempuan" @if ($emp->gender == "Perempuan") {{ 'selected' }} @endif>Female</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Education</label>
                        <select class="form-control form-control-lg" name="education" required>
                            <option value="" disabled selected>Choose your Education</option>
                            <option value="SD" @if ($emp->education == "SD") {{ 'selected' }} @endif>SD</option>
                            <option value="SMP" @if ($emp->education == "SMP") {{ 'selected' }} @endif>SMP</option>
                            <option value="SLTA" @if ($emp->education == "SLTA") {{ 'selected' }} @endif>SLTA</option>
                            <option value="D1" @if ($emp->education == "D1") {{ 'selected' }} @endif>D1</option>
                            <option value="D2" @if ($emp->education == "D2") {{ 'selected' }} @endif>D2</option>
                            <option value="D3" @if ($emp->education == "D3") {{ 'selected' }} @endif>D3</option>
                            <option value="S1/D4" @if ($emp->education == "S1/D4") {{ 'selected' }} @endif>S1/D4</option>
                            <option value="S2" @if ($emp->education == "S2") {{ 'selected' }} @endif>S2</option>
                        </select>
                    </diV>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Tanggal Lahir</label>
                        <input class="js-datepicker form-control" value="{{$emp->birthdate}}" type="text" name="birthdate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Join Date</label>
                        <input class="js-datepicker form-control" type="text" value="{{$emp->joinAt}}" name="joinAt" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Foto KTP</label>
                        <div class="custom-file">
                            <input type="file" name="foto_ktp" class="custom-file-input" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp">
                            <label class="custom-file-label">Pilih Foto</label>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Foto Profile</label>
                        <div class="custom-file">
                            <input type="file" name="foto_profile" class="custom-file-input" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp">
                            <label class="custom-file-label">Pilih Foto</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Timezones</label>
                        <select class="form-control form-control-lg" id="timezone" name="timezone" required>
                            <option value="" disabled selected>Choose your Timezone</option>
                            @foreach($timezone as $option)
                            <option value="{{ $option->id }}" {{ (collect(old('timezone'))->contains($option->id)) ? 'selected':'' }}>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-user mr-2"></i>Bank Account</h3>
                </div>
            </div>
            <div class="block-content">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nomor Rekening</label>
                        <input type="text" id="txtboxToFilter" class="form-control" value="{{ $emp->rekening}}" name="rekening" placeholder="Add rekening">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nama Bank</label>
                        <input type="text" class="form-control" name="bank" value="{{ $emp->bank }}" placeholder="Add bank">
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
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-user mr-2"></i>Employee Positioning</h3>
                </div>
            </div>
            <div class="block-content">
                <div class="row">

                    <div class="form-group col-md-6" id="routeGroup">
                        <label class="col-md-12" style="padding: 0">Route</label>
                        <div class="input-group mb-3 col-md-12" style="padding: 0">
                            <div style="width: 82%">
                                {{
                                    Form::select2Input('routeSelect', null, route('route-select2', ['type' => 1]), [
                                        'key' => 'obj.id + "|" + obj.name',
                                        'useLabel' => false,
                                        'elOptions' => [
                                            'placeholder' => 'Choose Route...',
                                            'style' => 'width: 100%',
                                            'id' => 'route',
                                        ]
                                    ]) 
                                }}
                                {{-- <select id="pasar" class="js-select2 form-control" style="width: 100%" data-placeholder="Pilih pasar...">
                                    @foreach($pasar as $data)
                                    <option value="{{ $data->id.'|'.$data->name}}">{{ $data->name }}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                            <div class="input-group-append" style="width: 18%">
                                <button id="routeAdd" class="btn btn-outline-secondary" type="button" style="width: 100%">Add</button>
                            </div>
                        </div>
                        <!-- Block’s content.. -->
                        <div class="block block-themed block-rounded">
                            <div class="block-header bg-gd-lake" style="padding: 5px">
                                <h3 class="block-title">Route Terpilih</h3>
                                <span id="selectedRouteDefault" style="color: #ffeb5e;padding-top: 5px;">Tolong tambahkan market.</span>
                                <div class="block-options">
                                    <input type="text" id="myInput3" class="form-control" onkeyup="searchFunctionRoute()" placeholder="Cari Route..">
                                </div>
                            </div>
                            <div class="block-content" style="padding: 0; width: 100%;">
                                <table id="selectedRouteTable" class="table table-striped table-vcenter" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th>Route</th>
                                            <th class="text-center" style="width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedRouteTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6" id="pasarGroup">
                        <label class="col-md-12" style="padding: 0">Market</label>
                        <div class="input-group mb-3 col-md-12" style="padding: 0">
                            <div style="width: 82%">
                                {{
                                    Form::select2Input('pasarSelect', null, route('route-select2', ['type' => 2]), [
                                        'key' => 'obj.id + "|" + obj.name',
                                        'useLabel' => false,
                                        'elOptions' => [
                                            'placeholder' => 'Choose Market...',
                                            'style' => 'width: 100%',
                                            'id' => 'pasar',
                                        ]
                                    ]) 
                                }}
                                {{-- <select id="pasar" class="js-select2 form-control" style="width: 100%" data-placeholder="Pilih pasar...">
                                    @foreach($pasar as $data)
                                    <option value="{{ $data->id.'|'.$data->name}}">{{ $data->name }}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                            <div class="input-group-append" style="width: 18%">
                                <button id="pasarAdd" class="btn btn-outline-secondary" type="button" style="width: 100%">Add</button>
                            </div>
                        </div>
                        <!-- Block’s content.. -->
                        <div class="block block-themed block-rounded">
                            <div class="block-header bg-gd-lake" style="padding: 5px">
                                <h3 class="block-title">Market Terpilih</h3>
                                <span id="selectedPasarDefault" style="color: #ffeb5e;padding-top: 5px;">Tolong tambahkan market.</span>
                                <div class="block-options">
                                    <input type="text" id="myInput2" class="form-control" onkeyup="searchFunctionPasar()" placeholder="Cari Market..">
                                </div>
                            </div>
                            <div class="block-content" style="padding: 0; width: 100%;">
                                <table id="selectedPasarTable" class="table table-striped table-vcenter" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th>Market</th>
                                            <th class="text-center" style="width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedPasarTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-alt-success">
                    <i class="fa fa-save"></i> Save
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-alt-secondary" data-dismiss="modal">Back</a>
            </div>
        </form>            
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

    $(function() {
      $('#example-inline-checkbox2').on('change', function(e) {
        e.stopPropagation();
        this.value = this.checked ? '1' : '0';
      });
    })

    $(function() {
        var pasar_selected = {!! $pasar_selected !!};
        $.each(pasar_selected, function( index, value ) {
            addItemPasar(value.pasars_item);
        });

        var route_selected = {!! $route_selected !!};
        $.each(route_selected, function( index, value ) {
            addItemRoute(value.routes_item);
        });

        $('#pasarAdd').click(function () {
            var pasar = $('#pasar').val();
            if (pasar != null) {
                $('#pasar').val('');
                $('#select2-pasar-container').html('');
                addItemPasar(pasar);
            }else{                
                notif('Warning',': Please the select Market first','warning');
            }
        });
        $('#routeAdd').click(function () {
            var route = $('#route').val();
            if (route != null) {
                $('#route').val('');
                $('#select2-route-container').html('');
                addItemRoute(route);
            }else{                
                notif('Warning',': Please the select Route first','warning');
            }
        });
    })

    var url = document.referrer;
    var positions = url.split("/");
    var pos = positions[positions.length -1];    
    $("#globalPosition").val(pos);
    
    var selectedStores = [], selectedStoresId = [], selectedStoresName = [], tableIndex = 0;
    var selectedPasar = [], selectedPasarId = [], selectedPasarName = [], tableIndex = 0;
    var selectedRoute = [], selectedRouteId = [], selectedRouteName = [], tableIndexRoute = 0;

    $('#timezone option[value="{{ $emp->timezone->id }}"]').attr('selected','selected');

        function clearStores() {
            $('#stores').val('');
            $('#select2-stores-container').html('<span class="select2-selection__placeholder">Choose your Store</span>');
        }
        function clearPasar() {
            $('#pasar').val('');
            $('#select2-pasar-container').html('<span class="select2-selection__placeholder">Choose your Pasar</span>');
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
        function addItemPasar(pasar, get = '') {
            var pasarSplit = pasar.split("|");
            var a = selectedPasarId.indexOf(''+pasarSplit[0]);

            if (get != 'get') {
                selectedPasar.push(pasar);
                selectedPasarId.push(pasarSplit[0]);
                selectedPasarName.push(pasarSplit[1]);
            }

            if (a < 0 || get == 'get') {
                tableIndex++;
                $('#selectedPasarTable').removeAttr('style');
                $('#selectedPasarDefault').css('display','none');
                $('#selectedPasarTableBody').append("<tr>"+
                    "<th class='text-center' scope='row'>"+ tableIndex +"</th>"+
                    "<td><span>"+ pasarSplit[1] +"</span>"+
                    "<input type='hidden' name='pasar[]' value='"+ pasarSplit[0] +
                    "'></td>"+
                    "<td class='text-center'>"+
                    "<div class='btn-group'>"+
                    "<button type='button' class='btn btn-sm btn-secondary js-tooltip-enabled' data-toggle='tooltip' title=' data-original-title='Delete' onclick='deleteItemPasar("+ pasarSplit[0] +")'>"+
                    "<i class='fa fa-times'></i>"+
                    "</button>"+
                    "</div>"+
                    "</td>"+
                    "</tr>");
            }else{
                var msg = " : Data Already Exist! data: "+pasarSplit[1];
                notif('Warning',msg,'warning');
            }
        }
        function addItemRoute(route, get = '') {
            var routeSplit = route.split("|");
            var a = selectedRouteId.indexOf(''+routeSplit[0]);

            if (get != 'get') {
                selectedRoute.push(route);
                selectedRouteId.push(routeSplit[0]);
                selectedRouteName.push(routeSplit[1]);
            }

            if (a < 0 || get == 'get') {
                tableIndexRoute++;
                $('#selectedRouteTable').removeAttr('style');
                $('#selectedRouteDefault').css('display','none');
                $('#selectedRouteTableBody').append("<tr>"+
                    "<th class='text-center' scope='row'>"+ tableIndexRoute +"</th>"+
                    "<td><span>"+ routeSplit[1] +"</span>"+
                    "<input type='hidden' name='route[]' value='"+ routeSplit[0] +
                    "'></td>"+
                    "<td class='text-center'>"+
                    "<div class='btn-group'>"+
                    "<button type='button' class='btn btn-sm btn-secondary js-tooltip-enabled' data-toggle='tooltip' title=' data-original-title='Delete' onclick='deleteItemRoute("+ routeSplit[0] +")'>"+
                    "<i class='fa fa-times'></i>"+
                    "</button>"+
                    "</div>"+
                    "</td>"+
                    "</tr>");
            }else{
                var msg = " : Data Already Exist! data: "+routeSplit[1];
                notif('Warning',msg,'warning');
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
        function deleteItemPasar(id) {
            var a = selectedPasarId.indexOf(''+id);
            if (a >= 0) {
                selectedPasar.splice(a, 1);
                selectedPasarId.splice(a, 1);
                selectedPasarName.splice(a, 1);
                tableIndex = 0;
                $('#selectedPasarTableBody').html('');
                $.each(selectedPasar, function( index, value ) {
                    addItemPasar(value,'get');
                });
            }else{
                console.log("Index Item Not Found!");
            }
        }
        function deleteItemRoute(id) {
            // console.log(id);
            var a = selectedRouteId.indexOf(''+id);
            if (a >= 0) {
                selectedRoute.splice(a, 1);
                selectedRouteId.splice(a, 1);
                selectedRouteName.splice(a, 1);
                tableIndexRoute = 0;
                $('#selectedRouteTableBody').html('');
                $.each(selectedRoute, function( index, value ) {
                    addItemRoute(value,'get');
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
        function searchFunctionPasar() {
            var input, filter, table, tr, a, i;
            input = document.getElementById("myInput2");
            filter = input.value.toUpperCase();
            table = document.getElementById("selectedPasarTableBody");
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

        function searchFunctionRoute() {
            var input, filter, table, tr, a, i;
            input = document.getElementById("myInput3");
            filter = input.value.toUpperCase();
            table = document.getElementById("selectedRouteTableBody");
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