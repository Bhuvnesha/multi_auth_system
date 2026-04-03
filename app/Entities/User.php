<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * User Entity
 *
 * Represents a user in the authentication system
 *
 * @property integer $id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $status active|inactive|suspended
 * @property string $email_verification_token
 * @property datetime $email_verified_at
 * @property string $password_reset_token
 * @property datetime $password_reset_expires
 * @property datetime $last_login
 * @property string $last_login_ip
 * @property integer $failed_login_attempts
 * @property datetime $locked_until
 * @property datetime $created_at
 * @property datetime $updated_at
 * @property datetime $deleted_at
 * @property array $roles Array of role data
 * @property array $permissions Array of permission data
 */
class User extends Entity
{
    /**
     * Define which properties are immutable (cannot be changed after creation)
     *
     * @var array
     */
    protected $immutable = ['id', 'created_at'];

    /**
     * Cast types for properties
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'is_active'         => 'boolean',
        'failed_login_attempts' => 'integer',
        'email_verified_at' => 'datetime',
        'last_login'        => 'datetime',
        'password_reset_expires' => 'datetime',
        'locked_until'      => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    /**
     * Append these properties to model data
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * Computed property for full name
     *
     * @return string
     */
    protected function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Set the password with automatic hashing
     *
     * @param string $password Plain password or existing hash
     * @return $this
     */
    public function setPassword(string $password): self
    {
        if (!empty($password)) {
            // Only hash if not already a bcrypt hash (starts with $2y$, $2a$, or $2b$)
            if (!preg_match('/^\$2[aby]\$\d{2}\$/', $password)) {
                $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
            } else {
                $this->attributes['password'] = $password;
            }
        }

        return $this;
    }

    /**
     * Verify a password against the hash
     *
     * @param string $password Plain password to verify
     * @return boolean
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Check if user is active
     *
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is admin (has super-admin role)
     *
     * @return boolean
     */
    public function isAdmin(): bool
    {
        if (!isset($this->roles)) {
            return false;
        }

        foreach ($this->roles as $role) {
            if ($role['slug'] === 'super-admin' || $role['slug'] === 'administrator') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if account is locked
     *
     * @return boolean
     */
    public function isLocked(): bool
    {
        if (empty($this->locked_until)) {
            return false;
        }

        return strtotime($this->locked_until) > time();
    }

    /**
     * Increment failed login attempts
     *
     * @return $this
     */
    public function incrementFailedLogin(): self
    {
        $attempts = (int)$this->failed_login_attempts + 1;
        $this->attributes['failed_login_attempts'] = $attempts;

        // Lock account after 5 failed attempts
        if ($attempts >= 5) {
            $this->attributes['locked_until'] = date('Y-m-d H:i:s', time() + 900); // 15 minutes lock
        }

        return $this;
    }

    /**
     * Reset failed login attempts on successful login
     *
     * @return $this
     */
    public function resetFailedLoginAttempts(): self
    {
        $this->attributes['failed_login_attempts'] = 0;
        $this->attributes['locked_until'] = null;
        $this->attributes['last_login'] = date('Y-m-d H:i:s');

        return $this;
    }

    /**
     * Generate email verification token
     *
     * @return string
     */
    public function generateEmailToken(): string
    {
        $this->attributes['email_verification_token'] = bin2hex(random_bytes(32));
        return $this->attributes['email_verification_token'];
    }

    /**
     * Generate password reset token
     *
     * @return string
     */
    public function generatePasswordResetToken(): string
    {
        $this->attributes['password_reset_token'] = bin2hex(random_bytes(32));
        $this->attributes['password_reset_expires'] = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
        return $this->attributes['password_reset_token'];
    }

    /**
     * Clear password reset token
     *
     * @return $this
     */
    public function clearPasswordResetToken(): self
    {
        $this->attributes['password_reset_token'] = null;
        $this->attributes['password_reset_expires'] = null;

        return $this;
    }

    /**
     * Mark email as verified
     *
     * @return $this
     */
    public function markEmailVerified(): self
    {
        $this->attributes['email_verified_at'] = date('Y-m-d H:i:s');
        $this->attributes['email_verification_token'] = null;

        return $this;
    }

    /**
     * Check if email is verified
     *
     * @return boolean
     */
    public function isEmailVerified(): bool
    {
        return !empty($this->email_verified_at);
    }
}
