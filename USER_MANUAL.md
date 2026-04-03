# User Manual - Multi-Auth System

## Table of Contents

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [User Features](#user-features)
4. [Admin Features](#admin-features)
5. [FAQ](#faq)
6. [Troubleshooting](#troubleshooting)

---

## Introduction

The Multi-Auth System is a secure, user-friendly platform for managing user accounts with role-based access control. This manual provides step-by-step instructions for end-users and administrators.

### System Features

- Easy registration and login
- Secure password management
- User profile management
- Role-based dashboard
- Admin user management
- Self-service password reset

---

## Getting Started

### Accessing the System

1. Open your web browser
2. Navigate to: `http://localhost/multi_auth_system/`
3. You'll see the landing page

### First Time Setup

**Default Admin Credentials**:
- Email: `admin@example.com`
- Password: `Admin@123`

**Important**: Change admin password after first login!

---

## User Features

### Registering an Account

1. Click **Register** on the homepage or navbar
2. Fill in the form:
   - First Name
   - Last Name
   - Email Address (valid email required)
   - Username (3+ characters)
   - Phone (optional)
   - Password (8+ characters with numbers and letters)
3. Enter password twice to confirm
4. Check the terms agreement
5. Click **Create Account**

✅ You'll be automatically logged in and redirected to the dashboard.

### Logging In

1. Click **Login** on the homepage
2. Enter your email or username
3. Enter your password
4. Check **Remember me** if desired (keeps you logged in for 30 days)
5. Click **Login**

✅ On success, you'll be redirected to the dashboard.

**Forgot Password?**
1. Click **Forgot your password?** link on login page
2. Enter your email address
3. Click **Send Reset Link**
4. Check your email for reset instructions
5. Follow the link and enter a new password

### Logging Out

1. Click your profile picture/name in the top-right navbar
2. Click **Logout**
3. You'll be redirected to the login page

### Viewing Your Profile

1. Click your profile picture/name in the navbar
2. Select **Profile** from dropdown
3. View your account information:
   - Full name
   - Email
   - Phone
   - Roles assigned
   - Status
   - Member since
   - Last login

### Editing Your Profile

1. Go to your profile page
2. Click **Edit Profile** button
3. Modify your information:
   - First Name
   - Last Name
   - Email
   - Phone
4. Click **Update Profile**

### Changing Your Password

1. Go to **Profile** → **Change Password** tab
2. Enter your **Current Password**
3. Enter **New Password** (minimum 8 characters)
4. Re-enter the new password
5. Click **Change Password**

**Password Requirements**:
- At least 8 characters long
- Contains uppercase letters
- Contains lowercase letters
- Contains numbers

---

## Admin Features

### Accessing Admin Panel

Only users with **Administrator** or **Super Admin** roles can access admin features.

1. Log in as admin
2. Click **Admin** in the navbar
3. You'll see the Admin Dashboard with system statistics

### Managing Users

As an admin, you can:
- View all users
- Create new users
- Edit user information
- Change user status (active/inactive/suspended)
- Delete users (soft delete)

#### Creating a User

1. Go to **Users** → **Create User**
2. Fill required fields (*):
   - First Name
   - Last Name
   - Email (must be unique)
   - Username (must be unique)
   - Password (auto-generated or set)
3. Optional:
   - Phone number
   - Status selection
   - Role assignments
4. Click **Create User**

**Note**: New users will be created with provided password. Communicate credentials securely.

#### Editing a User

1. Go to **Users** page
2. Find the user you want to edit
3. Click the **Pencil icon** (Edit)
4. Modify information
5. You can:
   - Change personal details
   - Update status
   - Change password (optional - leave blank to keep current)
   - Assign/remove roles
6. Click **Update User**

#### Toggling User Status

To quickly activate/deactivate a user:
1. On **Users** page, find the user
2. Click the **Pause/Play icon**
3. Status will toggle between Active and Inactive

**Status Meanings**:
- **Active**: User can log in and use the system
- **Inactive**: User cannot log in (awaiting verification or approval)
- **Suspended**: User blocked from system access

#### Deleting a User

1. On **Users** page, find the user
2. Click the **Trash icon**
3. Confirm deletion in the modal
4. User will be soft-deleted (can be restored)

**Cannot delete**:
- Your own account
- Users with existing role assignments (must remove roles first)

### Managing Roles

Roles define what a user can do in the system.

#### Viewing Roles

Go to **Roles** page to see:
- Role name and description
- Number of permissions
- Number of users assigned
- System role indicator

#### Creating a Role

1. Go to **Roles** → **Create Role**
2. Enter:
   - **Role Name**: (e.g., "Editor", "Moderator")
   - **Description**: What this role can do
   - **System Role**: Check if this is a protected system role
3. Select **Permissions** for this role
4. Click **Create Role**

**Permissions Selection**:
- Permissions are grouped by resource
- Check all that apply
- Use **Select All** to choose all permissions

**System Role**: Cannot be deleted or modified by non-super admins.

#### Editing a Role

1. On **Roles** page, click **Pencil icon**
2. Modify:
   - Name
   - Description
   - System status
   - Permissions (checked/uncheck as needed)
3. Click **Update Role**

**System roles** have restricted editing.

#### Deleting a Role

1. On **Roles** page, click **Trash icon**
2. Confirm deletion

**Cannot delete**:
- System roles (Super Admin, Administrator)
- Roles assigned to users (remove users first)

### Managing Permissions

Permissions are the basic access control units.

#### Viewing Permissions

Go to **Permissions** page to see all permissions with:
- Permission name
- Resource (e.g., users, roles)
- Action (view, create, edit, delete, manage)
- Number of roles that have this permission

#### Creating a Permission

1. Go to **Permissions** → **Create Permission**
2. Enter:
   - **Permission Name**: Human-readable (e.g., "View Users")
   - **Resource**: What it applies to (e.g., "users")
   - **Action**: What can be done (view/create/edit/delete/manage)
   - **Description**: Optional detailed description
3. Click **Create Permission**

**Examples**:
- Name: "View Dashboard", Resource: "dashboard", Action: "view"
- Name: "Manage Users", Resource: "users", Action: "manage"

**Note**: Slug is auto-generated as `resource.action`

#### Editing a Permission

1. On **Permissions** page, click **Pencil icon**
2. Modify fields
3. Click **Update Permission**

#### Deleting a Permission

1. On **Permissions** page, click **Trash icon**
2. Confirm deletion

**Cannot delete**:
- System permissions
- Permissions assigned to roles

---

## User Roles and Permissions

### Default Roles

#### 1. Super Admin
- **All permissions** automatically granted
- Cannot be modified
- Cannot be deleted
- Full system access

#### 2. Administrator
Can access:
- Dashboard
- Profile
- User Management (create, edit, view, manage)
- Role Management (create, edit, view, manage)
- Permission Management (view, manage)
- Settings (view, edit)

#### 3. Manager
Can access:
- Dashboard
- Profile
- User Management (view, create, edit)
- Reports (if added)

#### 4. User (Default)
Can access:
- Dashboard (view)
- Profile (view, edit)

### Custom Roles

You can create custom roles with any combination of permissions.

**Best Practices**:
1. Give minimum necessary permissions
2. Use descriptive names
3. Document role purpose in description
4. Test before assigning to users

### Assigning Roles to Users

1. Go to **Users** → Edit the user
2. Scroll to **Roles** section
3. Check roles to assign (uncheck to remove)
4. Save

**Important**: A user can have multiple roles.

---

## Access Control Examples

| User Action | Required Permission |
|-------------|-------------------|
| View dashboard | `dashboard.view` |
| Edit own profile | `profile.edit` |
| View user list | `users.view` |
| Create new user | `users.create` |
| Edit any user | `users.edit` |
| Delete users | `users.delete` |
| Manage roles | `roles.manage` |
| Change settings | `settings.edit` |

---

## FAQ

### Q: I forgot my password. What do I do?

**A**: Click **Forgot Password** on the login page. Enter your email and follow the instructions sent to your email.

### Q: Can I change my username?

**A**: Not through the UI currently. Contact an administrator to change your username.

### Q: What if I'm locked out of my account?

**A**: After 5 failed login attempts, your account is locked for 15 minutes. Wait or contact an administrator to unlock immediately.

### Q: How do I verify my email?

**A**: Email verification is currently disabled in development mode. In production, you'll receive a verification email after registration.

### Q: Can I delete my own account?

**A**: For security, you cannot delete your own account. Contact an administrator.

### Q: What's the difference between inactive and suspended?

**A**:
- **Inactive**: Account not yet activated (awaiting email verification or admin approval)
- **Suspended**: Account temporarily blocked for policy violations or security reasons

### Q: How do I see what permissions I have?

**A**: Check your dashboard - it shows your roles and permissions count. Admins can see complete user permissions.

### Q: Can a user have multiple roles?

**A**: Yes. A user can have multiple roles, and receives all permissions from all assigned roles.

---

## Troubleshooting

### Cannot Log In

1. Verify email/username is correct
2. Verify password is correct
3. Check if account is active (not suspended/inactive)
4. Check if account is locked (wait 15 minutes)
5. Clear browser cookies/cache

### Forgot Password Not Working

1. Ensure email is registered
2. Check spam folder for reset email
3. Reset token expires after 1 hour
4. Contact admin if multiple attempts fail

### Access Denied Errors

1. Your account may not have required permission
2. Contact your administrator to assign proper role
3. Refresh permissions by logging out and back in

### Page Not Found (404)

1. Check URL spelling
2. Ensure you're logged in for protected pages
3. Try accessing from dashboard menus instead of bookmarks

### Form Validation Errors

1. Check all required fields are filled
2. Email must be valid format
3. Password: minimum 8 characters with uppercase, lowercase, and number
4. Username: minimum 3 characters

### Email Not Received

1. Check spam/junk folder
2. Verify email address in profile
3. Contact administrator to verify email server configuration

---

## Support

For technical support or questions:

1. Check this manual first
2. Review troubleshooting section
3. Check system logs (`writable/logs/`)
4. Contact system administrator

---

**Manual Version**: 1.0
**Last Updated**: April 2025
