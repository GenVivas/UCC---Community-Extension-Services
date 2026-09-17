<div class="ces-breadcrumb mb-3">
    <a href="<?= url('proposals') ?>">Proposals</a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <span class="current"><?= e(truncate($proposal['title'],40)) ?></span>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Header -->
        <div class="ces-card mb-4">
            <div class="ces-card-header">
                <h3 class="card-title"><?= e($proposal['title']) ?></h3>
                <?= statusBadge($proposal['status']) ?>
            </div>
            <div class="ces-card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-3"><div class="detail-label">Program</div><div class="detail-val"><span class="badge bg-secondary"><?= e($proposal['program_code']) ?></span></div></div>
                    <div class="col-sm-3"><div class="detail-label">Barangay</div><div class="detail-val"><?= e($proposal['barangay_name']) ?></div></div>
                    <div class="col-sm-3"><div class="detail-label">Target Area</div><div class="detail-val"><?= e($proposal['target_area']) ?></div></div>
                    <div class="col-sm-3"><div class="detail-label">Budget</div><div class="detail-val"><?= peso($proposal['budget_requested']) ?></div></div>
                    <?php if ($proposal['start_date']): ?>
                    <div class="col-sm-3"><div class="detail-label">Start Date</div><div class="detail-val"><?= formatDate($proposal['start_date'],'M d, Y') ?></div></div>
                    <div class="col-sm-3"><div class="detail-label">End Date</div><div class="detail-val"><?= formatDate($proposal['end_date'],'M d, Y') ?></div></div>
                    <?php endif; ?>
                    <div class="col-sm-3"><div class="detail-label">Submitted By</div><div class="detail-val"><?= e($proposal['first_name'].' '.$proposal['last_name']) ?></div></div>
                    <div class="col-sm-3"><div class="detail-label">Date Submitted</div><div class="detail-val"><?= formatDate($proposal['created_at'],'M d, Y') ?></div></div>
                </div>

                <?php foreach (['description'=>'Description','objectives'=>'Objectives','target_beneficiaries'=>'Target Beneficiaries','expected_output'=>'Expected Output'] as $key=>$label): ?>
                    <?php if ($proposal[$key]): ?>
                    <div class="mb-3">
                        <div class="detail-label"><?= $label ?></div>
                        <div class="p-3 bg-light rounded" style="font-size:.875rem;line-height:1.7;"><?= nl2br(e($proposal[$key])) ?></div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($proposal['attachment']): ?>
                <div class="mt-3">
                    <a href="<?= asset('uploads/proposals/'.e($proposal['attachment'])) ?>" target="_blank" class="btn-ces-outline" style="font-size:.82rem;">
                        <i class="fa fa-paperclip"></i> View Attachment
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activities -->
        <div class="ces-card mb-4">
            <div class="ces-card-header">
                <h3 class="card-title">Activities (<?= count($activities) ?>)</h3>
            </div>
            <div class="ces-card-body p-0">
                <?php if (empty($activities)): ?>
                    <div class="empty-state"><i class="fa fa-calendar"></i><p>No activities yet.</p></div>
                <?php else: ?>
                <table class="ces-table">
                    <thead><tr><th>Title</th><th>Date</th><th>Venue</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($activities as $act): ?>
                    <tr>
                        <td style="font-weight:600;"><?= e($act['title']) ?></td>
                        <td><?= formatDate($act['activity_date'],'M d, Y') ?></td>
                        <td style="font-size:.82rem;"><?= e($act['venue'] ?: '—') ?></td>
                        <td><?= statusBadge($act['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approval History -->
        <?php if (!empty($history)): ?>
        <div class="ces-card">
            <div class="ces-card-header"><h3 class="card-title">Approval History</h3></div>
            <div class="ces-card-body p-0">
                <table class="ces-table">
                    <thead><tr><th>Action</th><th>By</th><th>Role</th><th>Remarks</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($history as $h): ?>
                    <tr>
                        <td><?= statusBadge($h['action']) ?></td>
                        <td><?= e($h['first_name'].' '.$h['last_name']) ?></td>
                        <td style="font-size:.78rem;"><?= e(ROLE_LABELS[$h['approver_role']] ?? $h['approver_role']) ?></td>
                        <td style="font-size:.82rem;"><?= e($h['remarks'] ?: '—') ?></td>
                        <td style="font-size:.78rem;color:#888;"><?= formatDateTime($h['acted_at'],'M d, Y h:i A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="ces-card mb-3">
            <div class="ces-card-header"><h3 class="card-title">Actions</h3></div>
            <div class="ces-card-body d-grid gap-2">

                <?php if (Auth::can('proposals.approve') && $proposal['status'] === 'pending'): ?>
                <button class="btn-ces-primary w-100" data-bs-toggle="modal" data-bs-target="#approvalModal">
                    <i class="fa fa-gavel"></i> Review & Decide
                </button>
                <?php endif; ?>

                <?php if (Auth::can('proposals.edit') && in_array($proposal['status'],['draft','pending','rejected'])): ?>
                <a href="<?= url('proposals/edit/'.$proposal['id']) ?>" class="btn-ces-outline w-100 text-center">
                    <i class="fa fa-edit"></i> Edit Proposal
                </a>
                <?php endif; ?>

                <?php if (Auth::can('proposals.delete') && $proposal['status']==='draft'): ?>
                <form method="POST" action="<?= url('proposals/delete/'.$proposal['id']) ?>">
                    <?php csrfField(); ?>
                    <button type="submit" class="btn btn-outline-danger w-100" data-confirm="Delete this proposal?">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>
                <?php endif; ?>

                <a href="<?= url('proposals') ?>" class="btn btn-outline-secondary w-100"><i class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <?php if ($proposal['cna_title']): ?>
        <div class="ces-card">
            <div class="ces-card-body">
                <div class="detail-label">Based on CNA</div>
                <div style="font-size:.85rem;font-weight:600;"><?= e($proposal['cna_title']) ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Approval Modal -->
<?php if (Auth::can('proposals.approve') && $proposal['status'] === 'pending'): ?>
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Proposal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('proposals/approve/'.$proposal['id']) ?>">
                <?php csrfField(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Decision</label>
                        <select name="action" class="form-select" required>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                            <option value="returned">Return for Revision</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-ces-primary">Submit Decision</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.detail-label { font-size:.72rem;color:#888;text-transform:uppercase;font-weight:700;margin-bottom:3px; }
.detail-val   { font-size:.88rem;font-weight:500; }
</style>
