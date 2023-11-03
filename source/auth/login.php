<?php
    require '../../include/_settings.inc.php';

    // // Verificação do CSRF
    // if (!IsCsrfTokenValid('login', $_POST['_token'])) {
    //     RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
    //     RedirectAndDie('BACK');
    // } 

    // Está autenticado? Então vai embora
    if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
        RedirectAndDie('login');
    }

    // Validação se existem parametros POST
    if (!isset($_POST['email'], $_POST['password']) || empty($_POST['email'])) {
        RedirectAndDie((isset($_POST['redirect']) ? 'login?redirect=' . urlencode($_POST['redirect']) : 'login'), 'O email/username ou a palavra-passe que inseriu estão incorretos.');
    }

    $adminUser = $db->query('SELECT * FROM user WHERE (email = ? OR username = ?) AND status = 2', htmlspecialchars($_POST['email']), htmlspecialchars($_POST['email']))->fetchAll();
    
    if(!empty($adminUser)) {
        if (!password_verify($_POST['password'], $adminUser[0]['password'])) {
            RegisterLog('Attempt login with admin account:' . $_POST['email'], true);
            RedirectAndDie((isset($_POST['redirect']) ? 'login?redirect=' . urlencode($_POST['redirect']) : 'login'), 'O email/username ou a palavra-passe que inseriu estão incorretos.');
        }

        $db->query('UPDATE user SET lastLoginAt = now() WHERE id = ?', $adminUser[0]['id']);

        $_SESSION['auth-bo']['user'] = $adminUser[0];

        RegisterLog('Login to backoffice');
        RedirectAndDie('gest');
    }

    if (MAINTENANCE) {
        RedirectAndDie('login');
    }

    $email = htmlspecialchars($_POST['email']);

    $user = $db->query('SELECT * FROM user WHERE (email = ? OR username = ?) AND status = 1', $email, $email)->fetchAll();
    
    if (empty($user)) {
        $_SESSION['email'] = $email;
        RedirectAndDie((isset($_POST['redirect']) ? 'login?redirect=' . urlencode($_POST['redirect']) : 'login'), 'O email/username ou a palavra-passe que inseriu estão incorretos.');
    }

    if (!password_verify($_POST['password'], $user[0]['password'])) {
        $_SESSION['email'] = $email;
        RedirectAndDie((isset($_POST['redirect']) ? 'login?redirect=' . urlencode($_POST['redirect']) : 'login'), 'O email/username ou a palavra-passe que inseriu estão incorretos.');
    }

    // Regista o login

    $update = $db->query('UPDATE user SET lastLoginAt = now() WHERE id = ?', $user[0]['id']);

    if ($update->affectedRows() <= 0) {
        $_SESSION['email'] = $email;
        RedirectAndDie((isset($_POST['redirect']) ? 'login?redirect=' . urlencode($_POST['redirect']) : 'login'), 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.');
    }

    SetDefaultPictures($user[0]);

    $_SESSION['auth']['user'] = $user[0];

    RegisterLog('Login');

    if (isset($_POST['redirect'])) {
        RedirectTo($_POST['redirect']);
    } else {
        RedirectTo('home');
    }
?>