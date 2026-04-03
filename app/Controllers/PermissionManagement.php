<?php namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Permission Management Controller
 *
 * Handles CRUD operations for permissions
 */
class PermissionManagement extends BaseController
{
    /**
     * Display permission list
     */
    public function index()
    {
        $permissionModel = model('PermissionModel');
        $permissions = $permissionModel->withRoleCounts();

        $data = [
            'title' => 'Permission Management',
            'permissions' => $permissions,
        ];

        return view('permissions/index', $data);
    }

    /**
     * Display create permission form
     */
    public function create()
    {
        $db = \Config\Database::connect();
        $resources = $db->table('permissions')
            ->select('resource')
            ->distinct()
            ->orderBy('resource', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Create Permission',
            'resources' => array_column($resources, 'resource'),
            'actions' => ['view', 'create', 'edit', 'delete', 'manage'],
        ];

        return view('permissions/create', $data);
    }

    /**
     * Store new permission
     */
    public function store()
    {
        $isAjax = $this->request->isAJAX();

        $name = $this->request->getPost('name');
        $resource = $this->request->getPost('resource');
        $action = $this->request->getPost('action');
        $description = $this->request->getPost('description');

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Permission name is required';
        }

        if (empty($resource)) {
            $errors['resource'] = 'Resource is required';
        }

        if (empty($action)) {
            $errors['action'] = 'Action is required';
        }

        if (empty($errors)) {
            $permissionModel = model('PermissionModel');

            $slug = strtolower($resource . '.' . $action);
            $slug = preg_replace('/[^a-z0-9\.]/', '', $slug);

            $data = [
                'name' => $name,
                'slug' => $slug,
                'resource' => $resource,
                'action' => $action,
                'description' => $description,
                'is_system' => 0,
            ];

            // Check for duplicate slug
            if ($permissionModel->where('slug', $slug)->first()) {
                $errors['action'] = 'A permission with this resource and action already exists';
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

        $permissionRepository = new \App\Repositories\PermissionRepository();
        $permissionId = $permissionRepository->create($data);

        if ($permissionId) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Permission created successfully',
                    'permission_id' => $permissionId
                ]);
            }

            return redirect()->to('/permissions')
                ->with('success', 'Permission created successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create permission'
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create permission');
    }

    /**
     * Display edit permission form
     *
     * @param integer $id
     */
    public function edit(int $id)
    {
        $permissionRepository = new \App\Repositories\PermissionRepository();
        $permission = $permissionRepository->find($id);

        if (!$permission) {
            return redirect()->to('/permissions')
                ->with('error', 'Permission not found');
        }

        // System permissions cannot be edited
        if ($permission->isSystemPermission() && !rbac()->userHasRoleSlug(auth()->getCurrentUserId(), 'super-admin')) {
            return redirect()->to('/permissions')
                ->with('error', 'System permissions cannot be modified');
        }

        $db = \Config\Database::connect();
        $resources = $db->table('permissions')
            ->select('resource')
            ->distinct()
            ->orderBy('resource', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Edit Permission',
            'permission' => $permission,
            'resources' => array_column($resources, 'resource'),
            'actions' => ['view', 'create', 'edit', 'delete', 'manage'],
        ];

        return view('permissions/edit', $data);
    }

    /**
     * Update permission
     *
     * @param integer $id
     */
    public function update(int $id)
    {
        $isAjax = $this->request->isAJAX();

        $permission = (new \App\Repositories\PermissionRepository())->find($id);

        if (!$permission) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Permission not found']);
            }

            return redirect()->to('/permissions')
                ->with('error', 'Permission not found');
        }

        // System permissions check
        if ($permission->isSystemPermission() && !rbac()->userHasRoleSlug(auth()->getCurrentUserId(), 'super-admin')) {
            if ($isAjax) {
                return $this->response->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'System permissions cannot be modified']);
            }

            return redirect()->back()
                ->with('error', 'System permissions cannot be modified');
        }

        $name = $this->request->getPost('name');
        $resource = $this->request->getPost('resource');
        $action = $this->request->getPost('action');
        $description = $this->request->getPost('description');

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Permission name is required';
        }

        if (empty($resource)) {
            $errors['resource'] = 'Resource is required';
        }

        if (empty($action)) {
            $errors['action'] = 'Action is required';
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

        $slug = strtolower($resource . '.' . $action);
        $slug = preg_replace('/[^a-z0-9\.]/', '', $slug);

        $success = (new \App\Repositories\PermissionRepository())->update($id, [
            'name' => $name,
            'slug' => $slug,
            'resource' => $resource,
            'action' => $action,
            'description' => $description,
        ]);

        if ($success) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Permission updated successfully'
                ]);
            }

            return redirect()->to('/permissions')
                ->with('success', 'Permission updated successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update permission'
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update permission');
    }

    /**
     * Delete permission
     *
     * @param integer $id
     */
    public function delete(int $id)
    {
        $isAjax = $this->request->isAJAX();

        $permission = (new \App\Repositories\PermissionRepository())->find($id);

        if (!$permission) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Permission not found']);
            }

            return redirect()->to('/permissions')
                ->with('error', 'Permission not found');
        }

        // System permissions cannot be deleted
        if ($permission->isSystemPermission() && !rbac()->userHasRoleSlug(auth()->getCurrentUserId(), 'super-admin')) {
            if ($isAjax) {
                return $this->response->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'System permissions cannot be deleted']);
            }

            return redirect()->back()
                ->with('error', 'System permissions cannot be deleted');
        }

        $permissionRepository = new \App\Repositories\PermissionRepository();
        $success = $permissionRepository->delete($id);

        $message = $success ? 'Permission deleted successfully' : 'Failed to delete permission';

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => $success,
                'message' => $message
            ]);
        }

        return redirect()->to('/permissions')
            ->with($success ? 'success' : 'error', $message);
    }
}
