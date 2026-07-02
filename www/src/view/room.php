<?php /** @var array $room */ ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala: <?= htmlspecialchars($room['name']) ?> - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success m-0">Sala: <?= htmlspecialchars($room['name']) ?></h2>
            <!-- Rota atualizada para o novo Router -->
            <a href="/" class="btn btn-outline-secondary">Sair da Sala</a>
        </div>

        <?php if (isset($_SESSION['room_error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['room_error']) ?>
                <?php unset($_SESSION['room_error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($room['status'] === 'Waiting'): ?>
            <!-- ESTADO: À ESPERA DE JOGADORES -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Jogadores na Sala (<?= count($room['players']) ?>/4)</h5>
                    <ul class="list-group list-group-flush mb-4">
                        <?php foreach ($room['players'] as $player): ?>
                            <li class="list-group-item">👤 <strong><?= htmlspecialchars($player) ?></strong></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (count($room['players']) === 4 && $room['owner_id'] === $_SESSION['user_id']): ?>
                        <div class="alert alert-info">A sala está cheia! Como proprietário, inicie a partida.</div>
                        <!-- CORREÇÃO DA ROTA: Usar ? em vez de & -->
                        <form action="/start_game?id=<?= $room['id'] ?>" method="POST">
                            <button type="submit" class="btn btn-success w-100 p-3 fw-bold">Iniciar Partida</button>
                        </form>
                    <?php elseif (count($room['players']) === 4): ?>
                        <div class="alert alert-info">A sala está cheia! A aguardar que o proprietário inicie a partida...</div>
                    <?php else: ?>
                        <div class="alert alert-warning">A aguardar pela entrada de mais jogadores...</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($room['status'] === 'Playing'): ?>
            <!-- ESTADO: A JOGAR (FRONTEND JAVASCRIPT) -->
            <div class="card shadow-sm border-0 bg-dark mb-4" id="game-board">
                <div class="card-body text-center text-white p-4">
                    <h4 class="text-success mb-3">Mesa de Jogo</h4>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h6>Trunfo: <span id="trump-card" class="badge bg-primary fs-6">-</span></h6>
                            <h6 id="turn-indicator" class="text-warning mt-2">A carregar estado do jogo...</h6>
                        </div>
                    </div>

                    <div class="bg-secondary p-4 rounded mb-4" style="min-height: 150px;">
                        <h5 class="text-light">Cartas na Mesa</h5>
                        <div id="table-cards-container">
                            <p class="text-white-50 mt-4 small">Ainda não foram jogadas cartas nesta vaza.</p>
                        </div>
                    </div>

                    <div>
                        <h5>A Minha Mão</h5>
                        <div id="my-hand-container" class="d-flex justify-content-center flex-wrap"></div>
                    </div>
                </div>
            </div>
            <script src="js/app.js"></script>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>