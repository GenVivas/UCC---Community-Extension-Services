<?php

/**
 * Auth Helper
 * Handles login state, user retrieval, and permission checks.
 */
class Auth
{
    /** Check if a user is currently logged in. */
    public static function check(): bool
    {
        return Session::has('auth_user');
    }

    /** Get the authenticated user array (or null). */
    public static function user(): ?array
    {
        return Session::get('auth_user');
    }

    /** Get a specific field of the logged-in user. */
    public static function id(): ?int
    {
        return Session::get('auth_user')['id'] ?? null;
    }

    public static function role(): ?string
    {
        return Session::get('auth_user')['role'] ?? null;
    }

    public static function fullName(): string
    {
        $user = Session::get('auth_user');
        if (!$user) return 'Guest';
        return trim($user['first_name'] . ' ' . $user['last_name']);
    }

    /**
     * Log in a user by storing their data in the session.
     * Regenerates session ID to prevent fixation.
     */
    public static function login(array $user): void
    {
        Session::regenerate();
        // Store only what's needed — never store raw password
        Session::set('auth_user', [
            'id'         => (int) $user['id'],
            'program_id' => $user['program_id'] ? (int) $user['program_id'] : null,
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'avatar'     => $user['avatar'],
        ]);
        Session::set('auth_time', time());
    }

    /** Log the current user out. */
    public static function logout(): void
    {
        Session::remove('auth_user');
        Session::remove('auth_time');
        Session::destroy();
    }

    /**
     * Check if the logged-in user has a specific role.
     * Accepts a single role string or an array of roles.
     */
    public static function hasRole(array|string $roles): bool
    {
        $userRole = self::role();
        if (!$userRole) return false;
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($userRole, $roles, true);
    }

    /**
     * Check if the logged-in user can perform an action.
     * Uses the PERMISSIONS map from config/roles.php.
     */
    public static function can(string $permission): bool
    {
        $userRole = self::role();
        if (!$userRole) return false;
        $allowed = PERMISSIONS[$permission] ?? [];
        return in_array($userRole, $allowed, true);
    }

    /**
     * Check if the session has timed out.
     */
    public static function isTimedOut(): bool
    {
        $authTime = Session::get('auth_time');
        if (!$authTime) return true;
        return (time() - $authTime) > SESSION_TIMEOUT;
    }

    /**
     * Refresh the session activity timestamp.
     */
    public static function touchSession(): void
    {
        Session::set('auth_time', time());
    }
}
