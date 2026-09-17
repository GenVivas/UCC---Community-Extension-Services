<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h2>Hello, <?= e(Auth::user()['first_name']) ?> 👋</h2>
        <p>Track your community extension activities, certificates, and evaluations.</p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa fa-calendar-check"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['activitiesJoined'] ?></div>
                <div class="stat-label">Activities Joined</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa fa-certificate"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['certificates'] ?></div>
                <div class="stat-label">Certificates Earned</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa fa-star"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['evaluations'] ?></div>
                <div class="stat-label">Evaluations Submitted</div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Activities -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="ces-card">
            <div class="ces-card-header">
                <h3 class="card-title"><i class="fa fa-calendar me-2 text-success"></i>Upcoming Activities</h3>
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
                                <div style="font-size:1.1rem;font-weight:700;color:var(--ces-green);line-height:1;"><?= date('d', strtotime($act['activity_date'])) ?></div>
                                <div style="font-size:.65rem;text-transform:uppercase;color:#aaa;"><?= date('M', strtotime($act['activity_date'])) ?></div>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.85rem;"><?= e(truncate($act['title'], 35)) ?></div>
                                <div style="font-size:.75rem;color:#888;"><?= e($act['venue'] ?? '') ?></div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ces-card">
            <div class="ces-card-header">
                <h3 class="card-title"><i class="fa fa-certificate me-2 text-success"></i>My Certificates</h3>
                <a href="<?= url('certificates') ?>" class="btn-ces-outline" style="font-size:.78rem;padding:5px 12px;">View All</a>
            </div>
            <div class="ces-card-body">
                <div class="empty-state" style="padding:32px 0;">
                    <i class="fa fa-certificate" style="color:#e0e0e0;"></i>
                    <p>Complete activities to earn certificates.</p>
                    <a href="<?= url('activities') ?>" class="btn-ces-primary mt-2" style="display:inline-flex;">
                        <i class="fa fa-search"></i> Browse Activities
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
