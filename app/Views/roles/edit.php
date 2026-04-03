<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Edit Role</h2>
    <a href="<?= base_url('/roles') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Roles
    </a>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('/roles/update/' . $role->id) ?>" method="post" id="roleForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name *</label>
                        <input type="text"
                               class="form-control"
                               id="name"
                               name="name"
                               value="<?= old('name', $role->name) ?>"
                               required>
                        <?php if ($role->is_system): ?>
                            <div class="form-text text-warning">
                                <i class="bi bi-exclamation-triangle"></i> This is a system role
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"
                                  id="description"
                                  name="description"
                                  rows="3"><?= old('description', $role->description) ?></textarea>
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
                                                   data-resource="<?= $permission->resource ?>"
                                                   <?= in_array($permission->id, $currentPermissionIds) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="permission_<?= $permission->id ?>">
                                                <?= $permission->name ?>
                                                <small class="text-muted">(<?= $permission->action ?>)</small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/roles" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Role
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
    // Select all permissions
    $('#select-all-permissions').change(function() {
        const checked = $(this).prop('checked');
        $('.permission-checkbox').prop('checked', checked);
    });

    // Check if all permissions are already selected
    const allChecked = $('.permission-checkbox').length === $('.permission-checkbox:checked').length;
    $('#select-all-permissions').prop('checked', allChecked);
});
</script>
<?= $this->endSection() ?>
