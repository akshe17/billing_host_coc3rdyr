<?php
// views/nurse.php
?>

<div class="mb-8">
    <h2 class="text-xl font-bold text-slate-900">Hello, Nurse <?= htmlspecialchars($currentUser['first_name']) ?> 💉</h2>
    <p class="mt-1 text-sm text-slate-500">Patient assignments and room status at a glance.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-slate-200 p-5 lg:col-span-2">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Room Status</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php
            $rooms = [
                ['101',   'Available', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                ['201',   'Occupied',  'bg-rose-50 text-rose-700 border-rose-200'],
                ['ICU-1', 'Occupied',  'bg-rose-50 text-rose-700 border-rose-200'],
                ['102',   'Available', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                ['202',   'Reserved',  'bg-blue-50 text-blue-700 border-blue-200'],
            ];
            foreach ($rooms as $r): ?>
                <div class="rounded-lg border p-3 <?= $r[2] ?>">
                    <p class="text-sm font-semibold">Room <?= $r[0] ?></p>
                    <p class="text-xs mt-0.5"><?= $r[1] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">My Tasks</h3>
        <ul class="space-y-3 text-sm">
            <li class="flex items-start gap-2">
                <span class="mt-1 w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                <span class="text-slate-700">Administer nebulizer — Room ICU-1</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="mt-1 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span class="text-slate-700">Vital signs check — Room 201</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="mt-1 w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-slate-700">Discharge prep — Room 101</span>
            </li>
        </ul>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-lg font-semibold text-slate-900">Current In-Patients</h3>
    </div>
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="text-left px-6 py-3 font-medium">Patient</th>
                <th class="text-left px-6 py-3 font-medium">Room</th>
                <th class="text-left px-6 py-3 font-medium">Admitted</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php
            $rows = [
                ['Juan Dela Peña',    '201',   '2026-09-01'],
                ['Carlos Villanueva', 'ICU-1', '2026-09-05'],
                ['Angelica Flores',   '102',   '2026-07-10'],
            ];
            foreach ($rows as $r): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium text-slate-900"><?= htmlspecialchars($r[0]) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($r[1]) ?></td>
                    <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($r[2]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>