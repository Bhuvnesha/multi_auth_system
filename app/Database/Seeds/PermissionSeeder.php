<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Permission Seeder
 *
 * Seeds all system permissions
 */
class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Authentication Permissions
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'description' => 'Can view dashboard', 'resource' => 'dashboard', 'action' => 'view', 'is_system' => 1],
            ['name' => 'View Profile', 'slug' => 'profile.view', 'description' => 'Can view own profile', 'resource' => 'profile', 'action' => 'view', 'is_system' => 1],
            ['name' => 'Edit Profile', 'slug' => 'profile.edit', 'description' => 'Can edit own profile', 'resource' => 'profile', 'action' => 'edit', 'is_system' => 1],

            // User Management Permissions
            ['name' => 'View Users', 'slug' => 'users.view', 'description' => 'Can view user list', 'resource' => 'users', 'action' => 'view', 'is_system' => 1],
            ['name' => 'Create Users', 'slug' => 'users.create', 'description' => 'Can create new users', 'resource' => 'users', 'action' => 'create', 'is_system' => 1],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'description' => 'Can edit users', 'resource' => 'users', 'action' => 'edit', 'is_system' => 1],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'description' => 'Can delete users', 'resource' => 'users', 'action' => 'delete', 'is_system' => 1],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'description' => 'Full user management access', 'resource' => 'users', 'action' => 'manage', 'is_system' => 1],

            // Role Management Permissions
            ['name' => 'View Roles', 'slug' => 'roles.view', 'description' => 'Can view roles', 'resource' => 'roles', 'action' => 'view', 'is_system' => 1],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'description' => 'Can create roles', 'resource' => 'roles', 'action' => 'create', 'is_system' => 1],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'description' => 'Can edit roles', 'resource' => 'roles', 'action' => 'edit', 'is_system' => 1],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'description' => 'Can delete roles', 'resource' => 'roles', 'action' => 'delete', 'is_system' => 1],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'description' => 'Full role management access', 'resource' => 'roles', 'action' => 'manage', 'is_system' => 1],

            // Permission Management Permissions
            ['name' => 'View Permissions', 'slug' => 'permissions.view', 'description' => 'Can view permissions', 'resource' => 'permissions', 'action' => 'view', 'is_system' => 1],
            ['name' => 'Create Permissions', 'slug' => 'permissions.create', 'description' => 'Can create permissions', 'resource' => 'permissions', 'action' => 'create', 'is_system' => 1],
            ['name' => 'Edit Permissions', 'slug' => 'permissions.edit', 'description' => 'Can edit permissions', 'resource' => 'permissions', 'action' => 'edit', 'is_system' => 1],
            ['name' => 'Delete Permissions', 'slug' => 'permissions.delete', 'description' => 'Can delete permissions', 'resource' => 'permissions', 'action' => 'delete', 'is_system' => 1],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'description' => 'Full permission management access', 'resource' => 'permissions', 'action' => 'manage', 'is_system' => 1],

            // System Settings Permissions
            ['name' => 'View Settings', 'slug' => 'settings.view', 'description' => 'Can view system settings', 'resource' => 'settings', 'action' => 'view', 'is_system' => 1],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'description' => 'Can edit system settings', 'resource' => 'settings', 'action' => 'edit', 'is_system' => 1],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'description' => 'Full settings management', 'resource' => 'settings', 'action' => 'manage', 'is_system' => 1],
        ];

        $this->db->table('permissions')->insertBatch($permissions);
    }
}
