<?php
// views/admin/billing.php
$statements = $pdo->query(
    'SELECT bs.statement_id, bs.statement_date, bs.total_amount, bs.balance_amount,
            p.first_name, p.last_name, bst.status_name
     FROM `billing_statement` bs
     INNER JOIN `admission` a ON a.admission_id = bs.admission_id
     INNER JOIN `patient` p ON p.patient_id = a.patient_id
     INNER JOIN `billing_status` bst ON bst.status_id = bs.status_id
     ORDER BY bs.statement_id DESC'
)->fetchAll();
?>

<div class="mb-8">
    <h2 class="text-xl font-bold text-slate-900">Billing Statements</h2>
    <p class="mt-1 text-sm text-slate-500">All patient billing statements.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="text-left px-6 py-3 font-medium">#</th>
                <th class="text-left px-6 py-3 font-medium">Patient</th>
                <th class="text-left px-6 py-3 font-medium">Date</th>
                <th class="text-right px-6 py-3 font-medium">Total</th>
                <th class="text-right px-6 py-3 font-medium">Balance</th>
                <th class="text-left px-6 py-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($statements as $s): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-slate-600">#<?= (int)$s['statement_id'] ?></td>
                    <td class="px-6 py-3 font-medium text-slate-900">
                        <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>
                    </td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($s['statement_date']) ?></td>
                    <td class="px-6 py-3 text-right text-slate-600">₱<?= number_format((float)$s['total_amount'], 2) ?></td>
                    <td class="px-6 py-3 text-right font-medium text-slate-900">₱<?= number_format((float)$s['balance_amount'], 2) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($s['status_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>