<div class="page-header">
    <div class="page-header-left">
        <h2>Community Needs Assessments</h2>
        <p>Manage needs analysis conducted in adopted barangays.</p>
    </div>
    <?php if (Auth::can('cna.create')): ?>
    <a href="<?= url('community-needs/create') ?>" class="btn-ces-primary">
        <i class="fa fa-plus"></i> New Assessment
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="ces-card mb-4">
    <div class="ces-card-body">
        <form method="GET" action="<?= url('community-needs') ?>" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search title..." value="<?= e($filters['search']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Barangay</label>
                <select name="barangay_id" class="form-select">
                    <option value="">All Barangays</option>
                    <?php foreach ($barangays as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $filters['barangay_id'] == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="draft"     <?= $filters['status']==='draft'     ? 'selected':'' ?>>Draft</option>
                    <option value="submitted" <?= $filters['status']==='submitted' ? 'selected':'' ?>>Submitted</option>
                    <option value="approved"  <?= $filters['status']==='approved'  ? 'selected':'' ?>>Approved</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-ces-primary flex-grow-1"><i class="fa fa-search"></i> Filter</button>
                <a href="<?= url('community-needs') ?>" class="btn-ces-outline"><i class="fa fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="ces-card">
    <div class="ces-card-body p-0">
        <?php if (empty($paginator['data'])): ?>
            <div class="empty-state"><i class="fa fa-search-location"></i><p>No assessments found.</p></div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="ces-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Barangay</th>
                        <th>Conducted By</th>
                        <th>Date</th>
                        <th>Needs</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($paginator['data'] as $i => $row): ?>
                    <tr>
                        <td class="text-muted" style="font-size:.75rem;"><?= (($paginator['current']-1)*PER_PAGE)+$i+1 ?></td>
                        <td>
                            <a href="<?= url('community-needs/view/'.$row['id']) ?>" style="font-weight:600;">
                                <?= e(truncate($row['title'], 50)) ?>
                            </a>
                        </td>
                        <td><?= e($row['barangay_name']) ?></td>
                        <td><?= e($row['first_name'].' '.$row['last_name']) ?></td>
                        <td style="white-space:nowrap;"><?= formatDate($row['assessment_date'], 'M d, Y') ?></td>
                        <td><span class="badge bg-secondary"><?= $row['item_count'] ?> item<?= $row['item_count']!=1?'s':'' ?></span></td>
                        <td><?= statusBadge($row['status']) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= url('community-needs/view/'.$row['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa fa-eye"></i></a>
                                <?php if (Auth::can('cna.edit') && $row['status'] !== 'approved'): ?>
                                <a href="<?= url('community-needs/edit/'.$row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (Auth::can('cna.delete')): ?>
                                <form method="POST" action="<?= url('community-needs/delete/'.$row['id']) ?>" class="d-inline">
                                    <?php csrfField(); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" data-confirm="Delete this assessment?"><i class="fa fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-3">
            <?php renderPagination($paginator, url('community-needs')); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
