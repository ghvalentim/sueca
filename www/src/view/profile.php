<?php
/** @var array $profileUser
 *  @var bool  $isOwner */
global $i18n;
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profileUser['username']) ?> - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body class="bg-welcome bg-body-tertiary d-flex flex-column min-vh-100 profile-body">

    <?php include __DIR__ . '/_partials_navbar.php'; ?>

    <main class="container flex-grow-1">

        <div class="d-flex align-items-center justify-content-between mb-4 bg-body rounded-4 shadow-sm p-3 border border-success border-opacity-25">
            <h4 class="fw-bold text-body m-0">
                <i class="bi bi-person-vcard-fill me-2 text-success"></i>
                <?= htmlspecialchars($profileUser['username']) ?>
            </h4>
            <a href="/" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i><?= $i18n['back_to_lobby'] ?>
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success d-flex align-items-center shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="row g-4 <?= !$isOwner ? 'justify-content-center' : '' ?>">

            <!-- Coluna Esquerda: Resumo -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-success text-white py-3 text-center">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i><?= $i18n['profile'] ?? 'Perfil' ?></h5>
                    </div>
                    <div class="card-body bg-body text-center p-4 d-flex flex-column">
                        <?php
                            $avatarUrl = !empty($profileUser['avatar'])
                                ? $profileUser['avatar']
                                : 'https://ui-avatars.com/api/?name=' . urlencode($profileUser['username']) . '&background=198754&color=fff&size=150';

                                $_SESSION['avatar'] = $avatarUrl; // Atualiza a sessão com o avatar correto
                        ?>
                        <div class="avatar-ring mx-auto mb-3" style="width: 158px; height: 158px;">
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; background: var(--bs-body-bg);">
                        </div>

                        <h4 class="fw-bold mb-0"><?= htmlspecialchars($profileUser['username']) ?></h4>

                        <?php if ($isOwner): ?>
                            <p class="text-muted small mb-3"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($profileUser['email']) ?></p>
                        <?php else: ?>
                            <div class="mb-3"></div>
                        <?php endif; ?>

                        <!-- Estatísticas -->
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

                        <!-- Redes sociais -->
                        <?php if (!empty($profileUser['discord']) || !empty($profileUser['steam']) || !empty($profileUser['instagram'])): ?>
                        <div class="d-flex justify-content-center gap-2 flex-wrap my-3">
                            <?php if (!empty($profileUser['discord'])): ?>
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3" title="Discord">
                                    <i class="bi bi-discord me-1"></i><?= htmlspecialchars($profileUser['discord']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($profileUser['steam'])): ?>
                                <a href="https://steamcommunity.com/id/<?= htmlspecialchars($profileUser['steam']) ?>" target="_blank"
                                   class="badge rounded-pill bg-dark bg-opacity-10 text-body border py-2 px-3 text-decoration-none" title="Steam">
                                    <i class="bi bi-steam me-1"></i><?= htmlspecialchars($profileUser['steam']) ?>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($profileUser['instagram'])): ?>
                                <a href="https://instagram.com/<?= htmlspecialchars($profileUser['instagram']) ?>" target="_blank"
                                   class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-2 px-3 text-decoration-none" title="Instagram">
                                    <i class="bi bi-instagram me-1"></i><?= htmlspecialchars($profileUser['instagram']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Biografia -->
                        <div class="text-start small fst-italic bg-body-tertiary p-3 rounded-4 border">
                            <i class="bi bi-quote fs-4 text-muted d-block"></i>
                            <?= !empty($profileUser['bio']) ? nl2br(htmlspecialchars($profileUser['bio'])) : '<span class="text-muted">'.($i18n['empty_bio'] ?? 'Sem biografia.').'</span>' ?>
                        </div>

                        <p class="text-muted small mt-auto pt-3 border-top mb-0">
                            <i class="bi bi-calendar-event me-1"></i>
                            <?= $i18n['member_since'] ?> <?= date('d/m/Y', strtotime($profileUser['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Edição -->
            <?php if ($isOwner): ?>
            <div class="col-lg-8">
                <!-- Detalhes -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i><?= $i18n['update_profile'] ?></h5>
                    </div>
                    <div class="card-body bg-body p-4">
                        <form method="POST" action="/profile" enctype="multipart/form-data">
                            <input type="hidden" name="form_type" value="details">

                            <div class="mb-4 bg-body-tertiary p-3 rounded-4 border">
                                <label class="form-label fw-bold"><i class="bi bi-image me-1 text-success"></i><?= $i18n['profile_picture'] ?></label>
                                <input type="file" class="form-control" name="avatar_file" accept=".jpg,.jpeg,.png,.gif,.webp" placeholder="<?= $i18n['choose_file'] ?>">
                                <small class="text-muted mt-1 d-block"><?= $i18n['leave_blank_to_keep'] ?></small>
                            </div>

                            <h6 class="border-bottom pb-2 mb-3 text-muted text-uppercase small fw-bold">
                                <i class="bi bi-link-45deg me-1"></i><?= $i18n['social_links'] ?>
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold"><i class="bi bi-discord text-primary"></i> Discord</label>
                                    <input type="text" class="form-control" name="discord" value="<?= htmlspecialchars($profileUser['discord'] ?? '') ?>" placeholder="Ex: ZeJogador#1234">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold"><i class="bi bi-steam"></i> Steam</label>
                                    <input type="text" class="form-control" name="steam" value="<?= htmlspecialchars($profileUser['steam'] ?? '') ?>" placeholder="Ex: ZeJogador">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold"><i class="bi bi-instagram text-danger"></i> Instagram</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" name="instagram" value="<?= htmlspecialchars($profileUser['instagram'] ?? '') ?>" placeholder="Ex: ZeJogador">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mt-4">
                                <label class="form-label small fw-bold"><i class="bi bi-chat-quote me-1"></i><?= $i18n['biography'] ?></label>
                                <textarea class="form-control" name="bio" rows="4" placeholder="<?= $i18n['bio_placeholder'] ?>"><?= htmlspecialchars($profileUser['bio'] ?? '') ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success fw-bold rounded-pill px-4">
                                <i class="bi bi-check2 me-1"></i><?= $i18n['save_profile'] ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Password -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-shield-lock-fill me-2"></i><?= $i18n['change_password'] ?></h5>
                    </div>
                    <div class="card-body bg-body p-4">
                        <form method="POST" action="/profile">
                            <input type="hidden" name="form_type" value="password">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"><?= $i18n['new_password'] ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill text-danger"></i></span>
                                        <input type="password" class="form-control" name="new_password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"><?= $i18n['confirm_password'] ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-lock-fill text-danger"></i></span>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4 mt-4">
                                <i class="bi bi-arrow-repeat me-1"></i><?= $i18n['update_password'] ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
