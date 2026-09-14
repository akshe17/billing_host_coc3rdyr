<?php
// views/doctor.php
?>

<div class="mb-8">
    <h2 class="text-xl font-bold text-slate-900">Good day, Dr. <?= htmlspecialchars($currentUser['last_name']) ?> 🩺</h2>
    <p class="mt-1 text-sm text-slate-500">Your assigned patients and pending consultations.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <?php
    $stats = [
        ['label' => 'Assigned Admissions',   'value' => '4'],
        ['label' => "Today's Consultations", 'value' => '2'],
        ['label' => 'Pending Requests',      'value' => '1'],
    ];
    foreach ($stats as $s): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide"><?= $s['label'] ?></p>
            <p class="mt-2 text-3xl font-bold text-slate-900"><?= $s['value'] ?></p>
        </div>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-900">Assigned Patients</h2>
    </div>
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="text-left px-6 py-3 font-medium">Patient</th>
                <th class="text-left px-6 py-3 font-medium">Room</th>
                <th class="text-left px-6 py-3 font-medium">Diagnosis</th>
                <th class="text-left px-6 py-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php
            $patients = [
                ['Juan Dela Peña',    '201 (Private)', 'Essential Hypertension', 'Admitted'],
                ['Liza Mendoza',      '101 (Ward)',    'Pneumonia',              'Discharged'],
                ['Carlos Villanueva', 'ICU-1',         'Asthma',                 'Admitted'],
                ['Angelica Flores',   '102 (Ward)',    'Fracture of Femur',      'Transferred'],
            ];
            $badge = [
                'Admitted'    => 'bg-emerald-50 text-emerald-700',
                'Discharged'  => 'bg-slate-100 text-slate-600',
                'Transferred' => 'bg-blue-50 text-blue-700',
            ];
            foreach ($patients as $p): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium text-slate-900"><?= htmlspecialchars($p[0]) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($p[1]) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($p[2]) ?></td>
                    <td class="px-6 py-3">
                        <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium <?= $badge[$p[3]] ?? '' ?>">
                            <?= htmlspecialchars($p[3]) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>