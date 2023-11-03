<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('logout', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    RegisterLog('Logout');

    unset($_SESSION['auth']);
    unset($_SESSION['auth-bo']);

    RedirectTo('home');
?>