<?php
require 'config.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Mailer.php';

// Já autenticado
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Sem admins ainda → usar registo direto
$stmt = $pdo->query("SELECT COUNT(*) FROM admins");
if ((int) $stmt->fetchColumn() === 0) {
    header('Location: register_admin.php');
    exit;
}

$erro   = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nome     = trim($_POST['nome']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';

    if (!$username || !$nome || !$email || !$password) {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif (strlen($password) < 8) {
        $erro = 'A password deve ter pelo menos 8 caracteres.';
    } elseif ($password !== $confirm) {
        $erro = 'As passwords não coincidem.';
    } else {
        // verificar duplicados em admins e em pedidos pendentes
        $stmt = $pdo->prepare("
            SELECT 1 FROM admins WHERE username = ? OR email = ?
            UNION
            SELECT 1 FROM admin_registration_requests
             WHERE (username = ? OR email = ?) AND status = 'pending'
        ");
        $stmt->execute([$username, $email, $username, $email]);

        if ($stmt->fetchColumn()) {
            $erro = 'Utilizador ou email já existe (ou tem um pedido pendente).';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $pdo->prepare("
                INSERT INTO admin_registration_requests (username, nome, email, password_hash)
                VALUES (?, ?, ?, ?)
            ")->execute([$username, $nome, $email, $hash]);

            // notificar todos os admins ativos por email
            $admins = $pdo->query("SELECT email, nome FROM admins WHERE ativo = 1")->fetchAll();
            foreach ($admins as $admin) {
                Mailer::send(
                    $admin['email'],
                    'Super Login — Novo pedido de acesso',
                    "
                    <p>Olá {$admin['nome']},</p>
                    <p>O utilizador <strong>" . htmlspecialchars($nome) . "</strong>
                    (<em>" . htmlspecialchars($email) . "</em>) submeteu um pedido de acesso ao Super Login.</p>
                    <p>Aceda à <strong>Gestão de admins</strong> para aprovar ou rejeitar o pedido.</p>
                    "
                );
            }

            $sucesso = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Login | Solicitar acesso</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .login-box { width: 400px; }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    </style>
</head>
<body>
<div class="login-container">

    <div class="login-left">
        <div class="overlay">
            <h1>Super Login</h1>
            <p>Sistema central de autenticação<br>para administração do parque.</p>
        </div>
    </div>

    <div class="login-right">
        <div class="login-box">
            <h2>Solicitar acesso</h2>
            <p class="subtitle">O pedido será analisado por um administrador</p>

            <?php if ($sucesso): ?>
                <div class="success">
                    Pedido enviado com sucesso. Será notificado por email quando for aprovado.
                </div>
                <div class="login-links" style="margin-top:20px">
                    <a href="login.php">Voltar ao login</a>
                </div>
            <?php else: ?>

                <?php if ($erro): ?>
                    <div class="error"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="row-2">
                        <div>
                            <label>Utilizador</label>
                            <input type="text" name="username"
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                   required autofocus>
                        </div>
                        <div>
                            <label>Nome</label>
                            <input type="text" name="nome"
                                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                                   required>
                        </div>
                    </div>

                    <label>Email</label>
                    <input type="email" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required>

                    <div class="row-2">
                        <div>
                            <label>Password</label>
                            <input type="password" name="password" required minlength="8">
                        </div>
                        <div>
                            <label>Confirmar</label>
                            <input type="password" name="confirm" required>
                        </div>
                    </div>

                    <button type="submit">Enviar pedido</button>
                </form>

                <div class="login-links">
                    <a href="login.php">Já tenho conta</a>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
