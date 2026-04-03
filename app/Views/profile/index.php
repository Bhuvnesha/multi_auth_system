<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-avatar">
                    <?= strtoupper(substr($user->first_name, 0, 1)) ?>
                </div>
                <h4><?= esc($user->full_name) ?></h4>
                <p class="text-muted">@<?= esc($user->username) ?></p>
                <div class="mt-3">
                    <?php foreach (explode(', ', $roles) as $role): ?>
                        <?php if ($role): ?>
                            <span class="badge bg-primary"><?= esc($role) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3">
                    <a href="<?= base_url('/profile/change-password') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profile Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/profile/update') ?>" method="post" id="profileForm">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text"
                                   class="form-control"
                                   id="first_name"
                                   name="first_name"
                                   value="<?= old('first_name', $user->first_name) ?>"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text"
                                   class="form-control"
                                   id="last_name"
                                   name="last_name"
                                   value="<?= old('last_name', $user->last_name) ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               value="<?= old('email', $user->email) ?>"
                               required>
                        <?php if (!$user->email_verified_at): ?>
                            <div class="form-text text-warning">
                                <i class="bi bi-exclamation-triangle"></i> Email not verified
                            </div>
                        <?php else: ?>
                            <div class="form-text text-success">
                                <i class="bi bi-check-circle"></i> Verified on <?= $user->email_verified_at->format('M d, Y') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel"
                               class="form-control"
                               id="phone"
                               name="phone"
                               value="<?= old('phone', $user->phone) ?>">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
