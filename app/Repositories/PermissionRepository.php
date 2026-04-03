<?php namespace App\Repositories;

use App\Entities\Permission as PermissionEntity;
use CodeIgniter\Model;

/**
 * Permission Repository
 *
 * Handles all data access operations for permissions
 */
class PermissionRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = model('PermissionModel');
    }

    /**
     * Find permission by ID
     *
     * @param integer $id
     * @return PermissionEntity|null
     */
    public function find(int $id): ?PermissionEntity
    {
        return $this->model->find($id);
    }

    /**
     * Find permission by slug
     *
     * @param string $slug
     * @return PermissionEntity|null
     */
    public function findBySlug(string $slug): ?PermissionEntity
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get all permissions with role counts
     *
     * @param string $resource Filter by resource
     * @return array
     */
    public function getAll(string $resource = null): array
    {
        $db = \Config\Database::connect();

        $query = $db->table('permissions')
            ->select('permissions.*, COUNT(DISTINCT role_permissions.role_id) as role_count')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id', 'left')
            ->groupBy('permissions.id')
            ->orderBy('permissions.resource', 'ASC')
            ->orderBy('permissions.action', 'ASC');

        if ($resource !== null) {
            $query->where('permissions.resource', $resource);
        }

        return $query->get()->getResultArray();
    }

    /**
     * Get permission with all roles that have it
     *
     * @param integer $permissionId
     * @return PermissionEntity|null
     */
    public function getWithRoles(int $permissionId): ?PermissionEntity
    {
        $db = \Config\Database::connect();

        $permission = $this->model->find($permissionId);

        if (!$permission) {
            return null;
        }

        $roles = $db->table('role_permissions')
            ->join('roles', 'roles.id = role_permissions.role_id')
            ->where('role_permissions.permission_id', $permissionId)
            ->select('roles.*')
            ->get()
            ->getResultArray();

        $permission->roles = $roles;
        $permission->role_count = count($roles);

        return $permission;
    }

    /**
     * Get permissions by resource
     *
     * @param string $resource
     * @return array
     */
    public function getByResource(string $resource): array
    {
        return $this->model
            ->where('resource', $resource)
            ->orderBy('action', 'ASC')
            ->findAll();
    }

    /**
     * Get all permissions as key-value pairs (slug => name)
     *
     * @return array
     */
    public function getAllAsMap(): array
    {
        $permissions = $this->model->orderBy('resource', 'ASC')->orderBy('action', 'ASC')->findAll();

        $map = [];
        foreach ($permissions as $permission) {
            $map[$permission['slug']] = $permission['name'];
        }

        return $map;
    }

    /**
     * Create a new permission
     *
     * @param array $data
     * @return integer|false Permission ID or false on failure
     */
    public function create(array $data)
    {
        if (!isset($data['slug'])) {
            $data['slug'] = strtolower($data['resource'] . '.' . $data['action']);
            $data['slug'] = preg_replace('/[^a-z0-9\.]/', '', $data['slug']);
        }

        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $this->model->insert($data);

        return $this->model->getInsertID();
    }

    /**
     * Update permission by ID
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function update(int $id, array $data): bool
    {
        if (isset($data['resource']) && isset($data['action']) && !isset($data['slug'])) {
            $data['slug'] = strtolower($data['resource'] . '.' . $data['action']);
            $data['slug'] = preg_replace('/[^a-z0-9\.]/', '', $data['slug']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->model->where('id', $id)->set($data)->update();
    }

    /**
     * Delete permission
     *
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        // First remove from role_permissions
        $db = \Config\Database::connect();
        $db->table('role_permissions')->where('permission_id', $id)->delete();

        return $this->model->where('id', $id)->delete();
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
