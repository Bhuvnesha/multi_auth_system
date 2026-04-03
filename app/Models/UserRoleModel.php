<?php namespace App\Models;

use CodeIgniter\Model;

/**
 * User Role Model
 *
 * Handles database operations for user_roles pivot table
 */
class UserRoleModel extends Model
{
    protected $table = 'user_roles';
    protected $primaryKey = ['user_id', 'role_id'];
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['user_id', 'role_id', 'created_at', 'updated_at'];

    protected $skipValidation = false;

    /**
     * Get all roles for a user
     *
     * @param integer $userId
     * @return array
     */
    public function getRoles(int $userId): array
    {
        $db = \Config\Database::connect();

        return $db->table('user_roles')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->select('roles.*')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all users for a role
     *
     * @param integer $roleId
     * @return array
     */
    public function getUsers(int $roleId): array
    {
        $db = \Config\Database::connect();

        return $db->table('user_roles')
            ->join('users', 'users.id = user_roles.user_id')
            ->where('user_roles.role_id', $roleId)
            ->where('users.deleted_at IS NULL')
            ->select('users.id, users.email, users.username, users.first_name, users.last_name')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if user has a role
     *
     * @param integer $userId
     * @param integer $roleId
     * @return boolean
     */
    public function hasRole(int $userId, int $roleId): bool
    {
        return $this->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->countAllResults() > 0;
    }

    /**
     * Assign role to user
     *
     * @param integer $userId
     * @param integer $roleId
     * @return boolean
     */
    public function assignRole(int $userId, int $roleId): bool
    {
        // Check if already exists
        if ($this->hasRole($userId, $roleId)) {
            return true;
        }

        $data = [
            'user_id' => $userId,
            'role_id' => $roleId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data) !== false;
    }
}
