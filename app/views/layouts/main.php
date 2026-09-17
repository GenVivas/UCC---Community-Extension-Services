<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> &mdash; <?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= asset('assets/vendor/fontawesome/css/all.min.css') ?>">
    <!-- App CSS -->
    <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body>

<div class="app-layout">

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <?php require APP_PATH . '/views/layouts/sidebar.php'; ?>

    <!-- Main -->
    <div class="main-content" id="mainContent">

        <!-- Topbar -->
        <?php require APP_PATH . '/views/layouts/header.php'; ?>

        <!-- Page body -->
        <main class="page-content">
            <?php renderFlash(); ?>
            <?= $content ?>
        </main>

        <!-- Footer -->
        <?php require APP_PATH . '/views/layouts/footer.php'; ?>

    </div><!-- /main-content -->

</div><!-- /app-layout -->

<!-- Bootstrap JS -->
<script src="<?= asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- App JS -->
<script src="<?= asset('assets/js/app.js') ?>"></script>

</body>
</html>
