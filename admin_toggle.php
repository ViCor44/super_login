<?php
require 'config.php';
require 'auth_helpers.php';

if (!isset($_SESSION['admin_id'])) exit;

if (!isRootAdmin($pdo, $_SESSION['admin_id'])) {
    http_response_code(403);
    exit;
}

$id = (int) $_GET['id'];

// impedir auto-desativação
if ($id === $_SESSION['admin_id']) {
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
