<?php
    require_once 'include/_settings.inc.php';
    RegisterLog('404:' . $_SERVER['REQUEST_URI'], true);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página não encontrada - MyItinerary</title>
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

    <?php include SITE_DIR . '/include/itinerary-modal-css.inc.php'; ?>
</head>
<body>
    <!-- Menu -->
    <div class="menu menu-white">
        <?php 
            if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
                include SITE_DIR . '/include/navbar-login.inc.php'; 
            } else {
                include SITE_DIR . '/include/navbar-default.inc.php';
            } 
        ?>
    </div>

    <div class="not-found">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h1>404</h1>
                    <h2>Página não encontrada :(</h2>
                    <p style="margin-bottom: 26px">A página que procura pode ter sido movida, eliminada ou encontra-se indisponível.<br><br><br><span style="font-size: 1.2rem; font-weight: 500;"><i class="fas fa-compass"></i>Perdido? Hora de usar a bússola!</span><br>Siga para Norte, em direção à página inicial; ou vá para Sul, em busca de mais itinerários!</p>
                </div>
            </div>
            <div class="row buttons justify-content-center">
                <div class="col-auto text-center">
                    <a href="<?= SITE_URL ?>" class="btn btn-primary me-sm-3 mb-3 mb-sm-0">N - Regressar à Página Inicial</a>
                    <a href="<?= SITE_URL ?>/explore" class="btn btn-secondary mb-3 mb-sm-0">S - Explorar Itinerários</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>

    <?php include SITE_DIR . '/include/itinerary-modal.inc.php'; ?>
    
    <?php include SITE_DIR . '/include/toast.inc.php'; ?>
</body>
</html>
<?php
    unset($_SESSION['errors']);
?>