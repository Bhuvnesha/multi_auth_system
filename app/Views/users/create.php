<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Create User</h2>
    <a href="<?= base_url('/users') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('/users/store') ?>" method="post" id="userForm">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text"
                                   class="form-control"
                                   id="first_name"
                                   name="first_name"
                                   value="<?= old('first_name') ?>"
                                   required>
                            <?php if (session()->getFlashdata('errors')['first_name'] ?? false): ?>
                                <div class="text-danger small"><?= session()->getFlashdata('errors')['first_name'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text"
                                   class="form-control"
                                   id="last_name"
                                   name="last_name"
                                   value="<?= old('last_name') ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               value="<?= old('email') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text"
                               class="form-control"
                               id="username"
                               name="username"
                               value="<?= old('username') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone (Optional)</label>
                        <input type="tel"
                               class="form-control"
                               id="phone"
                               name="phone"
                               value="<?= old('phone') ?>">
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="password"
                                       name="password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" id="password-strength" role="progressbar"></div>
                                </div>
                                <small class="text-muted strength-text"></small>
                            </div>
                            <div class="form-text">At least 8 characters</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password *</label>
                            <input type="password"
                                   class="form-control"
                                   id="password_confirm"
                                   name="password_confirm"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="inactive" <?= old('status', 'inactive') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="suspended" <?= old('status') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <?php if (empty($roles)): ?>
                            <p class="text-muted">No roles available. <a href="<?= base_url('/roles/create') ?>">Create a role first</a>.</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($roles as $role): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="roles[]"
                                                   id="role_<?= $role->id ?>"
                                                   value="<?= $role->id ?>"
                                                   <?= in_array($role->id, old('roles', [])) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="role_<?= $role->id ?>">
                                                <?= esc($role->name) ?>
                                                <?php if ($role->is_system): ?>
                                                    <span class="badge bg-info">System</span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/users') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        const icon = $(this).find('i');

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
});
</script>
<?= $this->endSection() ?>
