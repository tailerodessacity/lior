<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentsRequest;
use App\Http\Requests\UpdateCommentsRequest;
use App\Http\Resources\CommentResource;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use App\Models\Post;
use App\Notifications\AddNewComment;
use App\Services\CommentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CommentsController extends Controller
{
    public function __construct(private readonly CommentService $commentService)
    {
    }

    public function index(Post $post)
    {
        $approvedComments = $post->newQueryWithoutScope('approved')->get();
        return new ApiResponse($approvedComments->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentsRequest $request, Post $post)
    {
        try {
            $comment = $this->commentService->create($request->input(), $post);

            // get all users emails from this post
            auth()->user()->notify((new AddNewComment()));

            $data = CommentResource::make($comment)->resolve();
            return new ApiResponse($data);
        } catch (AuthorizationException $e) {
            Log::error("Error adding comment: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error added post: " . $e->getMessage());
            return new ApiResponse(
                ['error' => 'Failed to add comment. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentsRequest $request, Comment $comment)
    {
        try {
            $comment = $this->commentService->update($request->input(), $comment);
            return new ApiResponse(['Comment was updated']);
        } catch (AuthorizationException $e) {
            Log::error("Error updating comment: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error updating post: " . $e->getMessage());
            return new ApiResponse(
                ['error' => 'Failed to updating comment. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        try {
            $comment = $this->commentService->delete($comment);
            return new ApiResponse(['Comment was deleted']);
        } catch (AuthorizationException $e) {
            Log::error("Error deleting comment: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error deleting post: " . $e->getMessage());
            return new ApiResponse(
                ['error' => 'Failed to deleting comment. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
