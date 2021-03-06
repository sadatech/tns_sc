@extends('layouts.app')
@section('title', "FAQ Update")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">FAQ <small>Update</small></h2>
    <div class="container">
	<div class="block">
                <div class="block-content">
                    <form action="{{ route('update.faq',$faq->id) }}" method="post">
                    	{!! csrf_field() !!}
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="example-hf-email">Question</label>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" name="question" value="{{$faq->question}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="example-hf-password">Answer</label>
                            <div class="col-lg-7">
                             <textarea class="summernote" name="answer">{!!$faq->answer!!}</textarea>
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

 <script>
      $('.summernote').summernote({
        name : 'answer',
        tabsize: 2,
        height: 100
      });
    </script>
@endsection