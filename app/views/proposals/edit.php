<?php $areas = ['Education','Technology','Health','Sanitation','Livelihood','Environment','Other']; ?>
<div class="ces-breadcrumb mb-3">
    <a href="<?= url('proposals') ?>">Proposals</a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <a href="<?= url('proposals/view/'.$proposal['id']) ?>"><?= e(truncate($proposal['title'],30)) ?></a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <span class="current">Edit</span>
</div>

<form method="POST" action="<?= url('proposals/update/'.$proposal['id']) ?>">
    <?php csrfField(); ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ces-card mb-4">
                <div class="ces-card-header"><h3 class="card-title">Proposal Information</h3></div>
                <div class="ces-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?= e($proposal['title']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program</label>
                            <select name="program_id" class="form-select">
                                <?php foreach ($programs as $p): ?><option value="<?= $p['id'] ?>" <?= $proposal['program_id']==$p['id']?'selected':'' ?>><?= e($p['code'].' — '.$p['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barangay</label>
                            <select name="barangay_id" class="form-select">
                                <?php foreach ($barangays as $b): ?><option value="<?= $b['id'] ?>" <?= $proposal['barangay_id']==$b['id']?'selected':'' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Target Area</label>
                            <select name="target_area" class="form-select">
                                <?php foreach ($areas as $a): ?><option value="<?= $a ?>" <?= $proposal['target_area']===$a?'selected':'' ?>><?= $a ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Budget (₱)</label>
                            <input type="number" name="budget_requested" class="form-control" value="<?= e($proposal['budget_requested']) ?>" step="0.01" min="0">
                        </div>
                        <?php foreach (['description'=>['Description',4],'objectives'=>['Objectives',3],'target_beneficiaries'=>['Target Beneficiaries',2],'expected_output'=>['Expected Output',2]] as $field=>[$label,$rows]): ?>
                        <div class="col-12">
                            <label class="form-label"><?= $label ?></label>
                            <textarea name="<?= $field ?>" class="form-control" rows="<?= $rows ?>"><?= e($proposal[$field]) ?></textarea>
                        </div>
                        <?php endforeach; ?>
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?= e($proposal['start_date']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?= e($proposal['end_date']) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ces-card">
                <div class="ces-card-header"><h3 class="card-title">Update</h3></div>
                <div class="ces-card-body d-grid gap-2">
                    <button type="submit" name="action" value="draft" class="btn-ces-outline"><i class="fa fa-save"></i> Save Draft</button>
                    <button type="submit" name="action" value="submit" class="btn-ces-primary"><i class="fa fa-paper-plane"></i> Resubmit</button>
                    <a href="<?= url('proposals/view/'.$proposal['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
