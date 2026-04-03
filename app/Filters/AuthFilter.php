<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

/**
 * Authentication Filter
 *
 * Ensures user is logged in for protected routes
 */
class AuthFilter implements FilterInterface
{
    /**
     * Runs before the controller method
     *
     * @param array $arguments Filter arguments from Routes
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session();
        $authService = new \App\Services\AuthService();

        // Check if user is logged in
        if (!$authService->isLoggedIn()) {
            // Store intended URL for redirect after login
            $session->set('redirect_url', current_url());

            // Redirect to login page with error
            return redirect()->to('/login')
                ->with('error', 'Please log in to access this page');
        }

        // Verify remember me cookie if no session
        if (!$session->has('user_id')) {
            $authService->verifyRememberCookie();
        }

        // Check if user is still logged in after cookie check
        if (!$session->has('user_id')) {
            return redirect()->to('/login')
                ->with('error', 'Session expired. Please log in again.');
        }

        // Check if account is active
        $user = $authService->getCurrentUser();
        if (!$user) {
            $authService->logout();
            return redirect()->to('/login')
                ->with('error', 'User not found. Please log in again.');
        }

        if ($user->status !== 'active') {
            $authService->logout();
            return redirect()->to('/login')
                ->with('error', 'Your account is not active. Please contact administrator.');
        }

        // Check if account is locked
        if ($user->isLocked()) {
            return redirect()->to('/login')
                ->with('error', 'Account is temporarily locked due to multiple failed login attempts.');
        }
    }

    /**
     * Runs after the controller method
     *
     * @param array $arguments Filter arguments from Routes
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
