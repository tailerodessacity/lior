<?php

namespace Tests\Feature;

use App\Enums\PermissionComments;
use App\Enums\PermissionPost;
use App\Enums\Roles;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Factories\CommentsFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetAllPostComments()
    {
        $this->createUserAndRoles();

        $post = Post::factory()->create();
        Comment::factory(10)->create(['post_id' => $post->id]);

        $response = $this->getJson('/api/posts/'. $post->id . '/comments');

        $response->assertStatus(200);

        $comments = json_decode($response->getContent(), true);
        $this->assertCount(10, $comments);

        (new TestResponse($response))->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'email',
                'text',
                'is_approved',
                'created_at',
                'updated_at'
            ],
        ]);
    }

    public function testStore()
    {
        $token = $this->getTokenForUser();

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

    protected function getTokenForUser()
    {
        $admin = $this->createUserAndRoles();

        $response = $this->post('/api/auth/login', [
            'email' => $admin->email,
            'password' => 'HAS123456',
        ]);

        $token = $response->json('access_token');

        return $token;
    }

    protected function createUserAndRoles()
    {
        $admin = User::factory()->create([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('HAS123456')
        ]);

        $adminRole = Role::create(['name' => Roles::ADMIN]);

        $permissions = array_merge(PermissionComments::cases(), PermissionPost::cases());

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole->syncPermissions($permissions);
        $admin->assignRole('admin');

        return $admin;
    }
}

