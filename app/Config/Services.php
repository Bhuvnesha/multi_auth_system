<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use App\Services\UserService;
use App\Services\AuthService;
use App\Services\RBACService;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\RolePermissionRepository;

/**
 * Services Configuration
 *
 * This file provides a convenient method for defining services
 * within your application.
 */
class Services extends BaseService
{
    /**
     * Get UserService instance
     */
    public function user(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('user');
        }

        return new UserService(
            new UserRepository(),
            new RoleRepository(),
            new PermissionRepository()
        );
    }

    /**
     * Get AuthService instance
     */
    public function auth(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('auth');
        }

        return new AuthService();
    }

    /**
     * Get RBACService instance
     */
    public function rbac(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('rbac');
        }

        return new RBACService(
            new UserRoleRepository(),
            new RolePermissionRepository(),
            new RoleRepository(),
            new PermissionRepository()
        );
    }

    /**
     * Get Repository instances
     */
    public function userRepository(bool $getShared = true)
    {
        return $getShared
            ? static::getSharedInstance('userRepository')
            : new UserRepository();
    }

    public function roleRepository(bool $getShared = true)
    {
        return $getShared
            ? static::getSharedInstance('roleRepository')
            : new RoleRepository();
    }

    public function permissionRepository(bool $getShared = true)
    {
        return $getShared
            ? static::getSharedInstance('permissionRepository')
            : new PermissionRepository();
    }

    public function userRoleRepository(bool $getShared = true)
    {
        return $getShared
            ? static::getSharedInstance('userRoleRepository')
            : new UserRoleRepository();
    }

    public function rolePermissionRepository(bool $getShared = true)
    {
        return $getShared
            ? static::getSharedInstance('rolePermissionRepository')
            : new RolePermissionRepository();
    }
}
