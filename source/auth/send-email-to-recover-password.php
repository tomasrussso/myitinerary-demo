<?php
    require '../../include/_settings.inc.php';
    include SITE_DIR . '/include/_phpmailer.inc.php';

    // Está autenticado? Então vai embora
    if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
        RedirectAndDie('login');
    }

    // Verificação do CSRF
    if (!IsCsrfTokenValid('recover_pw', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    }

    // Validação se existem parametros POST
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        RedirectAndDie('login', [
            'modal' => 'passwordRecover',
            'email' => 'O email que inseriu não é válido.'
        ]);
    }

    $_SESSION['email'] = htmlspecialchars($_POST['email']);

    // Sanitização dos valores
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        RedirectAndDie('login', [
            'modal' => 'passwordRecover',
            'email' => 'O email que inseriu não é válido.'
        ]);
    }  

    $email = htmlspecialchars($_POST['email']);

    $user = $db->query('SELECT id, name FROM user WHERE email = ? AND status = 1', $email)->fetchAll();

    if (empty($user)) {
        RedirectAndDie('login', [
            'modal' => 'passwordRecover',
            'email' => 'O email que inseriu não é válido.'
        ]);
    }

    $token = GenerateToken(50);

    $insert = $db->query('INSERT INTO password_reset (email, token) VALUES (?, ?)', $email, $token);

    if ($insert->affectedRows() <= 0) {
        RedirectAndDie('login', [
            'modal' => 'passwordRecover',
            'email' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    $message = file_get_contents(SITE_DIR . '/files/templates/recover-password-template.html'); 
    $message = str_replace('%name%', $user[0]['name'], $message); 
    $message = str_replace('%link%', SITE_URL . '/recover-password?token=' . $token, $message); 

    $mail = SendEmail($user[0]['name'], $email, 'Recupere a sua palavra-passe', $message);

    $_SESSION['recover-pw'] = true;

    RegisterLog('Email to recover password:' . $email, true);
    RedirectTo('login');
?>