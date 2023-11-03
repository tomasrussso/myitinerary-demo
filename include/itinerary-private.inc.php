<?php
    require_once 'include/_settings.inc.php';

    $url = substr($_SERVER['REQUEST_URI'], 1);
    $urlArray = explode('myitinerary.pt/', $url);
    array_shift($urlArray);
    $redirect = implode('/', $urlArray);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Itinerário não disponível - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">

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
                    <h1><i class="fas fa-lock"></i></h1>
                    <h2>O itinerário é privado :(</h2>
                    <p style="margin-bottom: 12px">Apenas o autor deste itinerário o consegue ver.<?php if (!isset($_SESSION['auth'])): ?><br><br><br>É o autor? Inicie sessão para ver e editar o itinerário.<?php endif; ?></p>
                </div>
            </div>
            <?php if (!isset($_SESSION['auth'])): ?>
            <div class="row buttons justify-content-center">
                <div class="col-auto text-center">
                    <a href="<?= SITE_URL ?>/login?redirect=<?= htmlspecialchars(urlencode($redirect)) ?>" class="btn btn-primary">Iniciar sessão</a>
                </div>
            </div>
            <?php endif; ?>
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