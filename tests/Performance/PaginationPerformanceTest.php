<?php

namespace Test\Performance;

use App\Enums\PermissionComments;
use App\Enums\PermissionPost;
use App\Enums\Roles;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaginationPerformanceTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->createUserAndRoles();
        Post::factory(10000)->create();
    }

    public function testFirstPagePerformance()
    {
        $startTime = microtime(true);
        $response = $this->get('/api/posts?page=1');
        $endTime = microtime(true);

        $response->assertStatus(200);
        $executionTime = $endTime - $startTime;

        dump("Later Page Execution Time: {$executionTime} seconds");

        $startTime = microtime(true);
        $response = $this->get('/api/posts?page=1');
        $endTime = microtime(true);

        $response->assertStatus(200);
        $executionTime = $endTime - $startTime;

        dump("First Page Execution Time: {$executionTime} seconds");

        $startTime = microtime(true);
        $response = $this->get('/api/posts?page=900');
        $endTime = microtime(true);

        $response->assertStatus(200);
        $executionTime = $endTime - $startTime;

        dump("Later Page Execution Time: {$executionTime} seconds");
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
