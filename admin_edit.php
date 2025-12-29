<?php
require 'config.php';
require 'auth_helpers.php';

if (!isset($_SESSION['admin_id']) || !isRootAdmin($pdo, $_SESSION['admin_id'])) {
    http_response_code(403); exit;
}

$id = (int)($_GET['id'] ?? 0);

// não permitir editar o próprio root aqui (opcional, mas recomendado)
if ($id === $_SESSION['admin_id']) {
    header("Location: admin_manage.php"); exit;
}

// buscar admin
$stmt = $pdo->prepare("SELECT id, username, nome, email, ativo FROM admins WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();
if (!$admin) { header("Location: admin_manage.php"); exit; }

// sistemas
$systems = $pdo->query("SELECT id, nome FROM systems WHERE ativo = 1")->fetchAll();

// sistemas atuais do admin
$stmt = $pdo->prepare("SELECT system_id FROM admin_systems WHERE admin_id = ?");
$stmt->execute([$id]);
$current = array_column($stmt->fetchAll(), 'system_id');

$erro = $ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $sel   = $_POST['systems'] ?? [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif (empty($sel)) {
        $erro = 'Selecione pelo menos um sistema.';
    } else {
        $pdo->beginTransaction();
        try {
            $pdo->prepare("
                UPDATE admins SET nome = ?, email = ?, ativo = ?
                WHERE id = ?
            ")->execute([$nome, $email, $ativo, $id]);

            // atualizar sistemas
            $pdo->prepare("DELETE FROM admin_systems WHERE admin_id = ?")->execute([$id]);
            $ins = $pdo->prepare("INSERT INTO admin_systems (admin_id, system_id) VALUES (?, ?)");
            foreach ($sel as $sid) $ins->execute([$id, $sid]);

            $pdo->commit();
            $ok = 'Administrador atualizado.';
            $current = $sel;
            $admin['nome'] = $nome; $admin['email'] = $email; $admin['ativo'] = $ativo;
        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = 'Erro ao atualizar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Administrador</title>
    <link rel="stylesheet" href="assets/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Para ícones -->
</head>
<body>

<div class="topbar">
    <div class="logo">Super Login</div>

    <div class="user">
        <span class="user-name"><?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin') ?></span>

        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_create.php"><i class="fas fa-user-plus"></i> Novo administrador</a>
        <a href="admin_manage.php"><i class="fas fa-users-cog"></i> Gestão de admins</a>

        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>

        <button id="theme-toggle" class="theme-toggle"><i class="fas fa-moon"></i></button> <!-- Toggle dark mode -->
    </div>
</div>

<div class="dashboard">
    <h1>Editar Administrador</h1>

    <?php if ($erro): ?><div class="error"><?= $erro ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="success"><?= $ok ?></div><?php endif; ?>

    <form method="post" class="form-box">
        <label>Utilizador</label>
        <input value="<?= htmlspecialchars($admin['username']) ?>" disabled class="input-disabled">

        <label>Nome</label>
        <input name="nome" value="<?= htmlspecialchars($admin['nome']) ?>" required>

        <label>Email</label>
        <input name="email" type="email" value="<?= htmlspecialchars($admin['email']) ?>" required>

        <label class="checkbox-single">
            <input type="checkbox" name="ativo" <?= $admin['ativo'] ? 'checked' : '' ?>>
            <span>Ativo</span>
        </label>

        <fieldset class="checkbox-group">
            <legend>Sistemas com acesso</legend>
            <?php foreach ($systems as $s): ?>
                <label class="checkbox-item">
                    <input type="checkbox" name="systems[]" value="<?= $s['id'] ?>"
                        <?= in_array($s['id'], $current) ? 'checked' : '' ?>>
                    <span class="system-name"><?= htmlspecialchars($s['nome']) ?></span>
                </label>
            <?php endforeach; ?>
        </fieldset>

        <button type="submit" class="btn">Guardar</button>
    </form>
</div>

<script>
// Dark mode toggle (consistente com outras páginas)
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