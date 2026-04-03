<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-gear"></i> Admin Dashboard</h2>
        <p class="text-muted">System overview and statistics</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <small>Total Users</small>
            <h3><?= number_format($stats['total_users']) ?></h3>
            <div>Active: <?= number_format($stats['active_users']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <small>Roles</small>
            <h3><?= number_format($stats['total_roles']) ?></h3>
            <div>Permissions: <?= number_format($stats['total_permissions']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <small>User Status</small>
            <div class="mt-2">
                <div>Active: <?= number_format($stats['active_users']) ?></div>
                <div>Inactive: <?= number_format($stats['inactive_users']) ?></div>
                <div>Suspended: <?= number_format($stats['suspended_users']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <small>Quick Actions</small>
            <div class="mt-2">
                <a href="/users" class="btn btn-sm btn-light text-primary">Manage Users</a>
                <a href="/roles" class="btn btn-sm btn-light text-primary mt-1">Manage Roles</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Recent Registrations (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentRegistrations)): ?>
                    <p class="text-muted">No registrations in the last 7 days.</p>
                <?php else: ?>
                    <canvas id="registrationsChart" height="200"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Logins</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentLogins)): ?>
                    <p class="text-muted">No login activity recorded.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLogins as $login): ?>
                                    <tr>
                                        <td>
                                            <?= esc($login['username']) ?><br>
                                            <small class="text-muted"><?= esc($login['email']) ?></small>
                                        </td>
                                        <td>
                                            <?= $login['last_login'] ? date('M d H:i', strtotime($login['last_login'])) : 'Never' ?><br>
                                            <small class="text-muted"><?= esc($login['last_login_ip'] ?? '') ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> User Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> System Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td><strong>PHP Version</strong></td>
                            <td><?= phpversion() ?></td>
                        </tr>
                        <tr>
                            <td><strong>CodeIgniter</strong></td>
                            <td>4.x</td>
                        </tr>
                        <tr>
                            <td><strong>Environment</strong></td>
                            <td><?= ENVIRONMENT ?></td>
                        </tr>
                        <tr>
                            <td><strong>Time</strong></td>
                            <td><?= date('Y-m-d H:i:s') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Registrations Chart
    <?php if (!empty($recentRegistrations)): ?>
    const regCtx = document.getElementById('registrationsChart').getContext('2d');
    const regLabels = <?= json_encode(array_reverse(array_column($recentRegistrations, 'date'))) ?>;
    const regData = <?= json_encode(array_reverse(array_column($recentRegistrations, 'count'))) ?>;

    new Chart(regCtx, {
        type: 'line',
        data: {
            labels: regLabels,
            datasets: [{
                label: 'Registrations',
                data: regData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    <?php endif; ?>

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive', 'Suspended', 'Deleted'],
            datasets: [{
                data: [
                    <?= $stats['active_users'] ?>,
                    <?= $stats['inactive_users'] ?>,
                    <?= $stats['suspended_users'] ?>,
                    <?= $stats['soft_deleted_users'] ?? 0 ?>
                ],
                backgroundColor: [
                    '#198754',
                    '#0dcaf0',
                    '#ffc107',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
<?= $this->endSection() ?>
