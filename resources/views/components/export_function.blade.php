@php
    $direct   = isset($options['direct']) ? ( $options['direct'] ? true : false ) : true;
    $inDirect = isset($options['in-direct']) ? ( $options['in-direct'] ? true : false ) : true;
@endphp

@push('function-js')
{{-- <script type="text/javascript"> --}}
    @if ( $direct )
    var directDownloadButton = 0;
    function initDirectDownloadButton(elementId){
        if ( directDownloadButton == 0 ) {
            $('#'+elementId).before("<a id='download-file' target='_blank' download='DirectDownload.xlsx'></a>");
            directDownloadButton = 1;
        }
    }
    function directDownload(url, elementId) {
        var element = $("#"+elementId);
        var icon = $("#"+elementId+"-icon");
        initDirectDownloadButton(elementId);
        if (element.attr('disabled') != 'disabled') {
            var thisClass = icon.attr('class');

            $.ajax({
                url: url,
                type: 'POST',
                data: filters,
                beforeSend: function()
                {
                    element.attr('disabled', 'disabled');
                    icon.attr('class', 'fa fa-spinner fa-spin');
                },
                success: function (data) {
                    var a = $('#download-file');
                    var url = data.file;
                    a.prop('href', url);
                    window.open($("#download-file").attr('href'),'_blank')
                    a.prop('href', '#');

                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                },
                error: function(xhr, textStatus, errorThrown){
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    //swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
                }
            });
        }
    }
    @endif
    @if ( $inDirect )
    function inDirectDownload(url, elementId) {
        var element = $("#"+elementId);
        var icon = $("#"+elementId+"-icon");
        if (element.attr('disabled') != 'disabled') {
            var thisClass = icon.attr('class');

            $.ajax({
                type: 'POST',
                url: url,
                data: filters,
                beforeSend: function()
                {
                    element.attr('disabled', 'disabled');
                    icon.attr('class', 'fa fa-spinner fa-spin');
                },
                success: function (data) {
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    //swal(data.title, data.message, data.type);
                },
                error: function(xhr, textStatus, errorThrown){
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    //swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
                }
            });
        }
    }
    @endif
{{-- </script> --}}
@endpush