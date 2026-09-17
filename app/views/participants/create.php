<div class="ces-breadcrumb mb-3"><a href="<?= url('participants') ?>">Participants</a><span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span><span class="current">Add Participant</span></div>
<form method="POST" action="<?= url('participants/store') ?>">
    <?php csrfField(); ?>
    <div class="row g-4">
        <div class="col-lg-8"><div class="ces-card"><div class="ces-card-header"><h3 class="card-title">Participant Information</h3></div><div class="ces-card-body"><div class="row g-3">
            <div class="col-md-6"><label class="form-label">First Name <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Last Name <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Barangay <span class="text-danger">*</span></label><select name="barangay_id" class="form-select" required><option value="">Select...</option><?php foreach($barangays as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Category</label><select name="category" class="form-select"><?php foreach(['student','youth','senior','pwd','parent','other'] as $c): ?><option value="<?= $c ?>"><?= ucfirst($c) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Birthdate</label><input type="date" name="birthdate" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">—</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
            <div class="col-md-4"><label class="form-label">Civil Status</label><select name="civil_status" class="form-select"><option value="">—</option><?php foreach(['single','married','widowed','separated'] as $s): ?><option value="<?= $s ?>"><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Contact No.</label><input type="tel" name="contact_no" class="form-control" placeholder="09XX-XXX-XXXX"></div>
            <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
        </div></div></div></div>
        <div class="col-lg-4"><div class="ces-card"><div class="ces-card-header"><h3 class="card-title">Save</h3></div><div class="ces-card-body d-grid gap-2">
            <button type="submit" class="btn-ces-primary"><i class="fa fa-save"></i> Save Participant</button>
            <a href="<?= url('participants') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div></div></div>
    </div>
</form>
