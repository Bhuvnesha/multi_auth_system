<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Edit User</h2>
    <a href="<?= base_url('/users') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('/users/update/' . $user->id) ?>" method="post" id="userForm">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text"
                                   class="form-control"
                                   id="first_name"
                                   name="first_name"
                                   value="<?= old('first_name', $user->first_name) ?>"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text"
                                   class="form-control"
                                   id="last_name"
                                   name="last_name"
                                   value="<?= old('last_name', $user->last_name) ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               value="<?= old('email', $user->email) ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text"
                               class="form-control"
                               id="username"
                               name="username"
                               value="<?= old('username', $user->username) ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone (Optional)</label>
                        <input type="tel"
                               class="form-control"
                               id="phone"
                               name="phone"
                               value="<?= old('phone', $user->phone) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= old('status', $user->status) == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status', $user->status) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= old('status', $user->status) == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password">
                        <div class="form-text">Minimum 8 characters if changed</div>
                    </div>

                    <?php if ($user->id != auth()->getCurrentUserId()): ?>
                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Roles</label>
                            <?php if (empty($roles)): ?>
                                <p class="text-muted">No roles available.</p>
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
                                                       <?= in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' ?>>
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
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You cannot edit your own roles to prevent privilege escalation.
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between">
                        <a href="/users" class="btn btn-outline-secondary">Cancel</a>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
