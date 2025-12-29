<?php

function isRootAdmin(PDO $pdo, int $adminId): bool {
    $stmt = $pdo->query("SELECT MIN(id) FROM admins");
    $rootId = (int) $stmt->fetchColumn();
    return $adminId === $rootId;
}

if (
    ($_SESSION['must_change_password'] ?? 0) === 1 &&
    basename($_SERVER['PHP_SELF']) !== 'force_change_password.php'
) {
    header('Location: force_change_password.php');
    exit;
}