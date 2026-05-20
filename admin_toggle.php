<?php
require 'config.php';
require 'auth_helpers.php';

if (!isset($_SESSION['admin_id'])) { http_response_code(403); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$submitted = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $submitted)) {
    http_response_code(403);
    exit('CSRF validation failed');
}

if (!isRootAdmin($pdo, $_SESSION['admin_id'])) {
    http_response_code(403);
    exit;
}

$id = (int)($_POST['id'] ?? 0);

// impedir auto-desativação
if (!$id || $id === $_SESSION['admin_id']) {
    header("Location: admin_manage.php");
    exit;
}

$pdo->prepare("
    UPDATE admins
    SET ativo = NOT ativo
    WHERE id = ?
")->execute([$id]);

header("Location: admin_manage.php");
exit;
