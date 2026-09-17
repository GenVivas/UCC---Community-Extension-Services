<?php
$areas      = ['Education','Technology','Health','Sanitation','Livelihood','Environment','Other'];
$severities = ['low'=>'Low','medium'=>'Medium','high'=>'High','critical'=>'Critical'];
?>

<div class="ces-breadcrumb mb-3">
    <a href="<?= url('community-needs') ?>">Needs Assessment</a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <span class="current">New Assessment</span>
</div>

<form method="POST" action="<?= url('community-needs/store') ?>">
    <?php csrfField(); ?>

    <div class="row g-4">
        <!-- Left: main details -->
        <div class="col-lg-8">
            <div class="ces-card mb-4">
                <div class="ces-card-header"><h3 class="card-title">Assessment Details</h3></div>
                <div class="ces-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Barangay 96 Needs Assessment 2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select name="barangay_id" class="form-select" required>
                                <option value="">Select barangay...</option>
                                <?php foreach ($barangays as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assessment Date <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description / Background</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Brief background and context of the assessment..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Needs Items -->
            <div class="ces-card">
                <div class="ces-card-header">
                    <h3 class="card-title">Identified Needs</h3>
                    <button type="button" class="btn-ces-outline" id="addNeedBtn" style="font-size:.8rem;padding:5px 12px;">
                        <i class="fa fa-plus"></i> Add Need
                    </button>
                </div>
                <div class="ces-card-body">
                    <div id="needsContainer">
                        <!-- Row template (first row pre-loaded) -->
                        <div class="need-row border rounded p-3 mb-3" id="need-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-muted" style="font-size:.8rem;">NEED #1</span>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-need" style="display:none;"><i class="fa fa-times"></i></button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Area</label>
                                    <select name="area[]" class="form-select form-select-sm" required>
                                        <?php foreach ($areas as $a): ?><option value="<?= $a ?>"><?= $a ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Severity</label>
                                    <select name="severity[]" class="form-select form-select-sm">
                                        <?php foreach ($severities as $val=>$lbl): ?><option value="<?= $val ?>"><?= $lbl ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Problem / Need <span class="text-danger">*</span></label>
                                    <textarea name="problem[]" class="form-control form-control-sm" rows="2" placeholder="Describe the identified problem..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Recommended Action</label>
                                    <input type="text" name="recommended_action[]" class="form-control form-control-sm" placeholder="Suggested intervention...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted" style="font-size:.8rem;"><i class="fa fa-info-circle me-1"></i>Add at least one identified need to submit.</p>
                </div>
            </div>
        </div>

        <!-- Right: sidebar -->
        <div class="col-lg-4">
            <div class="ces-card">
                <div class="ces-card-header"><h3 class="card-title">Publish</h3></div>
                <div class="ces-card-body d-grid gap-2">
                    <button type="submit" name="action" value="draft" class="btn-ces-outline">
                        <i class="fa fa-save"></i> Save as Draft
                    </button>
                    <button type="submit" name="action" value="submit" class="btn-ces-primary">
                        <i class="fa fa-paper-plane"></i> Submit for Approval
                    </button>
                    <a href="<?= url('community-needs') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function () {
    let count = 1;
    const areas = <?= json_encode($areas) ?>;
    const sevs  = <?= json_encode($severities) ?>;

    document.getElementById('addNeedBtn').addEventListener('click', function () {
        count++;
        const container = document.getElementById('needsContainer');
        const div = document.createElement('div');
        div.className = 'need-row border rounded p-3 mb-3';
        div.id = 'need-' + count;

        let areaOpts = areas.map(a => `<option value="${a}">${a}</option>`).join('');
        let sevOpts  = Object.entries(sevs).map(([v,l]) => `<option value="${v}">${l}</option>`).join('');

        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-muted" style="font-size:.8rem;">NEED #${count}</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-need"><i class="fa fa-times"></i></button>
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Area</label>
                    <select name="area[]" class="form-select form-select-sm">${areaOpts}</select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Severity</label>
                    <select name="severity[]" class="form-select form-select-sm">${sevOpts}</select>
                </div>
                <div class="col-12">
                    <label class="form-label">Problem / Need <span class="text-danger">*</span></label>
                    <textarea name="problem[]" class="form-control form-control-sm" rows="2" placeholder="Describe the identified problem..." required></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Recommended Action</label>
                    <input type="text" name="recommended_action[]" class="form-control form-control-sm" placeholder="Suggested intervention...">
                </div>
            </div>`;

        container.appendChild(div);
        div.querySelector('.remove-need').addEventListener('click', function () { div.remove(); });
    });
})();
</script>
