<?php

namespace App\Services;

use App\Jobs\SendCommentNotification;
use App\Models\Post;
use App\Notifications\AddNewComment;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class CommentNotifyService
{
    public function notify(Post $post)
    {
        $currentUserEmail = auth()->user()->getEmail();

        $filterComments = $post->comments->unique('email')->filter(function (string $email) use ($currentUserEmail) {
            return $email != $currentUserEmail;
        });

        $batch = Bus::batch([])->then(function (Batch $batch) {
            Log::info('jobs completed successfully');
        })->catch(function (Batch $batch, Throwable $e) {
            
        })->finally(function (Batch $batch) {
            Log::info('batch has finished executing');
        })->name('Add New Comment Notification')
            ->onQueue('new_comment_notification_email')
            ->allowFailures(false)
            ->dispatch();

        foreach ($filterComments as $comment) {
            $batch->add(new SendCommentNotification($comment, new AddNewComment()));
        }
    }
}
