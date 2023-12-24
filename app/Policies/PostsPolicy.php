<?php

namespace App\Policies;

use App\Enums\PermissionPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostsPolicy
{
    public function createPost(User $user)
    {
        return $user->hasPermissionTo(PermissionPost::CREATE_POST, 'api');
    }

    public function updatePost(User $user)
    {
        return $user->hasPermissionTo(PermissionPost::UPDATE_POST, 'api');
    }

    public function destroyPost(User $user)
    {
        return $user->hasPermissionTo(PermissionPost::DELETE_POST, 'api');
    }
}
