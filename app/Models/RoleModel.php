<?php namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Role as RoleEntity;

/**
 * Role Model
 *
 * Handles database operations for roles table
 */
class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    protected $returnType = RoleEntity::class;
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[roles.name,id,{id}]',
        'slug' => 'required|min_length[2]|max_length[100]|is_unique[roles.slug,id,{id}]',
        'is_system' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Role name is required',
            'is_unique' => 'This role name already exists',
        ],
        'slug' => [
            'required' => 'Role slug is required',
            'is_unique' => 'This role slug already exists',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Get non-system roles
     *
     * @return array
     */
    public function getNonSystemRoles(): array
    {
        return $this->where('is_system', 0)->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Get all roles with user counts
     *
     * @return array
     */
    public function withUserCounts(): array
    {
        $db = \Config\Database::connect();

        $results = $db->table('roles')
            ->select('roles.*, COUNT(DISTINCT user_roles.user_id) as user_count')
            ->join('user_roles', 'user_roles.role_id = roles.id', 'left')
            ->groupBy('roles.id')
            ->orderBy('roles.name', 'ASC')
            ->get()
            ->getResultArray();

        // Convert arrays to Role entities
        $entities = [];
        foreach ($results as $result) {
            $entity = new \App\Entities\Role($result);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * Get role slugs as array
     *
     * @return array
     */
    public function getSlugs(): array
    {
        $roles = $this->select('slug')->findAll();
        return array_column($roles, 'slug');
    }

    /**
     * Get role by slug
     *
     * @param string $slug
     * @return RoleEntity|null
     */
    public function getBySlug(string $slug): ?RoleEntity
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Check if role is assigned to any users
     *
     * @param integer $roleId
     * @return boolean
     */
    public function hasUsers(int $roleId): bool
    {
        $db = \Config\Database::connect();

        return $db->table('user_roles')->where('role_id', $roleId)->countAllResults() > 0;
    }
}
