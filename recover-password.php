<?php
    require_once 'include/_settings.inc.php';

    if (!isset($_GET['token'])) {
        include SITE_DIR . '/404.php';
        die;
    }

    if (!isset($_SESSION['change-complete'])) {
        $token = htmlspecialchars($_GET['token']);

        $request = $db->query('SELECT * FROM password_reset WHERE token = ? AND createdAt >= TIMESTAMPADD(DAY, -1, now()) AND usedAt IS NULL', $token)->fetchAll();

        if (empty($request)) {
            include SITE_DIR . '/404.php';
            die;
        }

        $_SESSION['email-to-change'] = $request[0]['email'];
        $_SESSION['token-pw'] = $token;
    }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar password - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="theme-color" content="#40916c">
    <meta name="robots" content="noindex,nofollow">

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
            <?php 
            if (isset($_SESSION['change-complete'])): 
                include SITE_DIR . '/include/change-pw-complete.inc.php';
            else: ?>
                <div class="row title">
                    <h1 style="margin-bottom: 12px">Recuperar palavra-passe</h1>
                    <p>Escolha uma nova password para a sua conta.</p>
                </div>
                <div class="row form" style="margin-top: -12px">
                    <form action="<?= SITE_URL ?>/source/auth/change-password.php" method="post" onsubmit="DisableButton(document.getElementById('btn-submit'))">
                        <?php csrf('change-pw'); ?>

                        <label for="password">Palavra-passe</label> <br>
                        <input <?php if (isset($_SESSION['errors']['password'])): ?> class="input-error" <?php endif; ?> type="password" name="password" id="password" placeholder="Password" onclick="RemoveError(this)" required> <br>
                        <?php if (isset($_SESSION['errors']['password'])): ?>
                            <p class="error"><?= $_SESSION['errors']['password'] ?></p>
                        <?php endif; ?>

                        <label for="password_confirm">Confirme a palavra-passe</label> <br>
                        <input <?php if (isset($_SESSION['errors']['password_confirm'])): ?> class="input-error" <?php endif; ?> type="password" name="password_confirm" id="password_confirm" placeholder="Confirme a password" onclick="RemoveError(this)" required> <br>
                        <?php if (isset($_SESSION['errors']['password_confirm'])): ?>
                            <p class="error"><?= $_SESSION['errors']['password_confirm'] ?></p>
                        <?php endif; ?>

                        <button id="btn-submit" type="submit" style="margin-top: 52px" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('password'), document.getElementById('password_confirm'))">Alterar palavra-passe</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>

    <?php include SITE_DIR . '/include/toast.inc.php'; ?>
</body>
</html>
<?php
    unset($_SESSION['errors']);
    unset($_SESSION['change-complete']);
?>