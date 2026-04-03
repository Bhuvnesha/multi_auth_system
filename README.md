# Multi-Auth System

A professional, production-ready multi-user authentication system with role-based access control (RBAC) built on CodeIgniter 4.

## Features

- Complete multi-user authentication (login, register, logout)
- Role-Based Access Control (RBAC) with permissions
- User management with CRUD operations
- Role and permission management
- Soft deletes with ability to restore
- Email verification (ready to implement)
- Password reset functionality
- Session-based authentication with remember me
- CSRF protection
- SQL injection prevention via Query Builder
- XSS protection with output escaping
- Responsive Bootstrap 5 UI with jQuery
- Database migrations and seeders
- Repository and Service layer patterns
- Entity classes for data modeling
- Comprehensive validation

## Technical Stack

- **Backend**: CodeIgniter 4 (PSR-4 autoloading, improved architecture)
- **Frontend**: Bootstrap 5 + jQuery 3
- **Database**: MySQL 5.7+ (compatible with MariaDB)
- **PHP Version**: ^7.4 or ^8.0

## Requirements

- PHP >= 7.4
- MySQL >= 5.7 / MariaDB >= 10.2
- Apache with mod_rewrite (or Nginx with URL rewriting)
- Composer

## Installation

### 1. Clone/Download the Project

Place the project in your web server document root. For WAMP:
```
D:\wamp64\www\multi_auth_system\
```

### 2. Install Dependencies

Open terminal in project root and run:

```bash
composer install
```

### 3. Configure Environment

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` and configure your database:
   ```
   database.default.hostname = localhost
   database.default.database = multi_auth_system
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```

3. Generate encryption key:
   ```bash
   php spark key:generate
   ```
   Set the output in your `.env`:
   ```
   encryption.key = your-generated-key-here
   ```

### 4. Create Database

Create a new database in MySQL:

```sql
CREATE DATABASE multi_auth_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations

Run database migrations to create tables:

```bash
php spark migrate
```

This will create:
- `users` - User accounts
- `roles` - Role definitions
- `permissions` - Permission definitions
- `user_roles` - User-role relationships
- `role_permissions` - Role-permission relationships

### 6. Seed Initial Data

Seed the database with default roles, permissions, and admin user:

```bash
php spark db:seed DatabaseSeeder
```

Default admin credentials:
- **Email**: admin@example.com
- **Password**: Admin@123
- **Role**: Super Admin (all permissions)

### 7. Configure Base URL

In your `.env` file, set the base URL:

```
app.baseURL = http://localhost/multi_auth_system/
```

### 8. Set Permissions

Ensure writable directory is writable:

```bash
chmod 755 writable
chmod -R 755 writable/*
```

On Windows, this is usually not necessary but ensure the web server has write permissions.

### 9. Access the Application

Open your browser and navigate to:
```
http://localhost/multi_auth_system/
```

## Quick Start

### Login as Admin

1. Go to http://localhost/multi_auth_system/login
2. Use the admin credentials from the seeder
3. You'll be redirected to the dashboard

### Create Your First User

1. Go to **Users** → **Create User**
2. Fill in user details
3. Assign appropriate roles
4. User can login with the provided password

### Set Up Roles and Permissions

1. Go to **Roles** → **Create Role**
2. Define a role name and description
3. Select permissions for this role
4. Save

### Assign Roles to Users

1. Edit a user from the **Users** page
2. Select the roles they should have
3. Save

## Architecture

### Directory Structure

```
app/
├── Config/              # Configuration files
│   ├── App.php
│   ├── Autoload.php
│   ├── Database.php
│   ├── Filters.php
│   ├── Routes.php
│   └── Services.php
├── Controllers/         # Request handlers
│   ├── Auth.php
│   ├── Dashboard.php
│   ├── UserManagement.php
│   ├── RoleManagement.php
│   ├── PermissionManagement.php
│   ├── Profile.php
│   ├── Admin.php
│   └── BaseController.php
├── Entities/            # Data entity classes
│   ├── User.php
│   ├── Role.php
│   └── Permission.php
├── Models/              # Active Record models
│   ├── UserModel.php
│   ├── RoleModel.php
│   ├── PermissionModel.php
│   ├── UserRoleModel.php
│   └── RolePermissionModel.php
├── Repositories/        # Data access layer
│   ├── UserRepository.php
│   ├── RoleRepository.php
│   ├── PermissionRepository.php
│   ├── UserRoleRepository.php
│   └── RolePermissionRepository.php
├── Services/            # Business logic
│   ├── AuthService.php
│   ├── UserService.php
│   └── RBACService.php
├── Filters/             # Middleware
│   ├── AuthFilter.php
│   └── RBACFilter.php
├── Views/               # HTML templates
│   ├── layouts/
│   ├── auth/
│   ├── dashboard/
│   ├── users/
│   ├── roles/
│   ├── permissions/
│   ├── profile/
│   └── admin/
└── Database/
    ├── Migrations/     # Database schema
    └── Seeds/          # Initial data

public/
├── index.php            # Entry point
└── assets/
    ├── css/
    └── js/

writable/                # Cache, logs, sessions
system/                  # CodeIgniter framework
```

### Design Patterns Used

- **MVC (Model-View-Controller)**: Standard framework architecture
- **Repository Pattern**: Separates data access from business logic
- **Service Layer**: Encapsulates business rules
- **Entity Pattern**: Typed data objects with behavior
- **Middleware (Filters)**: Request/response filtering

### Security Features

1. **Password Hashing**: bcrypt with PHP's `password_hash()`
2. **CSRF Protection**: Built-in CodeIgniter CSRF middleware
3. **SQL Injection Prevention**: Query Builder/Prepared statements
4. **XSS Protection**: Output escaping in views
5. **Input Validation**: Both backend and frontend
6. **Rate Limiting**: Ready for implementation (session-based)
7. **Session Security**: Secure session configuration
8. **Account Lockout**: After 5 failed attempts (15 min lock)
9. **Password Reset Tokens**: Hashed with expiry

## API Documentation

All endpoints support both web (HTML forms) and AJAX requests.

### Authentication Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/login` | GET | Display login form |
| `/login` | POST | Authenticate user |
| `/logout` | GET | Destroy session |
| `/register` | GET | Display registration form |
| `/register` | POST | Create new user |
| `/forgot-password` | GET | Display forgot password form |
| `/forgot-password` | POST | Send reset email |
| `/reset-password/{token}` | GET | Display reset form |
| `/reset-password` | POST | Process password reset |

### AJAX Responses

All AJAX calls return JSON:

```json
{
  "success": true|false,
  "message": "Human readable message",
  "redirect": "/optional-redirect-url"
}
```

## RBAC System

### Permission System

Permissions follow the format: `{resource}.{action}`

- **resource**: users, roles, permissions, dashboard, profile, settings
- **action**: view, create, edit, delete, manage

Examples:
- `users.view` - View user list
- `users.create` - Create new users
- `users.manage` - All user operations
- `dashboard.view` - Access dashboard

### Role Hierarchy

1. **Super Admin** - All permissions (cannot be modified)
2. **Administrator** - User, Role, Settings management
3. **Manager** - View users, dashboard (no delete)
4. **User** - Dashboard, Profile (read-only)

### Assigning Permissions

1. Create a role via `/roles/create`
2. Select the permissions for that role
3. Assign the role to users via user edit page

### Protecting Routes

In `app/Config/Routes.php`:

```php
$routes->group('admin', ['filter' => 'rbac:manage_users'], function($routes) {
    // Only users with manage_users permission can access
});

// Multiple permissions (any required)
$routes->get('reports', 'Reports::index', ['filter' => 'rbac:users.view|roles.view']);
```

## Database Schema

### Users Table

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20) NULL,
    status ENUM('active', 'inactive', 'suspended'),
    email_verification_token VARCHAR(100) NULL,
    email_verified_at DATETIME NULL,
    password_reset_token VARCHAR(100) NULL,
    password_reset_expires DATETIME NULL,
    last_login DATETIME NULL,
    last_login_ip VARCHAR(45) NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_status (status),
    INDEX idx_deleted (deleted_at)
);
```

### Roles Table

```sql
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE,
    slug VARCHAR(100) UNIQUE,
    description TEXT NULL,
    is_system TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Permissions Table

```sql
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE,
    slug VARCHAR(150) UNIQUE,
    description TEXT NULL,
    resource VARCHAR(100),
    action ENUM('view', 'create', 'edit', 'delete', 'manage'),
    is_system TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_resource (resource)
);
```

### Pivot Tables

```sql
CREATE TABLE user_roles (
    user_id INT,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE role_permissions (
    role_id INT,
    permission_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE
);
```

## Customization

### Adding a New Resource

1. **Add permissions** via db/seed or database:
   ```sql
   INSERT INTO permissions (name, slug, resource, action) VALUES
   ('View Reports', 'reports.view', 'reports', 'view'),
   ('Generate Reports', 'reports.generate', 'reports', 'manage');
   ```

2. **Assign permissions** to a role via the admin UI

3. **Protect routes**:
   ```php
   $routes->get('reports', 'Reports::index', ['filter' => 'rbac:reports.view']);
   ```

4. **Check in code**:
   ```php
   if (rbac()->userHasPermission($userId, 'reports.view')) {
       // Show reports
   }
   ```

### Custom Validation Rules

Edit validation rules in Models:
- `app/Models/UserModel.php` - `$validationRules`

### Styling

Main CSS file: `public/assets/css/style.css`

Key CSS variables in `:root`:
- `--primary-color`: Main theme color
- `--secondary-color`
- `--success-color`, `--danger-color`, `--warning-color`

## Troubleshooting

### Database Connection Issues

1. Check `.env` database credentials
2. Ensure MySQL is running
3. Check database exists

### 404 Errors

1. Ensure `mod_rewrite` is enabled (Apache)
2. Check `RewriteBase` in `.htaccess`
3. Clear cache: `php spark cache:clear`

### CSRF Token Errors

1. Ensure CSRF filter is enabled in `app/Config/Filters.php`
2. Include `<?= csrf_field() ?>` in all forms

### Migration Errors

1. Check database connection
2. Ensure migrations are in correct order
3. Review error logs in `writable/logs/`

### Permission Issues

Use the debug utility in views:
```php
<?php
echo '<pre>';
print_r(session()->get('user_permissions'));
echo '</pre>';
?>
```

## Environment Configuration

### Development

Set in `.env`:
```
CI_ENVIRONMENT = development
```

Disables error display in production:
```
app.indexPage = index.php
```

### Production

Set in `.env`:
```
CI_ENVIRONMENT = production
database.default.DBDebug = false
```

## Additional Configuration

### Email Setup

Configure email in `app/Config/Email.php` for password reset emails.

### Session Storage

Default is file-based. For production, consider database:
```php
// In .env
session.driver = CodeIgniter\Session\Handlers\DatabaseHandler
```

## Security Recommendations

1. Change admin password immediately
2. Use HTTPS in production
3. Set secure cookies in `.env`
4. Enable rate limiting
5. Regular database backups
6. Keep PHP and dependencies updated
7. Implement email verification for production
8. Audit logs (available via `writable/logs/`)

## Testing

Run tests:

```bash
php spark test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new features
4. Submit a pull request

## License

MIT License. See LICENSE file for details.

## Support

- Documentation: See `DOCUMENTATION.md`
- Issues: Report bugs and feature requests
- Community: [CodeIgniter Forums](https://forum.codeigniter.com/)

---

**Version**: 1.0.0
**Last Updated**: April 2025
