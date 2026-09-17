<?php $u = Auth::user(); ?>
<div class="page-header"><div class="page-header-left"><h2>My Profile</h2><p>Manage your account information.</p></div></div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="ces-card text-center p-4">
            <div class="mx-auto mb-3" style="width:80px;height:80px;border-radius:50%;background:var(--ces-green);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#fff;">
                <?= strtoupper(substr($u['first_name'],0,1).substr($u['last_name'],0,1)) ?>
            </div>
            <div style="font-size:1.1rem;font-weight:700;"><?= e($u['first_name'].' '.$u['last_name']) ?></div>
            <div class="mt-1"><?= statusBadge($u['role']) ?></div>
            <div style="font-size:.82rem;color:#888;margin-top:8px;"><?= e($u['email']) ?></div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="ces-card"><div class="ces-card-header"><h3 class="card-title">Account Details</h3></div>
        <div class="ces-card-body"><div class="row g-3">
            <div class="col-sm-6"><div class="detail-label">First Name</div><div class="detail-val"><?= e($u['first_name']) ?></div></div>
            <div class="col-sm-6"><div class="detail-label">Last Name</div><div class="detail-val"><?= e($u['last_name']) ?></div></div>
            <div class="col-sm-6"><div class="detail-label">Email</div><div class="detail-val"><?= e($u['email']) ?></div></div>
            <div class="col-sm-6"><div class="detail-label">Role</div><div class="detail-val"><?= e(ROLE_LABELS[$u['role']] ?? $u['role']) ?></div></div>
        </div></div></div>
    </div>
</div>
<style>.detail-label{font-size:.72rem;color:#888;text-transform:uppercase;font-weight:700;margin-bottom:3px;}.detail-val{font-size:.9rem;font-weight:500;}</style>
