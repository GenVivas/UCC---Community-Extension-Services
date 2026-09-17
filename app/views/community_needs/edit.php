<?php
$areas      = ['Education','Technology','Health','Sanitation','Livelihood','Environment','Other'];
$severities = ['low'=>'Low','medium'=>'Medium','high'=>'High','critical'=>'Critical'];
?>

<div class="ces-breadcrumb mb-3">
    <a href="<?= url('community-needs') ?>">Needs Assessment</a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <a href="<?= url('community-needs/view/'.$assessment['id']) ?>"><?= e(truncate($assessment['title'],30)) ?></a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <span class="current">Edit</span>
</div>

<form method="POST" action="<?= url('community-needs/update/'.$assessment['id']) ?>">
    <?php csrfField(); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ces-card mb-4">
                <div class="ces-card-header"><h3 class="card-title">Assessment Details</h3></div>
                <div class="ces-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?= e($assessment['title']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select name="barangay_id" class="form-select" required>
                                <?php foreach ($barangays as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= $assessment['barangay_id']==$b['id']?'selected':'' ?>><?= e($b['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assessment Date <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" class="form-control" value="<?= e($assessment['assessment_date']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= e($assessment['description']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ces-card">
                <div class="ces-card-header">
                    <h3 class="card-title">Identified Needs</h3>
                    <button type="button" class="btn-ces-outline" id="addNeedBtn" style="font-size:.8rem;padding:5px 12px;"><i class="fa fa-plus"></i> Add Need</button>
                </div>
                <div class="ces-card-body">
                    <div id="needsContainer">
                    <?php foreach ($items as $idx => $item): ?>
                        <div class="need-row border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-muted" style="font-size:.8rem;">NEED #<?= $idx+1 ?></span>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-need"><i class="fa fa-times"></i></button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Area</label>
                                    <select name="area[]" class="form-select form-select-sm">
                                        <?php foreach ($areas as $a): ?>
                                            <option value="<?= $a ?>" <?= $item['area']===$a?'selected':'' ?>><?= $a ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Severity</label>
                                    <select name="severity[]" class="form-select form-select-sm">
                                        <?php foreach ($severities as $val=>$lbl): ?>
                                            <option value="<?= $val ?>" <?= $item['severity']===$val?'selected':'' ?>><?= $lbl ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Problem / Need</label>
                                    <textarea name="problem[]" class="form-control form-control-sm" rows="2"><?= e($item['problem']) ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Recommended Action</label>
                                    <input type="text" name="recommended_action[]" class="form-control form-control-sm" value="<?= e($item['recommended_action']) ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ces-card">
                <div class="ces-card-header"><h3 class="card-title">Update</h3></div>
                <div class="ces-card-body d-grid gap-2">
                    <button type="submit" name="action" value="draft" class="btn-ces-outline"><i class="fa fa-save"></i> Save Draft</button>
                    <button type="submit" name="action" value="submit" class="btn-ces-primary"><i class="fa fa-paper-plane"></i> Submit for Approval</button>
                    <a href="<?= url('community-needs/view/'.$assessment['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.querySelectorAll('.remove-need').forEach(btn => btn.addEventListener('click', () => btn.closest('.need-row').remove()));
let count = <?= count($items) ?>;
document.getElementById('addNeedBtn').addEventListener('click', function () {
    count++;
    const areas = <?= json_encode($areas) ?>;
    const sevs  = <?= json_encode($severities) ?>;
    const container = document.getElementById('needsContainer');
    const div = document.createElement('div');
    div.className = 'need-row border rounded p-3 mb-3';
    div.innerHTML = `<div class="d-flex justify-content-between align-items-center mb-2"><span class="fw-bold text-muted" style="font-size:.8rem;">NEED #${count}</span><button type="button" class="btn btn-sm btn-outline-danger remove-need"><i class="fa fa-times"></i></button></div>
    <div class="row g-2">
        <div class="col-md-4"><label class="form-label">Area</label><select name="area[]" class="form-select form-select-sm">${areas.map(a=>`<option>${a}</option>`).join('')}</select></div>
        <div class="col-md-4"><label class="form-label">Severity</label><select name="severity[]" class="form-select form-select-sm">${Object.entries(sevs).map(([v,l])=>`<option value="${v}">${l}</option>`).join('')}</select></div>
        <div class="col-12"><label class="form-label">Problem</label><textarea name="problem[]" class="form-control form-control-sm" rows="2"></textarea></div>
        <div class="col-12"><label class="form-label">Recommended Action</label><input type="text" name="recommended_action[]" class="form-control form-control-sm"></div>
    </div>`;
    container.appendChild(div);
    div.querySelector('.remove-need').addEventListener('click', () => div.remove());
});
</script>
