<?php namespace App\Services;

use App\Repositories\UserRepository;
use Config\Services as CI_Service;

/**
 * Authentication Service
 *
 * Handles all authentication-related operations
 */
class AuthService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->session = CI_Service::session();
    }

    /**
     * Attempt user login
     *
     * @param string $identifier Email or username
     * @param string $password Plain password
     * @param boolean $remember Should remember user
     * @return array ['success' => bool, 'user' => entity|null, 'message' => string]
     */
    public function login(string $identifier, string $password, bool $remember = false): array
    {
        // Find user by email or username
        $user = $this->userRepository->findByEmail($identifier);
        if (!$user) {
            $user = $this->userRepository->findByUsername($identifier);
        }

        if (!$user) {
            return [
                'success' => false,
                'user' => null,
                'message' => 'Invalid credentials'
            ];
        }

        // Check if user is soft deleted
        if ($user->deleted_at !== null) {
            return [
                'success' => false,
                'user' => null,
                'message' => 'Account does not exist'
            ];
        }

        // Check if account is suspended
        if ($user->status === 'suspended') {
            return [
                'success' => false,
                'user' => null,
                'message' => 'Account is suspended. Please contact administrator.'
            ];
        }

        // Check if account is inactive
        if ($user->status === 'inactive') {
            return [
                'success' => false,
                'user' => null,
                'message' => 'Account is not active. Please verify your email or contact administrator.'
            ];
        }

        // Check if account is locked
        if ($user->isLocked()) {
            return [
                'success' => false,
                'user' => null,
                'message' => 'Account is temporarily locked. Please try again later.'
            ];
        }

        // Verify password
        if (!$user->verifyPassword($password)) {
            // Record failed attempt
            $this->userRepository->getModel()->recordFailedLogin($user->id);

            return [
                'success' => false,
                'user' => null,
                'message' => 'Invalid credentials'
            ];
        }

        // Clear failed attempts on successful login
        $this->userRepository->getModel()->updateLastLogin($user->id, $this->getClientIp());

        // Update user entity with fresh data
        $user = $this->userRepository->find($user->id);

        // Set session data
        $this->setUserSession($user, $remember);

        return [
            'success' => true,
            'user' => $user,
            'message' => 'Login successful'
        ];
    }

    /**
     * Logout current user
     *
     * @return boolean
     */
    public function logout(): bool
    {
        $this->session->remove('user_id');
        $this->session->remove('user_email');
        $this->session->remove('user_username');
        $this->session->remove('user_name');
        $this->session->remove('user_roles');
        $this->session->remove('user_permissions');
        $this->session->destroy();

        return true;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    public function isLoggedIn(): bool
    {
        return $this->session->has('user_id');
    }

    /**
     * Get current logged in user ID
     *
     * @return integer|null
     */
    public function getCurrentUserId(): ?int
    {
        return $this->session->get('user_id');
    }

    /**
     * Get current user entity
     *
     * @return \App\Entities\User|null
     */
    public function getCurrentUser()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return null;
        }

        return $this->userRepository->find($userId);
    }

    /**
     * Check if current user has verified email
     *
     * @return boolean
     */
    public function isEmailVerified(): bool
    {
        $user = $this->getCurrentUser();
        return $user ? $user->isEmailVerified() : false;
    }

    /**
     * Start password reset process
     *
     * @param string $email
     * @return array ['success' => bool, 'message' => string]
     */
    public function initiatePasswordReset(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || $user->deleted_at !== null) {
            // Don't reveal that user doesn't exist for security
            return [
                'success' => true,
                'message' => 'If the email exists in our system, you will receive a reset link shortly.'
            ];
        }

        // Generate reset token
        $user->generatePasswordResetToken();
        $this->userRepository->update($user->id, [
            'password_reset_token' => $user->password_reset_token,
            'password_reset_expires' => $user->password_reset_expires,
        ]);

        // Send reset email (would need email service integration)
        // For now, we'll just log the token for development
        log_message('info', 'Password reset token for ' . $email . ': ' . $user->password_reset_token);

        return [
            'success' => true,
            'message' => 'If the email exists in our system, you will receive a reset link shortly.'
        ];
    }

    /**
     * Reset password with token
     *
     * @param string $token
     * @param string $newPassword
     * @return array ['success' => bool, 'message' => string]
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        $user = $this->userRepository->findByPasswordResetToken($token);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired reset token.'
            ];
        }

        // Check token expiry
        if (strtotime($user->password_reset_expires) < time()) {
            // Clear expired token
            $this->userRepository->update($user->id, [
                'password_reset_token' => null,
                'password_reset_expires' => null,
            ]);

            return [
                'success' => false,
                'message' => 'Reset token has expired. Please request a new one.'
            ];
        }

        // Update password and clear reset token
        $this->userRepository->update($user->id, [
            'password' => $newPassword,
            'password_reset_token' => null,
            'password_reset_expires' => null,
        ]);

        return [
            'success' => true,
            'message' => 'Password has been reset successfully. You can now login with your new password.'
        ];
    }

    /**
     * Set session data for user
     *
     * @param \App\Entities\User $user
     * @param boolean $remember
     * @return void
     */
    protected function setUserSession($user, bool $remember = false): void
    {
        $sessionData = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_username' => $user->username,
            'user_name' => $user->full_name,
            'user_status' => $user->status,
            'user_email_verified' => $user->isEmailVerified(),
            'logged_in' => true,
        ];

        $this->session->set($sessionData);

        // Set remember me cookie (30 days)
        if ($remember) {
            $cookie = [
                'name' => 'remember_me',
                'value' => encode($user->id . ':' . md5($user->password)),
                'expire' => 60 * 60 * 24 * 30,
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ];
            \Config\Services::response()->setCookie($cookie);
        }
    }

    /**
     * Get client IP address
     *
     * @return string|null
     */
    protected function getClientIp(): ?string
    {
        $request = \Config\Services::request();
        return $request->getIPAddress();
    }

    /**
     * Verify remember me cookie
     *
     * @return boolean
     */
    public function verifyRememberCookie(): bool
    {
        $cookie = \Config\Services::request()->getCookie('remember_me');

        if (!$cookie) {
            return false;
        }

        [$userId, $hash] = explode(':', $cookie) ?? [null, null];

        if (!$userId || !$hash) {
            return false;
        }

        $user = $this->userRepository->find((int)$userId);

        if (!$user || $user->deleted_at !== null || $user->status !== 'active') {
            return false;
        }

        // Verify hash
        if ($hash !== md5($user->password)) {
            return false;
        }

        // Log user in
        $this->setUserSession($user);

        return true;
    }
}
