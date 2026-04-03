<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="auth-container">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="bi bi-key"></i>
        </div>
        <h2>Forgot Password</h2>
        <p>Enter your email to reset your password</p>
    </div>

    <form action="<?= base_url('/forgot-password') ?>" method="post" class="auth-form" id="forgot-form">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="email"
                       class="form-control"
                       id="email"
                       name="email"
                       value="<?= old('email') ?>"
                       placeholder="Enter your email"
                       required
                       autofocus>
            </div>
            <div class="form-text">
                We'll send you a link to reset your password.
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-send"></i> Send Reset Link
            </button>
            <a href="<?= base_url('/login') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
