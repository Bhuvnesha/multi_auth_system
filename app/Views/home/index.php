<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <div class="mb-5">
            <i class="bi bi-shield-lock-fill" style="font-size: 5rem; color: var(--primary-color);"></i>
        </div>
        <h1 class="display-4 fw-bold mb-3">Multi-Auth System</h1>
        <p class="lead text-muted mb-4">
            Professional authentication system with role-based access control (RBAC).
            Secure, scalable, and easy to manage.
        </p>

        <div class="d-flex gap-3 justify-content-center mb-5">
            <a href="/login" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
            <a href="/register" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-person-plus"></i> Register
            </a>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card h-100 dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-person-check card-icon"></i>
                <h5 class="card-title">Role-Based Access</h5>
                <p class="card-text">
                    Fine-grained permission control with role inheritance. Assign permissions to roles and roles to users.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-shield-check card-icon"></i>
                <h5 class="card-title">Secure Authentication</h5>
                <p class="card-text">
                    Industry-standard security with password hashing, CSRF protection, and session management.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-people card-icon"></i>
                <h5 class="card-title">User Management</h5>
                <p class="card-text">
                    Complete CRUD operations for users, roles, and permissions. Soft deletes included.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Features</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> MVC Architecture</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Repository Pattern</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Service Layer</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Entity Classes</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> CSRF Protection</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> XSS Prevention</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Database Migrations</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Seeders</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Bootstrap 5 UI</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Responsive Design</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Password Reset</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Remember Me</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
