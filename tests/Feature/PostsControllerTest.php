<?php

namespace Tests\Feature;

use App\Enums\PermissionComments;
use App\Enums\PermissionPost;
use App\Enums\Roles;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIndex()
    {
        $this->createUserAndRoles();

        Post::factory(10)->create();

        $response = $this->get('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'preview',
                        'detail',
                    ],
                ],
            ]);
    }

    public function testShow()
    {
        $token = $this->getTokenForUser();

        $post = Post::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/posts/' . $post->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'preview' => $post->preview,
                'detail' => $post->detail,
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

