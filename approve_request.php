<?php
require 'config.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Mailer.php';
require_once __DIR__ . '/auth_helpers.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); exit('Não autenticado');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_manage.php'); exit;
}

$requestId = (int) ($_POST['request_id'] ?? 0);
$action    = $_POST['action'] ?? '';

if (!$requestId || !in_array($action, ['approve', 'reject'], true)) {
    header('Location: admin_manage.php'); exit;
}

$stmt = $pdo->prepare("
    SELECT * FROM admin_registration_requests
    WHERE id = ? AND status = 'pending'
");
$stmt->execute([$requestId]);
$req = $stmt->fetch();

if (!$req) {
    header('Location: admin_manage.php?erro=pedido_invalido'); exit;
}

if ($action === 'reject') {
    $pdo->prepare("
        UPDATE admin_registration_requests
        SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW()
        WHERE id = ?
    ")->execute([$_SESSION['admin_id'], $requestId]);

    Mailer::send(
        $req['email'],
        'Super Login — Pedido de acesso',
        "<p>Olá <strong>" . htmlspecialchars($req['nome']) . "</strong>,</p>
         <p>O seu pedido de acesso ao Super Login foi <strong>rejeitado</strong>.</p>
         <p>Para mais informações, contacte um administrador.</p>"
    );

    header('Location: admin_manage.php?ok=rejeitado'); exit;
}

// --- APROVAR ---

// verificar duplicados (pode ter sido criado entretanto)
$stmt = $pdo->prepare("SELECT 1 FROM admins WHERE username = ? OR email = ?");
$stmt->execute([$req['username'], $req['email']]);
if ($stmt->fetchColumn()) {
    $pdo->prepare("
        UPDATE admin_registration_requests SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW()
        WHERE id = ?
    ")->execute([$_SESSION['admin_id'], $requestId]);
    header('Location: admin_manage.php?erro=duplicado'); exit;
}

$pdo->beginTransaction();
try {
    $pdo->prepare("
        INSERT INTO admins (username, nome, email, password_hash, must_change_password)
        VALUES (?, ?, ?, ?, 0)
    ")->execute([$req['username'], $req['nome'], $req['email'], $req['password_hash']]);

    $newAdminId = (int) $pdo->lastInsertId();

    $pdo->prepare("
        UPDATE admin_registration_requests
        SET status = 'approved', reviewed_by = ?, reviewed_at = NOW()
        WHERE id = ?
    ")->execute([$_SESSION['admin_id'], $requestId]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: admin_manage.php?erro=db'); exit;
}

// notificar o utilizador aprovado
$scheme  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$loginUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/super_login/login.php';

Mailer::send(
    $req['email'],
    'Super Login — Acesso aprovado',
    "<p>Olá <strong>" . htmlspecialchars($req['nome']) . "</strong>,</p>
     <p>O seu pedido de acesso ao Super Login foi <strong>aprovado</strong>.</p>
     <p>Já pode fazer login em: <a href='{$loginUrl}'>{$loginUrl}</a></p>
     <p>Utilize o utilizador <strong>" . htmlspecialchars($req['username']) . "</strong> e a password que definiu no registo.</p>"
);

header('Location: admin_manage.php?ok=aprovado'); exit;
