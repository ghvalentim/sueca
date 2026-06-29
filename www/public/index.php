<?php

declare(strict_types=1);

$phpVersion = PHP_VERSION;

?>
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jogosueca</title>
</head>
<body>
    <main>
        <h1>Jogosueca</h1>
        <p>Portal PHP ativo.</p>
        <p>PHP <?= htmlspecialchars($phpVersion, ENT_QUOTES, 'UTF-8') ?></p>
    </main>
</body>
</html>
