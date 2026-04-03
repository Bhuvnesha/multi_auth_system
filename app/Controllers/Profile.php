<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Services\AuthService;
use App\Services\UserService;

/**
 * Profile Controller
 *
 * Handles user profile management
 */
class Profile extends BaseController
{
    protected $authService;
    protected $userService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
    }

    /**
     * Display profile page
     */
    public function index()
    {
        $user = $this->authService->getCurrentUser();
        $rbacService = new \App\Services\RBACService();

        $data = [
            'title' => 'My Profile',
            'user' => $user,
            'roles' => implode(', ', $rbacService->getUserRoleNames($user->id)),
        ];

        return view('profile/index', $data);
    }

    /**
     * Update profile
     */
    public function update()
    {
        $isAjax = $this->request->isAJAX();

        $currentUser = $this->authService->getCurrentUser();

        $data = [
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
        ];

        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }

        // Check if email is taken by another user
        if (empty($errors) && $data['email'] !== $currentUser->email) {
            $userModel = model('UserModel');
            if ($userModel->where('email', $data['email'])
                ->where('id !=', $currentUser->id)
                ->first()) {
                $errors['email'] = 'Email is already registered by another user';
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

        $result = $this->userService->updateUser($currentUser->id, $data);

        if ($result['success']) {
            // Update session email if changed
            if ($data['email'] !== $currentUser->email) {
                session()->set('user_email', $data['email']);
            }
            session()->set('user_name', trim($data['first_name'] . ' ' . $data['last_name']));

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            }

            return redirect()->to('/profile')
                ->with('success', 'Profile updated successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $result['errors']
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update profile');
    }

    /**
     * Display change password form
     */
    public function changePasswordForm()
    {
        $data = ['title' => 'Change Password'];
        return view('profile/change-password', $data);
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $isAjax = $this->request->isAJAX();

        $currentUser = $this->authService->getCurrentUser();

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $newPasswordConfirm = $this->request->getPost('new_password_confirm');

        $errors = [];

        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required';
        } else {
            // Verify current password
            if (!$currentUser->verifyPassword($currentPassword)) {
                $errors['current_password'] = 'Current password is incorrect';
            }
        }

        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters';
        } elseif ($newPassword !== $newPasswordConfirm) {
            $errors['new_password'] = 'Passwords do not match';
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

        // Update password
        $success = $this->userService->updateUser($currentUser->id, ['password' => $newPassword]);

        if ($success) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);
            }

            return redirect()->to('/profile')
                ->with('success', 'Password changed successfully');
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to change password'
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to change password');
    }
}
