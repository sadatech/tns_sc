@php
//################################
// name -> purpose of main view, for example menu name, use underscore, example: product_focus
// model -> model name, example: App\ProductFocus
// buttonId -> id of button that click to show the modal, example: upload-status
//################################
$route = str_replace('_','-',$name);
$model = str_replace('\\','bAckSlasH',$model);
$title = ucwords(str_replace('_',' ',$name));
@endphp

<div class="modal fade" id="trace-table" role="dialog" aria-labelledby="trace-table" aria-hidden="true" tabindex="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-popout modal-xl" role="document" >
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Job Status For {{ @$title }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="block-content" style="overflow-x: auto;">
                <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="traceTable" style="white-space: nowrap;width: 100% !important">
                    <thead>
                        <tr>
                            <th class="text-center"></th>    
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>File</th>
                            <th>Log</th>
                            <th>Request by</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('additional-css')
<style type="text/css">
    @media (min-width: 768px) {
      .modal-xl {
        width: 90%;
       max-width:1200px;
      }
    }
</style>
@endpush

@push('additional-js')
<script type="text/javascript">
    $('#{{$buttonId}}').attr('data-toggle','modal');
    $('#{{$buttonId}}').attr('href','#trace-table');
    $('#{{$buttonId}}').on('click',function(){
        $('#traceTable').dataTable().fnDestroy();
        $('#traceTable').dataTable({
            "processing": true,
            "serverSide": true,           
            "ajax": {
                url: "{{ route('job-trace.data', ['model' => $model, 'type' => 2]) }}",
                type: 'GET',
            },
            "rowId": "id",
            "columns": [
                { data: 'id',       name: 'id', visible: false},
                { data: 'type',     name: 'type'},
                { data: 'date',     name: 'date'},
                { data: 'status',   name: 'status'},
                { data: 'file',     name: 'file'},
                { data: 'log',      name: 'log'},
                { data: 'request_by', name: 'request_by'},
                { data: 'action',   name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "text-center", "targets": [0, 1, 3, 5, 6, 7]}
            ],
            "order": [ [0, 'desc'] ],  
            "searching": false,
            "pageLength": 5,
        });
    });

    $('#traceTable').on('click', 'tr td button.errorLog', function () {
        var log = $(this).val();            
            
        $("#exp-title").html("Upload {{$title}}");
        $("#exp-context").html(log);

        $("#explanation-modal").modal('show');
    });

    $('#traceTable').on('click', 'tr td button.deleteButton', function () {
        var id = $(this).val();
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover data!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "No, cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    $.ajax({
                        type: "POST",
                        url:  "{{ asset('job-trace/delete') }}/" + id,
                        success: function (data) {
                            $("#traceTable #"+id).remove();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });                        
                    swal("Deleted!", "Data has been deleted.", "success");
                } else {
                    swal("Cancelled", "Data is safe ", "success");
                }
            });
    });
</script>
@endpush