<?php

/**
 * Middleware
 * Guards routes based on authentication state and role permissions.
 */
class Middleware
{
    /**
     * Require a logged-in user.
     * Redirects to login if not authenticated or session timed out.
     */
    public static function requireAuth(): void
    {
        if (!Auth::check() || Auth::isTimedOut()) {
            Auth::logout();
            Session::flash('error', 'Your session has expired. Please log in again.');
            redirect('auth/login');
        }
        // Refresh session activity
        Auth::touchSession();
    }

    /**
     * Require one or more specific roles.
     * Call after requireAuth().
     *
     * @param array|string $roles
     */
    public static function requireRole(array|string $roles): void
    {
        if (!Auth::hasRole($roles)) {
            Session::flash('error', 'You do not have permission to access that page.');
            redirect('dashboard');
        }
    }

    /**
     * Require a specific permission key.
     * Call after requireAuth().
     *
     * @param string $permission e.g. 'proposals.approve'
     */
    public static function requirePermission(string $permission): void
    {
        if (!Auth::can($permission)) {
            Session::flash('error', 'You do not have permission to perform that action.');
            redirect('dashboard');
        }
    }

    /**
     * Redirect already-authenticated users away from guest-only pages (e.g. login).
     */
    public static function requireGuest(): void
    {
        if (Auth::check() && !Auth::isTimedOut()) {
            redirect('dashboard');
        }
    }
}
