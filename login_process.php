<?php
require 'config.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("
    SELECT * FROM admins
    WHERE username = ? AND ativo = 1
");
$stmt->execute([$username]);
$admin = $stmt->fetch();

if ($admin && password_verify($password, $admin['password_hash'])) {

    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_nome'] = $admin['nome'];

    $_SESSION['must_change_password'] = (int)$admin['must_change_password'];

    if ($_SESSION['must_change_password'] === 1) {
        header('Location: force_change_password.php');
        exit;
    }

    $pdo->prepare("
        UPDATE admins SET ultimo_login = NOW() WHERE id = ?
    ")->execute([$admin['id']]);

    header("Location: dashboard.php");
    exit;
}

header("Location: login.php?erro=1");
exit;
