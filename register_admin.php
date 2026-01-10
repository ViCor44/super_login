<?php
require 'config.php';

$systemsMap = require __DIR__ . '/systems_map.php';
$notMappedSystems = [];

// verificar se já existe algum admin
$stmt = $pdo->query("SELECT COUNT(*) FROM admins");
$totalAdmins = $stmt->fetchColumn();

if ($totalAdmins > 0 && !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif ($password !== $confirm) {
        $erro = 'As passwords não coincidem.';
    } elseif (strlen($password) < 6) {
        $erro = 'A password deve ter pelo menos 6 caracteres.';
    } else {
        // verificar se username ou email já existem
        $stmt = $pdo->prepare("
            SELECT id FROM admins WHERE username = ? OR email = ?
        ");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $erro = 'Utilizador ou email já existe.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO admins (username, nome, email, password_hash, must_change_password)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->execute([$username, $nome, $email, $hash]);

            $adminId = $pdo->lastInsertId();

            foreach ($systemsMap as $systemKey => $sys) {

                try {
                    $pdoSys = new PDO(
                        $sys['dsn'],
                        $sys['user'],
                        $sys['pass'],
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]
                    );

                    $stmt = $pdoSys->prepare("
                        SELECT {$sys['id_col']} AS user_id
                        FROM {$sys['user_table']}
                        WHERE {$sys['email_col']} = ?
                        LIMIT 1
                    ");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();

                    if ($user) {
                        $pdo->prepare("
                            INSERT IGNORE INTO admin_user_map
                            (admin_id, system_key, user_id)
                            VALUES (?, ?, ?)
                        ")->execute([
                            $adminId,
                            $systemKey,
                            $user['user_id']
                        ]);
                    } else {
                        $notMappedSystems[] = $sys['name'];
                    }

                } catch (Throwable $e) {
                    $notMappedSystems[] = $sys['name'];
                }
            }

            $sucesso = 'Administrador criado com sucesso.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Super Login | Novo Administrador</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-container">

    <!-- LADO ESQUERDO (igual ao login) -->
    <div class="login-left">
        <div class="overlay">
            <h1>Super Login</h1>
            <p>
                Configuração inicial<br>
                de administradores
            </p>
        </div>
    </div>

    <!-- LADO DIREITO -->
    <div class="login-right">
        <div class="login-box">

            <h2>Novo Administrador</h2>
            <p class="subtitle">Criar utilizador administrador</p>

            <?php if (!empty($erro)): ?>
                <div class="error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if (!empty($sucesso)): ?>
                <div class="success"><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <form method="post">

                <label>Utilizador</label>
                <input type="text" name="username" required>

                <label>Nome</label>
                <input type="text" name="nome" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Confirmar password</label>
                <input type="password" name="confirm" required>

                <button type="submit">Criar administrador</button>
            </form>

            <div class="login-links">
                <a href="login.php">Voltar ao login</a>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <span>·</span>
                    <a href="dashboard.php">Dashboard</a>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

</body>
</html>
