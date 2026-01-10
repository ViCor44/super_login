<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'], $_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

$adminId    = $_SESSION['admin_id'];
$adminEmail = $_SESSION['admin_email'];

$systemsMap = require __DIR__ . '/systems_map.php';

foreach ($systemsMap as $systemKey => $system) {

    // ligação ao sistema externo
    try {
        $pdoSystem = new PDO(
            $system['dsn'],
            $system['db_user'],
            $system['db_pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    } catch (Exception $e) {
        continue; // sistema offline ou erro de ligação
    }

    // procurar utilizador pelo email
    $emailField = $system['email_field'];

    $stmt = $pdoSystem->prepare("
        SELECT id
        FROM {$system['users_table']}
        WHERE {$emailField} = ?
        LIMIT 1
    ");
    $stmt->execute([$adminEmail]);
    $user = $stmt->fetch();

    if (!$user) {
        continue; // ainda não existe neste sistema
    }

    // verificar se já existe mapeamento
    $stmt = $pdo->prepare("
        SELECT 1
        FROM admin_user_map
        WHERE admin_id = ?
          AND system_key = ?
    ");
    $stmt->execute([$adminId, $systemKey]);

    if ($stmt->fetchColumn()) {
        continue; // já mapeado
    }

    // criar mapeamento
    $stmt = $pdo->prepare("
        INSERT INTO admin_user_map (admin_id, system_key, user_id)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $adminId,
        $systemKey,
        $user['id']
    ]);
}

// voltar à dashboard
header('Location: dashboard.php?refreshed=1');
exit;
