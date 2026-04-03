<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Database Seeder
 *
 * This class is the main entry point for seeding the database.
 * It calls other seeders to populate the database with initial data.
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('PermissionSeeder');
        $this->call('AdminUserSeeder');
        $this->call('RolePermissionSeeder');
    }
}
