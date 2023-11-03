<?php RegisterLog('Verify account', true); ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar conta - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="theme-color" content="#40916c">
    <meta name="robots" content="noindex,follow">

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/bootstrap/bootstrap.min.css">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/fontawesome/css/all.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/main.css">
</head>
<body>
    <!-- Menu -->
    <div class="menu menu-white">
        <?php include SITE_DIR . '/include/navbar-only-help.inc.php'; ?>
    </div>

    <!-- Caixa de Signin -->
    <div class="signin">
        <div class="container">
            <div class="row justify-content-center title">
                <div class="col-auto text-center">
                    <h1>Tudo a postos!</h1>
                    <p style="margin-top: 12px; font-size: 1.1rem">Só lhe resta iniciar sessão. Boa viagem!</p>
                    <a href="<?= SITE_URL ?>/login" class="btn btn-primary" style="margin-top:26px">Iniciar sessão</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>