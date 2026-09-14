<?php
// tools/verify.php

$plain  = $_POST['pw']   ?? '';
$hash   = $_POST['hash'] ?? '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = password_verify($plain, $hash) ? 'MATCH' : 'NO MATCH';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Hash</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 40px auto; padding: 24px; background: #f8fafc; color: #0f172a; }
        h2 { margin-top: 0; }
        label { display: block; font-weight: 600; margin-top: 20px; font-size: 14px; }
        input, textarea {
            width: 100%; padding: 10px; font-family: ui-monospace, monospace; font-size: 13px;
            margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;
        }
        textarea { height: 90px; resize: vertical; }
        button {
            margin-top: 20px; padding: 10px 24px; background: #2563eb; color: white;
            border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        .result { margin-top: 24px; padding: 18px; border-radius: 8px; font-weight: 700; font-size: 16px; }
        .ok   { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
        .fail { background: #fee2e2; color: #991b1b; border: 2px solid #ef4444; }
        .hint { font-weight: 400; font-size: 14px; margin-top: 8px; line-height: 1.5; }
        code { background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>
    <h2>🔐 Verify Password Hash</h2>
    <p style="color:#64748b;font-size:14px;margin-top:-8px;">
        Paste the plain password and the hash from the database to test if they match.
    </p>

    <form method="POST">
        <label>Plain password:</label>
        <input type="text" name="pw" value="<?= htmlspecialchars($plain) ?>" autofocus>

        <label>Hash from database (paste the full 60-char value, <code>$</code> included):</label>
        <textarea name="hash" placeholder="$2y$10$..."><?= htmlspecialchars($hash) ?></textarea>

        <button type="submit">Verify</button>
    </form>

    <?php if ($result !== null): ?>
        <div class="result <?= $result === 'MATCH' ? 'ok' : 'fail' ?>">
            Result: <?= $result ?>

            <?php if ($result === 'NO MATCH'): ?>
                <div class="hint">
                    The hash doesn't correspond to "<strong><?= htmlspecialchars($plain) ?></strong>".<br>
                    Regenerate it with <code>password_hash('<?= htmlspecialchars($plain) ?>', PASSWORD_BCRYPT)</code>
                    and update the DB row.
                </div>
            <?php else: ?>
                <div class="hint">
                    ✅ Hash is valid. The DB row is fine.<br>
                    Next: check DevTools → Network for <code>api/login.php</code> response,
                    and confirm <code>login.js</code> loads with status 200.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>