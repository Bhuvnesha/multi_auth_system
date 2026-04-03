<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Create Role</h2>
    <a href="<?= base_url('/roles') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Roles
    </a>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('/roles/store') ?>" method="post" id="roleForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name *</label>
                        <input type="text"
                               class="form-control"
                               id="name"
                               name="name"
                               value="<?= old('name') ?>"
                               required>
                        <div class="form-text">Enter a descriptive name for this role</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"
                                  id="description"
                                  name="description"
                                  rows="3"><?= old('description') ?></textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               id="is_system"
                               name="is_system"
                               value="1"
                               <?= old('is_system') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_system">
                            System Role
                            <small class="text-muted d-block">
                                System roles are protected and cannot be deleted
                            </small>
                        </label>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">Permissions</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="select-all-permissions">
                                <label class="form-check-label" for="select-all-permissions">
                                    Select All
                                </label>
                            </div>
                        </div>

                        <?php if (empty($groupedPermissions)): ?>
                            <p class="text-muted">No permissions available. <a href="<?= base_url('/permissions/create') ?>">Create permissions first</a>.</p>
                        <?php else: ?>
                            <?php foreach ($groupedPermissions as $resource => $permissions): ?>
                                <div class="permission-group">
                                    <h6><?= ucfirst($resource) ?></h6>
                                    <div class="permission-group-body">
                                        <?php foreach ($permissions as $permission): ?>
                                            <div class="permission-item form-check">
                                                <input type="checkbox"
                                                       class="form-check-input permission-checkbox"
                                                       name="permissions[]"
                                                       id="permission_<?= $permission->id ?>"
                                                       value="<?= $permission->id ?>"
                                                       data-resource="<?= $permission->resource ?>">
                                                <label class="form-check-label" for="permission_<?= $permission->id ?>">
                                                    <?= $permission->name ?>
                                                    <small class="text-muted">(<?= $permission->action ?>)</small>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/roles') ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
