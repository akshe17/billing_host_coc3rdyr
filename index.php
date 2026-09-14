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
        'dashboard',
        'users', 'users-create',
        'doctors', 'patients',
        'billing', 'reports',
        'master/gender', 'master/role', 'master/room-status',
        'master/admission-status', 'master/billing-status',
        'master/payment-type', 'master/specialization',
        'master/diagnosis', 'master/charge-category',
        'master/charge-item', 'master/room-type', 'master/room',
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
// Mirrors views/ in assets/js/
// e.g. users          → /assets/js/users.js
//      master/gender  → /assets/js/master/gender.js
$jsPath = __DIR__ . '/assets/js/' . $page . '.js';
$pageScript = file_exists($jsPath)
    ? BASE_URL . '/assets/js/' . $page . '.js'
    : null;

// ---------- Sidebar highlight ----------
$currentPage = $page;

// ---------- DB ----------
require_once __DIR__ . '/config/connection.php';

// ---------- Render ----------
require __DIR__ . '/views/layouts/' . $layout . '.php';