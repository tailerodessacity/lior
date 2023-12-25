<?php

namespace Tests\Feature;

use App\Enums\PermissionComments;
use App\Enums\PermissionPost;
use App\Enums\Roles;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
//        $this->runDatabaseMigrations();
    }

    public function testGetAllPosts()
    {
        $this->createGuestUser();

        Post::factory(10)->create(['user_id' => 3]);

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

    public function testShowPost()
    {
        $role = Roles::ADMIN->value;
        $admin = $this->createUserAndRoles($role);
        $token = $this->getJwtToken($admin);

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

    public function testCreatePost()
    {
        $role = Roles::ADMIN->value;
        $admin = $this->createUserAndRoles($role);
        $token = $this->getJwtToken($admin);

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

    public function testUpdatePost()
    {
        $role = Roles::ADMIN->value;
        $admin = $this->createUserAndRoles($role);

        $token = $this->getJwtToken($admin);

        $post = Post::factory()->create();

        $postData = [
            'title' => 'Test Title updated',
            'slug' => 'test-title',
            'preview' => 'Test Preview updated',
            'detail' => 'Test Detail',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patch('/api/posts/' . $post->id, $postData);

        $response->assertStatus(200)
            ->assertJson([
                'Updated post successfully',
            ]);

        $response->assertJsonCount(1);

    }

    protected function getJwtToken($user)
    {
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'HAS123456',
        ]);

        $token = $response->json('access_token');

        return $token;
    }

    public function createUserAndRoles(string $role): User
    {
        return match ($role) {
            Roles::GUEST->value => $this->createGuestUser(),
            Roles::CREATOR->value => $this->createCreatorUser(),
            default => $this->createAdminUser(),
        };
    }

    private function createGuestUser(): User
    {
        $guest = User::factory()->create([
            'id' => 3,
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => Hash::make('HAS123456')
        ]);

        $guestRole = Role::create(['name' => Roles::GUEST]);

        $permissions = [
            PermissionComments::ADD_COMMENT,
            PermissionComments::EDIT_COMMENT,
            PermissionComments::UPDATE_COMMENT,
            PermissionComments::VIEW_COMMENT,
            PermissionPost::VIEW_POST,
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $guestRole->syncPermissions($permissions);
        $guest->assignRole('guest');

        return $guest;
    }

    private function createCreatorUser(): User
    {
        $creator = User::factory()->create([
            'id' => 2,
            'name' => 'Test User',
            'email' => 'creator@example.com',
            'password' => Hash::make('HAS123456')
        ]);

        $creatorRole = Role::create(['name' => Roles::CREATOR]);

        $permissions = [
            PermissionComments::ADD_COMMENT,
            PermissionComments::EDIT_COMMENT,
            PermissionComments::UPDATE_COMMENT,
            PermissionComments::VIEW_COMMENT,
            PermissionPost::CREATE_POST,
            PermissionPost::EDIT_POST,
            PermissionPost::UPDATE_POST,
            PermissionPost::VIEW_POST,
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $creatorRole->syncPermissions($permissions);
        $creator->assignRole('creator');

        return $creator;
    }

    private function createAdminUser(): User
    {
        $admin = User::factory()->create([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('HAS123456')
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

