<?php
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'auth_helpers.php';

$isRoot = isRootAdmin($pdo, (int)$_SESSION['admin_id']);

// carregar mapa de sistemas
$systemsMap = require __DIR__ . '/systems_map.php';

$adminId    = (int) $_SESSION['admin_id'];
$adminEmail = $_SESSION['admin_email'] ?? '';

// varrer todos os sistemas e verificar se o email do admin existe em cada um
$systems = [];

foreach ($systemsMap as $systemKey => $system) {
    if (str_starts_with($systemKey, '_')) continue;

    try {
        $pdoSys = new PDO($system['dsn'], $system['db_user'], $system['db_pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $stmt = $pdoSys->prepare(
            "SELECT {$system['id_field']} AS uid
             FROM {$system['users_table']}
             WHERE {$system['email_field']} = ?
             LIMIT 1"
        );
        $stmt->execute([$adminEmail]);
        $row = $stmt->fetch();

        if ($row) {
            // email existe — garantir mapeamento atualizado para SSO
            $userId = (int) $row['uid'];
            $existing = $pdo->prepare(
                "SELECT id FROM admin_user_map WHERE admin_id = ? AND system_key = ?"
            );
            $existing->execute([$adminId, $systemKey]);

            if ($existing->fetchColumn()) {
                $pdo->prepare(
                    "UPDATE admin_user_map SET user_id = ? WHERE admin_id = ? AND system_key = ?"
                )->execute([$userId, $adminId, $systemKey]);
            } else {
                $pdo->prepare(
                    "INSERT INTO admin_user_map (admin_id, system_key, user_id) VALUES (?, ?, ?)"
                )->execute([$adminId, $systemKey, $userId]);
            }

            $systems[] = [
                'key'  => $systemKey,
                'name' => $system['name'],
                'logo' => $system['logo'] ?? null,
            ];
        } else {
            // email não existe — remover mapeamento obsoleto
            $pdo->prepare(
                "DELETE FROM admin_user_map WHERE admin_id = ? AND system_key = ?"
            )->execute([$adminId, $systemKey]);
        }

    } catch (Exception $e) {
        // sistema offline — mostrar se houver mapeamento em cache
        $stmt = $pdo->prepare(
            "SELECT 1 FROM admin_user_map WHERE admin_id = ? AND system_key = ?"
        );
        $stmt->execute([$adminId, $systemKey]);
        if ($stmt->fetchColumn()) {
            $systems[] = [
                'key'  => $systemKey,
                'name' => $system['name'],
                'logo' => $system['logo'] ?? null,
            ];
        }
    }
}

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
            <div class="card" data-name="<?= strtolower(htmlspecialchars($s['name'])) ?>">
                <form method="post" action="remove_system.php"
                      onsubmit="return confirm('Remover <?= htmlspecialchars($s['name'], ENT_QUOTES) ?> do dashboard?')">
                    <input type="hidden" name="system" value="<?= htmlspecialchars($s['key']) ?>">
                    <button type="submit" class="card-remove" title="Remover acesso">
                        <i class="fas fa-times"></i>
                    </button>
                </form>

                <div class="card-content">
                    <div class="card-logo">
                        <?php if (!empty($s['logo'])): ?>
                            <img src="assets/logos/<?= htmlspecialchars($s['logo']) ?>"
                                alt="<?= htmlspecialchars($s['name']) ?>">
                        <?php else: ?>
                            <div class="card-icon"><i class="fas fa-desktop"></i></div>
                        <?php endif; ?>
                    </div>

                    <h3><?= htmlspecialchars($s['name']) ?></h3>
                </div>

                <a href="enter_system.php?system=<?= htmlspecialchars($s['key']) ?>"
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