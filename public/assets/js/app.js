/**
 * App Global Scripts
 * UCC-CES Management System
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar Toggle ───────────────────────────────────────
    const sidebar        = document.getElementById('sidebar');
    const mainContent    = document.getElementById('mainContent');
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    const COLLAPSED_KEY = 'ces_sidebar_collapsed';

    // Restore state on desktop
    if (window.innerWidth > 991) {
        if (localStorage.getItem(COLLAPSED_KEY) === '1') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth <= 991) {
                // Mobile: slide in/out
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('active');
            } else {
                // Desktop: collapse/expand
                const isCollapsed = sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded', isCollapsed);
                localStorage.setItem(COLLAPSED_KEY, isCollapsed ? '1' : '0');
            }
        });
    }

    // Close sidebar on overlay click (mobile)
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
        });
    }

    // ── Auto-dismiss alerts ──────────────────────────────────
    document.querySelectorAll('.alert.alert-dismissible').forEach(function (el) {
        setTimeout(function () {
            const a = bootstrap.Alert.getOrCreateInstance(el);
            if (a) a.close();
        }, 5000);
    });

    // ── Confirm delete ───────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = this.dataset.confirm || 'Are you sure?';
            if (!confirm(msg)) e.preventDefault();
        });
    });

    // ── Tooltips ─────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // ── Active nav highlight (fallback) ──────────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-item-ces').forEach(function (link) {
        if (link.href && currentPath.includes(link.getAttribute('href').split('/').pop())) {
            link.classList.add('active');
        }
    });
});
