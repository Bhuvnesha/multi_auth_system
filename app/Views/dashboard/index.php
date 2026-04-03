<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">
            <i class="bi bi-speedometer2"></i> Dashboard
        </h2>
        <p class="text-muted">Welcome, <?= esc($user->full_name) ?>!</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-person-badge card-icon"></i>
                <h6 class="text-muted">Your Roles</h6>
                <div>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <span class="badge bg-primary role-badge"><?= esc($role) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted">No roles assigned</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-shield-check card-icon"></i>
                <h6 class="text-muted">Active Permissions</h6>
                <h3 class="mt-2"><?= count($permissions ?? []) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-<?= $user->isEmailVerified() ? 'check-circle' : 'clock' ?> card-icon"></i>
                <h6 class="text-muted">Email Status</h6>
                <div>
                    <?php if ($user->isEmailVerified()): ?>
                        <span class="badge bg-success">Verified</span>
                    <?php else: ?>
                        <span class="badge bg-warning">Not Verified</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Account Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Username:</strong> <?= esc($user->username) ?></p>
                        <p><strong>Email:</strong> <?= esc($user->email) ?></p>
                        <p><strong>Phone:</strong> <?= esc($user->phone ?: 'Not provided') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong>
                            <span class="status-badge status-<?= $user->status ?>">
                                <?= ucfirst($user->status) ?>
                            </span>
                        </p>
                        <p><strong>Member Since:</strong> <?= $user->created_at->format('M d, Y') ?></p>
                        <p><strong>Last Login:</strong>
                            <?= $user->last_login ? $user->last_login->format('M d, Y H:i') : 'Never' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning"></i> Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/profile" class="btn btn-outline-primary">
                        <i class="bi bi-person"></i> Edit Profile
                    </a>
                    <a href="/profile/change-password" class="btn btn-outline-secondary">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($can_manage_users) && $can_manage_users): ?>
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up"></i> System Statistics
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">You have administrative access. View the complete system statistics in the <a href="/admin/dashboard">Admin Dashboard</a>.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
