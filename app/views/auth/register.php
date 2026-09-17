<?php $old = Session::get('reg_old', []); Session::remove('reg_old'); ?>

<!-- Top Navbar -->
<nav class="auth-navbar">
    <img src="<?= asset('assets/img/ucc-seal.svg') ?>" alt="UCC Seal" class="auth-navbar-logo">
    <span class="auth-navbar-school">University of Caloocan City</span>
    <span class="auth-navbar-divider">|</span>
    <span class="auth-navbar-ces">Community Extension Services</span>
    <div class="auth-navbar-spacer"></div>
    <div class="auth-navbar-badge">
        <span class="dot"></span>
        System Online
    </div>
</nav>

<div class="auth-wrapper">

    <!-- LEFT -->
    <div class="auth-left">
        <div class="auth-seal-wrap">
            <img src="<?= asset('assets/img/ucc-seal.svg') ?>" alt="UCC" class="auth-seal">
        </div>
        <h1>Join the CES<br>Community</h1>
        <p class="ces-label">Student Volunteer Portal</p>
        <p class="ces-desc">
            Register as a student volunteer and be part of UCC's
            community extension programs in Barangay 96
            and partner communities.
        </p>
        <div class="auth-stats">
            <div class="stat-item">
                <span class="stat-number"><?= number_format($stats['activeProjects']) ?></span>
                <span class="stat-label">Active Projects</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= number_format($stats['volunteers']) ?></span>
                <span class="stat-label">Volunteers</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= number_format($stats['partners']) ?></span>
                <span class="stat-label">Partners</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= number_format($stats['beneficiaries']) ?>+</span>
                <span class="stat-label">Beneficiaries</span>
            </div>
        </div>
    </div>

    <!-- RIGHT: Register Card -->
    <div class="auth-right">
        <div class="auth-card auth-card--register">

            <div class="auth-card-header">
                <h2>Create Account</h2>
                <p>Register as a student volunteer for UCC-CES</p>
            </div>

            <?php renderFlash(); ?>

            <form method="POST" action="<?= url('auth/register-post') ?>" novalidate>
                <?php csrfField(); ?>

                <!-- Name row -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control auth-input"
                            placeholder="Juan"
                            value="<?= e($old['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control auth-input"
                            placeholder="Dela Cruz"
                            value="<?= e($old['last_name'] ?? '') ?>" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">UCC Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control auth-input"
                        placeholder="j.delacruz@ucc.edu.ph"
                        value="<?= e($old['email'] ?? '') ?>"
                        required autocomplete="email">
                </div>

                <!-- Program + ID -->
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <label class="form-label">Program <span class="text-danger">*</span></label>
                        <select name="program_id" class="form-select auth-input" required>
                            <option value="">Select program...</option>
                            <?php foreach ($programs as $p): ?>
                                <option value="<?= e($p['id']) ?>"
                                    <?= ($old['program_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                    <?= e($p['code']) ?> — <?= e($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-5">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" class="form-control auth-input"
                            placeholder="2024-00001"
                            value="<?= e($old['employee_id'] ?? '') ?>">
                    </div>
                </div>

                <!-- Contact -->
                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="tel" name="contact_no" class="form-control auth-input"
                        placeholder="09XX-XXX-XXXX"
                        value="<?= e($old['contact_no'] ?? '') ?>">
                </div>

                <!-- Passwords -->
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                class="form-control auth-input border-end-0"
                                placeholder="Min. 8 characters" required autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary auth-toggle-pw"
                                tabindex="-1" data-target="password">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <div class="pw-strength-bar-wrap">
                            <div id="pw-strength-bar"></div>
                        </div>
                        <small id="pw-strength-label"></small>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Confirm <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="password_confirm" name="password_confirm"
                                class="form-control auth-input border-end-0"
                                placeholder="Repeat password" required autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary auth-toggle-pw"
                                tabindex="-1" data-target="password_confirm">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-ces">
                    Create Account
                </button>
            </form>

            <div class="auth-footer-link">
                Already have an account? <a href="<?= url('auth/login') ?>">Sign in here</a>
            </div>

            <div class="auth-card-footer">
                Academic Year 2025–2026 &nbsp;&bull;&nbsp; UCC Office of CES
            </div>

        </div>
    </div>

</div>

<div class="auth-page-footer">
    &copy; <?= date('Y') ?> University of Caloocan City &mdash; CES Management System v<?= APP_VERSION ?>
</div>
