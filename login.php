<?php
    require_once 'include/_settings.inc.php';

    // Está autenticado? Então vai embora
    if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
        RedirectAndDie('home');
    }

    RegisterLog('Visit:Login');

    if (!MAINTENANCE):
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MyItinerary</title>
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

    <!-- Caixa de Login -->
    <div class="login">
        <div class="container">
            <?php 
            if (isset($_SESSION['recover-pw'])): 
                include SITE_DIR . '/include/recover-pw.inc.php';
            else: ?>
                <div class="row text-center title">
                    <h1>Bem-vindo de volta!</h1>
                    <p>Ainda não tem conta? <a href="<?= SITE_URL ?>/signin" class="link">Crie uma agora!</a></p>
                </div>

                <?php if (isset($_SESSION['errors']) && !isset($_SESSION['errors']['modal'])): ?>
                    <div class="row justify-content-center error-alert"> 
                        <div class="col-auto">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $_SESSION['errors'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row justify-content-center input">
                    <div class="col-auto">
                        <form action="<?= SITE_URL ?>/source/auth/login.php" method="post" id="login" onsubmit="DisableButton(document.getElementById('btn-submit'))">
                            <?php //csrf('login'); ?>
                            <?php if (isset($_GET['redirect'])): ?><input type="hidden" name="redirect" value="<?= htmlspecialchars(urldecode($_GET['redirect'])); ?>"><?php endif; ?>

                            <label for="email">Email ou Nome de utilizador</label> <br>
                            <input type="text" id="email" name="email" placeholder="Email ou username" <?php if (isset($_SESSION['email'])): ?> value="<?= htmlspecialchars($_SESSION['email']) ?>" <?php endif; ?> required> <br>

                            <label for="password">Palavra-passe</label> <br>
                            <input type="password" id="password" name="password" placeholder="Password"> <br>

                            <div class="text-center">                           
                                <button id="btn-submit" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('email'))">Iniciar sessão</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <a href="#passwordRecover" data-bs-toggle="modal" data-bs-target="#passwordRecover" class="link">Esqueceu-se da palavra-passe?</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="passwordRecover" tabindex="-1" aria-labelledby="passwordRecover" aria-hidden="true" onsubmit="DisableButton(document.getElementById('btn-submit-rec'))">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Esqueceu-se da palavra-passe?</h1>
                        <p>Não há problema! Introduza o email associado à sua conta.<br>Irá receber um link para redefinir a sua password.</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto col-lg-5">
                            <form action="<?= SITE_URL ?>/source/auth/send-email-to-recover-password.php" class="form" method="post">
                                <?php csrf('recover_pw'); ?>
                                <label for="email">Email</label> <br>
                                <input <?php if (isset($_SESSION['errors']['email'])): ?>class="input-error"<?php endif; ?> type="text" id="email-rec" name="email" placeholder="Email" onclick="RemoveError(this)" <?php if ((isset($_SESSION['email']) && filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)) || isset($_SESSION['errors']['modal'])): ?> value="<?= htmlspecialchars($_SESSION['email']) ?>"<?php endif; ?> required>
                                <?php if (isset($_SESSION['errors']['email'])): ?>
                                    <p class="error mb-4" style="margin-top: -8px"><?= $_SESSION['errors']['email'] ?></p>
                                <?php endif; ?>
                                <div class="text-center btns">
                                    <button id="btn-submit-rec" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('email-rec'))">Enviar link</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>

    <?php include SITE_DIR . '/include/toast.inc.php'; ?>

    <?php if (isset($_SESSION['errors']['modal'])): ?>
        <script>
            $(document).ready(function(){
                $("#<?= $_SESSION['errors']['modal'] ?>").modal('show');
            });
        </script>
    <?php endif; ?>
</body>
</html>
<?php
    unset($_SESSION['errors']);
    unset($_SESSION['email']);
    unset($_SESSION['recover-pw']);

    else:
?>



<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site em manutenção</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/bootstrap/bootstrap.min.css">

    <style>
        body {
            font-family: 'Inter';
            max-width: 1000px;
            margin: auto;
            text-align: center;
        }
        h5 {
            margin-bottom: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row text-center mt-5">
            <h1 class="mb-3">Site em manutenção :(</h1>
            <h5>Pedimos desculpa pelo incómodo. Prometemos ser breves.</h5>
        </div>
    </div>
    
    <p>Login apenas para pessoas autorizadas:</p>
    <form action="<?= SITE_URL ?>/source/auth/login.php" method="post">
        <?php //csrf('login'); ?>

        <label for="email">Email</label> <br>
        <input type="text" id="email" name="email" placeholder="Email" <?php if (isset($_SESSION['email'])): ?> value="<?= htmlspecialchars($_SESSION['email']) ?>" <?php endif; ?> required> <br>

        <label for="password">Palavra-passe</label> <br>
        <input type="password" id="password" name="password" placeholder="Password"> <br>

        <div class="text-center">                           
            <input type="submit" value="Iniciar sessão" class="btn btn-primary">
        </div>
    </form>
</body>
</html>
<?php
    unset($_SESSION['errors']);
    unset($_SESSION['email']);
    unset($_SESSION['recover-pw']);

    endif;
?>