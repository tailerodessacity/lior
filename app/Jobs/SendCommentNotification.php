<?php

namespace App\Jobs;

use App\Models\Comment;
use App\Notifications\AddNewComment;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCommentNotification implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Comment $comment,
        private readonly AddNewComment $notification)
    {
    }

    public function handle()
    {
        $this->comment->notify($this->notification);
    }
}
