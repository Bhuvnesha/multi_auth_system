<?php namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Permission as PermissionEntity;

/**
 * Permission Model
 *
 * Handles database operations for permissions table
 */
class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'slug',
        'description',
        'resource',
        'action',
        'is_system',
    ];

    protected $returnType = PermissionEntity::class;
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[permissions.name,id,{id}]',
        'slug' => 'required|min_length[2]|max_length[150]|is_unique[permissions.slug,id,{id}]',
        'resource' => 'required|min_length[2]|max_length[100]',
        'action' => 'required|in_list[view,create,edit,delete,manage]',
        'is_system' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Permission name is required',
            'is_unique' => 'This permission name already exists',
        ],
        'slug' => [
            'required' => 'Permission slug is required',
            'is_unique' => 'This permission slug already exists',
        ],
        'resource' => [
            'required' => 'Resource is required',
        ],
        'action' => [
            'required' => 'Action is required',
            'in_list' => 'Action must be one of: view, create, edit, delete, manage',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Get permissions by resource
     *
     * @param string $resource
     * @return array
     */
    public function getByResource(string $resource): array
    {
        return $this->where('resource', $resource)
            ->orderBy('action', 'ASC')
            ->findAll();
    }

    /**
     * Get all permissions grouped by resource
     *
     * @return array
     */
    public function getGroupedByResource(): array
    {
        $permissions = $this->orderBy('resource', 'ASC')
            ->orderBy('action', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($permissions as $permission) {
            $resource = $permission->resource;
            if (!isset($grouped[$resource])) {
                $grouped[$resource] = [];
            }
            $grouped[$resource][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get permission slugs
     *
     * @return array
     */
    public function getSlugs(): array
    {
        $permissions = $this->select('slug')->findAll();
        return array_column($permissions, 'slug');
    }

    /**
     * Get permission by slug
     *
     * @param string $slug
     * @return PermissionEntity|null
     */
    public function getBySlug(string $slug): ?PermissionEntity
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get all permissions with role counts
     *
     * @return array
     */
    public function withRoleCounts(): array
    {
        $db = \Config\Database::connect();

        $results = $db->table('permissions')
            ->select('permissions.*, COUNT(DISTINCT role_permissions.role_id) as role_count')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id', 'left')
            ->groupBy('permissions.id')
            ->orderBy('permissions.resource', 'ASC')
            ->orderBy('permissions.action', 'ASC')
            ->get()
            ->getResultArray();

        // Convert arrays to Permission entities
        $entities = [];
        foreach ($results as $result) {
            $entity = new \App\Entities\Permission($result);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * Check if permission is assigned to any roles
     *
     * @param integer $permissionId
     * @return boolean
     */
    public function hasRoles(int $permissionId): bool
    {
        $db = \Config\Database::connect();

        return $db->table('role_permissions')->where('permission_id', $permissionId)->countAllResults() > 0;
    }
}
