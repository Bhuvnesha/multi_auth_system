<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Services\UserService;

/**
 * User Management Controller
 *
 * Handles CRUD operations for users
 */
class UserManagement extends BaseController
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Display user list
     */
    public function index()
    {
        $userModel = model('UserModel');
        $roleModel = model('RoleModel');

        // Get search and filter parameters
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $role = $this->request->getGet('role');

        // Build query
        $query = $userModel->where('deleted_at IS NULL');

        if ($search) {
            $query->groupStart()
                ->like('email', $search)
                ->orLike('username', $search)
                ->orLike('first_name', $search)
                ->orLike('last_name', $search)
                ->groupEnd();
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($role) {
            $query->join('user_roles', 'user_roles.user_id = users.id', 'inner')
                  ->where('user_roles.role_id', $role);
        }

        // Get users with pagination
        $perPage = 20;
        $users = $query->orderBy('created_at', 'DESC')->paginate($perPage);

        // Load roles for each user
        foreach ($users as &$user) {
            $user->roles = $this->userService->getUserWithPermissions($user->id)->roles ?? [];
        }

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'pager' => $userModel->pager,
            'search' => $search,
            'status' => $status,
            'role' => $role,
            'roles' => $roleModel->findAll(),
        ];

        return view('users/index', $data);
    }

    /**
     * Display create user form
     */
    public function create()
    {
        $roleModel = model('RoleModel');

        $data = [
            'title' => 'Create User',
            'roles' => $roleModel->getNonSystemRoles(),
        ];

        return view('users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $isAjax = $this->request->isAJAX();

        $data = [
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'password_confirm' => $this->request->getPost('password_confirm'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'status' => $this->request->getPost('status') ?? 'inactive',
        ];

        $roleIds = $this->request->getPost('roles') ?: [];

        $result = $this->userService->createUser($data, $roleIds);

        if ($result['success']) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User created successfully',
                    'user_id' => $result['user_id']
                ]);
            }

            return redirect()->to(base_url('/users'))
                ->with('success', 'User created successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $result['errors']
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $result['errors']);
    }

    /**
     * Display edit user form
     *
     * @param integer $id
     */
    public function edit(int $id)
    {
        $user = $this->userService->getUserWithPermissions($id);

        if (!$user || $user->deleted_at !== null) {
            return redirect()->to(base_url('/users'))
                ->with('error', 'User not found');
        }

        $roleModel = model('RoleModel');

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roleModel->getNonSystemRoles(),
            'userRoleIds' => array_column($user->roles, 'id'),
        ];

        return view('users/edit', $data);
    }

    /**
     * Update user
     *
     * @param integer $id
     */
    public function update(int $id)
    {
        $isAjax = $this->request->isAJAX();

        $user = $this->userService->getUserWithPermissions($id);

        if (!$user || $user->deleted_at !== null) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'User not found']);
            }

            return redirect()->to(base_url('/users'))
                ->with('error', 'User not found');
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'status' => $this->request->getPost('status'),
        ];

        // Only update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $roleIds = $this->request->getPost('roles') ?: [];

        $result = $this->userService->updateUser($id, $data, $roleIds);

        if ($result['success']) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            }

            return redirect()->to('/users')
                ->with('success', 'User updated successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $result['errors']
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $result['errors']);
    }

    /**
     * Soft delete user
     *
     * @param integer $id
     */
    public function delete(int $id)
    {
        $isAjax = $this->request->isAJAX();

        $user = $this->userService->getUserWithPermissions($id);

        if (!$user || $user->deleted_at !== null) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'User not found']);
            }

            return redirect()->to(base_url('/users'))
                ->with('error', 'User not found');
        }

        // Prevent deleting self
        $currentUser = auth()->getCurrentUser();
        if ($currentUser && $currentUser->id == $id) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ]);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete your own account');
        }

        $success = $this->userService->deleteUser($id);

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => $success,
                'message' => $success ? 'User deleted successfully' : 'Failed to delete user'
            ]);
        }

        return redirect()->to('/users')
            ->with($success ? 'success' : 'error', $success ? 'User deleted successfully' : 'Failed to delete user');
    }

    /**
     * Toggle user status (active/inactive)
     *
     * @param integer $id
     */
    public function toggleStatus(int $id)
    {
        $isAjax = $this->request->isAJAX();

        $user = $this->userService->getUserWithPermissions($id);

        if (!$user || $user->deleted_at !== null) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'User not found']);
            }

            return redirect()->to(base_url('/users'))
                ->with('error', 'User not found');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $success = $this->userService->changeUserStatus($id, $newStatus);

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => $success,
                'message' => $success ? "User {$newStatus}" : 'Failed to update status',
                'status' => $newStatus
            ]);
        }

        return redirect()->back()
            ->with($success ? 'success' : 'error', $success ? "User {$newStatus}" : 'Failed to update status');
    }
}
