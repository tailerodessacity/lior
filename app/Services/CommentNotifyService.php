<?php

namespace App\Services;

use App\Models\Post;
use App\Notifications\AddNewComment;

class CommentNotifyService
{
    public function notify(Post $post)
    {
        $currentUserEmail = auth()->user()->getEmail();

        $filterComments = $post->comments->unique('email')->filter(function(string $email) use ($currentUserEmail){
            return $email != $currentUserEmail;
        });

        foreach ($filterComments as $comment) {
            $comment->notify(new AddNewComment());
        }
    }
}
