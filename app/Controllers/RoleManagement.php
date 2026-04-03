<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Services\RBACService;

/**
 * Role Management Controller
 *
 * Handles CRUD operations for roles
 */
class RoleManagement extends BaseController
{
    protected $rbacService;

    public function __construct()
    {
        $this->rbacService = new RBACService();
    }

    /**
     * Display role list
     */
    public function index()
    {
        $roleModel = model('RoleModel');
        $roles = $roleModel->withUserCounts();

        $data = [
            'title' => 'Role Management',
            'roles' => $roles,
        ];

        return view('roles/index', $data);
    }

    /**
     * Display create role form
     */
    public function create()
    {
        $permissionModel = model('PermissionModel');
        $groupedPermissions = $permissionModel->getGroupedByResource();

        $data = [
            'title' => 'Create Role',
            'groupedPermissions' => $groupedPermissions,
        ];

        return view('roles/create', $data);
    }

    /**
     * Store new role
     */
    public function store()
    {
        $isAjax = $this->request->isAJAX();

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $permissionIds = $this->request->getPost('permissions') ?: [];
        $isSystem = $this->request->getPost('is_system') == 1 ? 1 : 0;

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Role name is required';
        }

        if (empty($errors)) {
            $roleModel = model('RoleModel');

            $data = [
                'name' => $name,
                'slug' => url_title($name, '-', true),
                'description' => $description,
                'is_system' => $isSystem,
            ];

            // Check for duplicate name
            if ($roleModel->where('name', $name)->first()) {
                $errors['name'] = 'Role name already exists';
            }

            // Check for duplicate slug
            if ($roleModel->where('slug', $data['slug'])->first()) {
                $errors['name'] = 'Role slug already exists';
            }
        }

        if (!empty($errors)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errors
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }

        $roleRepository = new \App\Repositories\RoleRepository();
        $roleId = $roleRepository->create($data);

        if ($roleId) {
            // Assign permissions
            if (!empty($permissionIds)) {
                $roleRepository->syncPermissions($roleId, $permissionIds);
            }

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Role created successfully',
                    'role_id' => $roleId
                ]);
            }

            return redirect()->to('/roles')
                ->with('success', 'Role created successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create role'
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create role');
    }

    /**
     * Display edit role form
     *
     * @param integer $id
     */
    public function edit(int $id)
    {
        $roleRepository = new \App\Repositories\RoleRepository();
        $role = $roleRepository->getWithPermissions($id);

        if (!$role) {
            return redirect()->to('/roles')
                ->with('error', 'Role not found');
        }

        // System roles cannot be edited (except by super admin)
        if ($role->isSystemRole() && !rbac()->userHasRoleSlug(auth()->getCurrentUserId(), 'super-admin')) {
            return redirect()->to('/roles')
                ->with('error', 'System roles cannot be modified');
        }

        $permissionModel = model('PermissionModel');
        $groupedPermissions = $permissionModel->getGroupedByResource();

        // Get current permission IDs
        $currentPermissionIds = [];
        if (isset($role->permissions)) {
            $currentPermissionIds = array_column($role->permissions, 'id');
        }

        $data = [
            'title' => 'Edit Role',
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
            'currentPermissionIds' => $currentPermissionIds,
        ];

        return view('roles/edit', $data);
    }

    /**
     * Update role
     *
     * @param integer $id
     */
    public function update(int $id)
    {
        $isAjax = $this->request->isAJAX();

        $role = (new \App\Repositories\RoleRepository())->find($id);

        if (!$role) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Role not found']);
            }

            return redirect()->to('/roles')
                ->with('error', 'Role not found');
        }

        // System roles check
        if ($role->isSystemRole() && !rbac()->userHasRoleSlug(auth()->getCurrentUserId(), 'super-admin')) {
            if ($isAjax) {
                return $this->response->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'System roles cannot be modified']);
            }

            return redirect()->back()
                ->with('error', 'System roles cannot be modified');
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $permissionIds = $this->request->getPost('permissions') ?: [];

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Role name is required';
        }

        if (empty($errors)) {
            $roleRepository = new \App\Repositories\RoleRepository();

            // Check for duplicate name (excluding current)
            $existing = $roleRepository->getModel()
                ->where('name', $name)
                ->where('id !=', $id)
                ->first();

            if ($existing) {
                $errors['name'] = 'Role name already exists';
            }

            $slug = url_title($name, '-', true);
            $existingSlug = $roleRepository->getModel()
                ->where('slug', $slug)
                ->where('id !=', $id)
                ->first();

            if ($existingSlug) {
                $errors['name'] = 'Role slug already exists';
            }
        }

        if (!empty($errors)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errors
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }

        $roleRepository = new \App\Repositories\RoleRepository();

        $success = $roleRepository->update($id, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);

        if ($success) {
            // Sync permissions
            $roleRepository->syncPermissions($id, $permissionIds);

            // Clear permission cache for users with this role
            $rbacService = new \App\Services\RBACService();
            $userRoleRepo = new \App\Repositories\UserRoleRepository();
            $users = $userRoleRepo->getUsersByRole($id);
            foreach ($users as $user) {
                $rbacService->syncUserPermissions($user['id']);
            }

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Role updated successfully'
                ]);
            }

            return redirect()->to('/roles')
                ->with('success', 'Role updated successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update role'
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update role');
    }

    /**
     * Delete role
     *
     * @param integer $id
     */
    public function delete(int $id)
    {
        $isAjax = $this->request->isAJAX();

        $role = (new \App\Repositories\RoleRepository())->find($id);

        if (!$role) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Role not found']);
            }

            return redirect()->to('/roles')
                ->with('error', 'Role not found');
        }

        // System roles cannot be deleted
        if ($role->isSystemRole()) {
            if ($isAjax) {
                return $this->response->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'System roles cannot be deleted']);
            }

            return redirect()->back()
                ->with('error', 'System roles cannot be deleted');
        }

        $roleRepository = new \App\Repositories\RoleRepository();
        $success = $roleRepository->delete($id);

        $message = $success ? 'Role deleted successfully' : 'Cannot delete role: it is assigned to users';

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => $success,
                'message' => $message
            ]);
        }

        return redirect()->to('/roles')
            ->with($success ? 'success' : 'error', $message);
    }
}
