<?php

/**
 * Session Helper
 * Manages PHP session lifecycle and flash messages.
 */
class Session
{
    /**
     * Start the session with secure settings.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => false, // set true in production (HTTPS)
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    /** Set a session value. */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /** Get a session value with an optional default. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /** Check if a session key exists. */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /** Remove a session key. */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /** Destroy the entire session. */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    /** Regenerate session ID (prevent fixation). */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    // ── Flash Messages ───────────────────────────────────────

    /**
     * Set a one-time flash message.
     * Types: success | error | warning | info
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    /**
     * Retrieve and clear flash messages for a given type.
     * Returns an empty array if none exist.
     */
    public static function getFlash(string $type): array
    {
        $messages = $_SESSION['_flash'][$type] ?? [];
        unset($_SESSION['_flash'][$type]);
        return $messages;
    }

    /** Check if any flash messages exist. */
    public static function hasFlash(string $type): bool
    {
        return !empty($_SESSION['_flash'][$type]);
    }

    // ── CSRF ─────────────────────────────────────────────────

    /** Generate and store a CSRF token. */
    public static function generateCsrf(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /** Validate a submitted CSRF token. */
    public static function validateCsrf(string $token): bool
    {
        return isset($_SESSION['_csrf_token'])
            && hash_equals($_SESSION['_csrf_token'], $token);
    }
}
