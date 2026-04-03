<?php namespace App\Repositories;

use App\Entities\User as UserEntity;
use CodeIgniter\Model;

/**
 * User Repository
 *
 * Handles all data access operations for users
 */
class UserRepository
{
    /**
     * @var \CodeIgniter\Model
     */
    protected $model;

    public function __construct()
    {
        $this->model = model('UserModel');
    }

    /**
     * Find user by ID
     *
     * @param integer $id
     * @return UserEntity|null
     */
    public function find(int $id): ?UserEntity
    {
        return $this->model->find($id);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return UserEntity|null
     */
    public function findByEmail(string $email): ?UserEntity
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find user by username
     *
     * @param string $username
     * @return UserEntity|null
     */
    public function findByUsername(string $username): ?UserEntity
    {
        return $this->model->where('username', $username)->first();
    }

    /**
     * Find user by verification token
     *
     * @param string $token
     * @return UserEntity|null
     */
    public function findByVerificationToken(string $token): ?UserEntity
    {
        return $this->model->where('email_verification_token', $token)->first();
    }

    /**
     * Find user by password reset token
     *
     * @param string $token
     * @return UserEntity|null
     */
    public function findByPasswordResetToken(string $token): ?UserEntity
    {
        return $this->model
            ->where('password_reset_token', $token)
            ->where('password_reset_expires >=', date('Y-m-d H:i:s'))
            ->first();
    }

    /**
     * Get all active users with their roles
     *
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function getAll(int $limit = null, int $offset = null): array
    {
        $query = $this->model
            ->select('users.*')
            ->where('deleted_at IS NULL')
            ->orderBy('created_at', 'DESC');

        if ($limit !== null) {
            $query = $query->limit($limit, $offset);
        }

        return $query->findAll();
    }

    /**
     * Get user with all roles and permissions
     *
     * @param integer $userId
     * @return UserEntity|null
     */
    public function getWithRoles(int $userId): ?UserEntity
    {
        $user = $this->model->where('users.id', $userId)
            ->where('users.deleted_at IS NULL')
            ->join('user_roles', 'user_roles.user_id = users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->select('users.*, GROUP_CONCAT(DISTINCT roles.id) as role_ids, GROUP_CONCAT(DISTINCT roles.name) as role_names')
            ->groupBy('users.id')
            ->first();

        if (!$user) {
            return null;
        }

        // Load roles (the entity already exists, just set roles property)
        if (!empty($user->role_ids)) {
            $roleIds = explode(',', $user->role_ids);
            $roleNames = explode(',', $user->role_names);
            $roles = [];
            foreach ($roleIds as $index => $roleId) {
                $roles[] = [
                    'id' => $roleId,
                    'name' => $roleNames[$index] ?? '',
                ];
            }
            $user->roles = $roles;
        }

        return $user;
    }

    /**
     * Get paginated users
     *
     * @param integer $perPage
     * @param integer $page
     * @return array
     */
    public function paginate(int $perPage = 20, int $page = 1): array
    {
        $query = $this->model
            ->where('deleted_at IS NULL')
            ->orderBy('created_at', 'DESC');

        return $query->paginate($perPage, 'default', $page);
    }

    /**
     * Get total count of non-deleted users
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->model->where('deleted_at IS NULL')->countAll();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return integer|false User ID or false on failure
     */
    public function create(array $data)
    {
        // Auto-hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        // Set timestamps
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $this->model->insert($data);

        return $this->model->getInsertID();
    }

    /**
     * Update user by ID
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function update(int $id, array $data): bool
    {
        // Auto-hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->model->where('id', $id)->set($data)->update();
    }

    /**
     * Soft delete user (set deleted_at)
     *
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)->set(['deleted_at' => date('Y-m-d H:i:s')])->update();
    }

    /**
     * Restore soft-deleted user
     *
     * @param integer $id
     * @return boolean
     */
    public function restore(int $id): bool
    {
        return $this->model->where('id', $id)->set(['deleted_at' => null])->update();
    }

    /**
     * Hard delete user (remove from database)
     *
     * @param integer $id
     * @return boolean
     */
    public function hardDelete(int $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }

    /**
     * Update user roles (replace existing roles)
     *
     * @param integer $userId
     * @param array $roleIds
     * @return boolean
     */
    public function updateRoles(int $userId, array $roleIds): bool
    {
        $db = \Config\Database::connect();

        // Remove existing roles
        $db->table('user_roles')->where('user_id', $userId)->delete();

        // Add new roles
        $now = date('Y-m-d H:i:s');
        foreach ($roleIds as $roleId) {
            $db->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return true;
    }

    /**
     * Activate user
     *
     * @param integer $id
     * @return boolean
     */
    public function activate(int $id): bool
    {
        return $this->update($id, ['status' => 'active']);
    }

    /**
     * Deactivate user
     *
     * @param integer $id
     * @return boolean
     */
    public function deactivate(int $id): bool
    {
        return $this->update($id, ['status' => 'inactive']);
    }

    /**
     * Suspend user
     *
     * @param integer $id
     * @return boolean
     */
    public function suspend(int $id): bool
    {
        return $this->update($id, ['status' => 'suspended']);
    }

    /**
     * Get base model instance
     *
     * @return \CodeIgniter\Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
