<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\PostsFactory;
use Database\Factories\CommentsFactory;

class PostsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

//    public function testIndex()
//    {
//        $posts = Post::factory(10)->create();
//
//        $response = $this->get('/api/posts');
//
//        $response->assertStatus(200)
//            ->assertJsonCount(10, 'data')
//            ->assertJsonStructure([
//                'data' => [
//                    '*' => [
//                        'id',
//                        'title',
//                        'slug',
//                        'preview',
//                        'detail',
//                    ],
//                ],
//            ]);
//    }
//
//    public function testShow()
//    {
//        $post = Post::factory()->create();
//
//        $user = User::factory()->create();
//        $token = $this->getTokenForUser($user);
//
//        $response = $this->withHeaders([
//            'Authorization' => 'Bearer ' . $token,
//        ])->get('/api/posts/' . $post->id);
//
//        $response->assertStatus(200)
//            ->assertJson([
//                'id' => $post->id,
//                'title' => $post->title,
//                'slug' => $post->slug,
//                'preview' => $post->preview,
//                'detail' => $post->detail,
//            ]);
//    }

    public function testStore()
    {
        $user = User::factory()->create();
        $token = $this->getTokenForUser($user);

        $postData = [
            'title' => 'Test Title',
            'slug' => 'test-title',
            'preview' => 'Test Preview',
            'detail' => 'Test Detail',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/posts', $postData);

        $response->assertStatus(200)
            ->assertJson([
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'preview' => $postData['preview'],
                'detail' => $postData['detail'],
            ]);

    }

    protected function getTokenForUser(User $user)
    {
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'HAS123456',
        ]);

        $token = $response->json('access_token');

        return $token;
    }
}

