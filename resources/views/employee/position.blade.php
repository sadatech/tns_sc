@extends('layouts.app')
@section('title', "Position")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Position <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">        
            <div class="block-content block-content-full">
                <table class="table table-striped table-vcenter js-dataTable-full" id="positiontable">
                    <thead>
                        <th class="text-center">Name</th>
                        <th class="text-center" style="width: 15%;"> Action</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="fa fa-plus"></i> Update Position</h3>
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
                        <label>Position Name</label>
                        <input type="text" class="form-control" name="name" id="nameInput" required>
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
<link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
@endsection

@section('script')
<script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
    function editModal(id,name) {
        $('#editModal').modal('show');
        $('#editForm').attr('action', "{{ url('/employee/position/update') }}/"+id);
        $('#nameInput').val(name);
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
        $('#positiontable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('position.data') !!}',
            columns: [
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action' },
            ],
             'columnDefs': [
            {
                "targets": 0, // your case first column
                "className": "text-center",
                "width": "60%"
            },
            {
                "targets": 1, // your case first column
                "className": "text-center",
                "width": "30%"
            },
            ]
        });
    });
</script>
@endsection