<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="auth-container">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="bi bi-key-fill"></i>
        </div>
        <h2>Reset Password</h2>
        <p>Enter your new password</p>
    </div>

    <form action="<?= base_url('/reset-password') ?>" method="post" class="auth-form" id="reset-form">
        <?= csrf_field() ?>

        <input type="hidden" name="token" value="<?= esc($token) ?>">

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-key"></i>
                </span>
                <input type="password"
                       class="form-control"
                       id="password"
                       name="password"
                       placeholder="At least 8 characters"
                       required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <div class="password-strength mt-2">
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="text-muted strength-text"></small>
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-key-fill"></i>
                </span>
                <input type="password"
                       class="form-control"
                       id="password_confirm"
                       name="password_confirm"
                       placeholder="Re-enter new password"
                       required>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle"></i> Reset Password
            </button>
            <a href="<?= base_url('/login') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Toggle password visibility
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
