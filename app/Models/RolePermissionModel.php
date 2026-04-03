<?php namespace App\Models;

use CodeIgniter\Model;

/**
 * Role Permission Model
 *
 * Handles database operations for role_permissions pivot table
 */
class RolePermissionModel extends Model
{
    protected $table = 'role_permissions';
    protected $primaryKey = ['role_id', 'permission_id'];
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['role_id', 'permission_id', 'created_at', 'updated_at'];

    protected $skipValidation = false;

    /**
     * Get all permissions for a role
     *
     * @param integer $roleId
     * @return array
     */
    public function getPermissions(int $roleId): array
    {
        $db = \Config\Database::connect();

        return $db->table('role_permissions')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $roleId)
            ->select('permissions.*')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all roles for a permission
     *
     * @param integer $permissionId
     * @return array
     */
    public function getRoles(int $permissionId): array
    {
        $db = \Config\Database::connect();

        return $db->table('role_permissions')
            ->join('roles', 'roles.id = role_permissions.role_id')
            ->where('role_permissions.permission_id', $permissionId)
            ->select('roles.*')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if role has a permission
     *
     * @param integer $roleId
     * @param integer $permissionId
     * @return boolean
     */
    public function hasPermission(int $roleId, int $permissionId): bool
    {
        return $this->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->countAllResults() > 0;
    }

    /**
     * Assign permission to role
     *
     * @param integer $roleId
     * @param integer $permissionId
     * @return boolean
     */
    public function assignPermission(int $roleId, int $permissionId): bool
    {
        // Check if already exists
        if ($this->hasPermission($roleId, $permissionId)) {
            return true;
        }

        $data = [
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data) !== false;
    }
}
