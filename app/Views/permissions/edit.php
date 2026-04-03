<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Edit Permission</h2>
    <a href="<?= base_url('/permissions') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Permissions
    </a>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('/permissions/update/' . $permission->id) ?>" method="post" id="permissionForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name *</label>
                        <input type="text"
                               class="form-control"
                               id="name"
                               name="name"
                               value="<?= old('name', $permission->name) ?>"
                               required>
                        <?php if ($permission->is_system): ?>
                            <div class="form-text text-warning">
                                <i class="bi bi-exclamation-triangle"></i> This is a system permission
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="resource" class="form-label">Resource *</label>
                        <input type="text"
                               class="form-control"
                               id="resource"
                               name="resource"
                               value="<?= old('resource', $permission->resource) ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="action" class="form-label">Action *</label>
                        <select class="form-select" id="action" name="action" required>
                            <option value="">Select Action</option>
                            <option value="view" <?= old('action', $permission->action) == 'view' ? 'selected' : '' ?>>View</option>
                            <option value="create" <?= old('action', $permission->action) == 'create' ? 'selected' : '' ?>>Create</option>
                            <option value="edit" <?= old('action', $permission->action) == 'edit' ? 'selected' : '' ?>>Edit</option>
                            <option value="delete" <?= old('action', $permission->action) == 'delete' ? 'selected' : '' ?>>Delete</option>
                            <option value="manage" <?= old('action', $permission->action) == 'manage' ? 'selected' : '' ?>>Manage</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"
                                  id="description"
                                  name="description"
                                  rows="3"><?= old('description', $permission->description) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/permissions') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
