<?php
/**
 * Rules view — regras oficiais da Sueca, coerente com o design do lobby.
 * Estrutura: hero, índice lateral, secções com cards, glossário e CTAs.
 */
global $i18n;
$sections = [
    ['id' => 'overview',    'icon' => 'bi-info-circle-fill', 'color' => 'text-primary', 'title' => $i18n['rules_overview'] ?? 'Visão geral'],
    ['id' => 'deck',        'icon' => 'bi-collection-fill',  'color' => 'text-spades',  'title' => $i18n['rules_deck'] ?? 'Baralho e cartas'],
    ['id' => 'deal',        'icon' => 'bi-shuffle',          'color' => 'text-success', 'title' => $i18n['rules_deal'] ?? 'Distribuição e trunfo'],
    ['id' => 'play',        'icon' => 'bi-play-circle-fill', 'color' => 'text-danger',  'title' => $i18n['rules_play'] ?? 'Como se joga'],
    ['id' => 'scoring',     'icon' => 'bi-trophy-fill',      'color' => 'text-warning', 'title' => $i18n['rules_scoring'] ?? 'Pontuação'],
    ['id' => 'etiquette',   'icon' => 'bi-heart-fill',       'color' => 'text-hearts',  'title' => $i18n['rules_etiquette'] ?? 'Etiqueta e fair-play'],
];
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $i18n['rules'] ?? 'Regras' ?> - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/rules.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body class="bg-welcome bg-body-tertiary d-flex flex-column min-vh-100">

    <?php include __DIR__ . '/_partials_navbar.php'; ?>

    <main class="container flex-grow-1">

        <!-- HERO -->
        <div class="hero-rules bg-body-tertiary rounded-4 shadow-sm p-4 p-md-5 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="small text-uppercase fw-bold text-warning mb-2">
                        <i class="bi bi-book-half me-1"></i><?= $i18n['rules_kicker'] ?? 'Guia oficial' ?>
                    </div>
                    <h1 class="fw-bold text-body m-0"><?= $i18n['rules_title'] ?? 'Regras da Sueca' ?></h1>
                    <p class="text-muted m-0 mt-2 fs-5"><?= $i18n['rules_subtitle'] ?? 'Tudo o que precisas de saber para jogar como um portuga de gema.' ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="/" class="btn btn-outline-secondary rounded-pill px-3 me-1">
                        <i class="bi bi-arrow-left me-1"></i><?= $i18n['back_to_lobby'] ?? 'Voltar ao lobby' ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- ÍNDICE LATERAL -->
            <aside class="col-lg-3">
                <div class="toc-sticky">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-3">
                            <div class="small text-uppercase fw-bold text-muted mb-2 px-2">
                                <?= $i18n['rules_toc'] ?? 'Índice' ?>
                            </div>
                            <nav id="rules-toc" class="d-flex flex-column gap-1">
                                <?php foreach ($sections as $s): ?>
                                    <a href="#<?= $s['id'] ?>" class="toc-link" data-target="<?= $s['id'] ?>">
                                        <i class="bi <?= $s['icon'] ?> <?= $s['color'] ?>"></i>
                                        <span><?= $s['title'] ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTEÚDO -->
            <div class="col-lg-9 d-flex flex-column gap-4">

                <!-- VISÃO GERAL -->
                <section id="overview" class="rule-card card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i><?= $i18n['rules_overview'] ?? 'Visão geral' ?></h4>
                        <p class="text-body mb-3"><?= $i18n['rules_overview_p1'] ?? 'A Sueca é um jogo de cartas tradicional português para 4 jogadores, dispostos em duas parcerias sentadas frente a frente. O objetivo é somar 61 ou mais pontos em vazas ao longo da partida.' ?></p>
                        <div class="row row-cols-2 row-cols-md-4 g-3">
                            <div class="col"><div class="stat-tile bg-body rounded-4 shadow-sm p-3 h-100 border text-center">
                                <div class="small text-muted text-uppercase fw-bold"><?= $i18n['players'] ?? 'Jogadores' ?></div>
                                <div class="fs-3 fw-bold text-success">4</div>
                            </div></div>
                            <div class="col"><div class="stat-tile bg-body rounded-4 shadow-sm p-3 h-100 border text-center">
                                <div class="small text-muted text-uppercase fw-bold"><?= $i18n['teams'] ?? 'Equipas' ?></div>
                                <div class="fs-3 fw-bold text-primary">2</div>
                            </div></div>
                            <div class="col"><div class="stat-tile bg-body rounded-4 shadow-sm p-3 h-100 border text-center">
                                <div class="small text-muted text-uppercase fw-bold"><?= $i18n['cards'] ?? 'Cartas' ?></div>
                                <div class="fs-3 fw-bold text-warning">40</div>
                            </div></div>
                            <div class="col"><div class="stat-tile bg-body rounded-4 shadow-sm p-3 h-100 border text-center">
                                <div class="small text-muted text-uppercase fw-bold"><?= $i18n['goal'] ?? 'Meta' ?></div>
                                <div class="fs-3 fw-bold text-danger">61</div>
                            </div></div>
                        </div>
                    </div>
                </section>

                <!-- BARALHO -->
                <section id="deck" class="rule-card card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-collection-fill text-spades me-2"></i><?= $i18n['rules_deck'] ?? 'Baralho e cartas' ?></h4>
                        <p class="text-body"><?= $i18n['rules_deck_p1'] ?? 'Joga-se com um baralho de 40 cartas — as cartas 8, 9 e 10 são retiradas do baralho tradicional.' ?></p>

                        <div class="row g-3 my-3">
                            <div class="col-6 col-md-3"><div class="suit-tile"><i class="bi bi-suit-heart-fill fs-3 text-hearts"></i><div><div class="fw-bold"><?= $i18n['suit_hearts'] ?? 'Copas' ?></div><small class="text-muted">10 <?= $i18n['cards'] ?? 'cartas' ?></small></div></div></div>
                            <div class="col-6 col-md-3"><div class="suit-tile"><i class="bi bi-suit-diamond-fill fs-3 text-diamonds" style="color:#e74c3c"></i><div><div class="fw-bold"><?= $i18n['suit_diamonds'] ?? 'Ouros' ?></div><small class="text-muted">10 <?= $i18n['cards'] ?? 'cartas' ?></small></div></div></div>
                            <div class="col-6 col-md-3"><div class="suit-tile"><i class="bi bi-suit-club-fill fs-3 text-spades"></i><div><div class="fw-bold"><?= $i18n['suit_clubs'] ?? 'Paus' ?></div><small class="text-muted">10 <?= $i18n['cards'] ?? 'cartas' ?></small></div></div></div>
                            <div class="col-6 col-md-3"><div class="suit-tile"><i class="bi bi-suit-spade-fill fs-3 text-spades"></i><div><div class="fw-bold"><?= $i18n['suit_spades'] ?? 'Espadas' ?></div><small class="text-muted">10 <?= $i18n['cards'] ?? 'cartas' ?></small></div></div></div>
                        </div>

                        <div class="small text-uppercase fw-bold text-muted mb-2"><?= $i18n['rules_card_order'] ?? 'Ordem das cartas (mais alta → mais baixa)' ?></div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach (['A','7','R','V','D','6','5','4','3','2'] as $c): ?>
                                <span class="card-value"><?= $c ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- DISTRIBUIÇÃO -->
                <section id="deal" class="rule-card card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-shuffle text-success me-2"></i><?= $i18n['rules_deal'] ?? 'Distribuição e trunfo' ?></h4>
                        <ol class="list-unstyled d-flex flex-column gap-3 m-0">
                            <li class="d-flex gap-3"><span class="step-num">1</span><div><strong><?= $i18n['rules_deal_s1_t'] ?? 'Baralhar e cortar' ?>.</strong> <?= $i18n['rules_deal_s1_d'] ?? 'O jogador à direita do dador baralha e o da esquerda corta o baralho.' ?></div></li>
                            <li class="d-flex gap-3"><span class="step-num">2</span><div><strong><?= $i18n['rules_deal_s2_t'] ?? 'Dar as cartas' ?>.</strong> <?= $i18n['rules_deal_s2_d'] ?? 'O dador distribui 10 cartas a cada jogador, uma a uma, no sentido contrário aos ponteiros do relógio.' ?></div></li>
                            <li class="d-flex gap-3"><span class="step-num">3</span><div><strong><?= $i18n['rules_deal_s3_t'] ?? 'Definir o trunfo' ?>.</strong> <?= $i18n['rules_deal_s3_d'] ?? 'A última carta dada ao próprio dador é virada e o seu naipe define o trunfo da mão.' ?></div></li>
                            <li class="d-flex gap-3"><span class="step-num">4</span><div><strong><?= $i18n['rules_deal_s4_t'] ?? 'Início' ?>.</strong> <?= $i18n['rules_deal_s4_d'] ?? 'O jogador à direita do dador abre a primeira vaza.' ?></div></li>
                        </ol>
                    </div>
                </section>

                <!-- JOGO -->
                <section id="play" class="rule-card card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-play-circle-fill text-danger me-2"></i><?= $i18n['rules_play'] ?? 'Como se joga' ?></h4>
                        <ul class="list-unstyled d-flex flex-column gap-2 m-0">
                            <li><i class="bi bi-check2-circle text-success me-2"></i><?= $i18n['rules_play_r1'] ?? 'É obrigatório assistir — jogar uma carta do naipe pedido, se a tiveres.' ?></li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><?= $i18n['rules_play_r2'] ?? 'Se não tiveres o naipe pedido, podes trunfar ou descartar qualquer outra carta.' ?></li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><?= $i18n['rules_play_r3'] ?? 'A vaza é ganha pela carta mais alta do naipe pedido, salvo se houver trunfo — nesse caso, ganha o trunfo mais alto.' ?></li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><?= $i18n['rules_play_r4'] ?? 'Quem ganha a vaza abre a seguinte.' ?></li>
                            <li><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i><?= $i18n['rules_play_r5'] ?? 'Não é permitido comunicar com o parceiro por gestos, palavras ou sinais.' ?></li>
                        </ul>
                    </div>
                </section>

                <!-- PONTUAÇÃO -->
                <section id="scoring" class="rule-card card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i><?= $i18n['rules_scoring'] ?? 'Pontuação' ?></h4>
                        <p class="text-body"><?= $i18n['rules_scoring_p1'] ?? 'Só valem pontos as seguintes cartas. As restantes servem apenas para ganhar a vaza.' ?></p>
                        <div class="table-responsive rounded-4 border overflow-hidden">
                            <table class="table table-hover align-middle m-0">
                                <thead class="bg-body-secondary">
                                    <tr><th><?= $i18n['card'] ?? 'Carta' ?></th><th><?= $i18n['name'] ?? 'Nome' ?></th><th class="text-end"><?= $i18n['points'] ?? 'Pontos' ?></th></tr>
                                </thead>
                                <tbody>
                                    <tr class="score-row"><td><span class="card-value">A</span></td><td><?= $i18n['card_ace'] ?? 'Ás' ?></td><td class="text-end fw-bold text-success">11</td></tr>
                                    <tr class="score-row"><td><span class="card-value">7</span></td><td><?= $i18n['card_seven'] ?? 'Sete' ?></td><td class="text-end fw-bold text-success">10</td></tr>
                                    <tr class="score-row"><td><span class="card-value">R</span></td><td><?= $i18n['card_king'] ?? 'Rei' ?></td><td class="text-end fw-bold text-success">4</td></tr>
                                    <tr class="score-row"><td><span class="card-value">V</span></td><td><?= $i18n['card_jack'] ?? 'Valete' ?></td><td class="text-end fw-bold text-success">3</td></tr>
                                    <tr class="score-row"><td><span class="card-value">D</span></td><td><?= $i18n['card_queen'] ?? 'Dama' ?></td><td class="text-end fw-bold text-success">2</td></tr>
                                    <tr class="score-row"><td colspan="2" class="text-muted fst-italic"><?= $i18n['card_others'] ?? '6, 5, 4, 3, 2' ?></td><td class="text-end text-muted">0</td></tr>
                                </tbody>
                                <tfoot class="bg-body-secondary">
                                    <tr><th colspan="2"><?= $i18n['rules_total_hand'] ?? 'Total por mão' ?></th><th class="text-end">120</th></tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row row-cols-1 row-cols-md-3 g-3 mt-3">
                            <div class="col"><div class="stat-tile bg-body rounded-4 shadow-sm p-3 h-100 border">
                                <div class="small text-uppercase fw-bold text-muted"><?= $i18n['rules_win'] ?? 'Vitória' ?></div>
                                <div class="fw-bold text-success mt-1"><i class="bi bi-1-circle-fill me-1"></i>61–90 <?= $i18n['points'] ?? 'pontos' ?></div>
                                <small class="text-muted"><?= $i18n['rules_win_d'] ?? 'A equipa ganha a partida.' ?></small>
                            </div></div>
                            <div class="col"><div class="stat-tile bg-body rounded-4 shadow-sm p-3 h-100 border">
                                <div class="small text-uppercase fw-bold text-muted"><?= $i18n['rules_bola'] ?? 'Bola' ?></div>
                                <div class="fw-bold text-warning mt-1"><i class="bi bi-2-circle-fill me-1"></i>91–119</div>
                                <small class="text-muted"><?= $i18n['rules_bola_d'] ?? 'Vale por duas partidas.' ?></small>
                            </div></div>
                            <div class="col"><div class="stat-tile bg-body rounded-4 shadow-sm p-3 h-100 border">
                                <div class="small text-uppercase fw-bold text-muted"><?= $i18n['rules_capote'] ?? 'Capote' ?></div>
                                <div class="fw-bold text-danger mt-1"><i class="bi bi-3-circle-fill me-1"></i>120</div>
                                <small class="text-muted"><?= $i18n['rules_capote_d'] ?? 'Todas as vazas — vale por quatro.' ?></small>
                            </div></div>
                        </div>
                    </div>
                </section>

                <!-- ETIQUETA -->
                <section id="etiquette" class="rule-card card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-heart-fill text-hearts me-2"></i><?= $i18n['rules_etiquette'] ?? 'Etiqueta e fair-play' ?></h4>
                        <div class="row g-3">
                            <div class="col-md-6"><div class="suit-tile h-100"><i class="bi bi-hand-thumbs-up-fill fs-3 text-success"></i><div><div class="fw-bold"><?= $i18n['rules_etq1_t'] ?? 'Respeita o parceiro' ?></div><small class="text-muted"><?= $i18n['rules_etq1_d'] ?? 'Sem críticas às jogadas — cada mão é uma nova oportunidade.' ?></small></div></div></div>
                            <div class="col-md-6"><div class="suit-tile h-100"><i class="bi bi-chat-dots-fill fs-3 text-primary"></i><div><div class="fw-bold"><?= $i18n['rules_etq2_t'] ?? 'Sem sinais' ?></div><small class="text-muted"><?= $i18n['rules_etq2_d'] ?? 'Proibido comunicar cartas por gestos, códigos ou palavras.' ?></small></div></div></div>
                            <div class="col-md-6"><div class="suit-tile h-100"><i class="bi bi-clock-fill fs-3 text-warning"></i><div><div class="fw-bold"><?= $i18n['rules_etq3_t'] ?? 'Joga com ritmo' ?></div><small class="text-muted"><?= $i18n['rules_etq3_d'] ?? 'Pensa a jogada, mas não demores demasiado.' ?></small></div></div></div>
                            <div class="col-md-6"><div class="suit-tile h-100"><i class="bi bi-emoji-smile-fill fs-3 text-danger"></i><div><div class="fw-bold"><?= $i18n['rules_etq4_t'] ?? 'Diverte-te' ?></div><small class="text-muted"><?= $i18n['rules_etq4_d'] ?? 'É um jogo — ganhar é bom, jogar bem é melhor.' ?></small></div></div></div>
                        </div>
                    </div>
                </section>

                <!-- CTA FINAL -->
                <div class="text-center py-3 bg-body-tertiary rounded-4 shadow-sm border">
                    <a href="/" class="btn btn-carmine btn-lg rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i><?= $i18n['back_to_lobby'] ?? 'Voltar ao lobby' ?>
                    </a>
                </div>

            </div>
        </div>

    </main>

    <?php include __DIR__ . '/_partials_footer.php'; ?>

    <script src="../js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/rules.js"></script>
</body>
</html>
