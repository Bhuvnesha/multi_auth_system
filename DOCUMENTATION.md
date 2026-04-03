# Multi-Auth System - Comprehensive Documentation

## Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Installation Guide](#installation-guide)
4. [Configuration](#configuration)
5. [Database Structure](#database-structure)
6. [User Management](#user-management)
7. [RBAC System](#rbac-system)
8. [Developer Guide](#developer-guide)
9. [API Reference](#api-reference)
10. [Troubleshooting](#troubleshooting)
11. [Deployment](#deployment)
12. [Best Practices](#best-practices)

---

## Overview

The Multi-Auth System is a robust, enterprise-grade authentication and authorization system built with CodeIgniter 4. It implements industry best practices for security, maintainability, and scalability.

### Key Features

- **Multi-User Authentication**: Complete auth system with registration, login, logout
- **RBAC**: Fine-grained permission control using roles
- **User Management**: Full CRUD operations with soft deletes
- **Security First**: CSRF protection, XSS prevention, SQL injection protection
- **Responsive UI**: Bootstrap 5 frontend for all devices
- **Validation**: Both client and server-side validation
- **Password Reset**: Secure token-based password recovery
- **Remember Me**: Persistent login sessions

---

## Architecture

### High-Level Overview

```
┌─────────────────┐
│    HTTP Request │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Router        │ (Routes.php)
│   (URI -> Ctrl) │
└────────┬────────┘
         │
    ┌────┴─────┐
    ▼          ▼
┌──────┐  ┌────────┐
│Auth  │  │ Public │
│Filter│  │ Routes │
└──────┘  └────────┘
    │
    ▼
┌─────────────────┐
│ Controller      │
│ (Auth, Users)   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Service Layer   │
│ (Business Logic)│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Repository      │
│ (Data Access)   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Model + DB      │
└─────────────────┘
```

### Component Details

#### Controllers
- Handle HTTP requests/responses
- Validate input
- Call services
- Return views or JSON

#### Services
- Business logic layer
- Transaction coordination
- Validation rules

#### Repositories
- Data access abstraction
- Single responsibility for each entity
- Query encapsulation

#### Models
- Active Record patterns
- Validation rules
- Entity mapping

#### Entities
- Domain objects
- Business methods
- Type casting

---

## Installation Guide

### Step 1: Prerequisites

Verify you have:
- PHP >= 7.4 with extensions: `mysqli`, `gd`, `openssl`, `pdo_mysql`
- Composer
- MySQL 5.7+
- Apache or Nginx with URL rewriting

Check PHP version:
```bash
php -v
```

### Step 2: Download Project

Download as ZIP or clone via git to:
```
/wamp64/www/multi_auth_system/
```

### Step 3: Install Dependencies

```bash
cd multi_auth_system
composer install --no-dev --optimize-autoloader
```

For development with testing tools:
```bash
composer install
```

### Step 4: Database Setup

**Using phpMyAdmin**:
1. Open http://localhost/phpmyadmin
2. Create database: `multi_auth_system`
3. Character set: `utf8mb4`
4. Collation: `utf8mb4_unicode_ci`

**Using CLI**:
```bash
mysql -u root -p
CREATE DATABASE multi_auth_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 5: Environment Configuration

1. Copy `.env.example` to `.env`
2. Edit database settings:
   ```env
   database.default.hostname = localhost
   database.default.database = multi_auth_system
   database.default.username = root
   database.default.password =
   ```
3. Set base URL:
   ```env
   app.baseURL = http://localhost/multi_auth_system/
   ```

### Step 6: Generate Encryption Key

```bash
php spark key:generate
```

Copy the output and set in `.env`:
```env
encryption.key = BASE64:xxxxxxxxxxxxxxxxxxxx
```

### Step 7: Run Migrations

This creates all database tables:

```bash
php spark migrate
```

**Verify tables created**:
- users
- roles
- permissions
- user_roles
- role_permissions

### Step 8: Seed Database

Populate with default data:

```bash
php spark db:seed DatabaseSeeder
```

This creates:
- 4 default roles: Super Admin, Administrator, Manager, User
- Permissions for all common actions
- Admin user: admin@example.com / Admin@123

### Step 9: Set Permissions

```bash
chmod 755 writable
```

On Windows, ensure your user has full control over `writable/`.

### Step 10: Test

1. Start Apache/WAMP server
2. Navigate to: http://localhost/multi_auth_system/
3. Click "Login"
4. Use admin credentials
5. Explore the dashboard

---

## Configuration

### Environment Files

**Development** `.env`:
```env
CI_ENVIRONMENT = development
database.default.DBDebug = true
```

**Production**:
```env
CI_ENVIRONMENT = production
database.default.DBDebug = false
```

### Security Settings

**CSRF Protection** (enabled by default):
```env
security.tokenName = csrf_token
security.headerName = X-CSRF-TOKEN
security.cookieName = csrf_cookie
security.expires = 7200
security.regenerate = true
```

**Session Settings**:
```env
session.driver = FileHandler
session.cookie_name = ci_session
session.expiration = 7200
session.savePath = writable/session
session.matchIP = false
session.timeToUpdate = 300
```

**Cookies**:
```env
cookie.prefix = ci_
cookie.domain =
cookie.path = /
cookie.secure = false  # Set to true for HTTPS
cookie.httponly = false
cookie.samesite = Lax
```

### Custom Configuration

Create custom config files in `app/Config/`:

```php
<?php namespace Config;

class MyConfig extends BaseConfig
{
    public $items = [];
    public $default = 'value';
}
```

Access anywhere:
```php
$config = config('MyConfig');
echo $config->items['key'];
```

---

## Database Structure

### Table Schema

#### 1. Users Table

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Unique user ID |
| email | VARCHAR(255) | Unique email address |
| username | VARCHAR(100) | Unique username |
| password | VARCHAR(255) | Bcrypt hash |
| first_name | VARCHAR(100) | First name |
| last_name | VARCHAR(100) | Last name |
| phone | VARCHAR(20) | Optional phone |
| status | ENUM | active/inactive/suspended |
| email_verification_token | VARCHAR(100) | Email verification |
| email_verified_at | DATETIME | Verification timestamp |
| password_reset_token | VARCHAR(100) | Reset token |
| password_reset_expires | DATETIME | Token expiry |
| last_login | DATETIME | Last login time |
| last_login_ip | VARCHAR(45) | IP address |
| failed_login_attempts | INT | Security tracking |
| locked_until | DATETIME | Account lock expiry |
| created_at | TIMESTAMP | Creation time |
| updated_at | TIMESTAMP | Auto-updated |
| deleted_at | TIMESTAMP NULL | Soft delete |

**Indexes**: email, username, status, deleted_at

#### 2. Roles Table

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Unique role ID |
| name | VARCHAR(100) UNIQUE | Display name |
| slug | VARCHAR(100) UNIQUE | URL-safe identifier |
| description | TEXT NULL | Role description |
| is_system | TINYINT(1) | Protected system role |
| created_at | TIMESTAMP | Creation time |
| updated_at | TIMESTAMP | Auto-updated |

#### 3. Permissions Table

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Unique permission ID |
| name | VARCHAR(100) UNIQUE | Display name |
| slug | VARCHAR(150) UNIQUE | Format: resource.action |
| description | TEXT NULL | Permission description |
| resource | VARCHAR(100) | e.g., users, roles |
| action | ENUM | view/create/edit/delete/manage |
| is_system | TINYINT(1) | Protected system permission |
| created_at | TIMESTAMP | Creation time |
| updated_at | TIMESTAMP | Auto-updated |

**Indexes**: slug (for fast lookups), resource (for querying)

#### 4. Pivot Tables

**user_roles** (Many-to-Many):
- Composite PK: (user_id, role_id)
- FK: user_id → users.id (CASCADE)
- FK: role_id → roles.id (CASCADE)

**role_permissions** (Many-to-Many):
- Composite PK: (role_id, permission_id)
- FK: role_id → roles.id (CASCADE)
- FK: permission_id → permissions.id (CASCADE)

### Database Relationships

```
users ←→ user_roles ←→ roles ←→ role_permissions ←→ permissions
   │           │          │                           │
   └───────────┴──────────┴───────────────────────────┘
        (Many-to-Many relationships)
```

### Query Examples

Get user with all roles:
```php
$this->db->table('users')
    ->select('users.*, GROUP_CONCAT(roles.name) as role_names')
    ->join('user_roles', 'user_roles.user_id = users.id')
    ->join('roles', 'roles.id = user_roles.role_id')
    ->where('users.id', $userId)
    ->groupBy('users.id')
    ->get()
    ->getRow();
```

Get permissions for role:
```php
$this->db->table('roles')
    ->select('roles.*, permissions.slug as permission_slug')
    ->join('role_permissions', 'role_permissions.role_id = roles.id')
    ->join('permissions', 'permissions.id = role_permissions.permission_id')
    ->where('roles.id', $roleId)
    ->get()
    ->getResult();
```

---

## User Management

### Creating Users

**Programmatically**:
```php
$userService = new \App\Services\UserService();
$result = $userService->createUser([
    'email' => 'john@example.com',
    'username' => 'john',
    'password' => 'SecurePass123',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'phone' => '+1234567890',
    'status' => 'active'
], [1, 2]); // Role IDs

if ($result['success']) {
    $userId = $result['user_id'];
}
```

**Via Web UI**:
1. Log in as admin
2. Go to Users → Create User
3. Fill form with user details
4. Select roles
5. Submit

### Updating Users

```php
$userService->updateUser($userId, [
    'email' => 'newemail@example.com',
    'first_name' => 'Jane',
    'status' => 'inactive'
], [1]); // New role IDs
```

### Soft Deletes

When deleting a user:
- `deleted_at` is set to current timestamp
- User cannot login
- User data remains in database for audit

To restore:
```php
$userRepository->restore($userId);
```

To permanently delete:
```php
$userRepository->hardDelete($userId);
```

### User Statuses

- **active**: Can login and use system
- **inactive**: Cannot login (awaiting email verification or admin approval)
- **suspended**: Temporarily blocked (violation or security lock)

---

## RBAC System

### Permission Structure

Permissions use the format: `resource.action`

**Resources**:
- `dashboard` - Dashboard access
- `profile` - User profile
- `users` - User management
- `roles` - Role management
- `permissions` - Permission management
- `settings` - System settings

**Actions**:
- `view` - Read/list
- `create` - Create new
- `edit` - Update existing
- `delete` - Remove
- `manage` - All actions (shortcut)

### Checking Permissions

**In Code**:
```php
$rbacService = new \App\Services\RBACService();
$userId = session()->get('user_id');

// Check single permission
if ($rbacService->userHasPermission($userId, 'users.create')) {
    // Can create users
}

// Check by resource/action
if ($rbacService->userHasResourceAccess($userId, 'users', 'edit')) {
    // Can edit users
}

// Check any of multiple permissions
if ($rbacService->userHasAnyPermission($userId, [
    'users.view',
    'roles.view'
])) {
    // Has at least one
}

// Check all permissions
if ($rbacService->userHasAllPermissions($userId, [
    'users.view',
    'users.create'
])) {
    // Has all listed
}

// Super admin bypass
if ($rbacService->userHasRoleSlug($userId, 'super-admin')) {
    // Has all permissions automatically
}
```

**In Views**:
```php
<?php if (rbac()->userHasPermission(session()->get('user_id'), 'users.manage')): ?>
    <a href="/users" class="btn btn-primary">Manage Users</a>
<?php endif; ?>
```

**In Routes** (`app/Config/Routes.php`):
```php
// Require authentication
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
});

// Require specific permission
$routes->group('admin', ['filter' => 'rbac:users.manage'], function($routes) {
    $routes->get('/users', 'UserManagement::index');
});

// Multiple permissions (user needs ANY of them)
$routes->group('reports', ['filter' => 'rbac:reports.view|users.view'], function($routes) {
    $routes->get('/', 'Reports::index');
});
```

### Default Permissions

The DatabaseSeeder creates these default permissions:

#### Dashboard
- `dashboard.view`

#### Profile
- `profile.view`
- `profile.edit`

#### User Management
- `users.view`
- `users.create`
- `users.edit`
- `users.delete`
- `users.manage`

#### Role Management
- `roles.view`
- `roles.create`
- `roles.edit`
- `roles.delete`
- `roles.manage`

#### Permission Management
- `permissions.view`
- `permissions.create`
- `permissions.edit`
- `permissions.delete`
- `permissions.manage`

#### Settings
- `settings.view`
- `settings.edit`
- `settings.manage`

### Default Roles

1. **Super Admin** - All permissions automatically
2. **Administrator** - Dashboard, Profile, all User/Role/Settings permissions
3. **Manager** - Dashboard, Profile, View/Edit users (no delete)
4. **User** - Dashboard, Profile only

---

## Developer Guide

### Adding New Features

#### 1. Create Entity

```php
// app/Entities/Product.php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Product extends Entity
{
    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    protected $appends = ['full_name'];
}
```

#### 2. Create Model

```php
// app/Models/ProductModel.php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Product;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = Product::class;
    protected $allowedFields = ['name', 'price', 'status'];
    protected $useTimestamps = true;
}
```

#### 3. Create Repository

```php
// app/Repositories/ProductRepository.php
namespace App\Repositories;

use App\Entities\Product;

class ProductRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = model('ProductModel');
    }

    public function find(int $id): ?Product
    {
        return $this->model->find($id);
    }

    public function create(array $data): int
    {
        $this->model->insert($data);
        return $this->model->getInsertID();
    }

    // Other CRUD methods...
}
```

#### 4. Create Service

```php
// app/Services/ProductService.php
namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $data): array
    {
        // Business logic
        $productId = $this->productRepository->create($data);

        return ['success' => true, 'product_id' => $productId];
    }
}
```

#### 5. Create Controller

```php
// app/Controllers/Product.php
namespace App\Controllers;

class Product extends BaseController
{
    public function index()
    {
        $products = model('ProductModel')->findAll();
        return view('products/index', ['products' => $products]);
    }

    public function store()
    {
        $service = new \App\Services\ProductService();

        $result = $service->createProduct([
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
        ]);

        if ($result['success']) {
            return redirect()->to('/products')->with('success', 'Product created');
        }

        return redirect()->back()->with('error', 'Failed');
    }
}
```

#### 6. Create Views

Create view files in `app/Views/products/`.

#### 7. Add Routes

```php
// app/Config/Routes.php
$routes->group('products', ['filter' => 'rbac:products.view'], function($routes) {
    $routes->get('/', 'Product::index');
    $routes->post('/', 'Product::store');
});
```

### Testing New Features

1. Run migrations for new tables
2. Test in development environment
3. Add unit tests
4. Check security implications

---

## API Reference

### Authentication API

**Login** (AJAX)
```http
POST /login
Content-Type: application/x-www-form-urlencoded

email=user@example.com&password=pass123&remember=1
```

**Response**:
```json
{
  "success": true,
  "message": "Login successful",
  "redirect": "/dashboard"
}
```

**Errors**:
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

---

## Troubleshooting

### Common Issues

#### 1. Database Connection Error

**Error**: "Unable to connect to database"

**Solution**:
- Check database credentials in `.env`
- Verify MySQL is running
- Ensure database exists

#### 2. 404 Not Found

**Error**: Page not loading

**Solution**:
- Enable URL rewriting (`.htaccess`)
- Check `RewriteBase` matches your installation path
- Clear cache: `php spark cache:clear`
- Ensure `app.baseURL` is correct

#### 3. CSRF Token Mismatch

**Error**: "The action you have requested is not allowed."

**Solution**:
- Include `<?= csrf_field() ?>` in all forms
- Do not cache pages with forms
- Check session configuration

#### 4. Migration Fails

**Error**: Migration already exists or table exists

**Solution**:
```bash
# Check current migrations
php spark migrate:status

# Create fresh migration
php spark migrate:fresh
```

#### 5. Permission Denied

**Issue**: Access denied to authenticated users

**Solution**:
- Check user's role assignments
- Verify permissions are assigned to role
- Clear session: `session()->destroy()`

### Debug Mode

Enable debug mode in `.env`:
```env
CI_ENVIRONMENT = development
log.threshold = 4
```

Check logs in `writable/logs/` for detailed errors.

---

## Deployment

### Server Requirements

- PHP 7.4+ with extensions: mysqli, pdo_mysql, gd, openssl, mbstring
- MySQL 5.7+ or MariaDB 10.2+
- Apache 2.4+ or Nginx 1.18+
- 256MB+ RAM
- 100MB disk space

### Pre-Deployment Checklist

- [ ] Set `CI_ENVIRONMENT = production`
- [ ] Disable `database.default.DBDebug`
- [ ] Set `app.indexPage = index.php` (if not using clean URLs)
- [ ] Generate production encryption key
- [ ] Update `.env` database credentials
- [ ] Set secure cookie settings (HTTPS)
- [ ] Configure email service
- [ ] Enable caching
- [ ] Run database migrations on production
- [ ] Seed admin user securely
- [ ] Test all critical user flows

### Production .env

```env
CI_ENVIRONMENT = production
app.baseURL = https://yourdomain.com/
database.default.hostname = localhost
database.default.database = multi_auth_system
database.default.username = secure_user
database.default.password = strong_password
security.tokenName = csrf_token
cookie.secure = true
session.cookie_secure = true
```

### Backup Strategy

1. **Database Backup** (Daily):
   ```bash
   mysqldump -u username -p multi_auth_system > backup_$(date +%Y%m%d).sql
   ```

2. **File Backup** (Daily):
   ```bash
   tar -czf backup_files_$(date +%Y%m%d).tar.gz app/config writable/
   ```

3. **Rotation**: Keep last 7 backups

### HTTPS Setup

For Apache, add to `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Performance

- Enable query caching in database
- Use Redis or Memcached for sessions
- Optimize images
- Minify CSS/JS
- Enable GZip compression

---

## Best Practices

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable/function names
- Comment complex logic
- Keep methods under 30 lines
- Single responsibility per class

### Security

1. **Never** store passwords in plain text
2. **Always** validate and sanitize input
3. **Never** trust user input
4. Use parameterized queries only
5. Escape output in views
6. Keep dependencies updated
7. Implement rate limiting for login
8. Use strong session configuration

### Database

1. Use migrations for all schema changes
2. Index foreign keys and frequently queried columns
3. Use appropriate data types (VARCHAR length)
4. Normalize data (3NF)
5. Avoid NULL where possible

### Testing

1. Test all CRUD operations
2. Test RBAC logic thoroughly
3. Test with different user roles
4. Test edge cases (invalid input, missing data)
5. Test concurrent sessions

### Git Workflow

```
main (production-ready)
  ├── develop (integration)
  ├── feature/xxx
  ├── bugfix/xxx
  └── hotfix/xxx
```

### Logging

Log important events:
```php
log_message('info', 'User login: ' . $userId);
log_message('error', 'Failed login attempt: ' . $email);
```

---

## Help & Support

### Resources

- [CodeIgniter 4 User Guide](https://codeigniter.com/user_guide/)
- Project README.md
- Inline code comments
- API documentation above

### Getting Help

1. Check troubleshooting section
2. Review logs in `writable/logs/`
3. Search for error messages
4. Ask on CodeIgniter forums

---

**Document Version**: 1.0
**Last Updated**: April 2025
