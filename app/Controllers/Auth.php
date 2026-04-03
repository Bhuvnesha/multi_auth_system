<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Services\AuthService;
use App\Services\UserService;

/**
 * Authentication Controller
 *
 * Handles login, registration, logout, and password reset
 */
class Auth extends BaseController
{
    protected $authService;
    protected $userService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
    }

    /**
     * Display login form
     */
    public function loginForm()
    {
        // If already logged in, redirect to dashboard
        if ($this->authService->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Process login
     */
    public function login()
    {
        // Check if AJAX request
        $isAjax = $this->request->isAJAX();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember') == 1;

        // Validate required fields
        if (empty($email) || empty($password)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email and password are required'
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Email and password are required');
        }

        // Attempt login
        $result = $this->authService->login($email, $password, $remember);

        if ($result['success']) {
            // Cache user permissions in session
            $rbacService = new \App\Services\RBACService();
            $rbacService->cacheUserPermissions($result['user']->id);

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => '/dashboard'
                ]);
            }

            // Redirect to intended URL or dashboard
            $redirectUrl = session()->get('redirect_url') ?: '/dashboard';
            session()->remove('redirect_url');

            return redirect()->to($redirectUrl)
                ->with('success', 'Welcome back, ' . $result['user']->full_name);
        }

        // Login failed
        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Display registration form
     */
    public function registerForm()
    {
        // Check if registration is allowed
        if (!config('Auth')->allowRegistration) {
            return redirect()->to('/login')
                ->with('error', 'Registration is currently disabled');
        }

        // If already logged in, redirect to dashboard
        if ($this->authService->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    /**
     * Process registration
     */
    public function register()
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
        ];

        // Validate required fields
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($data['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password'] = 'Passwords do not match';
        }

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }

        // Check if user already exists
        if (empty($errors)) {
            $userModel = model('UserModel');
            if ($userModel->emailExists($data['email'])) {
                $errors['email'] = 'Email is already registered';
            }
            if ($userModel->usernameExists($data['username'])) {
                $errors['username'] = 'Username is already taken';
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

        // Create user
        $data['status'] = 'active'; // Auto-active for now, or set to 'inactive' for email verification

        $result = $this->userService->createUser($data);

        if ($result['success']) {
            // Auto-login after registration
            $loginResult = $this->authService->login($data['email'], $data['password']);

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Registration successful',
                    'redirect' => '/dashboard'
                ]);
            }

            return redirect()->to('/dashboard')
                ->with('success', 'Registration successful! Welcome to Multi-Auth System.');
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
     * Display forgot password form
     */
    public function forgotPasswordForm()
    {
        return view('auth/forgot-password');
    }

    /**
     * Process forgot password request
     */
    public function forgotPassword()
    {
        $isAjax = $this->request->isAJAX();

        $email = $this->request->getPost('email');

        if (empty($email)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email is required'
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Email is required');
        }

        $result = $this->authService->initiatePasswordReset($email);

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message']
            ]);
        }

        return redirect()->to('/login')
            ->with('success', $result['message']);
    }

    /**
     * Display reset password form
     *
     * @param string $token Reset token
     */
    public function resetPasswordForm(string $token)
    {
        $data = ['token' => $token];
        return view('auth/reset-password', $data);
    }

    /**
     * Process password reset
     */
    public function resetPassword()
    {
        $isAjax = $this->request->isAJAX();

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $password_confirm = $this->request->getPost('password_confirm');

        if (empty($token) || empty($password)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'All fields are required'
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'All fields are required');
        }

        if ($password !== $password_confirm) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Passwords do not match'
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Passwords do not match');
        }

        if (strlen($password) < 8) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password must be at least 8 characters'
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Password must be at least 8 characters');
        }

        $result = $this->authService->resetPassword($token, $password);

        if ($result['success']) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => '/login'
                ]);
            }

            return redirect()->to('/login')
                ->with('success', $result['message']);
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->authService->logout();

        // Clear remember me cookie
        $response = \Config\Services::response();
        $response->deleteCookie('remember_me');

        return redirect()->to('/login')
            ->with('success', 'You have been logged out successfully');
    }
}
