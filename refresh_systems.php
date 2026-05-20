<?php

require 'config.php';

if (!isset($_SESSION['admin_id'], $_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

$adminId    = $_SESSION['admin_id'];
$adminEmail = $_SESSION['admin_email'];

$systemsMap = require __DIR__ . '/systems_map.php';

foreach ($systemsMap as $systemKey => $system) {

    if (str_starts_with($systemKey, '_')) continue;

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
        // sistema offline — não remover mapeamento existente
        continue;
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
        // utilizador não existe neste sistema — remover mapeamento se existir
        $pdo->prepare("
            DELETE FROM admin_user_map
            WHERE admin_id = ? AND system_key = ?
        ")->execute([$adminId, $systemKey]);
        continue;
    }

    // atualizar user_id se o mapeamento já existe, inserir caso contrário
    $stmt = $pdo->prepare("
        SELECT id FROM admin_user_map
        WHERE admin_id = ? AND system_key = ?
    ");
    $stmt->execute([$adminId, $systemKey]);
    $existing = $stmt->fetchColumn();

    if ($existing) {
        $pdo->prepare("
            UPDATE admin_user_map SET user_id = ?
            WHERE admin_id = ? AND system_key = ?
        ")->execute([$user['id'], $adminId, $systemKey]);
    } else {
        $pdo->prepare("
            INSERT INTO admin_user_map (admin_id, system_key, user_id)
            VALUES (?, ?, ?)
        ")->execute([$adminId, $systemKey, $user['id']]);
    }
}

// voltar à dashboard
header('Location: dashboard.php?refreshed=1');
exit;
