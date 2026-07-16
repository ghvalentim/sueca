<?php /** @var array $room */
global $i18n;
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $i18n['room'] ?? 'Sala' ?>: <?= htmlspecialchars($room['name']) ?> - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/gameboard.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <style>
        .player-slot { transition: transform .2s ease; }
        .player-slot:hover { transform: translateY(-2px); }
        .pulse-dot { display:inline-block; width:8px; height:8px; border-radius:50%; background:#27ae60; box-shadow:0 0 0 rgba(39,174,96,.6); animation: pulse 1.6s infinite; }
        @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(39,174,96,.6)} 70%{box-shadow:0 0 0 12px rgba(39,174,96,0)} 100%{box-shadow:0 0 0 0 rgba(39,174,96,0)} }
    </style>
</head>
<body class="bg-welcome bg-body-tertiary d-flex flex-column min-vh-100">

    <?php include __DIR__ . '/_partials_navbar.php'; ?>

    <main class="container flex-grow-1">
        <div class="d-flex align-items-center justify-content-between mb-4 bg-body rounded-4 shadow-sm p-3 border border-success border-opacity-25">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-door-open-fill text-success fs-4"></i>
                <div>
                    <div class="small text-muted text-uppercase fw-bold"><?= $i18n['room'] ?></div>
                    <h4 class="fw-bold text-body m-0"><?= htmlspecialchars($room['name']) ?></h4>
                </div>
            </div>
            <a href="/leave_room?id=<?= $room['id'] ?>" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-box-arrow-left me-1"></i><?= $i18n['leave_room'] ?>
            </a>
        </div>

        <?php if ($room['status'] === 'Waiting'): ?>
            <!-- LOBBY DA SALA -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-success text-white py-3 d-flex align-items-center justify-content-between">
                            <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i><?= $i18n['waiting_players'] ?></h5>
                            <span class="badge bg-white text-success rounded-pill fs-6"><?= count($room['players']) ?>/4</span>
                        </div>
                        <div class="card-body bg-body p-4">
                            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                                <?php for ($i = 0; $i < 4; $i++): ?>
                                    <?php $player = $room['players'][$i] ?? null; ?>
                                    <div class="col">
                                        <div class="player-slot d-flex align-items-center gap-3 p-3 rounded-4 border <?= $player ? 'bg-success bg-opacity-10 border-success border-opacity-25' : 'bg-body-tertiary border-dashed' ?>">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle <?= $player ? 'bg-success text-white' : 'bg-body-secondary text-muted' ?>" style="width:44px; height:44px;">
                                                <?php if ($player): ?>
                                                    <i class="bi bi-person-fill fs-4"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-hourglass-split"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <?php if ($player): ?>
                                                    <div class="fw-bold text-body"><?= htmlspecialchars($player) ?></div>
                                                    <div class="small text-success"><span class="pulse-dot me-1"></span><?= $i18n['ready'] ?? 'Pronto' ?></div>
                                                <?php else: ?>
                                                    <div class="fw-bold text-muted"><?= $i18n['waiting_slot'] ?? 'À espera de um jogador…' ?></div>
                                                    <div class="small text-muted"><?= $i18n['empty_slot'] ?? 'Lugar livre' ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <?php if (count($room['players']) === 4 && $room['owner_id'] === $_SESSION['user_id']): ?>
                                <div class="alert alert-info d-flex align-items-center shadow-sm rounded-4">
                                    <i class="bi bi-info-circle-fill me-2"></i><?= $i18n['room_full_owner'] ?>
                                </div>
                                <form action="/start_game?id=<?= $room['id'] ?>" method="POST">
                                    <button type="submit" class="btn btn-success btn-lg w-100 p-3 fw-bold rounded-pill shadow-sm">
                                        <i class="bi bi-play-circle-fill me-2"></i><?= $i18n['start_game'] ?>
                                    </button>
                                </form>
                            <?php elseif (count($room['players']) === 4): ?>
                                <div class="alert alert-info d-flex align-items-center shadow-sm rounded-4 mb-0">
                                    <i class="bi bi-hourglass-top me-2"></i><?= $i18n['room_full_not_owner'] ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning d-flex align-items-center shadow-sm rounded-4 mb-0">
                                    <i class="bi bi-people me-2"></i><?= $i18n['waiting_more_players'] ?? 'À espera de mais jogadores para começar…' ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($room['status'] === 'Playing' || $room['status'] === 'Finished'): ?>

            <!-- MESA DE JOGO -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" id="game-board"
                 data-jwt="<?= htmlspecialchars($_SESSION['jwt_token'] ?? '') ?>"
                 data-room-id="<?= htmlspecialchars($room['id']) ?>"
                 data-external-game-api-url="<?= htmlspecialchars(Config\App::externalGameAPI()) ?>">

                <div class="card-body p-4" style="background-color: #1a252f;">

                    <!-- Top Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-4 px-4 py-3 bg-dark rounded-4 text-white shadow-sm border border-secondary border-opacity-50">
                        <div class="text-center">
                            <div class="small text-uppercase text-muted fw-bold"><?= $i18n['team_a'] ?></div>
                            <div class="fs-3 fw-bold text-warning" id="score-team-a">0</div>
                        </div>
                        <div class="text-center">
                            <span class="badge bg-primary rounded-pill fs-6 mb-2 px-3 py-2 border border-light border-opacity-25">
                                <i class="bi bi-suit-club-fill me-1"></i>Trunfo: <span id="trump-card">-</span>
                            </span>
                            <div id="turn-indicator" class="text-white small fw-bold"><span class="pulse-dot me-1"></span> A ligar ao Motor de Jogo...</div>
                        </div>
                        <div class="text-center">
                            <div class="small text-uppercase text-muted fw-bold"><?= $i18n['team_b'] ?></div>
                            <div class="fs-3 fw-bold text-warning" id="score-team-b">0</div>
                        </div>
                    </div>

                    <!-- MESA VERDE -->
                    <div class="position-relative mx-auto shadow-lg border border-5 border-secondary"
                         style="width: 100%; max-width: 800px; height: 450px; background: radial-gradient(circle, #27ae60, #145a32); border-radius: 220px;">

                        <div class="position-absolute top-0 start-50 translate-middle-x mt-3 text-center" style="z-index: 10;">
                            <img id="avatar-top" src="" class="rounded-circle border border-2 border-white shadow-sm d-none" width="60" height="60" style="object-fit: cover;">
                            <div id="name-top" class="text-white small fw-bold text-shadow mt-1"><?= $i18n['loading'] ?>...</div>
                            <div id="cards-top" class="badge bg-dark mt-1 border border-secondary">🂠 ?</div>
                        </div>

                        <div class="position-absolute top-50 start-0 translate-middle-y ms-4 text-center" style="z-index: 10;">
                            <img id="avatar-left" src="" class="rounded-circle border border-2 border-white shadow-sm d-none" width="60" height="60" style="object-fit: cover;">
                            <div id="name-left" class="text-white small fw-bold text-shadow mt-1"><?= $i18n['loading'] ?>...</div>
                            <div id="cards-left" class="badge bg-dark mt-1 border border-secondary">🂠 ?</div>
                        </div>

                        <div class="position-absolute top-50 end-0 translate-middle-y me-4 text-center" style="z-index: 10;">
                            <img id="avatar-right" src="" class="rounded-circle border border-2 border-white shadow-sm d-none" width="60" height="60" style="object-fit: cover;">
                            <div id="name-right" class="text-white small fw-bold text-shadow mt-1"><?= $i18n['loading'] ?>...</div>
                            <div id="cards-right" class="badge bg-dark mt-1 border border-secondary">🂠 ?</div>
                        </div>

                        <div class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center" id="table-cards-container" style="width: 200px; height: 200px;">
                            <span class="text-white-50 small fst-italic"><?= $i18n['empty_table'] ?></span>
                        </div>
                    </div>

                    <!-- Minha Mão -->
                    <div class="mt-4 text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2 gap-2">
                            <img id="avatar-me" src="" class="rounded-circle border border-2 border-warning shadow d-none" width="45" height="45" style="object-fit: cover;">
                            <h5 class="text-white m-0 fw-bold" id="name-me"><?= $i18n['drawing_hand'] ?>...</h5>
                        </div>
                        <div id="my-hand-container" class="d-flex justify-content-center flex-wrap px-3 py-3 bg-dark rounded-4 shadow border border-secondary" style="min-height: 100px;"></div>
                    </div>
                </div>
            </div>

            <script src="../js/app.js"></script>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
