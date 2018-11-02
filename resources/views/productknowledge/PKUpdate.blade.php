@extends('layouts.app')
@section('title', "Product Knowledges Update")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">Product Knowledges <small>Update</small></h2>
    <div class="container">
	<div class="block">
                <div class="block-content">
                    <form action="{{ route('update.pk', $pk->id) }}" method="post" enctype="multipart/form-data">
                    	{!! csrf_field() !!}
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="example-hf-email">Admin</label>
                            <div class="col-lg-7 mb-1">
                                <input type="text" class="form-control" name="admin" value="{{$pk->admin}}" readonly="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="example-hf-email">Sender</label>
                            <div class="col-lg-7 mb-1">
                                <input type="text" class="form-control" name="sender" value="{{$pk->sender}}" readonly="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="example-hf-email">Subject</label>
                            <div class="col-lg-7 mb-1">
                                <input type="text" class="form-control" name="subject" value="{{$pk->subject}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Type</label>
                            <div class="col-lg-7 pb-1"> 
                                 <input type="text" value="{{$pk->type}}" class="form-control" name="type" readonly="">
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="example-hf-email">File Upload</label>
                            <div class="col-lg-7 mb-1">
                                <input type="file" class="form-control" name="fileku" accept=".pdf,application/pdf" placeholder="File" >
                                <code> *Type File PDF</code>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Target</label>
                            <div class="col-lg-7 mb-1">
                                <select class="js-select2 custom-select" id="target" name="target" required>
                                 <option value="" disabled selected>Choose Target</option>
                            @foreach($positions as $time)
                                <option value="{{$time->id}}">{{$time->name}}</option>
                            @endforeach
                                <option value="All">All</option>
                        </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-9 ml-auto">
                                <button type="submit" class="btn btn-alt-primary">Save</button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
</div>

</div>

@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#target option[value="{{ $pk->target==null ? "All" : $pk->target }}').attr('selected','selected');
        $('#target').trigger('change');
    });
</script>


 <script>
      $('.summernote').summernote({
        name : 'answer',
        tabsize: 2,
        height: 100
      });
    </script>


@endsection