<?php
require 'config.php';
require_once 'auth_helpers.php';
$isRoot = isRootAdmin($pdo, $_SESSION['admin_id']);

// buscar sistemas a que o admin tem acesso
$stmt = $pdo->prepare("
    SELECT s.id, s.nome, s.code, s.logo
    FROM systems s
    JOIN admin_systems a ON a.system_id = s.id
    WHERE a.admin_id = ?
      AND s.ativo = 1
");

$stmt->execute([$_SESSION['admin_id']]);
$systems = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Login | Dashboard</title>
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

        <?php if ($isRoot): ?>
            <a href="admin_manage.php"><i class="fas fa-users-cog"></i> Gestão de admins</a>
        <?php endif; ?>

        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>

        <button id="theme-toggle" class="theme-toggle"><i class="fas fa-moon"></i></button> <!-- Toggle dark mode -->
    </div>
</div>

<div class="dashboard">
    <h1>Sistemas disponíveis</h1>
    <p class="subtitle">Selecione um sistema para aceder</p>

    <div class="search-bar">
        <input type="text" id="system-search" placeholder="Pesquisar sistemas...">
        <i class="fas fa-search"></i>
    </div>

    <div class="cards">
        <?php foreach ($systems as $s): ?>
            <div class="card" data-name="<?= strtolower(htmlspecialchars($s['nome'])) ?>">
                <div class="card-content"> <!-- Wrapper para flex column -->
                    <div class="card-logo">
                        <?php if (!empty($s['logo'])): ?>
                            <img src="assets/logos/<?= htmlspecialchars($s['logo']) ?>"
                                 alt="<?= htmlspecialchars($s['nome']) ?>">
                        <?php else: ?>
                            <div class="card-icon"><i class="fas fa-desktop"></i></div>
                        <?php endif; ?>
                    </div>

                    <h3><?= htmlspecialchars($s['nome']) ?></h3>
                </div>

                <a href="enter_system.php?id=<?= $s['id'] ?>"
                   class="btn"
                   target="_blank"
                   rel="noopener noreferrer">
                    Entrar
                </a>
            </div>
        <?php endforeach; ?>
    </div>
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

// Search filter
const searchInput = document.getElementById('system-search');
searchInput.addEventListener('input', (e) => {
    const filter = e.target.value.toLowerCase();
    document.querySelectorAll('.card').forEach(card => {
        const name = card.dataset.name;
        card.style.display = name.includes(filter) ? 'block' : 'none';
    });
});
</script>

</body>
</html>