<?php
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Não autenticado');
}

$systemId = (int)($_GET['id'] ?? 0);

// validar acesso do admin ao sistema
$stmt = $pdo->prepare("
    SELECT s.base_url
    FROM systems s
    JOIN admin_systems a ON a.system_id = s.id
    WHERE a.admin_id = ?
      AND s.id = ?
      AND s.ativo = 1
");
$stmt->execute([$_SESSION['admin_id'], $systemId]);
$system = $stmt->fetch();

if (!$system) {
    http_response_code(403);
    exit('Sem permissão');
}

// gerar token seguro (uso único)
$token = bin2hex(random_bytes(32));

$pdo->prepare("
    INSERT INTO admin_tokens (admin_id, system_id, token, expires_at)
    VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 2 MINUTE))
")->execute([
    $_SESSION['admin_id'],
    $systemId,
    $token
]);

// redirecionar para o sistema destino
$baseUrl = trim($system['base_url']);

$redirect = $baseUrl
          . '&token=' . urlencode($token);

header('Location: ' . $redirect);
exit;
