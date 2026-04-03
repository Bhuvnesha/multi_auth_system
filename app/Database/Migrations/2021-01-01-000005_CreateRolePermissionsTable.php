<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Role Permissions Pivot Table Migration
 *
 * @property integer $role_id Role ID (FK to roles.id)
 * @property integer $permission_id Permission ID (FK to permissions.id)
 * @property timestamps $created_at, $updated_at
 *
 * Composite primary key: (role_id, permission_id)
 * Composite index: (permission_id, role_id)
 */
class CreateRolePermissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'permission_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey(['role_id', 'permission_id'], true);
        $this->forge->addKey(['permission_id', 'role_id']);
        $this->forge->addKey(['role_id']);
        $this->forge->addKey(['permission_id']);

        // Add foreign keys after referenced tables exist
        $this->forge->createTable('role_permissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('role_permissions', true);
    }
}
