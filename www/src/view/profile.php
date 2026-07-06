<?php /** @var array $user */ ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O Meu Perfil - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success m-0">O Meu Perfil</h2>
            <a href="/" class="btn btn-outline-secondary">Voltar ao Lobby</a>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Coluna da Esquerda: Resumo Visual -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm text-center p-4 h-100">
                    <?php 
                        // Usa o avatar definido ou um avatar automático baseado no nome (UI Avatars)
                        $avatarUrl = !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . '&background=198754&color=fff&size=150';
                    ?>
                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="rounded-circle mx-auto mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #198754;">
                    
                    <h4 class="fw-bold mb-0"><?= htmlspecialchars($user['username']) ?></h4>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <?php if (!empty($user['external_username'])): ?>
                        <span class="badge bg-secondary mb-3">Discord/Steam: <?= htmlspecialchars($user['external_username']) ?></span>
                    <?php endif; ?>

                    <p class="text-start small fst-italic bg-light p-3 rounded border">
                        <?= !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : 'Nenhuma biografia definida. Apresente-se aos outros jogadores!' ?>
                    </p>

                    <p class="text-muted small mt-auto pt-3 border-top">Membro desde: <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>

            <!-- Coluna da Direita: Formulários de Edição -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 pt-2 pb-2 text-success">Atualizar Informações</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="/profile">
                            <input type="hidden" name="form_type" value="details">
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Link do Avatar (URL da imagem)</label>
                                <input type="url" class="form-control" name="avatar" value="<?= htmlspecialchars($user['avatar'] ?? '') ?>" placeholder="https://exemplo.com/minhafoto.jpg">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Username Externo (Discord, Steam, etc.)</label>
                                <input type="text" class="form-control" name="external_username" value="<?= htmlspecialchars($user['external_username'] ?? '') ?>" placeholder="Ex: ZeJogador#1234">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small">Biografia</label>
                                <textarea class="form-control" name="bio" rows="3" placeholder="Escreva algo sobre si..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Guardar Detalhes</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 pt-2 pb-2 text-danger">Alterar Password</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="/profile">
                            <input type="hidden" name="form_type" value="password">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small">Nova Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small">Confirmar Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger">Atualizar Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



