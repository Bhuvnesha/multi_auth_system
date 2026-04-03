<?php namespace App\Repositories;

use CodeIgniter\Model;

/**
 * User Role Repository
 *
 * Handles operations for user-role relationships
 */
class UserRoleRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = model('UserRoleModel');
    }

    /**
     * Get all roles for a user
     *
     * @param integer $userId
     * @return array
     */

    public function getRolesByUser(int $userId): array
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
     * Alias for getRolesByUser() - used by RBACService
     *
     * @param integer $userId
     * @return array
     */
    public function getRoles(int $userId): array
    {
        return $this->getRolesByUser($userId);
    }

    /**
     * Get all users for a role
     *
     * @param integer $roleId
     * @param boolean $activeOnly Only get active users
     * @return array
     */
    public function getUsersByRole(int $roleId, bool $activeOnly = true): array
    {
        $db = \Config\Database::connect();

        $query = $db->table('user_roles')
            ->join('users', 'users.id = user_roles.user_id')
            ->where('user_roles.role_id', $roleId)
            ->where('users.deleted_at IS NULL')
            ->select('users.id, users.email, users.username, users.first_name, users.last_name, users.status')
            ->orderBy('users.username', 'ASC');

        if ($activeOnly) {
            $query->where('users.status', 'active');
        }

        return $query->get()->getResultArray();
    }

    /**
     * Check if user has a specific role
     *
     * @param integer $userId
     * @param integer $roleId
     * @return boolean
     */
    public function userHasRole(int $userId, int $roleId): bool
    {
        $db = \Config\Database::connect();

        $count = $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Check if user has role by slug
     *
     * @param integer $userId
     * @param string $roleSlug
     * @return boolean
     */
    public function userHasRoleSlug(int $userId, string $roleSlug): bool
    {
        $db = \Config\Database::connect();

        $count = $db->table('user_roles')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('roles.slug', $roleSlug)
            ->countAllResults();

        return $count > 0;
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
        // Check if already assigned
        if ($this->userHasRole($userId, $roleId)) {
            return true;
        }

        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        return $db->table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $roleId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Remove role from user
     *
     * @param integer $userId
     * @param integer $roleId
     * @return boolean
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        $db = \Config\Database::connect();

        return $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->delete();
    }

    /**
     * Replace all user roles
     *
     * @param integer $userId
     * @param array $roleIds
     * @return boolean
     */
    public function syncRoles(int $userId, array $roleIds): bool
    {
        $db = \Config\Database::connect();

        // Remove existing roles
        $db->table('user_roles')->where('user_id', $userId)->delete();

        // Add new roles
        $now = date('Y-m-d H:i:s');
        foreach ($roleIds as $roleId) {
            $db->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return true;
    }

    /**
     * Get count of roles for user
     *
     * @param integer $userId
     * @return integer
     */
    public function countRoles(int $userId): int
    {
        $db = \Config\Database::connect();

        return $db->table('user_roles')
            ->where('user_id', $userId)
            ->countAllResults();
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
