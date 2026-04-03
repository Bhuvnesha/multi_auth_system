<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-tags"></i> Role Management</h2>
    <a href="<?= base_url('/roles/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Role
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($roles)): ?>
            <div class="text-center py-4">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="mt-3 text-muted">No roles found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><?= $role->id ?></td>
                                <td>
                                    <strong><?= esc($role->name) ?></strong>
                                    <?php if ($role->is_system): ?>
                                        <span class="badge bg-info">System</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= esc($role->slug) ?></code></td>
                                <td><?= esc($role->description ?: '-') ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= $role->permission_count ?> permissions</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $role->user_count ?> users</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm action-buttons">
                                        <a href="<?= base_url('/roles/edit/' . $role->id) ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (!$role->is_system && $role->user_count == 0): ?>
                                            <button class="btn btn-outline-danger delete-confirm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-delete-url="/roles/delete/<?= $role->id ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this role? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post" id="deleteForm" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Delete Role</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#deleteModal').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const deleteUrl = button.data('delete-url');
        const form = $('#deleteForm');
        form.attr('action', deleteUrl);
    });
});
</script>
<?= $this->endSection() ?>
