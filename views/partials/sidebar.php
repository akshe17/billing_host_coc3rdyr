<?php
// views/partials/sidebar.php

// --- Safety: make sure session + $currentUser exist ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUser = $currentUser ?? ($_SESSION['user'] ?? null);

if (!$currentUser) {
    return; // nothing to render — layout should have redirected already
}

$currentPage = $currentPage ?? ($_GET['page'] ?? '');

// --- Sidebar sections with labels ---
$sidebarSections = [
    'Main' => [
        [
            'label' => 'Dashboard',
            'href'  => BASE_URL . '/index.php?page=dashboard',
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
    ],

    'Master Files' => [
        [
            'label' => 'Genders',
            'href'  => BASE_URL . '/index.php?page=master/gender',
            'icon'  => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        ],
        [
            'label' => 'Roles',
            'href'  => BASE_URL . '/index.php?page=master/role',
            'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ],
        [
            'label' => 'Room Statuses',
            'href'  => BASE_URL . '/index.php?page=master/room-status',
            'icon'  => 'M9 12h6m-6 4h6M5 8h14M5 12h14M5 16h14',
        ],
        [
            'label' => 'Admission Statuses',
            'href'  => BASE_URL . '/index.php?page=master/admission-status',
            'icon'  => 'M9 12h6m-6 4h6M5 8h14M5 12h14M5 16h14',
        ],
        [
            'label' => 'Billing Statuses',
            'href'  => BASE_URL . '/index.php?page=master/billing-status',
            'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1',
        ],
        [
            'label' => 'Payment Types',
            'href'  => BASE_URL . '/index.php?page=master/payment-type',
            'icon'  => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        ],
        [
            'label' => 'Specializations',
            'href'  => BASE_URL . '/index.php?page=master/specialization',
            'icon'  => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253',
        ],
        [
            'label' => 'Diagnoses',
            'href'  => BASE_URL . '/index.php?page=master/diagnosis',
            'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        ],
        [
            'label' => 'Charge Categories',
            'href'  => BASE_URL . '/index.php?page=master/charge-category',
            'icon'  => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        ],
        [
            'label' => 'Charge Items',
            'href'  => BASE_URL . '/index.php?page=master/charge-item',
            'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        [
            'label' => 'Room Types',
            'href'  => BASE_URL . '/index.php?page=master/room-type',
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3',
        ],
        [
            'label' => 'Rooms',
            'href'  => BASE_URL . '/index.php?page=master/room',
            'icon'  => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        ],
    ],

    'People' => [
        [
            'label' => 'Users',
            'href'  => BASE_URL . '/index.php?page=users',
            'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        ],
        [
            'label' => 'Doctors',
            'href'  => BASE_URL . '/index.php?page=doctors',
            'icon'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label' => 'Patients',
            'href'  => BASE_URL . '/index.php?page=patients',
            'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0',
        ],
    ],

    'Reports' => [
        [
            'label' => 'Billing',
            'href'  => BASE_URL . '/index.php?page=billing',
            'icon'  => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        [
            'label' => 'Reports',
            'href'  => BASE_URL . '/index.php?page=reports',
            'icon'  => 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        ],
    ],
];
?>

<!-- Mobile overlay -->
<div id="sidebarOverlay"
     class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm hidden lg:hidden"
     onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col
              transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">

    <!-- Brand -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold">B</div>
            <div>
                <p class="text-sm font-semibold text-white">Billing Hospital</p>
                <p class="text-xs text-slate-400 capitalize"><?= htmlspecialchars($currentUser['role_name']) ?></p>
            </div>
        </div>
        <button type="button" onclick="closeSidebar()"
                class="lg:hidden text-slate-400 hover:text-white p-1 -mr-1">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-3">
        <?php foreach ($sidebarSections as $sectionName => $items): ?>
            <p class="px-3 mt-4 first:mt-0 mb-1.5 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                <?= htmlspecialchars($sectionName) ?>
            </p>
            <div class="space-y-0.5">
                <?php foreach ($items as $item):
                    $href = $item['href'] ?? '#';
                    $isActive = false;
                    if (str_contains($href, '?page=')) {
                        parse_str(parse_url($href, PHP_URL_QUERY) ?? '', $q);
                        $isActive = ($q['page'] ?? '') === $currentPage;
                    }
                ?>
                    <a href="<?= htmlspecialchars($href) ?>"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                              <?= $isActive
                                  ? 'bg-slate-800 text-white'
                                  : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $item['icon'] ?>"/>
                        </svg>
                        <span class="truncate"><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </nav>

    <!-- User footer -->
    <div class="border-t border-slate-800 p-4 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-sm font-semibold text-white">
                <?= strtoupper(substr($currentUser['first_name'], 0, 1)) ?>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    <?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>
                </p>
                <p class="text-xs text-slate-400 truncate capitalize">
                    <?= htmlspecialchars($currentUser['role_name']) ?>
                </p>
            </div>
        </div>
        <button id="logoutBtn"
                class="mt-3 w-full rounded-lg border border-slate-700 px-3 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800 transition">
            Sign out
        </button>
    </div>
</aside>

<!-- Mobile hamburger -->
<button type="button" onclick="openSidebar()"
        class="lg:hidden fixed top-3 left-3 z-20 rounded-lg bg-slate-900 text-white p-2 shadow-lg">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.remove('-translate-x-full');
        document.getElementById('sidebarOverlay').classList.remove('hidden');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.add('-translate-x-full');
        document.getElementById('sidebarOverlay').classList.add('hidden');
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSidebar();
    });
</script>