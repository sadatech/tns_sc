@extends('layouts.app')
@section('title', "Report Stockist Motorik")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Stockist Motorik <small>Report</small></h2>
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
                    </h3>
                    <div class="block-option">
                        <a href="#" title="Unduh Data" class="btn btn-success btn-square float-right ml-10"><i class="si si-cloud-download mr-2"></i>Unduh Data</a>
                    </div>
                </div>
                <table class="table table-striped table-vcenter js-dataTable-full table-responsive" id="category">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 70px;"></th>
                      </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection