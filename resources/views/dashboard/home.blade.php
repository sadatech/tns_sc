@extends('dashboard')
@section('konten')
<div class="row invisible" data-toggle="appear">
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-briefcase fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600" id="product"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Product</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-basket fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600" id="store"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Store</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-handbag fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600" id="pasar"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Pasar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-left mt-10 d-none d-sm-block">
                    <i class="si si-users fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600" id="employee"><i class="fa fa-spin fa-spinner"></i></div>
                <div class="font-size-sm font-w600 text-uppercase text-muted">Employee</div>
            </div>
        </a>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function(){
        $.ajax({
            url: '{{ route('data.dashboard') }}',
            type: 'GET',
            success: function (data) {
                // console.log(data);
                if (data.success){
                    $('#employee').html(data.count.employee);
                    $('#store').html(data.count.store);
                    $('#product').html(data.count.product);
                    $('#pasar').html(data.count.pasar);
                }
            }
        });
    })
</script>
@endsection