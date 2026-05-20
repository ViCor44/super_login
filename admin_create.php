<?php
require 'config.php';

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Mailer.php';

$systemsMap = require __DIR__ . '/systems_map.php';
$notMappedSystems = [];

// só admins autenticados
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (
    ($_SESSION['must_change_password'] ?? 0) === 1 &&
    basename($_SERVER['PHP_SELF']) !== 'force_change_password.php'
) {
    header('Location: force_change_password.php');
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
    } elseif (strlen($password) < 8) {
        $erro = 'A password deve ter pelo menos 8 caracteres.';    
    } else {

        // verificar duplicados
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $erro = 'Utilizador ou email já existe.';
        } else {

            $pdo->beginTransaction();

            try {
                // criar admin
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO admins (username, nome, email, password_hash)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$username, $nome, $email, $hash]);

                $adminId = $pdo->lastInsertId();

                

                foreach ($systemsMap as $systemKey => $sys) {

                    if (str_starts_with($systemKey, '_')) continue;

                    try {
                        $pdoSys = new PDO(
                            $sys['dsn'],
                            $sys['db_user'],
                            $sys['db_pass'],
                            [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                            ]
                        );

                        $stmt = $pdoSys->prepare("
                            SELECT id AS user_id
                            FROM {$sys['users_table']}
                            WHERE {$sys['email_field']} = ?
                            LIMIT 1
                        ");
                        $stmt->execute([$email]);
                        $user = $stmt->fetch();

                        if ($user) {
                            $pdo->prepare("
                                INSERT IGNORE INTO admin_user_map (admin_id, system_key, user_id)
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


                $pdo->commit();

                if (!empty($notMappedSystems)) {
                    $sucesso .= '<br><br><strong>Atenção:</strong> O administrador ainda não existe nos seguintes sistemas: '
                        . implode(', ', $notMappedSystems)
                        . '.<br>Deverá registar-se nesses sistemas utilizando o mesmo email.';
                }

                $scheme  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];

                $emailHtml = "
                <h2>Bem-vindo ao Super Login</h2>

                <p>A sua conta de administrador foi criada.</p>

                <p><strong>Utilizador:</strong> {$username}</p>
                <p><strong>Password:</strong> {$password}</p>

                <p>
                Por motivos de segurança, ao primeiro login será obrigado a definir
                uma nova password.
                </p>

                <p>
                <a href='{$baseUrl}/super_login/login.php'>
                👉 Aceder ao Super Login
                </a>
                </p>

                <p>Se não reconhece este email, ignore-o.</p>
                ";

                Mailer::send(
                    $email,
                    'Conta de administrador criada',
                    $emailHtml
                );


                $sucesso = 'Administrador criado com sucesso.';
            } catch (Exception $e) {
                $pdo->rollBack();
                $erro = 'Erro ao criar administrador.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Login | Criar Administrador</title>
    <link rel="stylesheet" href="assets/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Para ícones -->
</head>
<body>

<div class="topbar">
    <div class="logo">Super Login</div>

    <div class="user">
        <span class="user-name"><?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin') ?></span>

        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

        <a href="admin_manage.php"><i class="fas fa-users-cog"></i> Gestão de admins</a>

        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>

        <button id="theme-toggle" class="theme-toggle"><i class="fas fa-moon"></i></button> <!-- Toggle dark mode -->
    </div>
</div>

<div class="dashboard">
    <h1>Novo Administrador</h1>
    <p class="subtitle">Definir dados e acessos</p>

    <?php if ($erro): ?>
        <div class="error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form method="post" class="form-box">
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

        <button type="submit" class="btn">Criar administrador</button>
    </form>
</div>

<script>
// Dark mode toggle
const toggle = document.getElementById('theme-toggle');
const body = document.body;
toggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    const icon = toggle.querySelector('i');
    icon.classList.toggle('fa-moon');
    icon.classList.toggle('fa-sun');
    localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
});

// Load saved theme
if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark-mode');
    toggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
}
</script>

</body>
</html>