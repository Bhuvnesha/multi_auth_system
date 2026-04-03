<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * Permission Entity
 *
 * Represents a permission in the RBAC system
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $resource Resource name
 * @property string $action Action type
 * @property integer $is_system
 * @property datetime $created_at
 * @property datetime $updated_at
 * @property array $roles Array of roles that have this permission
 */
class Permission extends Entity
{
    protected $immutable = ['id'];

    protected $casts = [
        'id'        => 'integer',
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get full access level (resource.action)
     *
     * @return string
     */
    public function getFullAccess(): string
    {
        return $this->resource . '.' . $this->action;
    }

    /**
     * Check if this is a system permission (protected)
     *
     * @return boolean
     */
    public function isSystemPermission(): bool
    {
        return (bool)$this->is_system;
    }

    /**
     * Check if permission can be deleted
     *
     * @return boolean
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }

    /**
     * Get count of roles with this permission
     *
     * @return integer
     */
    public function getRoleCount(): int
    {
        return isset($this->roles) ? count($this->roles) : 0;
    }
}
