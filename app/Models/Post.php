<?php

namespace App\Models;

use Database\Factories\PostsFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class Post extends Model
{
    use HasUuids, HasFactory, HasRoles;

    protected $fillable = [
        'title',
        'slug',
        'is_approved',
        'preview',
        'detail',
        'image',
        'user_id',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeBlocked($query)
    {
        return $query->where('is_approved', false);
    }

    public function approvedComments()
    {
        return $this->comments()
            ->isApproved()
            ->get();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new PostsFactory();
    }

    public function quiqPaginate(string $page, int $perPage)
    {
        $offset = ($page - 1) * $perPage;

        $posts = $this->from(DB::raw('(SELECT id, title, preview, detail, ROW_NUMBER() OVER (ORDER BY created_at) AS num FROM posts) as OrderedRows'))
            ->whereBetween('num', [$offset + 1, $offset + $perPage])
            ->take($perPage)
            ->paginate();

        return $posts;
    }

}
