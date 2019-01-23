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
                    <div class="form-group col-md-6">
                        <label>Position</label>
                        <select class="form-control form-control-lg" name="position" id="position" required>
                            <option value="" disabled selected>Choose your Position</option>
                            @foreach($position as $option)
                            <option value="{{ $option->id }}" {{ (collect(old('position'))->contains($option->id)) ? 'selected':'' }}>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Agency</label>
                        <select id="agency" class="js-select form-control form-control-lg" style="width: 100%" name="agency" required>
                            <option value="" disabled selected>Choose your Agency</option>
                            @foreach($agency as $option)
                            <option value="{{ $option->id }}" {{ (collect(old('agency'))->contains($option->id)) ? 'selected':'' }}>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6" id="subarea" >
                        <label>Sub Area / Area</label>
                        <select class="js-select2 form-control form-control-lg" style="width: 100%" name="subarea[]" id="subareaInput">
                            <option disabled selected>Choose your Subarea</option>
                            @foreach($subarea as $option)
                            <option value="{{ $option->id }}" {{ (collect(old('subarea'))->contains($option->id)) ? 'selected':'' }}>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="custom-control custom-checkbox custom-control-inline mt-20" id="tl">
                        <input class="custom-control-input" type="checkbox" name="tl" id="example-inline-checkbox2">
                        <label class="custom-control-label" for="example-inline-checkbox2">TL Demo Cooking</label>
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
                                {{
                                    Form::select2Input('storesMobile', null, route('store-select2'), [
                                        'key' => 'obj.id + "|" + obj.name1',
                                        'text' => 'obj.name1',
                                        'useLabel' => false,
                                        'elOptions' => [
                                            'placeholder' => 'Choose store...',
                                            'style' => 'width: 100%',
                                            'id' => 'stores',
                                        ]
                                    ]) 
                                }}
                                {{-- <select id="stores" class="js-select2 form-control" style="width: 100%" data-placeholder="Choose store...">
                                    @foreach($store as $data)
                                    <option value="{{ $data->id.'|'.$data->name1}}">{{ $data->name1 }}</option>
                                    @endforeach
                                </select> --}}
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
                    <div class="form-group col-md-6" id="pasarMobile">
                        <label class="col-md-12" style="padding: 0">Pasar</label>
                        <div class="input-group mb-3 col-md-12" style="padding: 0">
                            <div style="width: 82%">
                                <select id="pasar" class="js-select2 form-control" style="width: 100%" data-placeholder="Pilih pasar...">
                                    @foreach($pasar as $data)
                                    <option value="{{ $data->id.'|'.$data->name}}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group-append" style="width: 18%">
                                <button id="pasarAdd" class="btn btn-outline-secondary" type="button" style="width: 100%">Add</button>
                            </div>
                        </div>
                        <!-- Block’s content.. -->
                        <div class="block block-themed block-rounded">
                            <div class="block-header bg-gd-lake" style="padding: 5px">
                                <h3 class="block-title">Pasar Terpilih</h3>
                                <span id="selectedStoreDefault" style="color: #ffeb5e;padding-top: 5px;">Tolong tambahkan pasar.</span>
                                <div class="block-options">
                                    <input type="text" id="myInput" class="form-control" onkeyup="searchFunction()" placeholder="Cari Pasar..">
                                </div>
                            </div>
                            <div class="block-content" style="padding: 0; width: 100%;">
                                <table id="selectedPasarTable" class="table table-striped table-vcenter" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th>Pasar</th>
                                            <th class="text-center" style="width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedPasarTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6" id="storeStay">
                        <label>Store</label>
                        <select class="js-select form-control" style="width: 100%" data-placeholder="Choose store..." name="store" id="stayInput">
                            @foreach($store as $data)
                            <option value="{{ $data->id }}">{{ $data->name1 }}</option>
                            @endforeach
                        </select>
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

    $("#example-inline-checkbox2").change(function() {
        if ($(this).Attr("checked")) {
            $('#example-inline-checkbox2').val(1);
        } else {
            $('#example-inline-checkbox2').val(0);
        }
    });

    var url = document.referrer;
    if (url.split("/")[5] == null) {
        $("#position option[value={{ App\Position::where(['level' => 'spggtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'mdgtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'motoric'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'tlgtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'dc'])->first()->id }}]").remove();
    } else if (url.split("/")[5] == "pasar") {
        $("#position option[value={{ App\Position::where(['level' => 'spgmtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'mdmtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'dc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'tlmtc'])->first()->id }}]").remove();
    } else if (url.split("/")[5] == "dc") {
        $("#position option[value={{ App\Position::where(['level' => 'spggtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'mdgtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'motoric'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'spgmtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'tlgtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'mdmtc'])->first()->id }}]").remove();
        $("#position option[value={{ App\Position::where(['level' => 'tlmtc'])->first()->id }}]").remove();

    }
    
    var selectedStores = [], selectedStoresId = [], selectedStoresName = [], tableIndex = 0;
    var selectedPasar = [], selectedPasarId = [], selectedPasarName = [], tableIndex = 0;

    $('#agency option[value="{{ $emp->agency->id }}"]').attr('selected','selected');
    $('#position option[value="{{ $emp->position->id }}"]').attr('selected','selected');
    $('#timezone option[value="{{ $emp->timezone->id }}"]').attr('selected','selected');
    $('#spv option[value="{{ $emp->id_position }}"]').attr('selected','selected');
    $('#status option[value="{{ $emp->status }}"]').attr('selected','selected');
    $('#timezone option[value="{{ $emp->timezone->id }}"]').attr('selected','selected');

    var position = $('#position').find(":selected").val();
    var status = $('#status').find(":selected").val();
    if (position == {{ App\Position::where(['level' => 'mdmtc'])->first()->id }}) {
        $('#status').show();
        $('#subarea').hide();
        $('#tl').hide();
        $('#pasarMobile').hide();
        $('#subareaInput').val(null);
        $('#status').val(null);
        if (status == 'Mobile') {
            var selected = {!! $store_selected !!};
            $('#storeStay').hide();
            $('#pasarMobile').hide();
            $('#storeMobile').show();
            $.each(selected, function( index, value ) {
              addItem(value.stores_item);
          });
        } else {
            var selected = {!! $store_selected !!};
            var getId = selected[0].stores_item.split("|")[0];
            $('#stayInput').val(getId).trigger("change");
            $('#storeMobile').hide();
            $('#storeStay').show();
        }
    } else if (position == {{ App\Position::where(['level' => 'spgmtc'])->first()->id }}) {
        if (status == 'Mobile') {
            var selected = {!! $store_selected !!};
            $('#storeStay').hide();
            $('#storeMobile').show();
            $.each(selected, function( index, value ) {
              addItem(value.stores_item);
          });
        } else {
            var selected = {!! $store_selected !!};
            var getId = selected[0].stores_item.split("|")[0];
            $('#stayInput').val(getId).trigger("change");
            $('#storeMobile').hide();
            $('#storeStay').show();
        }
        $('#status').show();
        $('#subarea').hide();
        $('#pasarMobile').hide();
        $('#tl').hide();
        $('#subareaInput').val(null);
        $('#status').val(null);
    } else if (position == {{ App\Position::where(['level' => 'spggtc'])->first()->id }}) {
        var selected = {!! $pasar_selected !!};
        $.each(selected, function( index, value ) {
          addItemPasar(value.pasars_item);
      });
        $('#pasarMobile').show();
        $('#status').hide();
        $('#subarea').hide();
        $('#tl').hide();
        $('#subareaInput').val(null);
        $('#status').val(null);
        $('#storeStay').hide();
        $('#storeMobile').hide();
    } else if (position == {{ App\Position::where(['level' => 'mdgtc'])->first()->id }}) {
        var selected = {!! $pasar_selected !!};
        $.each(selected, function( index, value ) {
          addItemPasar(value.pasars_item);
      });
        $('#pasarMobile').show();
        $('#status').hide();
        $('#tl').hide();
        $('#subarea').hide();
        $('#subareaInput').val(null);
        $('#status').val(null);
        $('#storeStay').hide();
        $('#storeMobile').hide();
    } else if (position == {{ App\Position::where(['level' => 'dc'])->first()->id }}) {
        var selectedArea2 = {!! $area_selected !!};
        var getIdArea2 = selectedArea2[0].subarea_item.split("|")[0];
        $('#subareaInput').val(getIdArea2).trigger("change");
        $('#subarea').show();
        $('#tl').show();
        $('#status').hide();
        $('#storeStay').hide();
        $('#storeMobile').hide();
        $('#pasarMobile').hide();
        $('#status').val(null);
        if({!! $isTl !!} == 1) {
            $('#example-inline-checkbox2').prop("checked", true);
        }
    } else if (position == {{ App\Position::where(['level' => 'tlmtc'])->first()->id }}) {
        var selectedArea = {!! $area_selected !!};
        var getIdArea = selectedArea[0].subarea_item.split("|")[0];
        $('#subareaInput').val(getIdArea).trigger("change");
        $('#subarea').show();
        $('#status').hide();
        $('#storeStay').hide();
        $('#storeMobile').hide();
        $('#pasarMobile').hide();
        $('#status').val(null);
        $('#tl').hide();
    } else if (position == {{ App\Position::where(['level' => 'tlgtc'])->first()->id }}) {
        var selectedArea = {!! $area_selected !!};
        var getIdArea = selectedArea[0].subarea_item.split("|")[0];
        $('#subareaInput').val(getIdArea).trigger("change");
        $('#subarea').show();
        $('#status').hide();
        $('#storeStay').hide();
        $('#storeMobile').hide();
        $('#pasarMobile').hide();
        $('#status').val(null);
        $('#tl').hide();
    } else if (position == {{ App\Position::where(['level' => 'motoric'])->first()->id }}) {
        $('#subarea').hide();
        $('#status').hide();
        $('#storeStay').hide();
        $('#storeMobile').hide();
        $('#pasarMobile').hide();
        $('#status').val(null);
        $('#subareaInput').val(null);
        $('#tl').hide();
    } else {
        $('#status').hide();
        $('#storeStay').hide();
        $('#storeMobile').hide();
        $('#status').val(null);
        $('#tl').hide();
            // $('#pasarMobile').hide();
            // $('#subarea').hide();
            // $('#status').hide();
            // $('#storeStay').hide();
            // $('#storeMobile').hide();
        }
        $(".js-select2").select2({
            tags: true
        });
        $(".js-select").select2();
        $(document).ready(function() {
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
        $('#pasarAdd').click(function () {
            var pasar = $('#pasar').val();
            if (pasar != null) {
                $('#pasar').val('');
                $('#select2-pasar-container').html('');
                addItemPasar(pasar);
            }else{                
                notif('Warning',': Please the select Pasar first','warning');
            }
        });
    });

        $('#position').on('change', e => {
            var select = $('#position').find(":selected").val()
            var status = $('#status').find(":selected").val()
            if (select == "{{ App\Position::where(['level' => 'mdmtc'])->first()->id }}") {
                $('#status').show();
                $('#subarea').hide();
                $('#tl').hide();
                $('#pasarMobile').hide();
                $('#subareaInput').val(null);
                $('#status').val(null);
            } else if (select == "{{ App\Position::where(['level' => 'spgmtc'])->first()->id }}") {
                $('#status').show();
                $('#subarea').hide();
                $('#tl').hide();
                $('#pasarMobile').hide();
                $('#subareaInput').val(null);
                $('#status').val(null);
            } else if (select == "{{ App\Position::where(['level' => 'spggtc'])->first()->id }}") {
                $('#pasarMobile').show();
                $('#status').hide();
                $('#subarea').hide();
                $('#tl').hide();
                $('#subareaInput').val(null);
                $('#status').val(null);
                $('#storeStay').hide();
                $('#storeMobile').hide();
            } else if (select == "{{ App\Position::where(['level' => 'mdgtc'])->first()->id }}") {
                $('#pasarMobile').show();
                $('#status').hide();
                $('#subarea').hide();
                $('#tl').hide();
                $('#subareaInput').val(null);
                $('#status').val(null);
                $('#storeStay').hide();
                $('#storeMobile').hide();
            } else if (select == "{{ App\Position::where(['level' => 'dc'])->first()->id }}") {
                $('#subarea').show();
                $('#tl').show();
                $('#status').hide();
                $('#storeStay').hide();
                $('#storeMobile').hide();
                $('#pasarMobile').hide();
                $('#status').val(null);
            } else if (select == "{{ App\Position::where(['level' => 'tlmtc'])->first()->id }}") {
                $('#status').hide();
                $('#storeStay').hide();
                $('#storeMobile').hide();
                $('#pasarMobile').hide();
                $('#status').val(null);
            } else if (select == "{{ App\Position::where(['level' => 'tlgtc'])->first()->id }}") {
                $('#status').hide();
                $('#storeStay').hide();
                $('#storeMobile').hide();
                $('#pasarMobile').hide();
                $('#status').val(null);
                $('#subareaInput').val(null);
                $('#subarea').show();
            } else if (select == "{{ App\Position::where(['level' => 'motoric'])->first()->id }}") {
                $('#status').hide();
                $('#storeStay').hide();
                $('#storeMobile').hide();
                $('#pasarMobile').hide();
                $('#status').val(null);
                $('#subareaInput').val(null);
                $('#subarea').hide();
                $('#tl').hide();
            } else {
                $('#status').hide();
                $('#storeStay').hide();
                $('#storeMobile').hide();
                $('#status').val(null);
            // $('#pasarMobile').hide();
            // $('#subarea').hide();
            // $('#status').hide();
            // $('#storeStay').hide();
            // $('#storeMobile').hide();
        }
    })
        $('#status').on('change', e => {
            var select = $('#position').find(":selected").val()
            var status = $('#status').find(":selected").val()
            if (status == 'Stay') {
                clearStores();
                $('#storeMobile').hide();
                $('#storeStay').show();
            } else {
                clearStores();
                $('#storeStay').hide();
                $('#storeMobile').show();
            } 
        })
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