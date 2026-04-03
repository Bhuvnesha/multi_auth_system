<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Users Table Migration
 *
 * @property integer $id Primary key
 * @property string $email User email address (unique, indexed)
 * @property string $username Username (unique, indexed)
 * @property string $password Hashed password
 * @property string $first_name User's first name
 * @property string $last_name User's last name
 * @property string $phone Phone number (optional)
 * @property enum $status Account status: active, inactive, suspended
 * @property string $email_verification_token Email verification token
 * @property datetime $email_verified_at Email verification timestamp
 * @property string $password_reset_token Password reset token
 * @property datetime $password_reset_expires Token expiry
 * @property timestamp $last_login Last login timestamp
 * @property string $last_login_ip IP of last login
 * @property integer $failed_login_attempts Count of failed attempts
 * @property datetime $locked_until Account lock expiry
 * @property timestamps $created_at, $updated_at
 * @property timestamp $deleted_at Soft delete timestamp
 */
class CreateUsersTable extends Migration
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
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'status' => [
                'type' => "ENUM('active', 'inactive', 'suspended')",
                'default' => 'inactive',
            ],
            'email_verification_token' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'email_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'password_reset_token' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'password_reset_expires' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_login_ip' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'failed_login_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        // Email and username have unique constraints (already indexed)
        // Only add additional indexes for non-unique fields
        $this->forge->addKey(['status']);
        $this->forge->addKey(['deleted_at']);

        // Foreign keys will be added after related tables are created
        $this->forge->createTable('users', true);
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
