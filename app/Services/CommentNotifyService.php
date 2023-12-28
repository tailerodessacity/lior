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
    public function notify(Post $post, string $currentUserEmail)
    {
        $filterComments = $post->comments->unique('email')->filter(function (string $email) use ($currentUserEmail) {
            return $email != $currentUserEmail;
        });

        $jobs = [];

        foreach ($filterComments as $comment) {
            $jobs[] = new SendCommentNotification($comment, new AddNewComment());
        }

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) {
                Log::info('Comments notifier: completed successfully');
            })->finally(function (Batch $batch) {
                $countSuccessfullyJobsCompleted = $batch->totalJobs - $batch->failedJobs;
                Log::info(sprintf(
                    'Comments notifier: total failed jobs %s and successfully completed %s', $batch->failedJobs, $countSuccessfullyJobsCompleted
                ));
            })->name('Notification About New Comment')
            ->onQueue('notification_comments')
            ->dispatch();

        return $batch;

    }
}
