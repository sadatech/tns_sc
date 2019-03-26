@extends('layouts.app')
@section('title', "Rejoin")
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
    <h2 class="content-heading pt-10">Rejoin <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">        
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <h3 class="block-title">
                    
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('rejoin.export') }}" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </div>
                </div>

                <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="Rejointable">
                  <thead>
                    <th class="text-center" style="width: 15%;"> Action</th>
                    <th class="text-center">NIK</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Agency</th>
                    <th class="text-center">Position</th>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="rejoin" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-edit"></i> Rejoin Confirmation</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="rejoinForm" method="post">
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
                            <td><b>Resign Date</b></td>
                            <td id="reasonJoindate">Example</td>
                        </tr>
                        <tr>
                            <td width="100"><b>Reason to Resign</b></td>
                            <td id="alasan" colspan="4">Example</td>
                        </tr>
                        <tr>
                            <td width="100"><b>Optional Information</b></td>
                            <td id="penjelasan" colspan="4">Example</td>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Rejoin Submission Date</label>
                            <input class="js-datepicker form-control" type="text" name="join_date" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Reason Rejoin</label>
                            <textarea class="form-control" name="alasan" placeholder="Reason" required></textarea>
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
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
@endsection

@section('script')
<script src="{{ asset('assets/js/pages/be_forms_wizard.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/moment/moment.min.js') }}"></script>
<script type="text/javascript">
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
    function modalRejoin(json) {
        $('#rejoinForm').attr('action', "{{ route('rejoin.add') }}");
        $('#employeeID').val(json.id);
        $('#reasonName').html(json.name);
        $('#reasonNIK').html(json.nik);
        $('#reasonPosition').html(json.position);
        $('#reasonStatus').html(json.status);
        $('#reasonAgency').html(json.agency);
        $('#alasan').html(json.alasan);
        $('#penjelasan').html(json.penjelasan);
        $('#reasonJoindate').html(json.resign_date+" ("+moment(json.resign_date).startOf('day').fromNow()+")");
        console.log(json);
    }
    $(function() {
        $('#Rejointable').DataTable({
            processing: true,
            serverSide: true,
            scrollY: "300px",
            ajax: '{!! route('rejoin.data') !!}',
            columns: [
            { data: 'action',        name: 'action' },
            { data: 'nik',           name: 'nik' },
            { data: 'name',          name: 'name' },
            { data: 'agency',        name: 'agency' },
            { data: 'position',      name:  'position'}
            ]
        });
    });
</script>
@endsection