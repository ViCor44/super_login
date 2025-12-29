<?php
require 'config.php';
require 'auth_helpers.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$isRoot = isRootAdmin($pdo, $_SESSION['admin_id']);

$stmt = $pdo->query("
    SELECT id, username, nome, email, ativo, criado_em
    FROM admins
    ORDER BY id
");
$admins = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Login | Gestão de Administradores</title>
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

        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>

        <button id="theme-toggle" class="theme-toggle"><i class="fas fa-moon"></i></button> <!-- Toggle dark mode -->
    </div>
</div>

<div class="dashboard">
    <h1>Administradores</h1>
    <p class="subtitle">
        <?= $isRoot ? 'Gestão completa de administradores' : 'Apenas visualização' ?>
    </p>

    <div class="search-bar">
        <input type="text" id="admin-search" placeholder="Pesquisar administradores...">
        <i class="fas fa-search"></i>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilizador</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($admins as $a): ?>
            <tr data-name="<?= strtolower(htmlspecialchars($a['username'] . ' ' . $a['nome'] . ' ' . $a['email'])) ?>">
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['username']) ?></td>
                <td><?= htmlspecialchars($a['nome']) ?></td>
                <td><?= htmlspecialchars($a['email']) ?></td>
                <td>
                    <span class="status <?= $a['ativo'] ? 'active' : 'inactive' ?>">
                        <?= $a['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </span>
                </td>
                <td>
                    <?php if ($isRoot && $a['id'] !== $_SESSION['admin_id']): ?>
                        <a href="admin_edit.php?id=<?= $a['id'] ?>" class="action-link"><i class="fas fa-edit"></i> Editar</a>
                        <a href="admin_toggle.php?id=<?= $a['id'] ?>"
                           class="action-link <?= $a['ativo'] ? 'deactivate' : 'activate' ?>"
                           onclick="return confirm('Tem a certeza?')">
                           <i class="fas <?= $a['ativo'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                           <?= $a['ativo'] ? 'Desativar' : 'Ativar' ?>
                        </a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Dark mode toggle (mesmo código da página anterior para consistência)
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

// Search filter for table
const searchInput = document.getElementById('admin-search');
searchInput.addEventListener('input', (e) => {
    const filter = e.target.value.toLowerCase();
    document.querySelectorAll('.admin-table tbody tr').forEach(row => {
        const name = row.dataset.name;
        row.style.display = name.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>