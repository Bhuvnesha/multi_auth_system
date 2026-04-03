<?php namespace App\Models;

use CodeIgniter\Model;
use App\Entities\User as UserEntity;

/**
 * User Model
 *
 * Handles database operations for users table
 */
class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email',
        'username',
        'password',
        'first_name',
        'last_name',
        'phone',
        'status',
        'email_verification_token',
        'email_verified_at',
        'password_reset_token',
        'password_reset_expires',
        'last_login',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'deleted_at',
    ];

    protected $returnType = UserEntity::class;
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[8]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'status' => 'required|in_list[active,inactive,suspended]',
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'This email is already registered',
        ],
        'username' => [
            'required' => 'Username is required',
            'min_length' => 'Username must be at least 3 characters',
            'is_unique' => 'This username is already taken',
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 8 characters',
        ],
        'first_name' => [
            'required' => 'First name is required',
            'min_length' => 'First name must be at least 2 characters',
        ],
        'last_name' => [
            'required' => 'Last name is required',
            'min_length' => 'Last name must be at least 2 characters',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Initialize the model
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active users only
     *
     * @return \CodeIgniter\Model
     */
    public function active()
    {
        return $this->where('status', 'active')->where('deleted_at IS NULL');
    }

    /**
     * Get users with roles
     *
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function withRoles()
    {
        return $this->select('users.*')
            ->join('user_roles', 'user_roles.user_id = users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->groupBy('users.id');
    }

    /**
     * Check if email exists (excluding soft deleted)
     *
     * @param string $email
     * @return boolean
     */
    public function emailExists(string $email): bool
    {
        return $this->where('email', $email)
            ->where('deleted_at IS NULL')
            ->countAllResults() > 0;
    }

    /**
     * Check if username exists (excluding soft deleted)
     *
     * @param string $username
     * @return boolean
     */
    public function usernameExists(string $username): bool
    {
        return $this->where('username', $username)
            ->where('deleted_at IS NULL')
            ->countAllResults() > 0;
    }

    /**
     * Update last login info
     *
     * @param integer $userId
     * @param string|null $ipAddress
     * @return boolean
     */
    public function updateLastLogin(int $userId, string $ipAddress = null): bool
    {
        $data = [
            'last_login' => date('Y-m-d H:i:s'),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ];

        if ($ipAddress !== null) {
            $data['last_login_ip'] = $ipAddress;
        }

        return $this->where('id', $userId)->set($data)->update();
    }

    /**
     * Record failed login attempt
     *
     * @param integer $userId
     * @return boolean
     */
    public function recordFailedLogin(int $userId): bool
    {
        $db = \Config\Database::connect();

        // Get current failed attempts
        $user = $this->find($userId);
        $attempts = (int)$user->failed_login_attempts + 1;

        $data = [
            'failed_login_attempts' => $attempts,
        ];

        // Lock account after 5 failed attempts
        if ($attempts >= 5) {
            $data['locked_until'] = date('Y-m-d H:i:s', time() + 900); // 15 minutes
        }

        return $this->where('id', $userId)->set($data)->update();
    }

    /**
     * Reset failed login attempts
     *
     * @param integer $userId
     * @return boolean
     */
    public function resetFailedAttempts(int $userId): bool
    {
        return $this->where('id', $userId)->set([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ])->update();
    }
}
