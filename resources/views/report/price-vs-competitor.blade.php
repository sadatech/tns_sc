@extends('layouts.app')
@section('title', "Sales Report - Price vs Competitor")
@section('content')
<div class="content">
  <h2 class="content-heading pt-10"> Price vs Competitor <small>Report</small></h2>
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
            <button class="btn btn-primary btn-square" data-toggle="modal" data-target="#tambahModal"><i class="fa fa-plus mr-2"></i>Setting Main Competitor</button>
          </h3>
          <div class="block-option">
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (Selected)</button>
            <button class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data (All)</button>
          </div>
        </div>

        <table class="table table-striped table-vcenter js-dataTable-full" id="reportTable">
        <thead>
          <tr>
            <th style="vertical-align: middle; text-align: center;">CATEGORY</th>
            <th style="vertical-align: middle; text-align: center;">SASA</th>
            <th style="vertical-align: middle; text-align: center;">MAIN KOMPETITOR</th>
            <th style="vertical-align: middle; text-align: center;">BRAND KOMPETITOR</th>
            <th style="vertical-align: middle; text-align: center;">SASA Price</th>
            <th style="vertical-align: middle; text-align: center;">KOMPETITOR Price</th>
          </tr>

        </thead>
        </table>

      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="tambahModal" role="dialog" aria-labelledby="tambahModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-popout modal-lg" role="document">
    <div class="modal-content">
      <div class="block block-themed block-transparent mb-0">
        <div class="block-header bg-primary p-10">
          <h3 class="block-title"><i class="fa fa-plus"></i> Add Price vs Competitor</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
              <i class="si si-close"></i>
            </button>
          </div>
        </div>
      </div>
      <form action="{{ route('priceData.store') }}" method="post">
        {!! csrf_field() !!}
        <div class="block-content">

          <div class="row">
            <div class="form-group col-md-6">
              <label>Produk</label>
            </div>
            <div class="form-group col-md-6">
              <label>Main Competitor</label>
            </div>
          </div>
          @foreach($subCategories as $subcategory)
            @php
            $products = $subcategory->name."products";
            $productCompetitors = $subcategory->name."productCompetitors";
            @endphp
            @foreach($$products as $product)
              <div class="row">
                <div class="form-group col-md-6">
                  <h5><small>{{ $subcategory->name }} -</small> {{ $product->name }} </h5>
                  <input type="text" name="products[]" id="products" class="form-control" value="{{ $product->id }}" hidden>
                </div>
                <div class="form-group col-md-6">
                  <select name="competitors[]" id="competitors" class="form-control js-select">
                    <option value="">(none)</option>
                    @foreach($$productCompetitors as $productCompetitor)
                    <option value="{{ $productCompetitor->id }}"@if($product->id_main_competitor == "$productCompetitor->id") selected @endif>{{ $productCompetitor->brand_name .'-'. $productCompetitor->name }}</option>
                    @endforeach
                  </select>
                </div>

              </div>
            @endforeach
          @endforeach

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
    <style type="text/css">
    [data-notify="container"] 
    {
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    th, td {
        white-space: nowrap;
    }
    </style>
@endsection

@section('script')

  <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
  <script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
  <script src="{{ asset('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('js/select2-handler.js') }}"></script>
  <script type="text/javascript">

        $(document).ready(function() {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $('#employeeSelect').select2(setOptions('{{ route("employee-select2") }}', 'Select Employee', function (params) {
          return filterData('employee', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.nik+' - '+obj.name}
            })
          }
        }));

        $('#storeSelect').select2(setOptions('{{ route("store-select2") }}', 'Select Store', function (params) {
          return filterData('store', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name1+' - '+obj.name2}
            })
          }
        }));


        $('#productSelect').select2(setOptions('{{ route("product-select2") }}', 'Select Product', function (params) {
          return filterData('product', params.term);
        }, function (data, params) {
          return {
            results: $.map(data, function (obj) {                                
              return {id: obj.id, text: obj.name+' ('+obj.deskripsi+')'}
            })
          }
        }));

      });

//   $("#datepicker").datepicker( {
//     format: "mm-yyyy",
//     viewMode: "months", 
//     minViewMode: "months"
// });
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
          $('#reportTable').DataTable({
              processing: true,
              serverSide: true,
              ajax: '{!! route('priceData.dataVs') !!}',
              columns: [
                { data: 'category_name', name: 'category_name'},
                { data: 'name', name: 'name'},
                { data: 'competitor_name', name: 'competitor_name'},
                { data: 'competitor_brand', name: 'competitor_brand'},
                { data: 'price', name: 'price'},
                { data: 'price_competitor', name: 'price_competitor'},
              ],
              "scrollX":        true, 
              "scrollCollapse": true,
          });
      });

  </script>
@endsection