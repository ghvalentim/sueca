<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jogosueca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center text-success mb-4">Iniciar Sessão</h3>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="?action=login">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success">Entrar</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="?action=register" class="text-decoration-none text-muted">Ainda não tem conta? Registe-se</a><br>
                            <a href="?action=home" class="text-decoration-none text-muted mt-2 d-inline-block">Voltar ao Lobby</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>