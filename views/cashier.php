<?php
// views/cashier.php
?>

<div class="mb-8">
    <h2 class="text-xl font-bold text-slate-900">Welcome, <?= htmlspecialchars($currentUser['first_name']) ?> 💵</h2>
    <p class="mt-1 text-sm text-slate-500">Statements and payments overview.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <?php
    $stats = [
        ['label' => 'Pending Statements', 'value' => '2', 'color' => 'bg-amber-50 text-amber-700'],
        ['label' => 'Paid Today',         'value' => '₱1,000.00', 'color' => 'bg-emerald-50 text-emerald-700'],
        ['label' => 'Overdue',            'value' => '1', 'color' => 'bg-rose-50 text-rose-700'],
    ];
    foreach ($stats as $s): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide"><?= $s['label'] ?></p>
            <p class="mt-2 text-2xl font-bold text-slate-900"><?= $s['value'] ?></p>
            <span class="mt-3 inline-block rounded-full px-2 py-0.5 text-xs font-medium <?= $s['color'] ?>">Live</span>
        </div>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-900">Billing Statements</h3>
        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">View all →</a>
    </div>
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="text-left px-6 py-3 font-medium">Statement</th>
                <th class="text-left px-6 py-3 font-medium">Patient</th>
                <th class="text-right px-6 py-3 font-medium">Total</th>
                <th class="text-right px-6 py-3 font-medium">Balance</th>
                <th class="text-left px-6 py-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php
            $bills = [
                ['#1', 'Juan Dela Peña',    '₱3,385.00', '₱3,385.00', 'Pending',        'bg-amber-50 text-amber-700'],
                ['#2', 'Liza Mendoza',      '₱2,830.00', '₱0.00',     'Paid',           'bg-emerald-50 text-emerald-700'],
                ['#3', 'Carlos Villanueva', '₱2,060.00', '₱1,060.00', 'Partially Paid', 'bg-blue-50 text-blue-700'],
                ['#4', 'Angelica Flores',   '₱808.00',   '₱808.00',   'Overdue',        'bg-rose-50 text-rose-700'],
            ];
            foreach ($bills as $b): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium text-slate-900"><?= $b[0] ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= $b[1] ?></td>
                    <td class="px-6 py-3 text-right text-slate-600"><?= $b[2] ?></td>
                    <td class="px-6 py-3 text-right font-medium text-slate-900"><?= $b[3] ?></td>
                    <td class="px-6 py-3">
                        <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium <?= $b[5] ?>">
                            <?= $b[4] ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>