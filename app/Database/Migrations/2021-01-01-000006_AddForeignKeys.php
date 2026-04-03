<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add Foreign Keys Migration
 *
 * This migration adds foreign key constraints to ensure referential integrity
 */
class AddForeignKeys extends Migration
{
    public function up()
    {
        // Add foreign key to user_roles table
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE', 'fk_user_roles_users');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE', 'fk_user_roles_roles');

        // Add foreign key to role_permissions table
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE', 'fk_role_permissions_roles');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE', 'fk_role_permissions_permissions');

        // For SQLite, we need to rebuild tables to add foreign keys
        if ($this->db->DBDriver === 'SQLite3') {
            $this->forge->rebuildTable('user_roles');
            $this->forge->rebuildTable('role_permissions');
        }
    }

    public function down()
    {
        // Drop foreign keys
        $this->db->query('ALTER TABLE `user_roles` DROP FOREIGN KEY `fk_user_roles_users`');
        $this->db->query('ALTER TABLE `user_roles` DROP FOREIGN KEY `fk_user_roles_roles`');
        $this->db->query('ALTER TABLE `role_permissions` DROP FOREIGN KEY `fk_role_permissions_roles`');
        $this->db->query('ALTER TABLE `role_permissions` DROP FOREIGN KEY `fk_role_permissions_permissions`');
    }
}
