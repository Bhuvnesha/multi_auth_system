# Quick Installation Guide

## 5-Minute Setup

### Step 1: Download & Extract
Place project in `D:\wamp64\www\multi_auth_system\`

### Step 2: Install Dependencies
```bash
cd D:\wamp64\www\multi_auth_system
composer install
```

### Step 3: Configure Database

Edit `.env` file:

```env
database.default.database = multi_auth_system
database.default.username = root
database.default.password = your_password
```

Create database in MySQL:
```sql
CREATE DATABASE multi_auth_system;
```

### Step 4: Generate Key
```bash
php spark key:generate
```
Copy output to `.env`:
```env
encryption.key = PASTE_KEY_HERE
```

### Step 5: Run Migrations & Seeds
```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

### Step 6: Access Application
Open browser to: `http://localhost/multi_auth_system/`

Login with:
- Email: `admin@example.com`
- Password: `Admin@123`

---

## Post-Installation Checklist

- [ ] Change admin password immediately
- [ ] Configure email settings in `app/Config/Email.php`
- [ ] Set proper file permissions (`writable/` should be writable)
- [ ] For production, set `CI_ENVIRONMENT = production`
- [ ] Configure HTTPS
- [ ] Set up regular database backups
- [ ] Review security settings in `.env`

## Default Data

The seeder creates:

**Roles**:
1. Super Admin (full access)
2. Administrator (user/role/settings)
3. Manager (limited access)
4. User (basic access)

**Permissions**:
All CRUD permissions for: dashboard, profile, users, roles, permissions, settings

**Admin User**:
- Email: admin@example.com
- Password: Admin@123
- Role: Super Admin

---

## Need Help?

See `README.md` for full documentation or `USER_MANUAL.md` for user guides.
