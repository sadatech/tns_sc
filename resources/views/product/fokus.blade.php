@extends('layouts.app')
@section('title', "Product Fokus Product")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Product Fokus <small>Manage</small></h2>
  @if($errors->any())
    <div class="alert alert-danger">
      <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
      @foreach ($errors->all() as $error)
      <div> {{ $error }}</div>
      @endforeach
    </div>
  @endif
  <div class="block block-themed"> 
    <div class="block-header bg-gd-sun pl-20 pr-20 pt-15 pb-15">
        <h3 class="block-title">Datatables</h3>
    </div>
    <div class="block">        
      <div class="block-content block-content-full">
        <div class="block-header p-0 mb-20">
          <h3 class="block-title">
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus mr-2"></i>Add Data</button>
          </h3>
          <div class="block-option">
            <button class="btn btn-info btn-square"><i class="si si-cloud-upload mr-2"></i>Import Data</button>
            <a href="{{ route('fokus.export') }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
          </div>
        </div>
        <table class="table table-striped table-vcenter js-dataTable-full" id="promoTable">
        <thead>
          <th class="text-center" style="width: 70px;"></th>
          <th>Product</th>
          <th>Channel</th>
          <th>Area</th>
          <th>Date From</th>
          <th>Date Until</th>
          <th class="text-center" style="width: 15%;"> Action</th>
        </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- MODAL EDIT FOCUS --}}
@include('product._form_focus', ['id' => 'editModal', 'type' => 'edit'])

{{-- MODAL ADD FOCUS --}}
@include('product._form_focus', ['id' => 'tambahModal', 'action' => route('fokus.add')])

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <style type="text/css">
    [data-notify="container"] 
    {
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    </style>
@endsection

@section('script')
  <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
  <script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
  <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
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
          $('#promoTable').DataTable({
              processing: true,
              scrollY: "300px",
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
              ajax: '{!! route('fokus.data') !!}',
              columns: [
	              { data: 'id', name: 'id' },
                { data: 'product.name', name: 'product.name'},
                { data: 'fokus', name: 'fokus' },
	              { data: 'fokusarea', name: 'fokusarea' },
                { data: 'from', name: 'from' },
                { data: 'to', name: 'to' },
	              { data: 'action', name: 'action' },
              ]
          });
      });
  </script>
@endsection