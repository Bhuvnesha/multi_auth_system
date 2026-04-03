<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Multi-Auth System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/dashboard') ?>">
                <i class="bi bi-shield-lock-fill"></i>
                Multi-Auth System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (session()->get('logged_in')): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        <?php if (rbac()->userHasPermission(session()->get('user_id'), 'users.manage')): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-people"></i> Users
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= base_url('/users') ?>">View Users</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('/users/create') ?>">Create User</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (rbac()->userHasPermission(session()->get('user_id'), 'roles.manage')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('/roles') ?>">
                                    <i class="bi bi-tags"></i> Roles
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (rbac()->userHasPermission(session()->get('user_id'), 'permissions.manage')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('/permissions') ?>">
                                    <i class="bi bi-key"></i> Permissions
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (rbac()->userHasRoleSlug(session()->get('user_id'), 'super-admin')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('/admin/dashboard') ?>">
                                    <i class="bi bi-gear"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?= esc(session()->get('user_name')) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= base_url('/profile') ?>">
                                    <i class="bi bi-person"></i> Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= base_url('/logout') ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                    <?= form_open(base_url('/logout'), ['id' => 'logout-form', 'style' => 'display:none;']) ?>
                                    <?= csrf_field() ?>
                                    <?= form_close() ?>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/register') ?>">Register</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="container py-4">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> Multi-Auth System. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
