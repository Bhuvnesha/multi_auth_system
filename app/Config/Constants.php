<?php

/**
 * Application Constants
 */

// Composer autoload path
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

// App namespace
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

// Debug mode - matches CI_ENVIRONMENT=development
// ENVIRONMENT may not be defined yet when this file is loaded, so we provide a fallback
$environment = defined('ENVIRONMENT') ? ENVIRONMENT : ($_SERVER['CI_ENVIRONMENT'] ?? 'production');
defined('CI_DEBUG') || define('CI_DEBUG', $environment === 'development');

// Exit status codes
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6);
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);

// Timing constants
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

// Application version
define('APP_VERSION', '1.0.0');

// Minimum PHP version requirement
define('MIN_PHP_VERSION', '7.4');

// Default timezone
define('DEFAULT_TIMEZONE', 'UTC');

// Default locale
define('DEFAULT_LOCALE', 'en');

// Permission definitions
define('PERMISSION_SEPARATOR', '.');

// User statuses
define('USER_STATUS_ACTIVE', 'active');
define('USER_STATUS_INACTIVE', 'inactive');
define('USER_STATUS_SUSPENDED', 'suspended');

// System roles (slug)
define('ROLE_SUPER_ADMIN', 'super-admin');
define('ROLE_ADMINISTRATOR', 'administrator');
define('ROLE_MANAGER', 'manager');
define('ROLE_USER', 'user');

// Error and success messages cache key
define('FLASH_ERROR_KEY', 'error');
define('FLASH_SUCCESS_KEY', 'success');
define('FLASH_INFO_KEY', 'info');
define('FLASH_WARNING_KEY', 'warning');

// Default pagination
define('DEFAULT_PER_PAGE', 20);

// Upload limits
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_MIME_TYPES', 'jpg,jpeg,png,gif,pdf,doc,docx');

// Email types
define('EMAIL_VERIFICATION', 'verification');
define('EMAIL_PASSWORD_RESET', 'password_reset');
define('EMAIL_WELCOME', 'welcome');

// Cache keys
define('CACHE_USER_PERMISSIONS', 'user_perms_');
define('CACHE_ROLE_PERMISSIONS', 'role_perms_');

// Log types
define('LOG_TYPE_AUTH', 'auth');
define('LOG_TYPE_USER', 'user');
define('LOG_TYPE_ADMIN', 'admin');
define('LOG_TYPE_ERROR', 'error');
define('LOG_TYPE_API', 'api');
