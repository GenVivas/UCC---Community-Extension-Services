<div class="page-header">
    <div class="page-header-left"><h2>User Management</h2><p>All system users and their roles.</p></div>
    <a href="<?= url('users/create') ?>" class="btn-ces-primary"><i class="fa fa-plus"></i> Create User</a>
</div>
<div class="ces-card mb-4"><div class="ces-card-body">
    <form method="GET" action="<?= url('users') ?>" class="row g-2 align-items-end">
        <div class="col-md-5"><label class="form-label">Search</label><input type="text" name="search" class="form-control" placeholder="Name or email..." value="<?= e($filters['search']) ?>"></div>
        <div class="col-md-3"><label class="form-label">Role</label><select name="role" class="form-select"><option value="">All Roles</option><?php foreach(ROLE_LABELS as $val=>$lbl): ?><option value="<?= $val ?>" <?= $filters['role']===$val?'selected':'' ?>><?= $lbl ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4 d-flex gap-2"><button type="submit" class="btn-ces-primary flex-grow-1"><i class="fa fa-search"></i> Filter</button><a href="<?= url('users') ?>" class="btn-ces-outline"><i class="fa fa-times"></i></a></div>
    </form>
</div></div>
<div class="ces-card"><div class="ces-card-body p-0">
    <?php if(empty($paginator['data'])): ?><div class="empty-state"><i class="fa fa-users"></i><p>No users found.</p></div><?php else: ?>
    <div class="table-responsive"><table class="ces-table">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Program</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($paginator['data'] as $i=>$row): ?>
        <tr>
            <td class="text-muted" style="font-size:.75rem;"><?= (($paginator['current']-1)*PER_PAGE)+$i+1 ?></td>
            <td style="font-weight:600;"><?= e($row['first_name'].' '.$row['last_name']) ?><?php if($row['employee_id']): ?><div style="font-size:.72rem;color:#aaa;"><?= e($row['employee_id']) ?></div><?php endif; ?></td>
            <td style="font-size:.82rem;"><?= e($row['email']) ?></td>
            <td><?= statusBadge($row['role']) ?></td>
            <td style="font-size:.8rem;"><?= e($row['program_name'] ?: '—') ?></td>
            <td><?= $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' ?></td>
            <td style="font-size:.78rem;color:#888;"><?= $row['last_login'] ? formatDate($row['last_login'],'M d, Y') : 'Never' ?></td>
            <td><div class="d-flex gap-1">
                <a href="<?= url('users/edit/'.$row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>
                <?php if($row['id'] != Auth::id() && $row['is_active']): ?>
                <form method="POST" action="<?= url('users/deactivate/'.$row['id']) ?>" class="d-inline"><?php csrfField(); ?><button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Deactivate this user?" title="Deactivate"><i class="fa fa-ban"></i></button></form>
                <?php endif; ?>
            </div></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <div class="p-3"><?php renderPagination($paginator, url('users')); ?></div>
    <?php endif; ?>
</div></div>
