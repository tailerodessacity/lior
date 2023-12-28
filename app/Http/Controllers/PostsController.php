<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePostsRequest;
use App\Http\Requests\UpdatePostsRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Pagination\CustomPaginator;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PostsController extends Controller
{
    private const PER_PAGE = 10;

    public function __construct(
        private readonly PostService $postService
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $currentPage = request('page', 1);

        $posts = CustomPaginator::create(Post::class, self::PER_PAGE, $currentPage);

        return new JsonResponse($posts);

    }

    /**
     * @param StorePostsRequest $request
     * @return JsonResponse
     */
    public function store(StorePostsRequest $request)
    {
        try {
            $post = $this->postService->create($request->input(), auth()->user()->getAuthIdentifier());
            $data = PostResource::make($post)->resolve();
            return new JsonResponse($data);
        } catch (\Exception $e) {
            Log::error("Error added post: " . $e->getMessage());
            return new JsonResponse(['error' => 'Failed to add post. Please try again.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post)
    {
        $data = PostResource::make($post)->resolve();
        return new JsonResponse($data);
    }

    /**
     * @param UpdatePostsRequest $request
     * @param Post $post
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UpdatePostsRequest $request, Post $post)
    {
        try {
            $post->updateOrFail($request->input());
            return new JsonResponse(['Updated post successfully']);
        } catch (\Exception $e) {
            Log::error("Error updating post: " . $e->getMessage());
            return new JsonResponse(['error' => 'Failed to updating post. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param Post $post
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function destroy(Post $post)
    {
        $this->authorize('deletePost', $post);

        try {
            $post->deleteOrFail();
            return new JsonResponse(['Deleted post successfully']);
        } catch (\Exception $e) {
            Log::error("Error deleting post: " . $e->getMessage());
            return new JsonResponse(['error' => 'Failed to deleting post. Please try again.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
