<div class="ces-breadcrumb mb-3"><a href="<?= url('participants') ?>">Participants</a><span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span><a href="<?= url('participants/view/'.$participant['id']) ?>"><?= e($participant['first_name'].' '.$participant['last_name']) ?></a><span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span><span class="current">Edit</span></div>
<form method="POST" action="<?= url('participants/update/'.$participant['id']) ?>">
    <?php csrfField(); ?>
    <div class="row g-4">
        <div class="col-lg-8"><div class="ces-card"><div class="ces-card-header"><h3 class="card-title">Edit Participant</h3></div><div class="ces-card-body"><div class="row g-3">
            <div class="col-md-6"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" value="<?= e($participant['first_name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" value="<?= e($participant['last_name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Barangay</label><select name="barangay_id" class="form-select"><?php foreach($barangays as $b): ?><option value="<?= $b['id'] ?>" <?= $participant['barangay_id']==$b['id']?'selected':'' ?>><?= e($b['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Category</label><select name="category" class="form-select"><?php foreach(['student','youth','senior','pwd','parent','other'] as $c): ?><option value="<?= $c ?>" <?= $participant['category']===$c?'selected':'' ?>><?= ucfirst($c) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Birthdate</label><input type="date" name="birthdate" class="form-control" value="<?= e($participant['birthdate']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">—</option><?php foreach(['male','female','other'] as $g): ?><option value="<?= $g ?>" <?= $participant['gender']===$g?'selected':'' ?>><?= ucfirst($g) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Civil Status</label><select name="civil_status" class="form-select"><option value="">—</option><?php foreach(['single','married','widowed','separated'] as $s): ?><option value="<?= $s ?>" <?= $participant['civil_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Contact No.</label><input type="tel" name="contact_no" class="form-control" value="<?= e($participant['contact_no']) ?>"></div>
            <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= e($participant['address']) ?></textarea></div>
        </div></div></div></div>
        <div class="col-lg-4"><div class="ces-card"><div class="ces-card-body d-grid gap-2">
            <button type="submit" class="btn-ces-primary"><i class="fa fa-save"></i> Save Changes</button>
            <a href="<?= url('participants/view/'.$participant['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div></div></div>
    </div>
</form>
