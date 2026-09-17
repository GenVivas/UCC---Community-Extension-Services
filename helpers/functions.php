<?php

/**
 * Global Helper Functions
 * UCC-CES Management System
 */

// ── URL & Redirect ───────────────────────────────────────────

/**
 * Generate a full URL relative to BASE_URL.
 */
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Generate a URL to a public asset.
 * e.g. asset('assets/css/app.css')
 */
function asset(string $path): string
{
    return url('public/' . ltrim($path, '/'));
}

/**
 * Redirect to a path relative to BASE_URL and exit.
 */
function redirect(string $path = ''): void
{
    header('Location: ' . url($path));
    exit;
}

// ── Output & Sanitization ────────────────────────────────────

/**
 * Escape a string for safe HTML output.
 */
function e(string|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Echo an escaped value.
 */
function out(string|null $value): void
{
    echo e($value);
}

// ── Date & Time ──────────────────────────────────────────────

/**
 * Format a date string for display.
 */
function formatDate(?string $date, string $format = 'F d, Y'): string
{
    if (empty($date)) return '—';
    return date($format, strtotime($date));
}

/**
 * Format a datetime string for display.
 */
function formatDateTime(?string $datetime, string $format = 'F d, Y h:i A'): string
{
    if (empty($datetime)) return '—';
    return date($format, strtotime($datetime));
}

/**
 * Return a human-readable "time ago" string.
 */
function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400)  return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return formatDate($datetime);
}

// ── String Helpers ───────────────────────────────────────────

/**
 * Truncate a string to a given length, appending '...' if truncated.
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Convert a snake_case or kebab-case string to Title Case.
 */
function titleCase(string $value): string
{
    return ucwords(str_replace(['_', '-'], ' ', $value));
}

// ── Number & Currency ────────────────────────────────────────

/**
 * Format a number as Philippine Peso.
 */
function peso(float|string|null $amount): string
{
    if ($amount === null || $amount === '') return '₱0.00';
    return '₱' . number_format((float) $amount, 2);
}

// ── Flash Messages ───────────────────────────────────────────

/**
 * Render all flash messages as Bootstrap alerts.
 * Call inside views where alerts should appear.
 */
function renderFlash(): void
{
    $types = ['success', 'error', 'warning', 'info'];
    $bsMap = ['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'];

    foreach ($types as $type) {
        if (Session::hasFlash($type)) {
            foreach (Session::getFlash($type) as $message) {
                $bsType = $bsMap[$type];
                echo '<div class="alert alert-' . $bsType . ' alert-dismissible fade show" role="alert">'
                    . e($message)
                    . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                    . '</div>';
            }
        }
    }
}

// ── CSRF ─────────────────────────────────────────────────────

/**
 * Render a hidden CSRF input field inside forms.
 */
function csrfField(): void
{
    $token = Session::generateCsrf();
    echo '<input type="hidden" name="_csrf" value="' . e($token) . '">';
}

// ── Badges & Status ──────────────────────────────────────────

/**
 * Return a Bootstrap badge for a proposal/activity status.
 */
function statusBadge(string $status): string
{
    $map = [
        'draft'     => 'secondary',
        'pending'   => 'warning',
        'approved'  => 'success',
        'rejected'  => 'danger',
        'ongoing'   => 'primary',
        'completed' => 'info',
        'cancelled' => 'dark',
        'scheduled' => 'secondary',
    ];
    $color = $map[strtolower($status)] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . e(titleCase($status)) . '</span>';
}

// ── Pagination ───────────────────────────────────────────────

/**
 * Render a Bootstrap pagination component.
 *
 * @param array  $paginator  Result from Model::paginate()
 * @param string $baseUrl    Current page URL without page param
 */
function renderPagination(array $paginator, string $baseUrl): void
{
    if ($paginator['pages'] <= 1) return;

    $current = $paginator['current'];
    $total   = $paginator['pages'];

    echo '<nav aria-label="Page navigation"><ul class="pagination justify-content-end mb-0">';

    // Previous
    if ($current > 1) {
        echo '<li class="page-item"><a class="page-link" href="' . e($baseUrl . '?page=' . ($current - 1)) . '">&laquo;</a></li>';
    }

    for ($i = 1; $i <= $total; $i++) {
        $active = $i === $current ? ' active' : '';
        echo '<li class="page-item' . $active . '"><a class="page-link" href="' . e($baseUrl . '?page=' . $i) . '">' . $i . '</a></li>';
    }

    // Next
    if ($current < $total) {
        echo '<li class="page-item"><a class="page-link" href="' . e($baseUrl . '?page=' . ($current + 1)) . '">&raquo;</a></li>';
    }

    echo '</ul></nav>';
}

// ── File Uploads ─────────────────────────────────────────────

/**
 * Handle a file upload and return the saved filename.
 * Returns null on failure.
 *
 * @param array  $file      $_FILES entry
 * @param string $directory Destination directory (absolute path)
 * @param array  $allowed   Allowed MIME types
 * @param int    $maxSize   Max size in bytes (default 5MB)
 */
function uploadFile(array $file, string $directory, array $allowed = ['application/pdf', 'image/jpeg', 'image/png'], int $maxSize = 5242880): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > $maxSize) return null;

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowed, true)) return null;

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('', true) . '.' . strtolower($ext);
    $dest     = rtrim($directory, '/') . '/' . $filename;

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $filename;
    }

    return null;
}

// ── Audit Logging ────────────────────────────────────────────

/**
 * Write an entry to the audit_logs table.
 */
function auditLog(string $action, string $module = '', ?int $recordId = null, string $description = ''): void
{
    try {
        $db     = Database::getInstance();
        $userId = Auth::id();
        $ip     = $_SERVER['REMOTE_ADDR'] ?? null;

        $stmt = $db->prepare(
            "INSERT INTO audit_logs (user_id, action, module, record_id, description, ip_address)
             VALUES (:user_id, :action, :module, :record_id, :description, :ip)"
        );
        $stmt->execute([
            ':user_id'     => $userId,
            ':action'      => $action,
            ':module'      => $module,
            ':record_id'   => $recordId,
            ':description' => $description,
            ':ip'          => $ip,
        ]);
    } catch (Throwable $e) {
        error_log('[AUDIT LOG ERROR] ' . $e->getMessage());
    }
}
