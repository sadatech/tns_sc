@extends('layouts.app')
@section('title', "NEWS Create")
@section('content')
<div class="content">
    <h2 class="content-heading pt-10">NEWS <small>Create</small></h2>
    <div class="container">
       <div class="block">
        <div class="block-content">
            <form action="{{ route('update.news',$news->id) }}" method="post">
               {!! csrf_field() !!}
               <div class="form-group row">
                <label class="col-lg-3 col-form-label" for="example-hf-email">Sender</label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" name="sender" value="{{$news->sender}}">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label" for="example-hf-email">Subject</label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" name="subject" value="{{$news->subject}}">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label" for="example-hf-password">Contents</label>
                <div class="col-lg-7">
                   <textarea class="summernote" name="content">{!!$news->content!!}</textarea>
               </div>
           </div>
           <div class="form-group row">
            <label class="col-lg-3 col-form-label">Target</label>
            <div class="col-lg-7">
              <select class="js-select2 custom-select" name="target" id="target" required>
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
        $('#target option[value="{{ $news->target==null ? "All" : $news->target }}"]').attr('selected','selected');
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