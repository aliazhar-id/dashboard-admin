@extends('dashboard.layouts.main')

@section('content')
  <div class="row my-3">
    <div class="col-lg-8">
      <h1 class="mb-3">{{ $post->title }}</h1>

      <a href="/dashboard/posts" class="btn btn-success"> <span data-feather="arrow-left"></span> Back to all my posts</a>
      <a href="/dashboard/posts/{{ $post->slug }}/edit" class="btn btn-warning"> <span data-feather="edit"></span> Edit</a>
      <a href="" class="btn btn-danger"> <span data-feather="x-circle"></span> Delete</a>

      <img src="{{ $post->image ? asset('storage/' . $post->image) : '/assets/default-banner.jpg' }}"
        class="img-fluid mt-3" alt="{{ $post->category->name }}">

      <article class="my-3 fs-5">{!! $post->body !!}</article>
    </div>
  </div>
@endsection
