<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Create Permission</h2>
    <a href="<?= base_url('/permissions') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Permissions
    </a>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('/permissions/store') ?>" method="post" id="permissionForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name *</label>
                        <input type="text"
                               class="form-control"
                               id="name"
                               name="name"
                               value="<?= old('name') ?>"
                               required>
                        <div class="form-text">Human-readable name (e.g., "View Users")</div>
                    </div>

                    <div class="mb-3">
                        <label for="resource" class="form-label">Resource *</label>
                        <input type="text"
                               class="form-control"
                               id="resource"
                               name="resource"
                               value="<?= old('resource') ?>"
                               required>
                        <div class="form-text">The resource this permission applies to (e.g., users, roles, permissions)</div>
                    </div>

                    <div class="mb-3">
                        <label for="action" class="form-label">Action *</label>
                        <select class="form-select" id="action" name="action" required>
                            <option value="">Select Action</option>
                            <option value="view" <?= old('action') == 'view' ? 'selected' : '' ?>>View</option>
                            <option value="create" <?= old('action') == 'create' ? 'selected' : '' ?>>Create</option>
                            <option value="edit" <?= old('action') == 'edit' ? 'selected' : '' ?>>Edit</option>
                            <option value="delete" <?= old('action') == 'delete' ? 'selected' : '' ?>>Delete</option>
                            <option value="manage" <?= old('action') == 'manage' ? 'selected' : '' ?>>Manage</option>
                        </select>
                        <div class="form-text">Note: Manage includes all other actions</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"
                                  id="description"
                                  name="description"
                                  rows="3"><?= old('description') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/permissions') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
