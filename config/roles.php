<?php

/**
 * Role Definitions & Permission Map
 * UCC-CES Management System
 */

// Available roles
define('ROLE_ADMIN',        'admin');
define('ROLE_CES_HEAD',     'ces_head');
define('ROLE_PROGRAM_HEAD', 'program_head');
define('ROLE_FACULTY',      'faculty');
define('ROLE_STUDENT',      'student');

// Role labels for display
define('ROLE_LABELS', [
    'admin'        => 'System Administrator',
    'ces_head'     => 'CES Head',
    'program_head' => 'Program Head',
    'faculty'      => 'Faculty',
    'student'      => 'Student',
]);

// Role hierarchy: higher index = more permissions
define('ROLE_HIERARCHY', [
    'student'      => 1,
    'faculty'      => 2,
    'program_head' => 3,
    'ces_head'     => 4,
    'admin'        => 5,
]);

/**
 * Module permission map.
 * Format: 'module.action' => [allowed_roles]
 * '*' means all authenticated roles.
 */
define('PERMISSIONS', [

    // ── Dashboard ──────────────────────────────────────────────
    'dashboard.view'               => ['admin', 'ces_head', 'program_head', 'faculty', 'student'],

    // ── Users ──────────────────────────────────────────────────
    'users.view'                   => ['admin'],
    'users.create'                 => ['admin'],
    'users.edit'                   => ['admin'],
    'users.delete'                 => ['admin'],

    // ── Barangays ──────────────────────────────────────────────
    'barangays.view'               => ['admin', 'ces_head', 'program_head', 'faculty'],
    'barangays.manage'             => ['admin', 'ces_head'],

    // ── Community Needs Analysis ───────────────────────────────
    'cna.view'                     => ['admin', 'ces_head', 'program_head', 'faculty'],
    'cna.create'                   => ['admin', 'ces_head', 'program_head', 'faculty'],
    'cna.edit'                     => ['admin', 'ces_head', 'program_head', 'faculty'],
    'cna.approve'                  => ['admin', 'ces_head'],
    'cna.delete'                   => ['admin', 'ces_head'],

    // ── Project Proposals ──────────────────────────────────────
    'proposals.view'               => ['admin', 'ces_head', 'program_head', 'faculty'],
    'proposals.create'             => ['admin', 'ces_head', 'program_head', 'faculty'],
    'proposals.edit'               => ['admin', 'ces_head', 'program_head', 'faculty'],
    'proposals.approve'            => ['admin', 'ces_head'],
    'proposals.delete'             => ['admin', 'ces_head'],

    // ── Activities ─────────────────────────────────────────────
    'activities.view'              => ['admin', 'ces_head', 'program_head', 'faculty', 'student'],
    'activities.manage'            => ['admin', 'ces_head', 'program_head', 'faculty'],

    // ── Participants ───────────────────────────────────────────
    'participants.view'            => ['admin', 'ces_head', 'program_head', 'faculty'],
    'participants.manage'          => ['admin', 'ces_head', 'program_head', 'faculty'],

    // ── Attendance ─────────────────────────────────────────────
    'attendance.view'              => ['admin', 'ces_head', 'program_head', 'faculty', 'student'],
    'attendance.record'            => ['admin', 'ces_head', 'program_head', 'faculty'],

    // ── Certificates ───────────────────────────────────────────
    'certificates.view'            => ['admin', 'ces_head', 'program_head', 'faculty', 'student'],
    'certificates.generate'        => ['admin', 'ces_head', 'program_head', 'faculty'],

    // ── Evaluations ────────────────────────────────────────────
    'evaluations.view'             => ['admin', 'ces_head', 'program_head', 'faculty'],
    'evaluations.submit'           => ['admin', 'ces_head', 'program_head', 'faculty', 'student'],
    'evaluations.results'          => ['admin', 'ces_head', 'program_head'],

    // ── Financial ──────────────────────────────────────────────
    'financial.view'               => ['admin', 'ces_head', 'program_head'],
    'financial.manage'             => ['admin', 'ces_head'],

    // ── Monitoring ─────────────────────────────────────────────
    'monitoring.view'              => ['admin', 'ces_head', 'program_head', 'faculty'],
    'monitoring.log'               => ['admin', 'ces_head', 'program_head', 'faculty'],

    // ── Linkages ───────────────────────────────────────────────
    'linkages.view'                => ['admin', 'ces_head', 'program_head'],
    'linkages.manage'              => ['admin', 'ces_head'],

    // ── Audit Logs ─────────────────────────────────────────────
    'audit.view'                   => ['admin', 'ces_head'],
]);
