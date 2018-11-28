@extends('layouts.app')
@section('title', "Report Kunjungan Demo Cooking")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Report Kunjungan Demo Cooking <small>Manage</small></h2>
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
                        
                        <a href="{{ route('plan.export') }}" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </h3>
                </div>
                <table class="table table-responsive table-striped table-vcenter js-dataTable-full" id="planTable">
                <thead>
                    <th style="width: 10%;"></th>
                    <th style="width: 300px;">Employee</th>
                    <th style="width: 10%;">Date</th>
                    <th style="width: 10%;">Plan</th>
                    <th style="width: 10%;">Stockist</th>
                    <th>Channel</th>
                    <th style="width: 10%;">Actual</th>
                    <th style="width: 10%;">Photo</th>
                </thead>
                </table>
            </div>
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
</style>
@endsection

@section('script')
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
        $('#planTable').DataTable({
            processing: true,
            ajax: '{!! route('dc.kunjungan.data') !!}',
            scrollY: "300px",
            columns: [
            { data: 'id'},
            { data: 'planEmployee', name: 'planEmployee' },
            { data: 'date', name: 'plan_dcs.date'},
            { data: 'plan', name: 'plan_dcs.plan'},
            { data: 'stocklist', name: 'plan_dcs.stocklist'},
            { data: 'channel', name: 'plan_dcs.channel'},
            { data: 'actual', name: 'plan_dcs.actual' },
            { data: 'photo', name: 'plan_dcs.photo' }
            ]
        });
    });
</script>
@endsection