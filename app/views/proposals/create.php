<?php
$areas = ['Education','Technology','Health','Sanitation','Livelihood','Environment','Other'];
?>
<div class="ces-breadcrumb mb-3">
    <a href="<?= url('proposals') ?>">Proposals</a>
    <span class="separator"><i class="fa fa-chevron-right" style="font-size:.6rem;"></i></span>
    <span class="current">New Proposal</span>
</div>

<form method="POST" action="<?= url('proposals/store') ?>" enctype="multipart/form-data">
    <?php csrfField(); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="ces-card mb-4">
                <div class="ces-card-header"><h3 class="card-title">Proposal Information</h3></div>
                <div class="ces-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Project title..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program <span class="text-danger">*</span></label>
                            <select name="program_id" class="form-select" required>
                                <option value="">Select program...</option>
                                <?php foreach ($programs as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= e($p['code'].' — '.$p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
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
                            <label class="form-label">Target Area <span class="text-danger">*</span></label>
                            <select name="target_area" class="form-select" required>
                                <option value="">Select area...</option>
                                <?php foreach ($areas as $a): ?><option value="<?= $a ?>"><?= $a ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Based on CNA</label>
                            <select name="assessment_id" class="form-select">
                                <option value="">None / Independent</option>
                                <?php foreach ($cnas as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $cnaId==$c['id']?'selected':'' ?>><?= e($c['title'].' ('.$c['barangay_name'].')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe the project..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Objectives</label>
                            <textarea name="objectives" class="form-control" rows="3" placeholder="List the project objectives..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Target Beneficiaries</label>
                            <textarea name="target_beneficiaries" class="form-control" rows="2" placeholder="Who will benefit from this project?"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Expected Output</label>
                            <textarea name="expected_output" class="form-control" rows="2" placeholder="What are the expected results?"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline & Budget -->
            <div class="ces-card mb-4">
                <div class="ces-card-header"><h3 class="card-title">Timeline & Budget</h3></div>
                <div class="ces-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Budget Requested (₱)</label>
                            <input type="number" name="budget_requested" class="form-control" placeholder="0.00" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachment -->
            <div class="ces-card">
                <div class="ces-card-header"><h3 class="card-title">Attachment</h3></div>
                <div class="ces-card-body">
                    <label class="form-label">Upload Proposal Document (PDF/DOC, max 5MB)</label>
                    <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx">
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ces-card">
                <div class="ces-card-header"><h3 class="card-title">Submit</h3></div>
                <div class="ces-card-body d-grid gap-2">
                    <button type="submit" name="action" value="draft" class="btn-ces-outline"><i class="fa fa-save"></i> Save as Draft</button>
                    <button type="submit" name="action" value="submit" class="btn-ces-primary"><i class="fa fa-paper-plane"></i> Submit for Approval</button>
                    <a href="<?= url('proposals') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
