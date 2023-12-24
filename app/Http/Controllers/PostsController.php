<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostsRequest;
use App\Http\Requests\UpdatePostsRequest;
use App\Http\Resources\PostResource;
use App\Http\Responses\ApiResponse;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PostsController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    )
    {
    }

    public function index()
    {
        $posts = Post::paginate(10);
        return new ApiResponse($posts);
    }

    public function store(StorePostsRequest $request)
    {
        try {
            $post = $this->postService->create($request->input(), auth()->user()->getAuthIdentifier());
            $data = PostResource::make($post)->resolve();
            return new ApiResponse($data);
        } catch (AuthorizationException $e) {
            Log::error("Error adding post: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error added post: " . $e->getMessage());
            return new ApiResponse(['error' => 'Failed to add post. Please try again.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Post $post)
    {
        $data = PostResource::make($post)->resolve();
        return new ApiResponse($data);
    }

    public function update(UpdatePostsRequest $request, Post $post)
    {
        try {
            $post->updateOrFail($request->input());
            return new ApiResponse(['Updated post successfully']);
        } catch (AuthorizationException $e) {
            Log::error("Error updating post: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error updating post: " . $e->getMessage());
            return new ApiResponse(['error' => 'Failed to updating post. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(Post $post)
    {
        $this->authorize('deletePost', $post);

        try {
            $post->deleteOrFail();
            return new ApiResponse(['Deleted post successfully']);
        } catch (AuthorizationException $e) {
            Log::error("Error deleting post: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error deleting post: " . $e->getMessage());
            return new ApiResponse(['error' => 'Failed to deleting post. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
