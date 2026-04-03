<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-key"></i> Change Password</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/profile/change-password') ?>" method="post" id="changePasswordForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control"
                                   id="current_password"
                                   name="current_password"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrent">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control"
                                   id="new_password"
                                   name="new_password"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNew">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted strength-text"></small>
                        </div>
                        <div class="form-text">At least 8 characters</div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirm" class="form-label">Confirm New Password</label>
                        <input type="password"
                               class="form-control"
                               id="new_password_confirm"
                               name="new_password_confirm"
                               required>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= base_url('/profile') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Change Password
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
    $('#toggleCurrent').on('click', function() {
        const input = $('#current_password');
        togglePassword(input, $(this));
    });

    $('#toggleNew').on('click', function() {
        const input = $('#new_password');
        togglePassword(input, $(this));
    });

    function togglePassword(input, button) {
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            button.find('i').removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            button.find('i').removeClass('bi-eye-slash').addClass('bi-eye');
        }
    }
});
</script>
<?= $this->endSection() ?>
