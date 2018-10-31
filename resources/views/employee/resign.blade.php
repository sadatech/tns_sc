@extends('layouts.app')
@section('title', "Resign")
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
    <h2 class="content-heading pt-10">Resign <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block-content block-content-full">
            <div class="block-header p-0 mb-20">
            </div>
            <table class="table table-striped table-vcenter js-dataTable-full" id="resigntable">
                <thead class="text-center">
                    <th></th>
                    <th width="200px">NIK</th>
                    <th width="200px">Name</th>
                    <th>Position</th>
                    <th width="200px">No. KTP</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th width="200px">Join Date</th>
                    <th>Agency</th>
                    <th>Gender</th>
                    <th>Education</th>
                    <th width="200px">Birth Date</th>
                </thead>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="resign" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Resign Confirmation</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="resignForm" method="post">
                {!! csrf_field() !!}
                <input type="hidden" name="employee" id="employeeID">
                <div class="block-content">
                    <label>User Information</label>
                    <table class="table table-bordered">
                        <tr>
                            <td width="100"><b>Name</b></td>
                            <td id="reasonName" colspan="4">Example</td>
                        </tr>
                        <tr>
                            <td><b>NIK</b></td>
                            <td id="reasonNIK" colspan="4">Example</td>
                        </tr>
                        <tr>
                            <td><b>Position</b></td>
                            <td id="reasonPosition">Example</td>
                            <td><b>Status</b></td>
                            <td id="reasonStatus">Example</td>
                        </tr>
                        <tr>
                            <td><b>Agency</b></td>
                            <td id="reasonAgency">Example</td>
                            <td><b>Join Date</b></td>
                            <td id="reasonJoindate">Example</td>
                        </tr>
                    </table>
                    <div class="row">
                        {{-- <div class="form-group col-md-6">
                            <label>Resign Submission Date</label>
                            <input class="js-datepicker form-control" type="date" name="submission" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Effective Resign Date</label>
                            <input class="js-datepicker form-control" type="date" name="effective" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                        </div> --}}
                        <div class="form-group col-md-6">
                            <label>Resign Submission Date</label>
                            <input class="js-datepicker form-control" type="text" name="submission" id="submission" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required onchange="dateCompare()">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Effective Resign Date</label>
                            <input class="js-datepicker form-control" type="text" name="effective" id="effective" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required onchange="dateCompare()">
                            <small id="alert" style="color:red;display:none;">Note: Effective date must equal or greater than submission.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Reason to Resign</label>
                            <select class="js-select2 form-control" id="example-select2" style="width: 100%" data-placeholder="Choose one.." name="reason[]" multiple="multiple">
                                <option>Sick</option>
                                <option>Pregnant</option>
                                <option>Cut</option>
                                <option>Bad Attitude</option>
                                <option>Take Out</option>
                                <option>Got a new job</option>
                                <option>Without Explanation</option>
                                <option>Family/Personal Needs</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Optional Information</label>
                            <textarea class="form-control" name="optional" id="reasonInput"{{--  required --}}></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-alt-success" id="btnSubmit">
                        <i class="fa fa-save"></i> Save
                    </button>
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="alertDateCompare" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-gd-sun p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Notification</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="block-content">
                Effective date must be greater than submission date. Please select another date.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/moment/moment.min.js') }}"></script>
<script type="text/javascript">
    $(".js-select2").select2({
      tags: true
    });
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
    function modalResign(json) {
        $('#resignForm').attr('action', "{{ route('resign.add') }}");
        $('#employeeID').val(json.id);
        $('#reasonName').html(json.name);
        $('#reasonNIK').html(json.nik);
        $('#reasonPosition').html(json.position);
        $('#reasonStatus').html(json.status);
        $('#reasonAgency').html(json.agency);
        $('#reasonJoindate').html(json.joindate+" ("+moment(json.joindate).startOf('day').fromNow()+")");
    }
    $(function() {
        $('#resigntable').DataTable({
            processing: true,
            scrollX: true,
            scrollY: "300px",
            ajax: '{!! route('resign.data') !!}',
            columns: [
            { data: 'action', name: 'action' },
            { data: 'nik', name: 'nik' },
            { data: 'name', name: 'name' },
            { data: 'position', name: 'position' },
            { data: 'ktp', name: 'ktp' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'joinAt', name: 'joinAt' },
            { data: 'agency', name: 'agency' },
            { data: 'gender', name: 'gender' },
            { data: 'education', name: 'education' },
            { data: 'birthdate', name: 'birthdate' },
            ]
        });
    });
    $(document).ready(function(){
        $("#btnSubmit").hide();
    });

    function dateCompare(){
        var submission = $("#submission").val();
        var effective = $("#effective").val();
        if(submission != '' && effective != ''){
            if(new Date(submission) > new Date(effective))
            {
                $('#alert').show();
                $("#btnSubmit").hide();
            }else{
                $('#alert').hide();
                $("#btnSubmit").show();
            }
        }else{
            $("#btnSubmit").hide();
        }
    }
</script>
@endsection