<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= asset('assets/vendor/fontawesome/css/all.min.css') ?>">
    <!-- Auth CSS -->
    <link rel="stylesheet" href="<?= asset('assets/css/auth.css') ?>">

    <style>
        /* Field fade-in animation */
        @keyframes fadeInField {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-field {
            animation: fadeInField .4s ease both;
        }
        /* Label focused state */
        .label-focused {
            color: #1a4d2a !important;
        }
    </style>
</head>
<body class="auth-body">

<!-- Noise texture overlay -->
<div class="auth-noise" aria-hidden="true"></div>

<?= $content ?>

<!-- Bootstrap JS -->
<script src="<?= asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- Auth JS -->
<script src="<?= asset('assets/js/auth.js') ?>"></script>

</body>
</html>
