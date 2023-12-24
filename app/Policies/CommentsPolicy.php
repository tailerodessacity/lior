<?php

namespace App\Policies;

use App\Enums\PermissionComments;
use App\Models\Comment;
use App\Models\User;

class CommentsPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function createComment(User $user): bool
    {
        return $user->hasPermissionTo(PermissionComments::ADD_COMMENT, 'api');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateComment(User $user): bool
    {
        return $user->hasPermissionTo(PermissionComments::UPDATE_COMMENT, 'api');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteComment(User $user, Comment $comments): bool
    {
        return $user->hasPermissionTo(PermissionComments::DELETE_COMMENT, 'api');
    }
}
