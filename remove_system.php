<?php
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Não autenticado');
}

$systemKey = trim($_POST['system'] ?? '');

if ($systemKey === '') {
    http_response_code(400);
    exit('Sistema inválido');
}

$pdo->prepare("
    DELETE FROM admin_user_map
    WHERE admin_id = ? AND system_key = ?
")->execute([$_SESSION['admin_id'], $systemKey]);

header('Location: dashboard.php');
exit;
