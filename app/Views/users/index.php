<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> User Management</h2>
    <a href="<?= base_url('/users/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create User
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text"
                           class="form-control"
                           name="search"
                           value="<?= esc($search) ?>"
                           placeholder="Search users...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $status == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="role">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $roleItem): ?>
                        <option value="<?= $roleItem->id ?>" <?= $roleItem->id == $role ? 'selected' : '' ?>>
                            <?= esc($roleItem->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-4">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="mt-3 text-muted">No users found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user->id ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($user->first_name, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= esc($user->first_name . ' ' . $user->last_name) ?></strong><br>
                                            <small class="text-muted">@<?= esc($user->username) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($user->email) ?></td>
                                <td>
                                    <?php if (!empty($user->roles)): ?>
                                        <?php foreach ($user->roles as $role): ?>
                                            <span class="badge bg-secondary role-badge"><?= esc($role['name']) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">No roles</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $user->status ?>">
                                        <?= ucfirst($user->status) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user->last_login): ?>
                                        <?= $user->last_login->format('M d, Y') ?><br>
                                        <small class="text-muted"><?= $user->last_login_ip ?? '' ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm action-buttons">
                                        <a href="<?= base_url('/users/edit/' . $user->id) ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-outline-<?= $user->status == 'active' ? 'warning' : 'success' ?> toggle-status"
                                                data-user-id="<?= $user->id ?>"
                                                data-current-status="<?= $user->status ?>">
                                            <i class="bi bi-<?= $user->status == 'active' ? 'pause' : 'play' ?>"></i>
                                        </button>
                                        <button class="btn btn-outline-danger delete-confirm"
                                                data-user-id="<?= $user->id ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-delete-url="<?= base_url('/users/delete/' . $user->id) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager)): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <?= $pager->links('default', 'default_full') ?>
                </nav>
            <?php endif; ?>
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
                Are you sure you want to delete this user? This action can be undone (soft delete).
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post" id="deleteUserForm" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Toggle user status
    $('.toggle-status').on('click', function() {
        const userId = $(this).data('user-id');
        const currentStatus = $(this).data('current-status');
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        const button = $(this);

        $.ajax({
            url: '/users/toggle-status/' + userId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    button.data('current-status', newStatus);
                    button.find('i').removeClass('bi-pause bi-play').addClass(newStatus === 'active' ? 'bi-pause' : 'bi-play');
                    button.closest('tr').find('.status-badge')
                        .removeClass('status-active status-inactive status-suspended')
                        .addClass('status-' + newStatus)
                        .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    showAlert('success', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to update status');
            }
        });
    });

    // Set delete form action
    $('#deleteModal').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const deleteUrl = button.data('delete-url');
        const form = $('#deleteUserForm');
        form.attr('action', deleteUrl);
    });
});
</script>
<?= $this->endSection() ?>
