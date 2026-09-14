<?php
// views/admin/patients.php
$patients = $pdo->query(
    'SELECT p.patient_id, p.first_name, p.last_name, p.birth_date,
            p.contact_number, p.email, g.gender_name
     FROM `patient` p
     INNER JOIN `gender` g ON g.gender_id = p.gender_id
     ORDER BY p.patient_id'
)->fetchAll();
?>

<div class="mb-8">
    <h2 class="text-xl font-bold text-slate-900">Patients</h2>
    <p class="mt-1 text-sm text-slate-500">All registered patients.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="text-left px-6 py-3 font-medium">Name</th>
                <th class="text-left px-6 py-3 font-medium">Gender</th>
                <th class="text-left px-6 py-3 font-medium">Birth Date</th>
                <th class="text-left px-6 py-3 font-medium">Contact</th>
                <th class="text-left px-6 py-3 font-medium">Email</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($patients as $p): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium text-slate-900">
                        <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>
                    </td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($p['gender_name']) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($p['birth_date']) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($p['contact_number'] ?? '—') ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($p['email'] ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>