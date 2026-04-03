<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Admin User Seeder
 *
 * Creates default admin user
 */
class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Get Super Admin role ID
        $role = $this->db->table('roles')
            ->where('slug', 'super-admin')
            ->get()
            ->getRowArray();

        if (!$role) {
            throw new \Exception('Super Admin role not found. Please run RoleSeeder first.');
        }

        $data = [
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => password_hash('Admin@123', PASSWORD_BCRYPT),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'phone' => '+1234567890',
            'status' => 'active',
            'email_verified_at' => $now,
            'last_login' => $now,
            'last_login_ip' => '127.0.0.1',
            'failed_login_attempts' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $userId = $this->db->table('users')->insert($data);

        // Assign Super Admin role to admin user
        $this->db->table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $role['id'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
