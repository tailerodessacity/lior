<?php

namespace App\Models;

use Database\Factories\CommentsFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Comment extends Model
{
    use HasUuids, HasFactory, HasRoles,  Notifiable;

    protected $fillable = [
        'name',
        'email',
        'is_approved',
        'post_id',
        'text'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new CommentsFactory();
    }

    public function scopeIsApproved($query)
    {
        return $query->where('is_approved', true);
    }

}
