<?php
$plain = $_GET['pw'] ?? 'password123';
echo '<pre>';
echo 'Plain: ' . htmlspecialchars($plain) . "\n\n";
echo 'Hash:  ' . password_hash($plain, PASSWORD_BCRYPT) . "\n";
echo '</pre>';