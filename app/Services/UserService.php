<?php namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;

/**
 * User Service
 *
 * Handles business logic for user management
 */
class UserService
{
    protected $userRepository;
    protected $roleRepository;
    protected $permissionRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->roleRepository = new RoleRepository();
        $this->permissionRepository = new PermissionRepository();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @param array $roleIds
     * @return array ['success' => bool, 'user_id' => int|null, 'errors' => array]
     */
    public function createUser(array $data, array $roleIds = []): array
    {
        // Validate required fields
        $errors = $this->validateUserData($data);

        if (!empty($errors)) {
            return ['success' => false, 'user_id' => null, 'errors' => $errors];
        }

        // Check if email exists
        if ($this->userRepository->getModel()->emailExists($data['email'])) {
            $errors['email'] = 'Email is already registered';
            return ['success' => false, 'user_id' => null, 'errors' => $errors];
        }

        // Check if username exists
        if ($this->userRepository->getModel()->usernameExists($data['username'])) {
            $errors['username'] = 'Username is already taken';
            return ['success' => false, 'user_id' => null, 'errors' => $errors];
        }

        // Create user
        $userId = $this->userRepository->create($data);

        if (!$userId) {
            return ['success' => false, 'user_id' => null, 'errors' => ['general' => 'Failed to create user']];
        }

        // Assign roles if provided
        if (!empty($roleIds)) {
            $this->userRepository->updateRoles($userId, $roleIds);
        }

        return ['success' => true, 'user_id' => $userId, 'errors' => []];
    }

    /**
     * Update existing user
     *
     * @param integer $userId
     * @param array $data
     * @param array $roleIds
     * @return array ['success' => bool, 'errors' => array]
     */
    public function updateUser(int $userId, array $data, array $roleIds = []): array
    {
        $errors = [];

        // If email is being changed, check uniqueness
        if (isset($data['email'])) {
            $existingUser = $this->userRepository->getModel()
                ->where('email', $data['email'])
                ->where('id !=', $userId)
                ->first();

            if ($existingUser) {
                $errors['email'] = 'Email is already used by another user';
            }
        }

        // If username is being changed, check uniqueness
        if (isset($data['username'])) {
            $existingUser = $this->userRepository->getModel()
                ->where('username', $data['username'])
                ->where('id !=', $userId)
                ->first();

            if ($existingUser) {
                $errors['username'] = 'Username is already taken';
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Update user
        $success = $this->userRepository->update($userId, $data);

        if (!$success) {
            return ['success' => false, 'errors' => ['general' => 'Failed to update user']];
        }

        // Update roles if provided
        if (!empty($roleIds)) {
            $this->userRepository->updateRoles($userId, $roleIds);
        }

        return ['success' => true, 'errors' => []];
    }

    /**
     * Soft delete user
     *
     * @param integer $userId
     * @return boolean
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->delete($userId);
    }

    /**
     * Restore soft-deleted user
     *
     * @param integer $userId
     * @return boolean
     */
    public function restoreUser(int $userId): bool
    {
        return $this->userRepository->restore($userId);
    }

    /**
     * Hard delete user completely
     *
     * @param integer $userId
     * @return boolean
     */
    public function hardDeleteUser(int $userId): bool
    {
        // First remove user roles
        $db = \Config\Database::connect();
        $db->table('user_roles')->where('user_id', $userId)->delete();

        // Then delete user
        return $this->userRepository->hardDelete($userId);
    }

    /**
     * Change user status
     *
     * @param integer $userId
     * @param string $status active|inactive|suspended
     * @return boolean
     */
    public function changeUserStatus(int $userId, string $status): bool
    {
        $validStatuses = ['active', 'inactive', 'suspended'];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->userRepository->getModel()->where('id', $userId)->set(['status' => $status])->update();
    }

    /**
     * Assign role to user
     *
     * @param integer $userId
     * @param integer $roleId
     * @return boolean
     */
    public function assignRole(int $userId, int $roleId): bool
    {
        $role = $this->roleRepository->find($roleId);

        if (!$role) {
            return false;
        }

        // Check if user already has role
        $user = $this->userRepository->find($userId);
        foreach ($user->roles as $existingRole) {
            if ($existingRole['id'] == $roleId) {
                return true; // Already assigned
            }
        }

        return $this->userRepository->getModel()->assignRole($userId, $roleId);
    }

    /**
     * Remove role from user
     *
     * @param integer $userId
     * @param integer $roleId
     * @return boolean
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        return $this->userRepository->getModel()->removeRole($userId, $roleId);
    }

    /**
     * Get user with full role and permission data
     *
     * @param integer $userId
     * @return \App\Entities\User|null
     */
    public function getUserWithPermissions(int $userId)
    {
        return $this->userRepository->getWithRoles($userId);
    }

    /**
     * Validate user input data
     *
     * @param array $data
     * @return array
     */
    protected function validateUserData(array $data): array
    {
        $errors = [];

        // Required fields
        $required = ['email', 'username', 'password', 'first_name', 'last_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Email validation
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        // Password strength validation
        if (isset($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }
            if (!preg_match('/[A-Z]/', $data['password'])) {
                $errors['password'] = 'Password must contain at least one uppercase letter';
            }
            if (!preg_match('/[a-z]/', $data['password'])) {
                $errors['password'] = 'Password must contain at least one lowercase letter';
            }
            if (!preg_match('/[0-9]/', $data['password'])) {
                $errors['password'] = 'Password must contain at least one number';
            }
        }

        return $errors;
    }
}
