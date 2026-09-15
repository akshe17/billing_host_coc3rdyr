<?php
// index.php

session_start();
require_once __DIR__ . '/config/config.php';

if (empty($_SESSION['user'])) {
    require __DIR__ . '/views/login.php';
    exit;
}

// ---------- Shared variables ----------
$currentUser = $_SESSION['user'];
$fullName    = trim($currentUser['first_name'] . ' ' . $currentUser['last_name']);
$roleId      = (int)$currentUser['role_id'];

// ---------- Requested page ----------
$defaultPage = match ($roleId) {
    1 => 'dashboard',
    2 => 'doctor',
    3 => 'nurse',
    4 => 'cashier',
    default => 'dashboard',
};

$page = $_GET['page'] ?? $defaultPage;

// ---------- Allowed pages per role ----------
$allowed = [
    1 => [
        // Main
        'dashboard',

        // People
        'users', 'users-create',
        'doctors', 'patients',

        // Reports
        'billing', 'reports',

        // Master files (flat URLs)
        'gender',
        'role',
        'room-status',
        'admission-status',
        'billing-status',
        'payment-type',
        'specialization',
        'diagnosis',
        'charge-category',
        'charge-item',
        'room-type',
        'room',
    ],
    2 => ['doctor'],
    3 => ['nurse'],
    4 => ['cashier'],
];

if (!in_array($page, $allowed[$roleId] ?? [], true)) {
    http_response_code(403);
    echo 'Access denied. Requested page: ' . htmlspecialchars($page);
    exit;
}

// ---------- Layout ----------
$layout = match ($roleId) {
    1 => 'admin',
    2 => 'doctor',
    3 => 'nurse',
    4 => 'cashier',
    default => 'admin',
};

// ---------- Content view ----------
$contentFile = __DIR__ . '/views/' . ($roleId === 1 ? 'admin/' : '') . $page . '.php';

if (!file_exists($contentFile)) {
    http_response_code(404);
    echo 'Content file not found: ' . htmlspecialchars($contentFile);
    exit;
}

// ---------- Auto page title (from filename) ----------
$pageTitle = ucwords(str_replace(['-', '/'], [' ', ' · '], $page));

// ---------- Auto page script ----------
// Look in assets/js/master/ first, then assets/js/
$jsCandidates = [
    '/assets/js/master/' . $page . '.js',
    '/assets/js/'        . $page . '.js',
];

$pageScript = null;
foreach ($jsCandidates as $relPath) {
    if (file_exists(__DIR__ . $relPath)) {
        $pageScript = BASE_URL . $relPath;
        break;
    }
}

// ---------- Sidebar highlight ----------
$currentPage = $page;

// ---------- DB ----------
require_once __DIR__ . '/config/connection.php';

// ---------- Render ----------
require __DIR__ . '/views/layouts/' . $layout . '.php';