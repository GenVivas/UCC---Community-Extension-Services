<?php
// Prepare chart data
$areaLabels  = array_column($projectsByArea ?? [], 'area');
$areaTotals  = array_column($projectsByArea ?? [], 'total');

$months      = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$monthData   = array_fill(0, 12, 0);
foreach ($monthlyActivities ?? [] as $row) {
    $monthData[(int)$row['month'] - 1] = (int)$row['total'];
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h2>Welcome back, <?= e(Auth::user()['first_name']) ?> 👋</h2>
        <p>Here's what's happening with the Community Extension Services today.</p>
    </div>
    <?php if (Auth::can('proposals.create')): ?>
    <a href="<?= url('proposals/create') ?>" class="btn-ces-primary">
        <i class="fa fa-plus"></i> New Proposal
    </a>
    <?php endif; ?>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa fa-file-alt"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['total_proposals'] ?></div>
                <div class="stat-label">Total Proposals</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa fa-clock"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['pending_proposals'] ?></div>
                <div class="stat-label">Pending Approval</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa fa-spinner"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['ongoing_projects'] ?></div>
                <div class="stat-label">Ongoing Projects</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fa fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['completed_projects'] ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fa fa-users"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($stats['total_participants']) ?></div>
                <div class="stat-label">Beneficiaries</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fa fa-handshake"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['total_linkages'] ?></div>
                <div class="stat-label">Partners</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Monthly Activities Chart -->
    <div class="col-lg-8">
        <div class="ces-card h-100">
            <div class="ces-card-header">
                <h3 class="card-title"><i class="fa fa-chart-bar me-2 text-success"></i>Monthly Activities (<?= date('Y') ?>)</h3>
            </div>
            <div class="ces-card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <!-- Projects by Area Doughnut -->
    <div class="col-lg-4">
        <div class="ces-card h-100">
            <div class="ces-card-header">
                <h3 class="card-title"><i class="fa fa-chart-pie me-2 text-success"></i>Projects by Area</h3>
            </div>
            <div class="ces-card-body d-flex align-items-center justify-content-center">
                <?php if (empty($projectsByArea)): ?>
                    <div class="empty-state"><i class="fa fa-chart-pie"></i><p>No project data yet.</p></div>
                <?php else: ?>
                    <canvas id="areaChart" height="220"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Proposals + Upcoming Activities -->
<div class="row g-3">
    <!-- Recent Proposals -->
    <div class="col-lg-7">
        <div class="ces-card">
            <div class="ces-card-header">
                <h3 class="card-title"><i class="fa fa-file-alt me-2 text-success"></i>Recent Proposals</h3>
                <a href="<?= url('proposals') ?>" class="btn-ces-outline" style="font-size:.78rem;padding:5px 12px;">View All</a>
            </div>
            <div class="ces-card-body p-0">
                <?php if (empty($recentProposals)): ?>
                    <div class="empty-state"><i class="fa fa-file-alt"></i><p>No proposals yet.</p></div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="ces-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Program</th>
                                <th>Area</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentProposals as $p): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('proposals/view/' . $p['id']) ?>" class="fw-600" style="font-weight:600;">
                                        <?= e(truncate($p['title'], 40)) ?>
                                    </a>
                                    <div style="font-size:.75rem;color:#888;"><?= e($p['first_name'] . ' ' . $p['last_name']) ?></div>
                                </td>
                                <td><span class="badge bg-secondary"><?= e($p['program_code']) ?></span></td>
                                <td style="font-size:.8rem;"><?= e($p['target_area']) ?></td>
                                <td><?= statusBadge($p['status']) ?></td>
                                <td style="font-size:.78rem;color:#888;"><?= formatDate($p['created_at'], 'M d, Y') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Activities -->
    <div class="col-lg-5">
        <div class="ces-card">
            <div class="ces-card-header">
                <h3 class="card-title"><i class="fa fa-calendar-check me-2 text-success"></i>Upcoming Activities</h3>
                <a href="<?= url('activities') ?>" class="btn-ces-outline" style="font-size:.78rem;padding:5px 12px;">View All</a>
            </div>
            <div class="ces-card-body p-0">
                <?php if (empty($upcomingActivities)): ?>
                    <div class="empty-state"><i class="fa fa-calendar"></i><p>No upcoming activities.</p></div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($upcomingActivities as $act): ?>
                    <li class="list-group-item px-4 py-3 border-0 border-bottom">
                        <div class="d-flex align-items-start gap-3">
                            <div class="text-center" style="min-width:44px;">
                                <div style="font-size:1.1rem;font-weight:700;color:var(--ces-green);line-height:1;">
                                    <?= date('d', strtotime($act['activity_date'])) ?>
                                </div>
                                <div style="font-size:.65rem;text-transform:uppercase;color:#aaa;">
                                    <?= date('M', strtotime($act['activity_date'])) ?>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div style="font-weight:600;font-size:.85rem;"><?= e(truncate($act['title'], 38)) ?></div>
                                <div style="font-size:.75rem;color:#888;"><?= e(truncate($act['project_title'], 38)) ?></div>
                                <?php if ($act['venue']): ?>
                                <div style="font-size:.72rem;color:#aaa;"><i class="fa fa-map-marker-alt me-1"></i><?= e($act['venue']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div><?= statusBadge($act['status']) ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN fallback (local) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    // Monthly bar chart
    const monthCtx = document.getElementById('monthlyChart');
    if (monthCtx) {
        new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Activities',
                    data: <?= json_encode($monthData) ?>,
                    backgroundColor: 'rgba(76,175,80,0.7)',
                    borderColor: 'rgba(76,175,80,1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Area doughnut
    const areaCtx = document.getElementById('areaChart');
    if (areaCtx) {
        new Chart(areaCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($areaLabels) ?>,
                datasets: [{
                    data: <?= json_encode($areaTotals) ?>,
                    backgroundColor: [
                        '#1a4d2e','#4caf50','#2196f3','#ff9800',
                        '#9c27b0','#00bcd4','#f44336'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 } } }
                },
                cutout: '65%'
            }
        });
    }
})();
</script>
