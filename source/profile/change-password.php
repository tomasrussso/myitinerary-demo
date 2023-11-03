<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-password', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    $id = $_SESSION['auth']['user']['id'];
    $username = $_SESSION['auth']['user']['username'];

    // Validação se existem parametros POST
    if (!isset($_POST['pw-current'], $_POST['pw-new'], $_POST['pw-new-confirm'])) {
        RedirectAndDie('profile/' . $username);
    }

    $errors = array();
    $hasErrors = false;

    $errors['modal'] = 'changePassword';

    $currentPw = $db->query('SELECT password FROM user WHERE id = ? AND status = 1', $id)->fetchAll()[0]['password'];
    
    if(!password_verify($_POST['pw-current'], $currentPw)) {
        $hasErrors = true;
        $errors['pw-current'] = 'A password que inseriu está incorreta.';
    }

    if (strlen($_POST['pw-new']) <= 5) {
        $hasErrors = true;
        $errors['pw-new'] = 'A sua password deve ser maior que 5 caracteres.'; 
    }

    if (strlen($_POST['pw-new']) >= 50) {
        $hasErrors = true;
        $errors['pw-new'] = 'A sua password deve ser menor que 50 caracteres.'; 
    }

    if ($_POST['pw-new'] != $_POST['pw-new-confirm']) {
        $hasErrors = true;
        $errors['pw-new-confirm'] = 'As passwords que inseriu não coincidem.'; 
    }

    if ($hasErrors) {
        RedirectAndDie('profile/' . $username, $errors);
    }

    $passwordHashed = password_hash($_POST['pw-new'], PASSWORD_DEFAULT);

    $update = $db->query('UPDATE user SET password = ?, lastUpdateAt = now() WHERE id = ? AND status = 1', $passwordHashed, $id);

    if ($update->affectedRows() <= 0) {
        RedirectAndDie('profile/' . $username, [
            'modal' => 'changePassword',
            'pw-new-confirm' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    RegisterLog('Change password (profile)', true);

    unset($_SESSION['auth']);

    $_SESSION['toast'] = 'A sua palavra-passe foi alterada.<br>Por favor, inicie sessão novamente.';

    RedirectTo('login');
?>