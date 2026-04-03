<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="auth-container">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h2>Welcome Back</h2>
        <p>Sign in to your account</p>
    </div>

    <form action="<?= base_url('/login') ?>" method="post" class="auth-form" id="login-form">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="form-label">Email or Username</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-person"></i>
                </span>
                <input type="text"
                       class="form-control"
                       id="email"
                       name="email"
                       value="<?= old('email') ?>"
                       placeholder="Enter email or username"
                       required
                       autofocus>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-key"></i>
                </span>
                <input type="password"
                       class="form-control"
                       id="password"
                       name="password"
                       placeholder="Enter password"
                       required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </div>
    </form>

    <div class="auth-footer">
        <p class="mb-2">
            <a href="<?= base_url('/forgot-password') ?>" class="text-decoration-none">
                <i class="bi bi-question-circle"></i> Forgot your password?
            </a>
        </p>
        <p class="mb-0">
            Don't have an account?
            <a href="<?= base_url('/register') ?>" class="text-decoration-none fw-bold">Register here</a>
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

    // Form validation
    $('#login-form').on('submit', function(e) {
        let valid = true;
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        return valid;
    });
});
</script>
<?= $this->endSection() ?>
