<?php

namespace App\Repositories;

use App\Models\Post;

class CommentRepository
{
    public function filterUniqueEmail(Post $post, string $currentUserEmail)
    {
        return $post->comments->unique('email')
            ->filter(function (string $email) use ($currentUserEmail) {
                return $email != $currentUserEmail;
            });
    }
}
