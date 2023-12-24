<?php

namespace App\Services;

use App\Http\Responses\ApiResponse;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
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

    public function update(array $data, Post $post): Post
    {
        try {
            $this->authorize('updatePost', $post);
            Log::info("Start updating post with ID: {$post->getId()}");
            $post->updateOrFail($data);
            Log::info("Finish updating post with ID: {$post->getId()}");
            return new ApiResponse(['message' => 'Post updated successfully']);
        } catch (AuthorizationException $e) {
            Log::error("Error updating post with ID: {$post->getId()}: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error updating post with ID: {$post->getId()}: " . $e->getMessage());
            return new ApiResponse(['error' => 'Failed to update post. Please try again.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
        }
    }

    public function delete(Post $post): Post
    {
        try {
            $this->authorize('destroyPost', $post);
            Log::info("Start deleting post with ID: {$post->getId()}");
            $post->delete();
            Log::info("Finish deleting post with ID: {$post->getId()}");
            return new ApiResponse(['message' => 'Post deleted successfully']);
        } catch (AuthorizationException $e) {
            Log::error("Error deleting post with ID: {$post->getId()}: " . $e->getMessage());
            return new ApiResponse(['error' => 'Permission denied.'], JsonResponse::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error("Error deleting post with ID: {$post->getId()}: " . $e->getMessage());
            return new ApiResponse(['error' => 'Failed to delete post. Please try again.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
