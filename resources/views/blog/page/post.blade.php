@extends('blog.layouts.main')

@section('content')
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-md-8 mt-5">
        <h1 class="mb-3">{{ $post->title }}</h1>
        <img src="https://source.unsplash.com/1200x400?{{ $post->category->name }}" class="img-fluid"
          alt="{{ $post->category->name }}">

        <article class="my-3 fs-5">{!! $post->body !!}</article>

        <a href="/posts" class="d-block mt-3">Back to Posts</a>
      </div>
    </div>
  </div>
@endsection
