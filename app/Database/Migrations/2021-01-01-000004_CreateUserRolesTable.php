<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * User Roles Pivot Table Migration
 *
 * @property integer $user_id User ID (FK to users.id)
 * @property integer $role_id Role ID (FK to roles.id)
 * @property timestamps $created_at, $updated_at
 *
 * Composite primary key: (user_id, role_id)
 * Composite index: (role_id, user_id)
 */
class CreateUserRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'role_id' => [
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

        $this->forge->addKey(['user_id', 'role_id'], true);
        $this->forge->addKey(['role_id', 'user_id']);
        $this->forge->addKey(['user_id']);
        $this->forge->addKey(['role_id']);

        // Add foreign keys after referenced tables exist
        $this->forge->createTable('user_roles', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_roles', true);
    }
}
