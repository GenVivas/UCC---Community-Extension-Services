<?php

/**
 * Application Entry Point
 * UCC-CES Management System
 * All requests are routed through this file via .htaccess
 */

// ── Load Configuration ───────────────────────────────────────
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/roles.php';

// ── Load Core Classes ────────────────────────────────────────
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Middleware.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/App.php';

// ── Load Helpers ─────────────────────────────────────────────
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/validation.php';

// ── Start Session ────────────────────────────────────────────
Session::start();

// ── Bootstrap Application ────────────────────────────────────
new App();
