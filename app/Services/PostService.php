<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostService
{
    public function create(array $data, int $userId): Post
    {
        Log::debug("Start adding post");

        $post = new Post();
        $post->title = $data['title'];
        $post->slug = Str::slug($post->title);
        $post->preview = $data['preview'];
        $post->detail = $data['detail'];
        $post->is_approved = false;
        $post->user_id = $userId;
        $post->save();

        Log::debug("Finish adding post");

        return $post;
    }
}
