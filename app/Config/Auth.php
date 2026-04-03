<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Authentication Configuration
 */
class Auth extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Registration
     //  Whether new users can register themselves
     * --------------------------------------------------------------------------
     *
     * @var boolean
     */
    public $allowRegistration = true;

    /**
     * --------------------------------------------------------------------------
     * Email Verification
     * --------------------------------------------------------------------------
     *
     * Require users to verify their email address before account activation
     *
     * @var boolean
     */
    public $requireEmailVerification = false;

    /**
     * --------------------------------------------------------------------------
     * Default User Role
     * --------------------------------------------------------------------------
     *
     * Role ID or slug to assign to new registrations
     *
     * @var mixed
     */
    public $defaultUserRole = 'user'; // Can be role name or ID

    /**
     * --------------------------------------------------------------------------
     * Password Reset
     * --------------------------------------------------------------------------
     *
     * Token expiration time in seconds (default: 1 hour)
     *
     * @var integer
     */
    public $resetTokenExpiration = 3600;

    /**
     * --------------------------------------------------------------------------
     * Session
     * --------------------------------------------------------------------------
     *
     * Session key for storing user information
     *
     * @var string
     */
    public $sessionKey = 'user_id';

    /**
     * --------------------------------------------------------------------------
     * Remember Me
     * --------------------------------------------------------------------------
     *
     * Duration in seconds for remember me cookie (default: 30 days)
     *
     * @var integer
     */
    public $rememberMeLength = 2592000;

    /**
     * --------------------------------------------------------------------------
     * Login Attempts
     * --------------------------------------------------------------------------
     *
     * Maximum number of failed login attempts before lockout
     *
     * @var integer
     */
    public $maxLoginAttempts = 5;

    /**
     * --------------------------------------------------------------------------
     * Lockout Duration
     * --------------------------------------------------------------------------
     *
     * Account lockout duration in seconds (default: 15 minutes)
     *
     * @var integer
     */
    public $lockoutDuration = 900;
}
