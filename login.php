<?php
require 'config.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM admins");
$totalAdmins = (int) $stmt->fetchColumn();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Super Login</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-container">

    <!-- LADO ESQUERDO -->
    <div class="login-left">
        <div class="overlay">
            <h1>Super Login</h1>
            <p>
                Sistema central de autenticação<br>
                para administração do parque.
            </p>
        </div>
    </div>

    <!-- LADO DIREITO -->
    <div class="login-right">
        <div class="login-box">
            <h2>Login</h2>
            <p class="subtitle">Introduza as suas credenciais</p>

            <?php if (!empty($_GET['erro'])): ?>
                <div class="error">Utilizador ou password inválidos</div>
            <?php endif; ?>

            <form method="post" action="login_process.php">
                <label>Utilizador</label>
                <input type="text" name="username" required autofocus>

                <label>Password</label>
                <input type="password" name="password" required>

                <button type="submit">Entrar</button>
            </form>
            <div class="login-links">
                <a href="forgot_password.php">Esqueci-me da password</a>

                <?php if ($totalAdmins === 0): ?>
                    <span>·</span>
                    <a href="register_admin.php">Criar administrador</a>
                <?php endif; ?>
            </div>        
        </div>
    </div>

</div>

</body>
</html>
