<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

/**
 * RBAC (Role-Based Access Control) Filter
 *
 * Checks user permissions for protected routes
 *
 * Usage in Routes:
 * $routes->group('admin', ['filter' => 'rbac:manage_users']);
 *
 * Or check for specific permission:
 * $routes->group('admin', ['filter' => 'rbac:users.view']);
 *
 * Multiple permissions:
 * $routes->group('admin', ['filter' => 'rbac:users.view|users.edit']);
 */
class RBACFilter implements FilterInterface
{
    /**
     * Runs before the controller method
     *
     * @param array $arguments Filter arguments (permission slugs or role slugs)
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (empty($arguments)) {
            return; // No specific permission required
        }

        $session = Services::session();
        $authService = new \App\Services\AuthService();
        $rbacService = new \App\Services\RBACService();

        // Get current user
        $userId = $authService->getCurrentUserId();
        if (!$userId) {
            return redirect()->to('/login')
                ->with('error', 'Please log in to access this page');
        }

        // Parse permissions from arguments
        $permissions = is_array($arguments) ? $arguments : explode('|', $arguments[0]);

        // Check if any of the permissions match
        $hasAccess = false;

        foreach ($permissions as $permission) {
            $permission = trim($permission);

            // Check if it's a permission slug (contains a dot) or role slug
            if (strpos($permission, '.') !== false) {
                // It's a permission slug (e.g., users.view, users.manage)
                if ($rbacService->userHasPermission($userId, $permission)) {
                    $hasAccess = true;
                    break;
                }
            } else {
                // It's a role slug (e.g., administrator, super-admin)
                if ($rbacService->userHasRoleSlug($userId, $permission)) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        if (!$hasAccess) {
            // Check if it's an AJAX request
            if ($request->isAJAX()) {
                return Services::response()
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' => 'You do not have permission to access this resource'
                    ]);
            }

            // Redirect with error message
            return redirect()->to('/dashboard')
                ->with('error', 'You do not have permission to access this page');
        }
    }

    /**
     * Runs after the controller method
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
