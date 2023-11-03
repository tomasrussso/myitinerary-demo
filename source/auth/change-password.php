<?php
    require '../../include/_settings.inc.php';

    // Está autenticado? Então vai embora
    if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
        RedirectAndDie('login');
    }

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-pw', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    }

    $userEmail = $_SESSION['email-to-change'];
    $tokenPw = $_SESSION['token-pw'];

    unset($_SESSION['email-to-change']);
    unset($_SESSION['token-pw']);

    // Validação se existem parametros POST
    if (!isset($_POST['password'], $_POST['password_confirm'])) {
        RedirectAndDie('recover-password?token=' . $tokenPw);
    }

    $errors = array();
    $hasErrors = false;

    if (strlen($_POST['password']) <= 5) {
        $hasErrors = true;
        $errors['password'] = 'A sua password deve ser maior que 5 caracteres.'; 
    }

    if (strlen($_POST['password']) >= 50) {
        $hasErrors = true;
        $errors['password'] = 'A sua password deve ser menor que 50 caracteres.'; 
    }

    if ($_POST['password'] != $_POST['password_confirm']) {
        $hasErrors = true;
        $errors['password_confirm'] = 'As passwords que inseriu não coincidem.'; 
    }

    if ($hasErrors) {
        RedirectAndDie('recover-password?token=' . $tokenPw, $errors);
    }

    $passwordHashed = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $update = $db->query('UPDATE user SET password = ?, lastUpdateAt = now() WHERE email = ?', $passwordHashed, $userEmail);

    if ($update->affectedRows() <= 0) {
        RedirectAndDie('recover-password?token=' . $tokenPw, [
            'password_confirm' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    $update2 = $db->query('UPDATE password_reset SET usedAt = now() WHERE token = ?', $tokenPw);

    if ($update2->affectedRows() <= 0) {
        RedirectAndDie('recover-password?token=' . $tokenPw, [
            'password_confirm' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    $_SESSION['change-complete'] = true;

    RegisterLog('Change password (recovered):' . $userEmail, true);
    RedirectTo('recover-password?token=' . $tokenPw);
?>