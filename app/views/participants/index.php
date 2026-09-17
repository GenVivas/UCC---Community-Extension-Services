<div class="page-header">
    <div class="page-header-left"><h2>Participants / Beneficiaries</h2><p>Community members enrolled in CES programs.</p></div>
    <?php if(Auth::can('participants.manage')): ?><a href="<?= url('participants/create') ?>" class="btn-ces-primary"><i class="fa fa-plus"></i> Add Participant</a><?php endif; ?>
</div>
<div class="ces-card mb-4"><div class="ces-card-body">
    <form method="GET" action="<?= url('participants') ?>" class="row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label">Search</label><input type="text" name="search" class="form-control" placeholder="Name or contact..." value="<?= e($filters['search']) ?>"></div>
        <div class="col-md-3"><label class="form-label">Barangay</label><select name="barangay_id" class="form-select"><option value="">All</option><?php foreach($barangays as $b): ?><option value="<?= $b['id'] ?>" <?= $filters['barangay_id']==$b['id']?'selected':'' ?>><?= e($b['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Category</label><select name="category" class="form-select"><option value="">All</option><?php foreach(['student','youth','senior','pwd','parent','other'] as $c): ?><option value="<?= $c ?>" <?= $filters['category']===$c?'selected':'' ?>><?= ucfirst($c) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn-ces-primary flex-grow-1"><i class="fa fa-search"></i> Filter</button><a href="<?= url('participants') ?>" class="btn-ces-outline"><i class="fa fa-times"></i></a></div>
    </form>
</div></div>
<div class="ces-card"><div class="ces-card-body p-0">
    <?php if(empty($paginator['data'])): ?>
        <div class="empty-state"><i class="fa fa-users"></i><p>No participants found.</p></div>
    <?php else: ?>
    <div class="table-responsive"><table class="ces-table">
        <thead><tr><th>#</th><th>Name</th><th>Barangay</th><th>Category</th><th>Contact</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($paginator['data'] as $i=>$row): ?>
        <tr>
            <td class="text-muted" style="font-size:.75rem;"><?= (($paginator['current']-1)*PER_PAGE)+$i+1 ?></td>
            <td><a href="<?= url('participants/view/'.$row['id']) ?>" style="font-weight:600;"><?= e($row['last_name'].', '.$row['first_name']) ?></a><?php if($row['birthdate']): ?><div style="font-size:.73rem;color:#aaa;"><?= formatDate($row['birthdate'],'M d, Y') ?></div><?php endif; ?></td>
            <td><?= e($row['barangay_name']) ?></td>
            <td><span class="badge bg-light text-dark border"><?= ucfirst($row['category']) ?></span></td>
            <td style="font-size:.82rem;"><?= e($row['contact_no'] ?: '—') ?></td>
            <td><div class="d-flex gap-1">
                <a href="<?= url('participants/view/'.$row['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa fa-eye"></i></a>
                <?php if(Auth::can('participants.manage')): ?><a href="<?= url('participants/edit/'.$row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a><?php endif; ?>
            </div></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <div class="p-3"><?php renderPagination($paginator, url('participants')); ?></div>
    <?php endif; ?>
</div></div>
