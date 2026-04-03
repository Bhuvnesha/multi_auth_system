<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Role Seeder
 *
 * Seeds initial roles in the system
 */
class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'is_system' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Administrator with user management access',
                'is_system' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager with elevated access',
                'is_system' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Standard user with basic access',
                'is_system' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
