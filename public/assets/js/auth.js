/**
 * UCC-CES — Modern Auth Page Scripts
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Password Toggle ──────────────────────────────────────
    document.querySelectorAll('.auth-toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target || 'password';
            const input    = document.getElementById(targetId);
            const icon     = this.querySelector('i');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // ── Password Strength ────────────────────────────────────
    const pwInput = document.getElementById('password');
    if (pwInput && document.getElementById('pw-strength-bar')) {
        pwInput.addEventListener('input', function () { updateStrength(this.value); });
    }

    function updateStrength(pw) {
        const bar   = document.getElementById('pw-strength-bar');
        const label = document.getElementById('pw-strength-label');
        if (!bar) return;

        let score = 0;
        if (pw.length >= 8)            score++;
        if (pw.length >= 12)           score++;
        if (/[A-Z]/.test(pw))          score++;
        if (/[0-9]/.test(pw))          score++;
        if (/[^A-Za-z0-9]/.test(pw))   score++;

        const levels = [
            { w: '0%',   color: '#e2e8f0', label: '',        labelColor: '#94a3b8' },
            { w: '25%',  color: '#f87171', label: 'Weak',    labelColor: '#dc2626' },
            { w: '50%',  color: '#fb923c', label: 'Fair',    labelColor: '#ea580c' },
            { w: '70%',  color: '#facc15', label: 'Good',    labelColor: '#ca8a04' },
            { w: '85%',  color: '#4ade80', label: 'Strong',  labelColor: '#16a34a' },
            { w: '100%', color: '#22c55e', label: 'Very Strong', labelColor: '#15803d' },
        ];

        const lvl = levels[Math.min(score, 5)];
        bar.style.width      = lvl.w;
        bar.style.background = lvl.color;
        if (label) { label.textContent = lvl.label; label.style.color = lvl.labelColor; }
    }

    // ── Password Confirm Match ───────────────────────────────
    const confirmInput = document.getElementById('password_confirm');
    if (confirmInput && pwInput) {
        confirmInput.addEventListener('input', function () {
            if (this.value && this.value !== pwInput.value) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }

    // ── Auto-dismiss alerts ──────────────────────────────────
    document.querySelectorAll('.alert.alert-dismissible').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s ease, transform .4s ease, max-height .4s ease';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(-8px)';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });

    // ── Button loading state ─────────────────────────────────
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('.btn-ces[type="submit"], button[type="submit"].btn-ces');
            if (btn) {
                setTimeout(function () {
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Signing in...';
                    btn.disabled = true;
                }, 10);
            }
        });
    });

    // ── Animated counter for stats ───────────────────────────
    document.querySelectorAll('.stat-number').forEach(function (el) {
        const rawText = el.textContent.trim();
        const hasPlusSign = rawText.endsWith('+');
        const numericStr = rawText.replace(/[^0-9.]/g, '');
        const target = parseFloat(numericStr);
        if (isNaN(target) || target === 0) return;

        el.textContent = '0' + (hasPlusSign ? '+' : '');
        let start = null;
        const duration = 1200;

        function step(timestamp) {
            if (!start) start = timestamp;
            const progress = Math.min((timestamp - start) / duration, 1);
            // Ease out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(eased * target);
            el.textContent = current.toLocaleString() + (hasPlusSign ? '+' : '');
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString() + (hasPlusSign ? '+' : '');
        }

        // Trigger when visible
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    requestAnimationFrame(step);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.1 });
        observer.observe(el);
    });

    // ── Staggered input focus animations ─────────────────────
    document.querySelectorAll('.auth-card .form-control, .auth-card .form-select').forEach(function (el, i) {
        el.style.animationDelay = (i * 0.07) + 's';
        el.classList.add('fade-in-field');
    });

    // ── Input float animation on type ───────────────────────
    document.querySelectorAll('.auth-input').forEach(function (input) {
        input.addEventListener('focus', function () {
            this.closest('.mb-3, .col-6, .col-7, .col-5, .col-md-4, .col-md-6')
                ?.querySelector('.form-label')
                ?.classList.add('label-focused');
        });
        input.addEventListener('blur', function () {
            if (!this.value) {
                this.closest('.mb-3, .col-6, .col-7, .col-5, .col-md-4, .col-md-6')
                    ?.querySelector('.form-label')
                    ?.classList.remove('label-focused');
            }
        });
    });
});
