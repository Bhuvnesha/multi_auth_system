<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="auth-container">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="bi bi-person-plus"></i>
        </div>
        <h2>Create Account</h2>
        <p>Register for a new account</p>
    </div>

    <form action="<?= base_url('/register') ?>" method="post" class="auth-form" id="register-form">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name *</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text"
                           class="form-control"
                           id="first_name"
                           name="first_name"
                           value="<?= old('first_name') ?>"
                           placeholder="John"
                           required>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name *</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text"
                           class="form-control"
                           id="last_name"
                           name="last_name"
                           value="<?= old('last_name') ?>"
                           placeholder="Doe"
                           required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="email"
                       class="form-control"
                       id="email"
                       name="email"
                       value="<?= old('email') ?>"
                       placeholder="john@example.com"
                       required>
            </div>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username *</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-at"></i>
                </span>
                <input type="text"
                       class="form-control"
                       id="username"
                       name="username"
                       value="<?= old('username') ?>"
                       placeholder="johndoe"
                       required>
            </div>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone (Optional)</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-telephone"></i>
                </span>
                <input type="tel"
                       class="form-control"
                       id="phone"
                       name="phone"
                       value="<?= old('phone') ?>"
                       placeholder="+1 (555) 123-4567">
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password *</label>
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
            <div class="form-text">
                Password must be at least 8 characters with uppercase, lowercase, and numbers.
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirm" class="form-label">Confirm Password *</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-key-fill"></i>
                </span>
                <input type="password"
                       class="form-control"
                       id="password_confirm"
                       name="password_confirm"
                       placeholder="Re-enter password"
                       required>
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="terms" name="terms" value="1" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="#" target="_blank">Terms and Conditions</a>
            </label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus-fill"></i> Create Account
            </button>
        </div>
    </form>

    <div class="auth-footer">
        <p class="mb-0">
            Already have an account?
            <a href="<?= base_url('/login') ?>" class="text-decoration-none fw-bold">Login here</a>
        </p>
    </div>
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
