<?php
/**
 * Sidebar partial
 * Role-based nav items are shown/hidden via Auth::can()
 */
$currentUrl = $_GET['url'] ?? '';
$segment    = explode('/', trim($currentUrl, '/'))[0] ?? '';

/**
 * Helper â€” render a nav link
 */
function navLink(string $href, string $icon, string $label, string $activeSegment, string $current): void
{
    $isActive = ($current === $activeSegment) ? 'active' : '';
    echo '<a href="' . url($href) . '" class="nav-item-ces ' . $isActive . '" data-tooltip="' . e($label) . '">'
        . '<span class="nav-icon"><i class="fa ' . $icon . '"></i></span>'
        . '<span class="nav-label">' . e($label) . '</span>'
        . '</a>';
}
?>

<aside class="sidebar" id="sidebar">

    <!-- Brand -->
    <a href="<?= url('dashboard') ?>" class="sidebar-brand">
        <img src="<?= asset('assets/img/ucc-seal.svg') ?>" alt="UCC">
        <div class="sidebar-brand-text">
            <span class="brand-name">UCC â€” CES</span>
            <span class="brand-sub">Management System</span>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav">

        <!-- Main -->
        <div class="sidebar-section-title">Main</div>
        <?php navLink('dashboard', 'fa-tachometer-alt', 'Dashboard', 'dashboard', $segment); ?>

        <?php if (Auth::can('cna.view')): ?>
            <!-- CES Process -->
            <div class="sidebar-section-title">CES Process</div>
            <?php navLink('community-needs', 'fa-search-location', 'Needs Assessment', 'community-needs', $segment); ?>
        <?php endif; ?>

        <?php if (Auth::can('proposals.view')): ?>
            <?php navLink('proposals', 'fa-file-alt', 'Project Proposals', 'proposals', $segment); ?>
        <?php endif; ?>

        <?php if (Auth::can('activities.view')): ?>
            <?php navLink('activities', 'fa-calendar-check', 'Activities', 'activities', $segment); ?>
        <?php endif; ?>

        <!-- Records -->
        <div class="sidebar-section-title">Records</div>

        <?php if (Auth::can('participants.view')): ?>
            <?php navLink('participants', 'fa-users', 'Participants', 'participants', $segment); ?>
        <?php endif; ?>

        <?php if (Auth::can('attendance.view')): ?>
            <?php navLink('attendance', 'fa-clipboard-list', 'Attendance', 'attendance', $segment); ?>
        <?php endif; ?>

        <?php if (Auth::can('certificates.view')): ?>
            <?php navLink('certificates', 'fa-certificate', 'Certificates', 'certificates', $segment); ?>
        <?php endif; ?>

        <?php if (Auth::can('evaluations.submit')): ?>
            <?php navLink('evaluations', 'fa-star-half-alt', 'Evaluations', 'evaluations', $segment); ?>
        <?php endif; ?>

        <!-- Management -->
        <?php if (Auth::can('financial.view') || Auth::can('monitoring.view') || Auth::can('linkages.view')): ?>
            <div class="sidebar-section-title">Management</div>

            <?php if (Auth::can('monitoring.view')): ?>
                <?php navLink('monitoring', 'fa-chart-line', 'Monitoring', 'monitoring', $segment); ?>
            <?php endif; ?>

            <?php if (Auth::can('financial.view')): ?>
                <?php navLink('financial', 'fa-money-bill-wave', 'Financial', 'financial', $segment); ?>
            <?php endif; ?>

            <?php if (Auth::can('linkages.view')): ?>
                <?php navLink('linkages', 'fa-handshake', 'Linkages', 'linkages', $segment); ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Admin -->
        <?php if (Auth::can('users.view') || Auth::can('audit.view')): ?>
            <div class="sidebar-section-title">Administration</div>

            <?php if (Auth::can('users.view')): ?>
                <?php navLink('users', 'fa-user-cog', 'User Management', 'users', $segment); ?>
            <?php endif; ?>

            <?php if (Auth::can('audit.view')): ?>
                <?php navLink('audit', 'fa-history', 'Audit Logs', 'audit', $segment); ?>
            <?php endif; ?>
        <?php endif; ?>

    </nav>

    <!-- User profile at bottom -->
    <div class="sidebar-user">
        <div class="sidebar-avatar">
            <?php if (Auth::user()['avatar']): ?>
                <img src="<?= asset('uploads/' . e(Auth::user()['avatar'])) ?>" alt="">
            <?php else: ?>
                <?= strtoupper(substr(Auth::user()['first_name'], 0, 1) . substr(Auth::user()['last_name'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div class="sidebar-user-info">
            <span class="user-name"><?= e(Auth::fullName()) ?></span>
            <span class="user-role"><?= e(ROLE_LABELS[Auth::role()] ?? Auth::role()) ?></span>
        </div>
    </div>

</aside>

