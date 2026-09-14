<?php
// views/admin/doctors.php
$doctors = $pdo->query(
    'SELECT d.doctor_id, d.license_number, d.consultation_fee, d.is_active,
            u.first_name, u.last_name, u.email
     FROM `doctor` d
     INNER JOIN `user` u ON u.user_id = d.user_id
     ORDER BY d.doctor_id'
)->fetchAll();
?>

<div class="mb-8">
    <h2 class="text-xl font-bold text-slate-900">Doctors</h2>
    <p class="mt-1 text-sm text-slate-500">Registered physicians.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="text-left px-6 py-3 font-medium">Name</th>
                <th class="text-left px-6 py-3 font-medium">Email</th>
                <th class="text-left px-6 py-3 font-medium">License</th>
                <th class="text-right px-6 py-3 font-medium">Consultation Fee</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($doctors as $d): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium text-slate-900">
                        Dr. <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?>
                    </td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($d['email']) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($d['license_number']) ?></td>
                    <td class="px-6 py-3 text-right text-slate-900">
                        ₱<?= number_format((float)$d['consultation_fee'], 2) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>