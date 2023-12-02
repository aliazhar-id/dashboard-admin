<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Cviebrock\EloquentSluggable\Services\SlugService;

class PostController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('dashboard.posts', [
      'title' => 'My Post',
      'posts' => Post::latest()->where('id_user', auth()->user()->id_user)->paginate(10)
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('dashboard.create', [
      'title' => 'Create',
      'categories' => Category::all()
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $customError = [
      'title.unique' => 'There is already a post with this title on your blog.'
    ];

    $validatedData = $request->validate([
      'title' => [
        'required',
        'min:3',
        'max:255',
        Rule::unique('posts')->where(fn ($query) => $query->where('title', request()->title)->where('id_user', auth()->user()->id_user))
      ],
      'id_category' => 'required',
      'body' => 'required'
    ], $customError);

    $validatedData['id_user'] = auth()->user()->id_user;
    $validatedData['slug'] = SlugService::createSlug(Post::class, 'slug', $validatedData['title']);
    $validatedData['excerpt'] = Str::limit(strip_tags($validatedData['body']), 150, '...');
    $validatedData['image'] = 'https://source.unsplash.com/1200x400';

    Post::create($validatedData);

    return redirect('/dashboard/posts');
  }

  /**
   * Display the specified resource.
   */
  public function show(Post $post)
  {
    return view('dashboard.read', [
      'title' => 'Post',
      'post' => $post
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Post $post)
  {
    if (auth()->user()->id_user != $post->id_user) {
      return abort(403);
    }

    return view('dashboard.edit', [
      'title' => 'Edit',
      'post' => $post,
      'categories' => Category::all()
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Post $post)
  {
    if (auth()->user()->id_user != $post->id_user) {
      return abort(403);
    }


    $customError = [
      'title.unique' => 'There is already a post with this title on your blog.'
    ];

    $isProfileUpdated = false;
    $rules = [];

    if ($request->title != $post->title) {
      $rules['title'] = [
        'required',
        'min:3',
        'max:255',
        Rule::unique('posts')->where(fn ($query) => $query->where('title', request()->title)->where('id_user', auth()->user()->id_user))
      ];

      $isProfileUpdated = true;
    }

    if ($request->id_category != $post->id_category) {
      $rules['id_category'] = 'required';
      $isProfileUpdated = true;
    }

    if ($request->body != $post->body) {
      $rules['body'] = 'required';
      $isProfileUpdated = true;
    }

    if (!$isProfileUpdated) {
      return back()->with('error', 'No profile changes detected!');
    }

    $validatedData = $request->validate($rules, $customError);
    $validatedData['image'] = 'https://source.unsplash.com/1200x400';

    if (isset($validatedData['title'])) {
      $validatedData['slug'] = SlugService::createSlug(Post::class, 'slug', $validatedData['title']);
    }

    if (isset($validatedData['body'])) {
      $validatedData['excerpt'] = Str::limit(strip_tags($validatedData['body']), 150, '...');
    }

    Post::where('id_post', $post->id_post)->update($validatedData);
    return redirect('/dashboard/posts')->with('success', 'Your post has been updated successfully.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Post $post)
  {
    //
  }
}
