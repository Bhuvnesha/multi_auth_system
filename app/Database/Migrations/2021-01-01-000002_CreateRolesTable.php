<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Roles Table Migration
 *
 * @property integer $id Primary key
 * @property string $name Role name (unique e.g., admin, manager, user)
 * @property string $slug URL-friendly slug (unique)
 * @property string $description Role description
 * @property boolean $is_system Whether this is a protected system role
 * @property timestamps $created_at, $updated_at
 */
class CreateRolesTable extends Migration
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
                'constraint' => 100,
                'unique' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_system' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Protected system role that cannot be deleted',
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
        $this->forge->addKey(['is_system']);
        $this->forge->createTable('roles', true);
    }

    public function down()
    {
        $this->forge->dropTable('roles', true);
    }
}
