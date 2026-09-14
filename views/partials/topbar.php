<?php
// views/partials/topbar.php
// Expects: $pageTitle, $currentUser
?>
<header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 sticky top-0 z-10">
    <div>
        <h1 class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-sm text-slate-500 hidden sm:block">
            <?= htmlspecialchars($currentUser['first_name']) ?>
        </span>
    </div>
</header>