<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\PermissionComments;
use App\Enums\PermissionPost;
use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('HAS123456')
        ]);

        $creator = User::factory()->create([
            'name' => 'Creator User',
            'email' => 'creator@example.com',
            'password' => bcrypt('HAS123456')
        ]);

        $guest = User::factory()->create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => bcrypt('HAS123456')
        ]);

        $adminRole = Role::create(['name' => Roles::ADMIN]);
        $creatorRole = Role::create(['name' => Roles::CREATOR]);
        $guestRole = Role::create(['name' => Roles::GUEST]);

        $permissions = array_merge(PermissionComments::cases(), PermissionPost::cases());

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole->syncPermissions($permissions);
        $admin->assignRole('admin');

        $creatorRole->syncPermissions(
            [
                PermissionComments::ADD_COMMENT,
                PermissionComments::EDIT_COMMENT,
                PermissionComments::UPDATE_COMMENT,
                PermissionComments::VIEW_COMMENT,
                PermissionPost::CREATE_POST,
                PermissionPost::EDIT_POST,
                PermissionPost::UPDATE_POST,
                PermissionPost::VIEW_POST,
            ]
        );
        $creator->assignRole('creator');

        $guestRole->syncPermissions(
            [
                PermissionComments::ADD_COMMENT,
                PermissionComments::EDIT_COMMENT,
                PermissionComments::UPDATE_COMMENT,
                PermissionComments::VIEW_COMMENT,
                PermissionPost::VIEW_POST,
            ]
        );
        $guest->assignRole('guest');

        $this->call([
            PostsSeeder::class,
            CommentsSeeder::class,
        ]);
    }
}
