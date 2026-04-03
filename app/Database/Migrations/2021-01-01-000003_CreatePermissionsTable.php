<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Permissions Table Migration
 *
 * @property integer $id Primary key
 * @property string $name Permission name (unique)
 * @property string $slug URL-friendly slug (unique)
 * @property string $description Permission description
 * @property string $resource Resource this permission applies to
 * @property string $action Action: view, create, edit, delete, manage
 * @property boolean $is_system Whether this is a protected system permission
 * @property timestamps $created_at, $updated_at
 */
class CreatePermissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'unique' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'resource' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'e.g., users, roles, permissions, settings',
            ],
            'action' => [
                'type' => "ENUM('view', 'create', 'edit', 'delete', 'manage')",
                'default' => 'view',
            ],
            'is_system' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Protected system permission that cannot be deleted',
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

        $this->forge->addKey('id', true);
        // slug has unique constraint, already indexed
        $this->forge->addKey(['resource']);
        $this->forge->addKey(['is_system']);
        $this->forge->createTable('permissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('permissions', true);
    }
}
