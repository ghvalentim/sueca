<?php
$user = $user ?? ['username' => '', 'email' => '', 'created_at' => null];
$error = $error ?? null;
$success = $success ?? null;
$createdAt = !empty($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : 'Indisponível';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O Meu Perfil - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="card-title text-success m-0">O Meu Perfil</h3>
                            <a href="?action=home" class="btn btn-outline-secondary btn-sm">Voltar ao Lobby</a>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Username</label>
                            <p class="fw-bold fs-5"><?= htmlspecialchars((string)($user['username'] ?? '')) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Email</label>
                            <p class="fw-bold"><?= htmlspecialchars((string)($user['email'] ?? '')) ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Membro desde</label>
                            <p><?= htmlspecialchars($createdAt) ?></p>
                        </div>

                        <hr>

                        <h5 class="mb-3">Alterar Password</h5>
                        <form method="POST" action="?action=profile">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nova Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nova Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Atualizar Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>