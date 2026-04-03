<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * Role Entity
 *
 * Represents a role in the RBAC system
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property integer $is_system
 * @property datetime $created_at
 * @property datetime $updated_at
 * @property array $permissions Array of permission data
 * @property integer $user_count Count of users with this role
 */
class Role extends Entity
{
    protected $immutable = ['id'];

    protected $casts = [
        'id'        => 'integer',
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if role is a system role (protected)
     *
     * @return boolean
     */
    public function isSystemRole(): bool
    {
        return (bool)$this->is_system;
    }

    /**
     * Check if role can be deleted
     *
     * @return boolean
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system && !$this->user_count;
    }

    /**
     * Get all permission slugs for this role
     *
     * @return array
     */
    public function getPermissionSlugs(): array
    {
        if (!isset($this->permissions) || !is_array($this->permissions)) {
            return [];
        }

        return array_column($this->permissions, 'slug');
    }

    /**
     * Check if role has a specific permission
     *
     * @param string $permissionSlug Permission slug
     * @return boolean
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return in_array($permissionSlug, $this->getPermissionSlugs());
    }

    /**
     * Check if role has permission for a resource/action
     *
     * @param string $resource Resource name
     * @param string $action Action (view, create, edit, delete, manage)
     * @return boolean
     */
    public function hasResourceAccess(string $resource, string $action): bool
    {
        if (!isset($this->permissions) || !is_array($this->permissions)) {
            return false;
        }

        foreach ($this->permissions as $permission) {
            if ($permission['resource'] === $resource && $permission['action'] === $action) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all resources this role has access to
     *
     * @return array
     */
    public function getAccessibleResources(): array
    {
        if (!isset($this->permissions) || !is_array($this->permissions)) {
            return [];
        }

        $resources = [];
        foreach ($this->permissions as $permission) {
            if (!in_array($permission['resource'], $resources)) {
                $resources[] = $permission['resource'];
            }
        }

        return $resources;
    }
}
