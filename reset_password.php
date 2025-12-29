<?php
require 'config.php';

$token = $_GET['token'] ?? '';
$erro = '';
$ok = false;

$stmt = $pdo->prepare("
    SELECT * FROM password_resets
    WHERE token = ?
      AND used = 0
      AND expires_at > NOW()
");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    $erro = 'Link inválido ou expirado.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reset) {
    $pass = $_POST['password'];
    $conf = $_POST['confirm'];

    if ($pass !== $conf) {
        $erro = 'As passwords não coincidem.';
    } elseif (strlen($pass) < 6) {
        $erro = 'Password demasiado curta.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $pdo->prepare("
            UPDATE admins SET password_hash = ? WHERE id = ?
        ")->execute([$hash, $reset['admin_id']]);

        $pdo->prepare("
            UPDATE password_resets SET used = 1 WHERE id = ?
        ")->execute([$reset['id']]);

        $ok = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Super Login | Nova Password</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-right">
        <div class="login-box">
            <h2>Nova password</h2>

            <?php if ($erro): ?>
                <div class="error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if ($ok): ?>
                <div class="success">
                    Password alterada com sucesso.<br>
                    <a href="login.php">Voltar ao login</a>
                </div>
            <?php elseif ($reset): ?>
                <form method="post">
                    <label>Nova password</label>
                    <input type="password" name="password" required>

                    <label>Confirmar password</label>
                    <input type="password" name="confirm" required>

                    <button type="submit">Alterar password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
