<?php
require 'auth_helpers.php';
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p1 = $_POST['password'] ?? '';
    $p2 = $_POST['password_confirm'] ?? '';

    if (strlen($p1) < 8) {
        $error = 'A password deve ter pelo menos 8 caracteres.';
    } elseif ($p1 !== $p2) {
        $error = 'As passwords não coincidem.';
    } else {
        $hash = password_hash($p1, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE admins
            SET password_hash = ?, 
                must_change_password = 0,
                password_changed_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$hash, $_SESSION['admin_id']]);

        $_SESSION['must_change_password'] = 0;

        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Login | Alterar Password</title>
    <link rel="stylesheet" href="assets/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Para ícones -->
</head>
<body>

<div class="login-container">
    <div class="login-right">
        <div class="login-box form-box">
            <h2>🔒 Primeiro acesso</h2>
            <p class="subtitle">Por motivos de segurança, deve definir uma nova password.</p>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <label>Nova password</label>
                <input type="password" name="password" required>

                <label>Confirmar password</label>
                <input type="password" name="password_confirm" required>

                <button type="submit" class="btn">Guardar nova password</button>
            </form>
        </div>
    </div>
</div>

<script>
// Load saved theme (no toggle, as it's a forced page)
const body = document.body;
if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark-mode');
}
</script>

</body>
</html>