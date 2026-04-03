<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Role Permission Seeder
 *
 * Assigns permissions to roles
 */
class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Get all roles
        $roles = $this->db->table('roles')->get()->getResultArray();

        // Get all permissions
        $allPermissions = $this->db->table('permissions')->get()->getResultArray();

        foreach ($roles as $role) {
            $rolePermissions = [];

            if ($role['slug'] === 'super-admin') {
                // Super Admin gets all permissions
                foreach ($allPermissions as $permission) {
                    $rolePermissions[] = [
                        'role_id' => $role['id'],
                        'permission_id' => $permission['id'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            } elseif ($role['slug'] === 'administrator') {
                // Administrator gets user management & dashboard permissions
                foreach ($allPermissions as $permission) {
                    if (in_array($permission['resource'], ['dashboard', 'profile', 'users', 'roles', 'settings'])) {
                        $rolePermissions[] = [
                            'role_id' => $role['id'],
                            'permission_id' => $permission['id'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            } elseif ($role['slug'] === 'manager') {
                // Manager gets limited permissions
                foreach ($allPermissions as $permission) {
                    if (in_array($permission['resource'], ['dashboard', 'profile', 'users']) && $permission['action'] !== 'delete') {
                        $rolePermissions[] = [
                            'role_id' => $role['id'],
                            'permission_id' => $permission['id'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            } elseif ($role['slug'] === 'user') {
                // User gets only basic permissions
                foreach ($allPermissions as $permission) {
                    if (in_array($permission['slug'], ['dashboard.view', 'profile.view', 'profile.edit'])) {
                        $rolePermissions[] = [
                            'role_id' => $role['id'],
                            'permission_id' => $permission['id'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            if (!empty($rolePermissions)) {
                $this->db->table('role_permissions')->insertBatch($rolePermissions);
            }
        }
    }
}
