@extends('layouts.app')
@section('title', "Store")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Store <small>Manage</small></h2>
    <div class="block block-themed"> 
        <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
            <h3 class="block-title">Datatables</h3>
        </div>
        <div class="block">
            <div class="block-content block-content-full">
                <div class="block-header p-0 mb-20">
                    <h3 class="block-title">
                        <a href="{{ route('tambah.store') }}" class="btn btn-primary btn-square" title="Add Data Store"><i class="fa fa-plus mr-2"></i>Add Data</a>
                    </h3>
                    <div class="block-option">
                        <button class="btn btn-info btn-square"  data-toggle="modal" data-target="#importModal"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
                        <a href="{{route('store.exportXLS')}}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </div>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered dataTable" id="storetable">
                    <thead>
                        <th class="text-center" style="width: 60px;">Action</th>
                        <th width="200px">Name</th>
                        <th width="200px">Optional Name</th>
                        <th width="200px">Sub Area</th>
                        <th width="200px">Account</th>
                        <th width="200px">Sales Tiers</th>
                        <th>Store Panel</th>
                        <th>Coverage</th>
                        <th>Is Vito</th>
                        <th>Is Jawa</th>
                        <th>Delivery</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th width="200px">Address</th>
                    </thead>
                </table>
            </div> 
        </div> 
    </div>


<div class="modal s" id="importModal" role="dialog" aria-labelledby="importModal" aria-hidden="true" >
  <div class="modal-dialog modal-dialog-popout" role="document" style="max-width: 100%;padding-left: 10px;">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="si si-cloud-upload mr-2"></i> Import <i>Data Store</i></h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form id="import-form" method="post" enctype="multipart/form-data" action="{{ route('store.importXLS') }}">
        {{ csrf_field() }}
        <div class="block-content">
          <div class="form-group">
            <a href="{{ route('store.exampleSheet') }}" class="btn btn-sm btn-info" style="float: right;">Download Import Format</a>
          </div>
          <div class="block-content">
            <h5> Sample Data :</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td><b>Name</b></td>
                        <td><b>Optiopnal Name</b></td>
                        <td><b>Address</b></td>
                        <td><b>Latitude</b></td>
                        <td><b>Longitude</b></td>
                        <td><b>Account</b></td>
                        <td><b>Sub Area</b></td>
                        <td><b>Timezone</b></td>
                        <td><b>Sales Tier</b></td>
                        <td><b>Is Vito</b></td>
                        <td><b>Is Jawa</b></td>
                        <td><b>Store Panel</b></td>
                        <td><b>Coverage</b></td>
                        <td><b>Delivery</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John</td>
                        <td>Johnny</td>
                        <td>26932 Arno Rapids Apt. 771</td>
                        <td>13.607672</td>
                        <td>151.669133</td>
                        <td>id_account</td>
                        <td>id_subarea</td>
                        <td>id_timezone</td>
                        <td>id_salestier</td>
                        <td>Vito / Non Vito</td>
                        <td>Jawa / Non Jawa</td>
                        <td>No</td>
                        <td>Direct</td>
                        <td>Direct</td>
                    </tr>
                    <tr>
                </tbody>
            </table>
          </div>
          <div class="form-group">
            <label>Select File</label>
            <input type="file" name="file" class="form-control">
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
        $('#storetable').DataTable({
            processing: true,
            scrollX: true,
            drawCallback: function(){ 
                $('.popup-image').magnificPopup({
                    type: 'image',
                });
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
            ajax: '{!! route('store.data') !!}',
            serverSide: true,
            scrollY: "300px",
            columns: [  
            { data: 'action', name: 'action' },
            { data: 'name1', name: 'name1' },
            { data: 'name2', name: 'name2' },
            { data: 'subarea', name: 'subarea' },
            { data: 'account', name: 'account' },
            { data: 'sales', name: 'sales' },
            { data: 'store_panel', name: 'store_panel' },
            { data: 'coverage', name: 'coverage' },
            { data: 'is_vito', name: 'is_vito' },
            { data: 'is_jawa', name: 'is_jawa' },
            { data: 'delivery', name: 'delivery' },
            { data: 'longitude', name: 'longitude' },
            { data: 'latitude', name: 'latitude' },
            { data: 'address', name: 'address' },
            ]
        });
    });
    </script>
@endsection
