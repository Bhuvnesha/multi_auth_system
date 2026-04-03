<?php namespace App\Repositories;

use App\Entities\Role as RoleEntity;
use CodeIgniter\Model;

/**
 * Role Repository
 *
 * Handles all data access operations for roles
 */
class RoleRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = model('RoleModel');
    }

    /**
     * Find role by ID
     *
     * @param integer $id
     * @return RoleEntity|null
     */
    public function find(int $id): ?RoleEntity
    {
        return $this->model->find($id);
    }

    /**
     * Find role by slug
     *
     * @param string $slug
     * @return RoleEntity|null
     */
    public function findBySlug(string $slug): ?RoleEntity
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get all roles with permission counts
     *
     * @return array
     */
    public function getAll(): array
    {
        $db = \Config\Database::connect();

        $query = $db->table('roles')
            ->select('roles.*, COUNT(DISTINCT role_permissions.permission_id) as permission_count')
            ->select('COUNT(DISTINCT user_roles.user_id) as user_count')
            ->join('role_permissions', 'role_permissions.role_id = roles.id', 'left')
            ->join('user_roles', 'user_roles.role_id = roles.id', 'left')
            ->groupBy('roles.id')
            ->orderBy('roles.name', 'ASC')
            ->get()
            ->getResultArray();

        return $query;
    }

    /**
     * Get role with all permissions
     *
     * @param integer $roleId
     * @return RoleEntity|null
     */
    public function getWithPermissions(int $roleId): ?RoleEntity
    {
        $db = \Config\Database::connect();

        $role = $this->model->where('roles.id', $roleId)->first();

        if (!$role) {
            return null;
        }

        $permissions = $db->table('role_permissions')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $roleId)
            ->select('permissions.*')
            ->get()
            ->getResultArray();

        $role->permissions = $permissions;

        // Get user count
        $role->user_count = $db->table('user_roles')
            ->where('role_id', $roleId)
            ->countAllResults();

        return $role;
    }

    /**
     * Create a new role
     *
     * @param array $data
     * @return integer|false Role ID or false on failure
     */
    public function create(array $data)
    {
        if (!isset($data['slug'])) {
            $data['slug'] = url_title($data['name'], '-', true);
        }

        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $this->model->insert($data);

        return $this->model->getInsertID();
    }

    /**
     * Update role by ID
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function update(int $id, array $data): bool
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = url_title($data['name'], '-', true);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->model->where('id', $id)->set($data)->update();
    }

    /**
     * Delete role (check for system roles and assigned users first)
     *
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        // Check if system role
        $role = $this->find($id);
        if ($role && $role->isSystemRole()) {
            return false;
        }

        // Check if users assigned
        $db = \Config\Database::connect();
        $hasUsers = $db->table('user_roles')->where('role_id', $id)->countAllResults() > 0;

        if ($hasUsers) {
            return false;
        }

        // Remove role permissions first
        $db->table('role_permissions')->where('role_id', $id)->delete();

        return $this->model->where('id', $id)->delete();
    }

    /**
     * Get all system roles
     *
     * @return array
     */
    public function getSystemRoles(): array
    {
        return $this->model->where('is_system', 1)->findAll();
    }

    /**
     * Get all non-system roles
     *
     * @return array
     */
    public function getNonSystemRoles(): array
    {
        return $this->model->where('is_system', 0)->findAll();
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
        $db = \Config\Database::connect();

        // Check if already exists
        $exists = $db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->countAllResults() > 0;

        if ($exists) {
            return true;
        }

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
     * Sync role permissions (replace all permissions)
     *
     * @param integer $roleId
     * @param array $permissionIds
     * @return boolean
     */
    public function syncPermissions(int $roleId, array $permissionIds): bool
    {
        $db = \Config\Database::connect();

        // Remove existing permissions
        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        // Add new permissions
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
