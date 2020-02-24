@php
//################################
// button_id    -> name of page that using this function, example: product_focus
// name         -> name of page that using this function, example: product_focus
// form_url     -> url of form action
// template_url -> url for download template
// sample_data  -> sample input with the table
//################################

$title  = ucwords(str_replace('_',' ',$name));
$id     = str_replace(' ','',$title);

$tableHeader    = "";
$tableContent   = "";

foreach(array_keys($sample_data) as $key => $value){
    $child = $sample_data[$value];
    $tableHeader .= "<th>".strtoupper(str_replace('_',' ',$value))."</th>";
}
foreach(array_keys($child) as $key => $value){
    $tableContent .= '<tr>';
    foreach(array_keys($sample_data) as $key2 => $value2){
        $tableContent .= '<td>'.$sample_data[$value2][$key].'</td>';
    }
    $tableContent .= '</tr>';
}
@endphp

<div class="modal fade" id="importModal{{ $id }}" role="dialog" aria-labelledby="importModal{{ $id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary p-10">
                    <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import {{ $title }} Data</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <form id="importForm{{ $id }}" method="post" enctype="multipart/form-data" action="{{ $form_url }}">
                {{ csrf_field() }}
                <div class="block-content">
                    <div class="form-group">
                      <a href="{{ $template_url }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
                    </div>
                    <div class="block-content">
                        <h5> Sample Data :</h5>
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    {!! $tableHeader !!}
                                </tr>
                            </thead>
                            <tbody>
                                {!! $tableContent !!}
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Upload Your {{ $title }} Data:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                            <label class="custom-file-label">Choose file Excel</label>
                            <code> *Type File Excel</code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-alt-success">
                        <i class="fa fa-save"></i> Import
                    </button>
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('additional-css')
<style type="text/css">
    
</style>
@endpush

@push('additional-js')
<script type="text/javascript">
    $('#{{$button_id}}').attr('data-toggle','modal');
    $('#{{$button_id}}').attr('href','#importModal{{ $id }}');
</script>
@endpush