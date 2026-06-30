<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container text-center mt-5">
        <h1 class="text-white fw-bold mb-4">Lobby Jogosueca</h1>
        <div class="card shadow-sm mx-auto p-4" style="max-width: 600px;">
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <p class="lead mb-4">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>

                <!-- Formulário de Criação de Sala -->
                <div class="bg-light p-3 rounded mb-4 text-start border">
                    <h6 class="mb-3">Criar Nova Sala</h6>
                    <form action="?action=create_room" method="POST" class="d-flex gap-2">
                        <input type="text" name="room_name" class="form-control" placeholder="Introduza o Nome da Sala" required>
                        <button type="submit" class="btn btn-success px-4">Criar</button>
                    </form>
                </div>

                <!-- Listagem das Salas -->
                <div class="text-start mb-4">
                    <h6 class="mb-3">Salas Disponíveis (A aguardar jogadores)</h6>
                    <?php if (empty($rooms)): ?>
                        <p class="text-muted small">Não existem salas abertas no momento.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($rooms as $r): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($r['name']) ?></strong> 
                                        <span class="badge bg-secondary ms-2"><?= $r['player_count'] ?>/4 Jogadores</span>
                                        <br><small class="text-muted">Proprietário: <?= htmlspecialchars($r['owner_name']) ?></small>
                                    </div>
                                    <?php if ($r['player_count'] < 4): ?>
                                        <a href="?action=join_room&id=<?= $r['id'] ?>" class="btn btn-sm btn-spades-card">Entrar</a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Cheia</button>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2">
                    <a href="?action=profile" class="btn btn-carmine text-white">O Meu Perfil</a>
                    <a href="?action=logout" class="btn btn-outline-spades">Terminar Sessão</a>
                </div>
            <?php else: ?>
                <p class="lead mb-4">Bem-vindo ao Portal Web!</p>
                <div class="d-grid gap-3">
                    <a href="?action=login" class="btn btn-success btn-lg">Iniciar Sessão</a>
                    <a href="?action=register" class="btn btn-outline-primary btn-lg">Criar Conta</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>