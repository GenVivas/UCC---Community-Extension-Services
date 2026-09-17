<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h2>Welcome back, <?= e(Auth::user()['first_name']) ?> 👋</h2>
        <p>Manage your program's CES projects and activities.</p>
    </div>
    <?php if (Auth::can('proposals.create')): ?>
    <a href="<?= url('proposals/create') ?>" class="btn-ces-primary">
        <i class="fa fa-plus"></i> New Proposal
    </a>
    <?php endif; ?>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa fa-file-alt"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['total'] ?></div>
                <div class="stat-label">My Proposals</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa fa-clock"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['pending'] ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa fa-spinner"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['ongoing'] ?></div>
                <div class="stat-label">Ongoing</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fa fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['completed'] ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Proposals + Upcoming Activities -->
<div class="row g-3">
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
                        <thead><tr><th>Title</th><th>Area</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentProposals as $p): ?>
                            <tr>
                                <td><a href="<?= url('proposals/view/' . $p['id']) ?>" style="font-weight:600;"><?= e(truncate($p['title'], 40)) ?></a></td>
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
    <div class="col-lg-5">
        <div class="ces-card">
            <div class="ces-card-header">
                <h3 class="card-title"><i class="fa fa-calendar-check me-2 text-success"></i>Upcoming Activities</h3>
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
                                <div style="font-size:1.1rem;font-weight:700;color:var(--ces-green);line-height:1;"><?= date('d', strtotime($act['activity_date'])) ?></div>
                                <div style="font-size:.65rem;text-transform:uppercase;color:#aaa;"><?= date('M', strtotime($act['activity_date'])) ?></div>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.85rem;"><?= e(truncate($act['title'], 35)) ?></div>
                                <div style="font-size:.75rem;color:#888;"><?= e(truncate($act['project_title'], 35)) ?></div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
