<?php if (!function_exists('auth')):

/**
 * Get authentication service instance
 *
 * @return \App\Services\AuthService
 */
function auth()
{
    return new \App\Services\AuthService();
}

endif;

if (!function_exists('rbac')):

/**
 * Get RBAC service instance
 *
 * @return \App\Services\RBACService
 */
function rbac()
{
    return new \App\Services\RBACService();
}

endif;

if (!function_exists('current_user')):

/**
 * Get current logged in user
 *
 * @return \App\Entities\User|null
 */
function current_user()
{
    return auth()->getCurrentUser();
}

endif;

if (!function_exists('user_id')):

/**
 * Get current user ID
 *
 * @return integer|null
 */
function user_id()
{
    return auth()->getCurrentUserId();
}

endif;

if (!function_exists('has_permission')):

/**
 * Check if current user has permission
 *
 * @param string $permission
 * @param integer|null $userId
 * @return boolean
 */
function has_permission(string $permission, int $userId = null): bool
{
    $userId = $userId ?? user_id();
    return rbac()->userHasPermission($userId, $permission);
}

endif;

if (!function_exists('has_role')):

/**
 * Check if current user has role
 *
 * @param string $roleSlug
 * @param integer|null $userId
 * @return boolean
 */
function has_role(string $roleSlug, int $userId = null): bool
{
    $userId = $userId ?? user_id();
    return rbac()->userHasRoleSlug($userId, $roleSlug);
}

endif;

if (!function_exists('is_admin')):

/**
 * Check if current user is admin
 * (has administrator or super-admin role)
 *
 * @return boolean
 */
function is_admin(): bool
{
    $userId = user_id();
    if (!$userId) {
        return false;
    }

    return rbac()->userHasAnyPermission($userId, ['users.manage', 'roles.manage']);
}

endif;

if (!function_exists('is_super_admin')):

/**
 * Check if current user is super admin
 *
 * @return boolean
 */
function is_super_admin(): bool
{
    $userId = user_id();
    if (!$userId) {
        return false;
    }

    return rbac()->userHasRoleSlug($userId, ROLE_SUPER_ADMIN);
}

endif;

if (!function_exists('user_roles')):

/**
 * Get current user roles
 *
 * @return array
 */
function user_roles(): array
{
    $userId = user_id();
    if (!$userId) {
        return [];
    }

    return rbac()->getUserRoleNames($userId);
}

endif;

if (!function_exists('user_permissions')):

/**
 * Get current user permissions
 *
 * @return array
 */
function user_permissions(): array
{
    $userId = user_id();
    if (!$userId) {
        return [];
    }

    return rbac()->getUserPermissions($userId);
}

endif;

if (!function_exists('role_badge')):

/**
 * Generate HTML for role badges
 *
 * @param array $roles
 * @return string
 */
function role_badge(array $roles): string
{
    $html = '';
    foreach ($roles as $role) {
        $html .= '<span class="role-badge badge bg-primary">' . esc($role) . '</span>';
    }
    return $html;
}

endif;

if (!function_exists('status_badge')):

/**
 * Generate HTML for status badges
 *
 * @param string $status
 * @param string|null $label
 * @return string
 */
function status_badge(string $status, string $label = null): string
{
    $label = $label ?? ucfirst($status);
    $class = 'status-' . $status;

    return '<span class="status-badge ' . $class . '">' . esc($label) . '</span>';
}

endif;

if (!function_exists('format_datetime')):

/**
 * Format datetime for display
 *
 * @param \DateTime|string|null $datetime
 * @param string $format
 * @return string
 */
function format_datetime($datetime, string $format = 'M d, Y H:i'): string
{
    if (empty($datetime)) {
        return 'N/A';
    }

    if (is_string($datetime)) {
        $datetime = new \DateTime($datetime);
    }

    return $datetime->format($format);
}

endif;

if (!function_exists('time_ago')):

/**
 * Get time ago string
 *
 * @param \DateTime|string|null $datetime
 * @return string
 */
function time_ago($datetime): string
{
    if (empty($datetime)) {
        return 'Never';
    }

    if (is_string($datetime)) {
        $datetime = new \DateTime($datetime);
    }

    $now = new \DateTime();
    $diff = $now->diff($datetime);

    if ($diff->y > 0) {
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    } elseif ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    } elseif ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'just now';
    }
}

endif;

if (!function_exists('gravatar_url')):

/**
 * Get Gravatar URL for email
 *
 * @param string $email
 * @param integer $size
 * @param string $default
 * @return string
 */
function gravatar_url(string $email, int $size = 80, string $default = 'mp'): string
{
    $hash = md5(strtolower(trim($email)));
    return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}";
}

endif;
