<?php

namespace App\Services;

use App\Jobs\SendCommentNotification;
use App\Models\Post;
use App\Notifications\AddNewComment;
use App\Repositories\CommentRepository;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class CommentNotifyService
{
    public function __construct(private CommentRepository $commentRepository)
    {
    }

    public function notify(Post $post, string $currentUserEmail)
    {
        $jobs = [];

        foreach ($this->commentRepository->filterUniqueEmail($post, $currentUserEmail) as $comment) {
            $jobs[] = new SendCommentNotification($comment, new AddNewComment());
        }

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) {
                Log::info('Comments notifier: completed successfully');
            })->finally(function (Batch $batch) {
                $countSuccessfullyJobsCompleted = $batch->totalJobs - $batch->failedJobs;
                Log::info(sprintf(
                    'Comments notifier: total failed jobs %s and successfully completed %s',
                    $batch->failedJobs,
                    $countSuccessfullyJobsCompleted
                ));
            })->name('Notification About New Comment')
            ->onQueue('notification_comments')
            ->dispatch();

        return $batch;

    }
}
