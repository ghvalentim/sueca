<?php
$room = $room ?? [
    'id' => null,
    'name' => '',
    'owner_id' => null,
    'players' => [],
];

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala: <?= htmlspecialchars($room['name']) ?> - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success m-0">Sala: <?= htmlspecialchars($room['name']) ?></h2>
            <a href="?action=home" class="btn btn-outline-secondary">Sair da Sala (Voltar ao Lobby)</a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Jogadores na Sala (<?= count($room['players']) ?>/4)</h5>
                <ul class="list-group list-group-flush mb-4">
                    <?php foreach ($room['players'] as $player): ?>
                        <li class="list-group-item">👤 <strong><?= htmlspecialchars($player) ?></strong></li>
                    <?php endforeach; ?>
                </ul>

                <?php if (count($room['players']) === 4 && $room['owner_id'] === $_SESSION['user_id']): ?>
                    <div class="alert alert-info">A sala está cheia! Como proprietário, pode iniciar a partida.</div>
                    <button class="btn btn-success w-100 p-3 fw-bold" id="btn-start-game" disabled>Iniciar Partida (Será implementado na Sprint 4)</button>
                <?php elseif (count($room['players']) === 4): ?>
                    <div class="alert alert-info">A sala está cheia! A aguardar que o proprietário inicie a partida...</div>
                <?php else: ?>
                    <div class="alert alert-warning">A aguardar pela entrada de mais jogadores...</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 bg-dark">
            <div class="card-body text-center text-white p-5">
                <h4 class="mb-3">Mesa de Jogo</h4>
                <p class="mb-0 text-muted"><em>O tabuleiro da Sueca (Frontend Vanilla JS) será carregado aqui futuramente, consumindo os endpoints da API através do JWT.</em></p>
            </div>
        </div>
    </div>
</body>
</html>