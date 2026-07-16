<?php 
/** @var array $rooms 
 * @var array $profileUser
*/
global $i18n; 
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $i18n['lobby_title'] ?? 'Lobby - Jogosueca' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body class="bg-welcome bg-body-tertiary d-flex flex-column min-vh-100">

    <?php include __DIR__ . '/_partials_navbar.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="container flex-grow-1">
        
        <?php if (isset($_SESSION['user_id'])): ?>
            
            <!-- VISÃO: UTILIZADOR AUTENTICADO -->
            <div class="row g-4">
                
                <!-- Coluna Esquerda: Painel Pessoal -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> <?= $i18n['welcome'] ?>, <?= htmlspecialchars($_SESSION['username']) ?>!</h5>
                        </div>
                        <div class="card-body bg-body">
                            <div class="mb-3">
                              <?php
                            $avatarUrl = !empty($profileUser['avatar'])
                                ? $profileUser['avatar']
                                : 'https://ui-avatars.com/api/?name=' . urlencode($profileUser['username']) . '&background=198754&color=fff&size=150';
                        ?>
                        <div class="avatar-ring mx-auto mb-3" style="width: 158px; height: 158px;">
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; background: var(--bs-body-bg);">
                        </div>

                        <h4 class="fw-bold mb-0"><?= htmlspecialchars($profileUser['username']) ?></h4>  
                                
                            <div class="row g-2 my-2">
                            <div class="col-4">
                                <div class="stat-tile p-3 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25 h-100">
                                    <div class="fs-4 fw-bold text-info"><?= htmlspecialchars($profileUser['games_played'] ?? 0) ?></div>
                                    <div class="small text-muted"><?= $i18n['games_played'] ?></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-tile p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 h-100">
                                    <div class="fs-4 fw-bold text-success"><?= htmlspecialchars($profileUser['games_won'] ?? 0) ?></div>
                                    <div class="small text-muted"><?= $i18n['games_won'] ?></div>
                                </div>
                            </div>
                            <?php $winRate = ($profileUser['games_played'] ?? 0) > 0 ? round(($profileUser['games_won'] ?? 0) / $profileUser['games_played'] * 100) : 0; ?>
                            <div class="col-4">
                                <div class="stat-tile p-3 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 h-100">
                                    <div class="fs-4 fw-bold text-warning"><?= $winRate ?>%</div>
                                    <div class="small text-muted"><?= $i18n['win_rate'] ?></div>
                                </div>
                            </div>
                        </div>
                            </div>
                            <div class="d-grid gap-2 mb-4">
                                <a href="/profile" class="btn btn-primary fw-bold"><i class="bi bi-person-vcard me-2"></i><?= $i18n['profile_btn'] ?? 'O Meu Perfil' ?></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita: O Salão (Grid de Salas) -->
                <div class="col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-3 bg-body rounded-4 shadow-sm p-3 border border-success border-opacity-25">
                        <h4 class="fw-bold text-body m-0"><i class="bi bi-door-open-fill me-2 text-success"></i> <?= $i18n['available_rooms'] ?></h4>
                        <span class="badge bg-secondary rounded-pill fs-6"><?= count($rooms) ?> <?= $i18n['rooms'] ?></span>
                    </div>

                    <?php if (empty($rooms)): ?>
                        <!-- Empty State -->
                        <div class="text-center p-5 bg-body rounded-4 shadow-sm border border-dashed text-muted mt-3">
                            <i class="bi bi-wind fs-1 mb-3 d-block opacity-50"></i>
                            <h5><?= $i18n['no_rooms'] ?></h5>
                            <p class="mb-0"><?= $i18n['be_first'] ?></p>
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <?php foreach ($rooms as $r): ?>
                                <div class="col">
                                    <div class="card room-card border-success border-opacity-25 h-100 bg-body">
                                        <div class="card-body">
                                            <h5 class="card-title fw-bold text-success mb-3"><?= htmlspecialchars($r['name']) ?></h5>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <small class="text-muted"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($r['owner_name']) ?></small>
                                                <span class="badge <?= $r['player_count'] == 4 ? 'bg-danger' : 'bg-primary' ?> rounded-pill">
                                                    <i class="bi bi-people-fill me-1"></i> <?= $r['player_count'] . '/4 ' ?>
                                                </span>
                                            </div>

                                            <?php if ($r['player_count'] < 4): ?>
                                                <a href="/join_room?id=<?= $r['id'] ?>" class="btn btn-outline-success w-100 fw-bold">
                                                    <i class="bi bi-box-arrow-in-right me-1"></i> <?= $i18n['join_btn'] ?>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary w-100 opacity-50 fw-bold" disabled>
                                                    <i class="bi bi-lock-fill me-1"></i> <?= $i18n['full_btn'] ?>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <br>
                  <div class="d-flex align-items-center justify-content-between mb-3 bg-body rounded-4 shadow-sm p-3 border border-success border-opacity-25"> 
                    <div class="col">
                        <div class="card room-card border-success border-opacity-25 h-100 bg-body">
                            <div class="card-body">
                                <div class="row row-cols-1 row-cols-md-2 g-3"> 
                                    <h6 class="fw-bold mb-3 mt-3 text-body"><i class="bi bi-plus-square-dotted me-2"></i> <?= $i18n['create_room_title'] ?></h6>
                                        <form action="/create_room" method="POST">
                                            <div class="input-group mb-3 shadow-sm">
                                                <input type="text" name="room_name" class="form-control" placeholder="<?= $i18n['room_name_placeholder'] ?>" required>
                                                <button type="submit" class="btn btn-success fw-bold px-3"><?= $i18n['create_btn'] ?></button>
                                            </div>
                                        </form>
                                </div>
                                <div class="row row-cols-1 row-cols-md-2 g-3">
                                    <h6 class="fw-bold mb-3 mt-3 text-body"><i class="bi bi-robot me-2 text-warning"></i><?= $i18n['training_room'] ?></h6>
                                        <form action="/create_training" method="POST">
                                            <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm py-2">
                                            <?= $i18n['create_training_btn'] ?>
                                            </button>
                                        </form>
                                </div>
                            </div>
                        </div>
                  </div>
                </div>
            </div>
        </div>

        <?php else: ?>
            <!-- VISÃO: VISITANTE (LANDING PAGE) -->
            <div class="p-5 mb-5 bg-body-tertiary text-body rounded-5 shadow text-center position-relative overflow-hidden mt-4">
                <!-- Efeito visual de fundo (uma carta ou naipe grande atrás) -->
                <i class="bi bi-suit-spade-fill position-absolute text-body opacity-10" style="font-size: 15rem; top: -30px; right: 20px; transform: rotate(15deg);"></i>
                
                <h1 class="display-3 fw-bolder position-relative z-1 mb-3">Sueca Online</h1>
                <p class="lead position-relative z-1 mb-4 fs-4 opacity-75"><?= $i18n['lead'] ?? 'O clássico jogo de cartas, agora com os seus amigos.' ?></p>
                
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center position-relative z-1 mt-4">
                    <a href="/login" class="btn btn-hearts-card text-white btn-lg px-5 fw-bold rounded-pill shadow-sm"><?= $i18n['login_btn'] ?></a>
                    <a href="/register" class="btn btn-success btn-lg px-5 fw-bold rounded-pill"><?= $i18n['register_btn'] ?></a>
                </div>
            </div>

            <!-- Features -->
            <div class="row text-center mt-5 g-4">
                <div class="col-md-3">
                    <div class="p-4 bg-body rounded-4 shadow-sm h-100 border-top border-success border-4">
                        <i class="bi bi-globe-americas text-success fs-1 mb-3"></i>
                        <h4 class="fw-bold"><?= $i18n['multiplayer'] ?? 'Multiplayer Global' ?></h4>
                        <p class="text-muted"><?= $i18n['multiplayer_desc'] ?? 'Jogue com pessoas de todo o mundo em tempo real.' ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 bg-body rounded-4 shadow-sm h-100 border-top border-warning border-4">
                        <i class="bi bi-robot text-warning fs-1 mb-3"></i>
                        <h4 class="fw-bold"><?= $i18n['bots'] ?? 'Treino com Bots' ?></h4>
                        <p class="text-muted"><?= $i18n['bots_desc'] ?? 'Pratique as suas jogadas contra a nossa Inteligência Artificial.' ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 bg-body rounded-4 shadow-sm h-100 border-top border-primary border-4">
                        <i class="bi bi-graph-up-arrow text-primary fs-1 mb-3"></i>
                        <h4 class="fw-bold"><?= $i18n['stats'] ?? 'Estatísticas' ?></h4>
                        <p class="text-muted"><?= $i18n['stats_desc'] ?? 'Acompanhe a sua taxa de vitórias e suba no ranking.' ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 bg-body rounded-4 shadow-sm h-100 border-top border-info border-4">
                        <i class="bi bi-book text-info fs-1 mb-3"></i>
                        <h4 class="fw-bold"><?= $i18n['rules'] ?? 'Regras do Jogo' ?></h4>
                        <p class="text-muted"><?= $i18n['rules_desc'] ?? 'Aprenda as regras da Sueca e torne-se um mestre do jogo.' ?></p>
                        <a href="/rules" class="btn btn-outline-info mt-3 fw-bold"><?= $i18n['rules_btn'] ?? 'Ver Regras' ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
        <br>
    <!-- FOOTER -->
    <?php include __DIR__ . '/_partials_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme.js"></script>
</body>
</html>