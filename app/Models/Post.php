<?php

namespace App\Models;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
  use HasFactory;

  protected $guarded = ['id_post'];
  protected $primaryKey = 'id_post';
  protected $with = ['category', 'author'];

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function author()
  {
    return $this->belongsTo(User::class, 'id_user');
  }

  public function getRouteKeyName()
  {
    return 'slug';
  }

  public function scopeFilter($query, array $filters)
  {
    $query->when($filters['searchx'] ?? false, function ($query, $search) {
      return $query->where(function ($query) use ($search) {
        $query->where('title', 'like', '%' . $search . '%');
      });
    });

    $query->when($filters['search'] ?? false, function ($query, $search) {
      return $query->where(function ($query) use ($search) {
        $query->where('title', 'like', '%' . $search . '%')
          ->orWhere('body', 'like', '%' . $search . '%');
      });
    });

    $query->when($filters['category'] ?? false, function ($query, $category) {
      return $query->whereHas('category', function ($query) use ($category) {
        $query->where('slug', $category);
      });
    });

    $query->when(
      $filters['author'] ?? false,
      fn ($query, $author) =>
      $query->whereHas(
        'author',
        fn ($query) =>
        $query->where('username', $author)
      )
    );
  }
}