<!-- ══════════════════════════════════════════════════════════
     UCC-CES Login Page — Modern Redesign
     ══════════════════════════════════════════════════════════ -->

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

<!-- Main -->
<div class="auth-wrapper">

    <!-- ── LEFT: Branding ────────────────────────────────── -->
    <div class="auth-left">

        <div class="auth-seal-wrap">
            <img src="<?= asset('assets/img/ucc-seal.svg') ?>" alt="UCC" class="auth-seal">
        </div>

        <h1>University of<br>Caloocan City</h1>
        <p class="ces-label">Community Extension Services</p>
        <p class="ces-desc">
            Integrated management system for CES programs,
            projects, volunteer coordination, and community
            partnerships.
        </p>

        <!-- Live stats -->
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

    <!-- ── RIGHT: Login Card ─────────────────────────────── -->
    <div class="auth-right">
        <div class="auth-card">

            <div class="auth-card-header">
                <h2>Sign in to CES Portal</h2>
                <p>Enter your UCC credentials to continue</p>
            </div>

            <?php renderFlash(); ?>

            <form method="POST" action="<?= url('auth/login-post') ?>" novalidate>
                <?php csrfField(); ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control auth-input"
                        placeholder="j.aguilar@ucc.edu.ph"
                        value="<?= e($_POST['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control auth-input border-end-0"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="btn btn-outline-secondary auth-toggle-pw" tabindex="-1" aria-label="Toggle password">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-ces">
                    Sign In
                </button>

            </form>

            <div class="auth-footer-link">
                Don't have an account? <a href="<?= url('auth/register') ?>">Register here</a>
            </div>

            <div class="auth-card-footer">
                Academic Year 2025–2026 &nbsp;&bull;&nbsp; UCC Office of CES
            </div>

        </div>
    </div>

</div>

<!-- Page Footer -->
<div class="auth-page-footer">
    &copy; <?= date('Y') ?> University of Caloocan City &mdash; CES Management System v<?= APP_VERSION ?>
</div>
