@extends('layouts.app')
@section('title', "Promo Activity")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Promo Activity <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <!-- <h3 class="block-title">
                        <a href="{{ route('tambah.faq') }}" class="btn btn-primary btn-square" title="Add Data Store"><i class="fa fa-plus mr-2"></i>Add Data</a>
                    </h3> -->
                    <div class="block-option">
                       <!--  <button class="btn btn-info btn-square"  data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button> -->
                        <a href="{{route('pa.exportXLS')}}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </div>
                </div>

                <table class="table table-striped table-vcenter js-dataTable-full dataTable" id="faqtable">
                    <thead>
                        <th class="text-center" style="width: 150px;">Action</th>
                        <th width="200px">Store Name</th>
                        <th width="200px">Employee Name</th>
                        <th width="200px">Product Name</th>
                        <th width="200px">Brand Name</th>
                        <th width="200px">Type</th>
                        <th width="200px">Description</th>
                        <th width="200px">Start Promo</th>
                        <th width="200px">End Promo</th>
                    </thead>
                </table>
            </div> 
        </div> 
    </div>


<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Import Data Account</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('pa.importXLS') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
       <!--  <div class="block-content">
          <div class="form-group">
              <a href="{{ route('account.download-template') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <h5> Sample Data :</h5>
          <table class="table table-bordered table-vcenter">
            <thead>
              <tr>
                  <td><b>account</b></td>
                  <td><b>channel</b></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                  <td>Name Account 1</td>
                  <td>Name Channel 1</td>
              </tr>
              <tr>
                  <td>Name Account 1</td>
                  <td>Name Channel 1</td>
              </tr>
            </tbody>
          </table>
        </div> -->
        <div class="block-content">
          <div class="form-group">
          <label>Upload Your Data Account:</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="file" data-toggle="custom-file-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                <label class="custom-file-label">Choose file Excel</label>
                <code> *Type File Excel</code>
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
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/magnific-popup/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <style type="text/css">
        [data-notify="container"] {
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .pac-container {
            z-index: 99999;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
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
    $(function() {
        $('#faqtable').DataTable({
            processing: true,
            scrollX: true,
            drawCallback: function(){ 
                $('.js-swal-delete').on('click', function(){
                    var url = $(this).data("url");
                    swal({
                        title: 'Are you sure?',
                        text: 'You will not be able to recover this data!',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d26a5c',
                        confirmButtonText: 'Yes, delete it!',
                        html: false,
                        preConfirm: function() {
                            return new Promise(function (resolve) {
                                setTimeout(function () {
                                    resolve();
                                }, 50);
                            });
                        }
                    }).then(function(result){
                        if (result.value) {
                            window.location = url;
                        } else if (result.dismiss === 'cancel') {
                            swal('Cancelled', 'Your data is safe :)', 'error');
                        }
                    });
                });
            },
            ajax: '{!! route('pa.data') !!}',
            serverSide: true,
            scrollY: "300px",
            columns: [  
            { data: 'action', name: 'action' },
            { data: 'store', name: 'store' },
            { data: 'employee', name: 'employee' },
            { data: 'product', name: 'product' },
            { data: 'brand', name: 'brand' },
            { data: 'type', name: 'type' },
            { data: 'description', name: 'description' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            ]
        });
    });
    </script>
@endsection
