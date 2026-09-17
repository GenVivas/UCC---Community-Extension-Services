<div class="ces-breadcrumb mb-3"><a href="<?= url('participants') ?>">Participants</a><span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span><span class="current"><?= e($participant['first_name'].' '.$participant['last_name']) ?></span></div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="ces-card mb-4"><div class="ces-card-header"><h3 class="card-title"><?= e($participant['first_name'].' '.$participant['last_name']) ?></h3><span class="badge bg-secondary"><?= ucfirst($participant['category']) ?></span></div>
        <div class="ces-card-body"><div class="row g-3">
            <div class="col-sm-4"><div class="detail-label">Barangay</div><div class="detail-val"><?= e($participant['barangay_name']) ?></div></div>
            <div class="col-sm-4"><div class="detail-label">Birthdate</div><div class="detail-val"><?= formatDate($participant['birthdate']) ?></div></div>
            <div class="col-sm-4"><div class="detail-label">Gender</div><div class="detail-val"><?= ucfirst($participant['gender'] ?: '—') ?></div></div>
            <div class="col-sm-4"><div class="detail-label">Civil Status</div><div class="detail-val"><?= ucfirst($participant['civil_status'] ?: '—') ?></div></div>
            <div class="col-sm-4"><div class="detail-label">Contact No.</div><div class="detail-val"><?= e($participant['contact_no'] ?: '—') ?></div></div>
            <div class="col-sm-8"><div class="detail-label">Address</div><div class="detail-val"><?= e($participant['address'] ?: '—') ?></div></div>
        </div></div></div>
        <div class="ces-card"><div class="ces-card-header"><h3 class="card-title">Activity History (<?= count($history) ?>)</h3></div>
        <div class="ces-card-body p-0">
            <?php if(empty($history)): ?><div class="empty-state"><i class="fa fa-calendar"></i><p>No activities yet.</p></div><?php else: ?>
            <table class="ces-table"><thead><tr><th>Activity</th><th>Project</th><th>Date</th><th>Time In</th><th>Time Out</th></tr></thead><tbody>
            <?php foreach($history as $h): ?><tr>
                <td style="font-weight:600;"><?= e($h['activity_title']) ?></td>
                <td style="font-size:.8rem;"><?= e(truncate($h['project_title'],30)) ?></td>
                <td><?= formatDate($h['activity_date'],'M d, Y') ?></td>
                <td><?= $h['time_in'] ? date('h:i A', strtotime($h['time_in'])) : '—' ?></td>
                <td><?= $h['time_out'] ? date('h:i A', strtotime($h['time_out'])) : '—' ?></td>
            </tr><?php endforeach; ?>
            </tbody></table><?php endif; ?>
        </div></div>
    </div>
    <div class="col-lg-4"><div class="ces-card"><div class="ces-card-header"><h3 class="card-title">Actions</h3></div><div class="ces-card-body d-grid gap-2">
        <?php if(Auth::can('participants.manage')): ?><a href="<?= url('participants/edit/'.$participant['id']) ?>" class="btn-ces-primary"><i class="fa fa-edit"></i> Edit</a><?php endif; ?>
        <a href="<?= url('participants') ?>" class="btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> Back</a>
    </div></div></div>
</div>
<style>.detail-label{font-size:.72rem;color:#888;text-transform:uppercase;font-weight:700;margin-bottom:3px;}.detail-val{font-size:.88rem;font-weight:500;}</style>
