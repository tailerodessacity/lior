<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class CommentService
{
    public function create(array $data, Post $post): Comment
    {
        Log::debug("Start adding comment");

        $comment = new Comment();
        $comment->name = $data['name'];
        $comment->email = $data['email'];
        $comment->text = $data['text'];
        $comment->post_id = $post->id;
        $comment->is_approved = false;
        $comment->save();

        Log::debug("Finish adding comment");

        return $comment;
    }
}
