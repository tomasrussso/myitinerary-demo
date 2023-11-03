<?php
    require_once 'include/_settings.inc.php';

    // Está autenticado? Então vai embora
    if (isset($_SESSION['auth'])  || isset($_SESSION['auth-bo'])) {
        RedirectAndDie('home');
    }

    RegisterLog('Visit:Signin');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registo - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="description" content="Crie o seu itinerário no MyItinerary e partilhe a sua viagem com outros Exploradores">
    <meta name="theme-color" content="#40916c">

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
            if (isset($_SESSION['signin-complete'])): 
                include SITE_DIR . '/include/signin-complete.inc.php';
            else: ?>
                <div class="row title">
                    <h1>Bem-vindo a bordo!</h1>
                    <h2>Pronto para explorar Portugal?</h2>
                    <p>Já tem conta? <a href="<?= SITE_URL ?>/login" class="link">Inicie sessão aqui.</a></p>
                </div>
                <div class="row form">
                    <form action="<?= SITE_URL ?>/source/auth/signin.php" method="post" id="signin" onsubmit="DisableButton(document.getElementById('btn-submit'))">
                        <?php csrf('signin'); ?>

                        <label for="name" class="first">Nome</label> <br>
                        <input <?php if (isset($_SESSION['errors']['name'])): ?> class="input-error" <?php endif; ?> type="text" name="name" id="name" placeholder="O seu primeiro e último nome" <?php if (isset($_SESSION['name'])): ?> value="<?= $_SESSION['name'] ?>" <?php endif; ?> onclick="RemoveError(this)" required> <br>
                        <?php if (isset($_SESSION['errors']['name'])): ?>
                            <p class="error"><?= $_SESSION['errors']['name'] ?></p>
                        <?php endif; ?>

                        <label for="username">Nome de utilizador</label> <br>
                        <input <?php if (isset($_SESSION['errors']['username'])): ?> class="input-error" <?php endif; ?> type="text" name="username" id="username" placeholder="Escolha o seu username" <?php if (isset($_SESSION['username'])): ?> value="<?= $_SESSION['username'] ?>" <?php endif; ?> onclick="RemoveError(this)" required> <br>
                        <?php if (isset($_SESSION['errors']['username'])): ?>
                            <p class="error"><?= $_SESSION['errors']['username'] ?></p>
                        <?php endif; ?>

                        <label for="email">Email</label> <br>
                        <input <?php if (isset($_SESSION['errors']['email'])): ?> class="input-error" <?php endif; ?> type="text" name="email" id="email" placeholder="Indique o seu email" <?php if (isset($_SESSION['email'])): ?> value="<?= $_SESSION['email'] ?>" <?php endif; ?> onclick="RemoveError(this)" required> <br>
                        <?php if (isset($_SESSION['errors']['email'])): ?>
                            <p class="error"><?= $_SESSION['errors']['email'] ?></p>
                        <?php endif; ?>

                        <div class="row">
                        <div class="col-12 col-md-auto">
                        <label for="password">Palavra-passe</label> <br>
                        <input <?php if (isset($_SESSION['errors']['password'])): ?> class="input-error" <?php endif; ?> type="password" name="password" id="password" placeholder="Escolha uma password forte" onclick="RemoveError(this)" required> <br>
                        <?php if (isset($_SESSION['errors']['password'])): ?>
                            <p class="error"><?= $_SESSION['errors']['password'] ?></p>
                        <?php endif; ?>
                        </div>

                        <div class="col-12 col-md-auto">
                        <label for="password_confirm">Confirme a palavra-passe</label> <br>
                        <input <?php if (isset($_SESSION['errors']['password_confirm'])): ?> class="input-error" <?php endif; ?> type="password" name="password_confirm" id="password_confirm" placeholder="Confirme a sua password" onclick="RemoveError(this)" required> <br>
                        <?php if (isset($_SESSION['errors']['password_confirm'])): ?>
                            <p class="error"><?= $_SESSION['errors']['password_confirm'] ?></p>
                        <?php endif; ?>
                        </div>
                        </div>

                        <p class="info">Ao prosseguir, declara que concorda com os nossos <a href="<?= SITE_URL ?>/terms" class="link">Termos e Condições</a> e <a href="<?= SITE_URL ?>/policies" class="link">Política de Privacidade e Cookies</a>.</p>
                        <button id="btn-submit" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('name'), document.getElementById('email'), document.getElementById('username'), document.getElementById('password'), document.getElementById('password_confirm'))">Criar a minha conta</button>
                    </form>
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

    <?php include SITE_DIR . '/include/toast.inc.php'; ?>
</body>
</html>
<?php
    unset($_SESSION['errors']);
    unset($_SESSION['email']); 
    unset($_SESSION['name']);
    unset($_SESSION['username']);
    unset($_SESSION['signin-complete']);
?>