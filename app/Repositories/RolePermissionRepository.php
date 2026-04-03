<?php namespace App\Repositories;

use CodeIgniter\Model;

/**
 * Role Permission Repository
 *
 * Handles operations for role-permission relationships
 */
class RolePermissionRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = model('RolePermissionModel');
    }

    /**
     * Get all permissions for a role
     *
     * @param integer $roleId
     * @return array
     */
    public function getPermissionsByRole(int $roleId): array
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
    public function getRolesByPermission(int $permissionId): array
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
     * Check if role has a specific permission
     *
     * @param integer $roleId
     * @param integer $permissionId
     * @return boolean
     */
    public function roleHasPermission(int $roleId, int $permissionId): bool
    {
        $db = \Config\Database::connect();

        $count = $db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Check if role has permission by slug
     *
     * @param integer $roleId
     * @param string $permissionSlug
     * @return boolean
     */
    public function roleHasPermissionSlug(int $roleId, string $permissionSlug): bool
    {
        $db = \Config\Database::connect();

        $count = $db->table('role_permissions')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $roleId)
            ->where('permissions.slug', $permissionSlug)
            ->countAllResults();

        return $count > 0;
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
        if ($this->roleHasPermission($roleId, $permissionId)) {
            return true;
        }

        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        return $db->table('role_permissions')->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Remove permission from role
     *
     * @param integer $roleId
     * @param integer $permissionId
     * @return boolean
     */
    public function removePermission(int $roleId, int $permissionId): bool
    {
        $db = \Config\Database::connect();

        return $db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->delete();
    }

    /**
     * Sync role permissions (replace all)
     *
     * @param integer $roleId
     * @param array $permissionIds
     * @return boolean
     */
    public function syncPermissions(int $roleId, array $permissionIds): bool
    {
        $db = \Config\Database::connect();

        // Remove existing
        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        // Add new
        $now = date('Y-m-d H:i:s');
        foreach ($permissionIds as $permissionId) {
            $db->table('role_permissions')->insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return true;
    }

    /**
     * Get base model instance
     *
     * @return \CodeIgniter\Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
