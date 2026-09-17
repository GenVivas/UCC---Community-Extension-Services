<div class="ces-breadcrumb mb-3"><a href="<?= url('users') ?>">Users</a><span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span><span class="current">Create User</span></div>
<form method="POST" action="<?= url('users/store') ?>">
    <?php csrfField(); ?>
    <div class="row g-4">
        <div class="col-lg-8"><div class="ces-card"><div class="ces-card-header"><h3 class="card-title">User Information</h3></div><div class="ces-card-body"><div class="row g-3">
            <div class="col-md-6"><label class="form-label">First Name <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Last Name <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" required></div>
            <div class="col-md-8"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Employee / Student ID</label><input type="text" name="employee_id" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Role <span class="text-danger">*</span></label><select name="role" class="form-select" required><?php foreach(ROLE_LABELS as $val=>$lbl): ?><option value="<?= $val ?>"><?= $lbl ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Program</label><select name="program_id" class="form-select"><option value="">— None —</option><?php foreach($programs as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['code'].' — '.$p['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Contact No.</label><input type="tel" name="contact_no" class="form-control" placeholder="09XX-XXX-XXXX"></div>
            <div class="col-md-6"></div>
            <div class="col-md-6"><label class="form-label">Password <span class="text-danger">*</span></label><input type="password" name="password" class="form-control" required minlength="8"></div>
            <div class="col-md-6"><label class="form-label">Confirm Password <span class="text-danger">*</span></label><input type="password" name="password_confirm" class="form-control" required></div>
        </div></div></div></div>
        <div class="col-lg-4"><div class="ces-card"><div class="ces-card-body d-grid gap-2">
            <button type="submit" class="btn-ces-primary"><i class="fa fa-user-plus"></i> Create User</button>
            <a href="<?= url('users') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div></div></div>
    </div>
</form>
