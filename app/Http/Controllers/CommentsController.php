<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentsRequest;
use App\Http\Requests\UpdateCommentsRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentNotifyService;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CommentsController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
        private readonly CommentNotifyService $commentNotifyService
    )
    {
    }

    /**
     * @param Post $post
     * @return JsonResponse
     */
    public function index(Post $post)
    {
        $approvedComments = $post->approvedComments();
        return new JsonResponse($approvedComments->toArray());
    }

    /**
     * @param StoreCommentsRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function store(StoreCommentsRequest $request, Post $post)
    {
        try {
            $comment = $this->commentService->create($request->input(), $post);
            $this->commentNotifyService->notify($post);
            $data = CommentResource::make($comment)->resolve();
            return new JsonResponse($data);
        } catch (\Exception $e) {
            Log::error("Error added post: " . $e->getMessage());
            return new JsonResponse(
                ['error' => 'Failed to add comment. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param UpdateCommentsRequest $request
     * @param Comment $comment
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UpdateCommentsRequest $request, Comment $comment)
    {
        try {
            $comment->updateOrFail($request->input());
            return new JsonResponse(['Updated post successfully']);
        } catch (\Exception $e) {
            Log::error("Error updating post: " . $e->getMessage());
            return new JsonResponse(
                ['error' => 'Failed to updating comment. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param Comment $comment
     * @return JsonResponse
     * @throws \Throwable
     */
    public function destroy(Comment $comment)
    {
        try {
           $comment->deleteOrFail();
            return new JsonResponse(['Deleted post successfully']);
        } catch (\Exception $e) {
            Log::error("Error deleting post: " . $e->getMessage());
            return new JsonResponse(
                ['error' => 'Failed to deleting comment. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
