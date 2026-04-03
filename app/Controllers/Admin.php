<?php namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Admin Controller
 *
 * Handles admin-only functionality
 */
class Admin extends BaseController
{
    /**
     * Admin dashboard with system stats
     */
    public function dashboard()
    {
        $db = \Config\Database::connect();

        // Get system statistics
        $totalUsers = $db->table('users')->where('deleted_at IS NULL')->countAllResults();
        $activeUsers = $db->table('users')->where('deleted_at IS NULL')->where('status', 'active')->countAllResults();
        $inactiveUsers = $db->table('users')->where('deleted_at IS NULL')->where('status', 'inactive')->countAllResults();
        $suspendedUsers = $db->table('users')->where('deleted_at IS NULL')->where('status', 'suspended')->countAllResults();
        $softDeletedUsers = $db->table('users')->where('deleted_at IS NOT NULL')->countAllResults();

        $totalRoles = $db->table('roles')->countAllResults();
        $totalPermissions = $db->table('permissions')->countAllResults();

        // Recent registrations (last 7 days)
        $recentRegistrations = $db->table('users')
            ->select("DATE(created_at) as date, COUNT(*) as count")
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'DESC')
            ->get()
            ->getResultArray();

        // Login activity
        $recentLogins = $db->table('users')
            ->select('id, username, email, last_login, last_login_ip')
            ->where('last_login IS NOT NULL')
            ->orderBy('last_login', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Admin Dashboard',
            'stats' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'inactive_users' => $inactiveUsers,
                'suspended_users' => $suspendedUsers,
                'soft_deleted_users' => $softDeletedUsers,
                'total_roles' => $totalRoles,
                'total_permissions' => $totalPermissions,
            ],
            'recentRegistrations' => $recentRegistrations,
            'recentLogins' => $recentLogins,
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Display system settings
     */
    public function settings()
    {
        $data = ['title' => 'System Settings'];

        return view('admin/settings', $data);
    }

    /**
     * Update system settings
     */
    public function updateSettings()
    {
        $isAjax = $this->request->isAJAX();

        $allowRegistration = $this->request->getPost('allow_registration') == 1 ? 1 : 0;
        $requireEmailVerification = $this->request->getPost('require_email_verification') == 1 ? 1 : 0;
        $defaultUserRole = $this->request->getPost('default_user_role');

        // Save to database or config file
        // For now, we'll just return success

        $settings = [
            'allow_registration' => $allowRegistration,
            'require_email_verification' => $requireEmailVerification,
            'default_user_role' => $defaultUserRole,
        ];

        // You would typically save these to a settings table
        // session()->set('system_settings', $settings);

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        }

        return redirect()->to('/admin/settings')
            ->with('success', 'Settings updated successfully');
    }
}
