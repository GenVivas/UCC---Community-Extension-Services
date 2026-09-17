<?php
$severityColors = ['low'=>'success','medium'=>'warning','high'=>'orange','critical'=>'danger'];
?>

<div class="ces-breadcrumb mb-3">
    <a href="<?= url('community-needs') ?>">Needs Assessment</a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <span class="current"><?= e(truncate($assessment['title'],40)) ?></span>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Assessment Info -->
        <div class="ces-card mb-4">
            <div class="ces-card-header">
                <h3 class="card-title"><?= e($assessment['title']) ?></h3>
                <?= statusBadge($assessment['status']) ?>
            </div>
            <div class="ces-card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <div style="font-size:.75rem;color:#888;text-transform:uppercase;font-weight:700;margin-bottom:4px;">Barangay</div>
                        <div style="font-weight:600;"><?= e($assessment['barangay_name']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div style="font-size:.75rem;color:#888;text-transform:uppercase;font-weight:700;margin-bottom:4px;">Date Conducted</div>
                        <div><?= formatDate($assessment['assessment_date']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div style="font-size:.75rem;color:#888;text-transform:uppercase;font-weight:700;margin-bottom:4px;">Conducted By</div>
                        <div><?= e($assessment['first_name'].' '.$assessment['last_name']) ?></div>
                    </div>
                </div>
                <?php if ($assessment['description']): ?>
                <div class="p-3 bg-light rounded" style="font-size:.875rem;line-height:1.7;">
                    <?= nl2br(e($assessment['description'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Needs Items -->
        <div class="ces-card">
            <div class="ces-card-header">
                <h3 class="card-title">Identified Needs (<?= count($items) ?>)</h3>
            </div>
            <div class="ces-card-body p-0">
                <?php if (empty($items)): ?>
                    <div class="empty-state"><i class="fa fa-list"></i><p>No needs items recorded.</p></div>
                <?php else: ?>
                <table class="ces-table">
                    <thead><tr><th>#</th><th>Area</th><th>Problem</th><th>Severity</th><th>Recommended Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td class="text-muted"><?= $i+1 ?></td>
                            <td><span class="badge bg-secondary"><?= e($item['area']) ?></span></td>
                            <td><?= e($item['problem']) ?></td>
                            <td><?= statusBadge($item['severity']) ?></td>
                            <td style="font-size:.82rem;color:#666;"><?= e($item['recommended_action'] ?: '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right sidebar -->
    <div class="col-lg-4">
        <div class="ces-card mb-3">
            <div class="ces-card-header"><h3 class="card-title">Actions</h3></div>
            <div class="ces-card-body d-grid gap-2">

                <?php if (Auth::can('cna.approve') && $assessment['status'] === 'submitted'): ?>
                <form method="POST" action="<?= url('community-needs/approve/'.$assessment['id']) ?>">
                    <?php csrfField(); ?>
                    <button type="submit" class="btn-ces-primary w-100"><i class="fa fa-check"></i> Approve Assessment</button>
                </form>
                <?php endif; ?>

                <?php if (Auth::can('cna.edit') && $assessment['status'] !== 'approved'): ?>
                <a href="<?= url('community-needs/edit/'.$assessment['id']) ?>" class="btn-ces-outline w-100 text-center">
                    <i class="fa fa-edit"></i> Edit Assessment
                </a>
                <?php endif; ?>

                <?php if (Auth::can('proposals.create') && $assessment['status'] === 'approved'): ?>
                <a href="<?= url('proposals/create?cna_id='.$assessment['id']) ?>" class="btn btn-outline-success w-100">
                    <i class="fa fa-file-alt"></i> Create Proposal from this
                </a>
                <?php endif; ?>

                <?php if (Auth::can('cna.delete')): ?>
                <form method="POST" action="<?= url('community-needs/delete/'.$assessment['id']) ?>">
                    <?php csrfField(); ?>
                    <button type="submit" class="btn btn-outline-danger w-100" data-confirm="Delete this assessment? This cannot be undone.">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>
                <?php endif; ?>

                <a href="<?= url('community-needs') ?>" class="btn btn-outline-secondary w-100"><i class="fa fa-arrow-left"></i> Back to List</a>
            </div>
        </div>

        <?php if ($assessment['status'] === 'approved'): ?>
        <div class="ces-card">
            <div class="ces-card-body">
                <div style="font-size:.75rem;color:#888;text-transform:uppercase;font-weight:700;margin-bottom:6px;">Approved By</div>
                <div style="font-weight:600;"><?= e(($assessment['approver_first']??'').' '.($assessment['approver_last']??'')) ?></div>
                <div style="font-size:.8rem;color:#aaa;"><?= formatDateTime($assessment['approved_at']) ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
