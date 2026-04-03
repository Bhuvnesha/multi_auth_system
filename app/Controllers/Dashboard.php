<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Services\AuthService;
use App\Services\RBACService;

/**
 * Dashboard Controller
 *
 * Handles user dashboard
 */
class Dashboard extends BaseController
{
    protected $authService;
    protected $rbacService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->rbacService = new RBACService();
    }

    /**
     * Display dashboard
     */
    public function index()
    {
        $user = $this->authService->getCurrentUser();

        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'roles' => $this->rbacService->getUserRoleNames($user->id),
            'permissions' => $this->rbacService->getUserPermissions($user->id),
            'can_manage_users' => $this->rbacService->userHasPermission($user->id, 'users.manage'),
            'can_manage_roles' => $this->rbacService->userHasPermission($user->id, 'roles.manage'),
            'can_manage_permissions' => $this->rbacService->userHasPermission($user->id, 'permissions.manage'),
        ];

        return view('dashboard/index', $data);
    }
}
