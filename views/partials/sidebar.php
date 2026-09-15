<?php
// views/partials/sidebar.php

// Safety — session + user
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentUser = $currentUser ?? ($_SESSION['user'] ?? null);
if (!$currentUser) return;

// These come from the layout
$sidebarSections = $sidebarSections ?? [];
$currentPage     = $currentPage ?? ($_GET['page'] ?? '');
?>

<div id="sidebarOverlay"
     class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm hidden lg:hidden"
     onclick="closeSidebar()"></div>

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col
              transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">

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