<div class="page-header">
    <div class="page-header-left">
        <h2>Project Proposals</h2>
        <p>CES project proposals from all programs.</p>
    </div>
    <?php if (Auth::can('proposals.create')): ?>
    <a href="<?= url('proposals/create') ?>" class="btn-ces-primary"><i class="fa fa-plus"></i> New Proposal</a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="ces-card mb-4">
    <div class="ces-card-body">
        <form method="GET" action="<?= url('proposals') ?>" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search title..." value="<?= e($filters['search']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Program</label>
                <select name="program_id" class="form-select">
                    <option value="">All Programs</option>
                    <?php foreach ($programs as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filters['program_id']==$p['id']?'selected':'' ?>><?= e($p['code']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <?php foreach (['draft','pending','approved','rejected','ongoing','completed'] as $s): ?>
                        <option value="<?= $s ?>" <?= $filters['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-ces-primary flex-grow-1"><i class="fa fa-search"></i> Filter</button>
                <a href="<?= url('proposals') ?>" class="btn-ces-outline"><i class="fa fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="ces-card">
    <div class="ces-card-body p-0">
        <?php if (empty($paginator['data'])): ?>
            <div class="empty-state"><i class="fa fa-file-alt"></i><p>No proposals found.</p></div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="ces-table">
                <thead><tr><th>#</th><th>Title</th><th>Program</th><th>Barangay</th><th>Area</th><th>Budget</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($paginator['data'] as $i => $row): ?>
                <tr>
                    <td class="text-muted" style="font-size:.75rem;"><?= (($paginator['current']-1)*PER_PAGE)+$i+1 ?></td>
                    <td>
                        <a href="<?= url('proposals/view/'.$row['id']) ?>" style="font-weight:600;"><?= e(truncate($row['title'],45)) ?></a>
                        <div style="font-size:.75rem;color:#888;"><?= e($row['first_name'].' '.$row['last_name']) ?></div>
                    </td>
                    <td><span class="badge bg-secondary"><?= e($row['program_code']) ?></span></td>
                    <td style="font-size:.82rem;"><?= e($row['barangay_name']) ?></td>
                    <td style="font-size:.8rem;"><?= e($row['target_area']) ?></td>
                    <td style="font-size:.82rem;"><?= peso($row['budget_requested']) ?></td>
                    <td><?= statusBadge($row['status']) ?></td>
                    <td style="font-size:.78rem;color:#888;white-space:nowrap;"><?= formatDate($row['created_at'],'M d, Y') ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= url('proposals/view/'.$row['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa fa-eye"></i></a>
                            <?php if (Auth::can('proposals.edit') && in_array($row['status'],['draft','pending','rejected'])): ?>
                            <a href="<?= url('proposals/edit/'.$row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>
                            <?php endif; ?>
                            <?php if (Auth::can('proposals.delete') && $row['status']==='draft'): ?>
                            <form method="POST" action="<?= url('proposals/delete/'.$row['id']) ?>" class="d-inline">
                                <?php csrfField(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Delete this proposal?" title="Delete"><i class="fa fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-3"><?php renderPagination($paginator, url('proposals')); ?></div>
        <?php endif; ?>
    </div>
</div>
