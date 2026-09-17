<?php
$user      = Auth::user();
$initials  = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<header class="topbar">
    <!-- Left: toggle + page title -->
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fa fa-bars"></i>
        </button>
        <h1 class="page-title"><?= e($pageTitle) ?></h1>
    </div>

    <!-- Right: actions + user dropdown -->
    <div class="topbar-right">

        <!-- Notifications (placeholder) -->
        <button class="topbar-btn" title="Notifications">
            <i class="fa fa-bell"></i>
            <span class="topbar-badge"></span>
        </button>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="topbar-user dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="topbar-avatar">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= asset('uploads/' . e($user['avatar'])) ?>" alt="">
                    <?php else: ?>
                        <?= e($initials) ?>
                    <?php endif; ?>
                </div>
                <span class="topbar-user-name"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <span class="dropdown-item-text px-3 py-2">
                        <div style="font-weight:700;font-size:.85rem;"><?= e(Auth::fullName()) ?></div>
                        <div style="font-size:.75rem;color:#888;"><?= e(ROLE_LABELS[Auth::role()] ?? Auth::role()) ?></div>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?= url('profile') ?>">
                        <i class="fa fa-user"></i> My Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= url('auth/logout') ?>">
                        <i class="fa fa-sign-out-alt"></i> Sign Out
                    </a>
                </li>
            </ul>
        </div>

    </div>
</header>
