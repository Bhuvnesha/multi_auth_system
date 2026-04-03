<?php namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\RolePermissionRepository;

/**
 * RBAC (Role-Based Access Control) Service
 *
 * Manages permissions, roles, and access control logic
 */
class RBACService
{
    protected $roleRepository;
    protected $permissionRepository;
    protected $userRoleRepository;
    protected $rolePermissionRepository;

    public function __construct()
    {
        $this->userRoleRepository = new UserRoleRepository();
        $this->rolePermissionRepository = new RolePermissionRepository();
        $this->roleRepository = new RoleRepository();
        $this->permissionRepository = new PermissionRepository();
    }

    /**
     * Get all permissions for a user
     *
     * @param integer $userId
     * @return array Array of permission slugs
     */
    public function getUserPermissions(int $userId): array
    {
        $roleIds = $this->getUserRoleIds($userId);

        if (empty($roleIds)) {
            return [];
        }

        return $this->getPermissionsForRoles($roleIds);
    }

    /**
     * Check if user has a specific role by slug
     *
     * @param integer $userId
     * @param string $roleSlug
     * @return boolean
     */
    public function userHasRoleSlug(int $userId, string $roleSlug): bool
    {
        return $this->userRoleRepository->userHasRoleSlug($userId, $roleSlug);
    }

    /**
     * Get all role IDs for a user
     *
     * @param integer $userId
     * @return array
     */
    public function getUserRoleIds(int $userId): array
    {
        $roles = $this->userRoleRepository->getRoles($userId);

        return array_column($roles, 'id');
    }

    /**
     * Get all role names for a user
     *
     * @param integer $userId
     * @return array
     */
    public function getUserRoleNames(int $userId): array
    {
        $roles = $this->userRoleRepository->getRoles($userId);

        return array_column($roles, 'name');
    }

    /**
     * Get all role slugs for a user
     *
     * @param integer $userId
     * @return array
     */
    public function getUserRoleSlugs(int $userId): array
    {
        $roles = $this->userRoleRepository->getRoles($userId);

        return array_column($roles, 'slug');
    }

    /**
     * Get all permissions for a set of roles
     *
     * @param array $roleIds
     * @return array Array of permission slugs
     */
    public function getPermissionsForRoles(array $roleIds): array
    {
        if (empty($roleIds)) {
            return [];
        }

        $permissions = [];
        $db = \Config\Database::connect();

        foreach ($roleIds as $roleId) {
            $rolePerms = $db->table('role_permissions')
                ->join('permissions', 'permissions.id = role_permissions.permission_id')
                ->where('role_permissions.role_id', $roleId)
                ->select('permissions.slug')
                ->get()
                ->getResultArray();

            $permissions = array_merge($permissions, array_column($rolePerms, 'slug'));
        }

        // Remove duplicates
        return array_unique($permissions);
    }

    /**
     * Check if user has a specific permission
     *
     * @param integer $userId
     * @param string $permissionSlug
     * @return boolean
     */
    public function userHasPermission(int $userId, string $permissionSlug): bool
    {
        // Super admin check
        $roleSlugs = $this->getUserRoleSlugs($userId);

        if (in_array('super-admin', $roleSlugs)) {
            return true; // Super admin has all permissions
        }

        $permissions = $this->getUserPermissions($userId);

        return in_array($permissionSlug, $permissions);
    }

    /**
     * Check if user has permission for a resource/action
     *
     * @param integer $userId
     * @param string $resource
     * @param string $action
     * @return boolean
     */
    public function userHasResourceAccess(int $userId, string $resource, string $action): bool
    {
        // Super admin check
        $roleSlugs = $this->getUserRoleSlugs($userId);

        if (in_array('super-admin', $roleSlugs)) {
            return true;
        }

        // Check for manage action (includes all others)
        $managePermission = $resource . '.manage';
        if ($this->userHasPermission($userId, $managePermission)) {
            return true;
        }

        // Check specific action
        $permissionSlug = $resource . '.' . $action;
        return $this->userHasPermission($userId, $permissionSlug);
    }

    /**
     * Check if user has any of the given permissions
     *
     * @param integer $userId
     * @param array $permissionSlugs
     * @return boolean
     */
    public function userHasAnyPermission(int $userId, array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $slug) {
            if ($this->userHasPermission($userId, $slug)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     *
     * @param integer $userId
     * @param array $permissionSlugs
     * @return boolean
     */
    public function userHasAllPermissions(int $userId, array $permissionSlugs): bool
    {
        $userPermissions = $this->getUserPermissions($userId);

        foreach ($permissionSlugs as $slug) {
            if (!in_array($slug, $userPermissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all accessible resources for user
     *
     * @param integer $userId
     * @return array
     */
    public function getUserAccessibleResources(int $userId): array
    {
        $permissionSlugs = $this->getUserPermissions($userId);
        $resources = [];

        foreach ($permissionSlugs as $slug) {
            [$resource, $action] = explode('.', $slug);
            if (!in_array($resource, $resources)) {
                $resources[] = $resource;
            }
        }

        return $resources;
    }

    /**
     * Get all users with a specific permission
     *
     * @param integer $permissionId
     * @return array
     */
    public function getUsersWithPermission(int $permissionId): array
    {
        $db = \Config\Database::connect();

        return $db->table('user_roles')
            ->join('users', 'users.id = user_roles.user_id')
            ->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
            ->where('role_permissions.permission_id', $permissionId)
            ->where('users.deleted_at IS NULL')
            ->where('users.status', 'active')
            ->select('users.id, users.email, users.username, users.first_name, users.last_name')
            ->groupBy('users.id')
            ->orderBy('users.username', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Cache user permissions in session
     *
     * @param integer $userId
     * @return void
     */
    public function cacheUserPermissions(int $userId): void
    {
        $permissions = $this->getUserPermissions($userId);
        $roleNames = $this->getUserRoleNames($userId);

        $session = \Config\Services::session();
        $session->set([
            'user_permissions' => $permissions,
            'user_roles' => $roleNames,
        ]);
    }

    /**
     * Clear cached user permissions
     *
     * @return void
     */
    public function clearCache(): void
    {
        $session = \Config\Services::session();
        $session->remove('user_permissions');
        $session->remove('user_roles');
    }

    /**
     * Sync user permissions after role changes
     *
     * @param integer $userId
     * @return void
     */
    public function syncUserPermissions(int $userId): void
    {
        $this->cacheUserPermissions($userId);
    }
}
