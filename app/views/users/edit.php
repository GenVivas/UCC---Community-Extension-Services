<div class="ces-breadcrumb mb-3"><a href="<?= url('users') ?>">Users</a><span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span><span class="current">Edit User</span></div>
<form method="POST" action="<?= url('users/update/'.$editUser['id']) ?>">
    <?php csrfField(); ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ces-card mb-4"><div class="ces-card-header"><h3 class="card-title">User Information</h3></div><div class="ces-card-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" value="<?= e($editUser['first_name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" value="<?= e($editUser['last_name']) ?>" required></div>
                <div class="col-md-8"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($editUser['email']) ?>" required></div>
                <div class="col-md-4"><label class="form-label">Employee / Student ID</label><input type="text" name="employee_id" class="form-control" value="<?= e($editUser['employee_id']) ?>"></div>
                <div class="col-md-6"><label class="form-label">Role</label><select name="role" class="form-select"><?php foreach(ROLE_LABELS as $val=>$lbl): ?><option value="<?= $val ?>" <?= $editUser['role']===$val?'selected':'' ?>><?= $lbl ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Program</label><select name="program_id" class="form-select"><option value="">— None —</option><?php foreach($programs as $p): ?><option value="<?= $p['id'] ?>" <?= $editUser['program_id']==$p['id']?'selected':'' ?>><?= e($p['code'].' — '.$p['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Contact No.</label><input type="tel" name="contact_no" class="form-control" value="<?= e($editUser['contact_no']) ?>"></div>
                <div class="col-md-6"><label class="form-label">Status</label><select name="is_active" class="form-select"><option value="1" <?= $editUser['is_active']?'selected':'' ?>>Active</option><option value="0" <?= !$editUser['is_active']?'selected':'' ?>>Inactive</option></select></div>
            </div></div></div>
            <div class="ces-card"><div class="ces-card-header"><h3 class="card-title">Change Password <small class="text-muted" style="font-weight:400;font-size:.8rem;">(leave blank to keep current)</small></h3></div><div class="ces-card-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" minlength="8"></div>
                <div class="col-md-6"><label class="form-label">Confirm New Password</label><input type="password" name="new_password_confirm" class="form-control"></div>
            </div></div></div>
        </div>
        <div class="col-lg-4"><div class="ces-card"><div class="ces-card-body d-grid gap-2">
            <button type="submit" class="btn-ces-primary"><i class="fa fa-save"></i> Save Changes</button>
            <a href="<?= url('users') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div></div></div>
    </div>
</form>
