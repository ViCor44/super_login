<?php
require 'config.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Mailer.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND ativo = 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin) {
        $token = bin2hex(random_bytes(32));

        $pdo->prepare("
            UPDATE password_resets
            SET used = 1
            WHERE admin_id = ?
        ")->execute([$admin['id']]);

        $pdo->prepare("
            INSERT INTO password_resets (admin_id, token, expires_at)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))
        ")->execute([$admin['id'], $token]);

        $resetLink = "http://localhost/super_login/reset_password.php?token=$token";

        $html = "
            <h2>Redefinição de password</h2>

            <p>Recebemos um pedido para redefinir a sua password.</p>

            <p>
                <a href='{$resetLink}'
                style='display:inline-block;padding:10px 15px;
                        background:#2563eb;color:#fff;
                        text-decoration:none;border-radius:5px;'>
                    Redefinir password
                </a>
            </p>

            <p>Este link é válido por <strong>30 minutos</strong>.</p>

            <p>Se não pediu esta alteração, pode ignorar este email.</p>
        ";

        Mailer::send(
            $email,
            'Super Login | Redefinição de password',
            $html
        );
    }


    // resposta neutra (segurança)
    $msg = 'Se o email existir, irá receber instruções para redefinir a password.';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Login | Reset Password</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Para ícones -->
</head>
<body>

<div class="login-container">
    <div class="login-right">
        <div class="login-box form-box">
            <h2>Reset de password</h2>
            <p class="subtitle">Introduza o seu email</p>

            <?php if ($msg): ?>
                <div class="success"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <form method="post">
                <label>Email</label>
                <input type="email" name="email" required>

                <button type="submit" class="btn">Enviar</button>
            </form>

            <div class="login-links">
                <a href="login.php">Voltar ao login</a>
            </div>
        </div>
    </div>
</div>

<script>
// No dark mode toggle here, as it's a public login page, but if needed, can add similar to others
</script>

</body>
</html>