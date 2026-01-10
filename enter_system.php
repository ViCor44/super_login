<?php
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Não autenticado');
}

$systemKey = $_GET['system'] ?? null;

if (!$systemKey) {
    http_response_code(400);
    exit('Sistema inválido');
}

$systemsMap = require __DIR__ . '/systems_map.php';


if (!isset($systemsMap[$systemKey])) {
    http_response_code(404);
    exit('Sistema não existe');
}

// verificar se o admin tem mapeamento para este sistema
$stmt = $pdo->prepare("
    SELECT user_id
    FROM admin_user_map
    WHERE admin_id = ?
      AND system_key = ?
");
$stmt->execute([$_SESSION['admin_id'], $systemKey]);
$map = $stmt->fetch();

if (!$map) {
    http_response_code(403);
    exit('Sem permissão');
}

// gerar token SSO
$token = bin2hex(random_bytes(32));

$pdo->prepare("
    INSERT INTO admin_tokens (admin_id, system_key, token, expires_at)
    VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 2 MINUTE))
")->execute([
    $_SESSION['admin_id'],
    $systemKey,
    $token
]);

// redirecionar
$baseUrl = $systemsMap[$systemKey]['login_url'];

$redirect = $baseUrl
          . (str_contains($baseUrl, '?') ? '&' : '?')
          . 'token=' . urlencode($token);

header('Location: ' . $redirect);
exit;
