@extends('layouts.app')
@section('title', "Confirm Delete ".$title)
@section('content')
<div class="content">
  <div class="block block-themed">
    <div class="block-header bg-gd-cherry">
      <h3 class="block-title">Confirm Delete {{ $title }}</h3>
      <span class="block-option"><i class="si si-trash mr-2"></i></span>
    </div>
    <div class="block-content">
      <label>Data yang akan terhapus:</label>
      <div class="bg-danger-light p-10">
        <ul class="pl-20 m-0">
          @foreach($data as $val)
          <li>{{ $val->name }}</li>
          @endforeach
        </ul>
      </div>
      <blockquote>
        asdasdas
      </blockquote>
    </div>
  </div>
</div>
@endsection

@section('css')
@endsection

@section('script')
@endsection