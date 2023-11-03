<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('delete-account', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    $id = $_SESSION['auth']['user']['id'];
    $username = $_SESSION['auth']['user']['username'];

    $delete = $db->query('DELETE FROM user WHERE id = ?', $id);

    if ($delete->affectedRows() <= 0) {
        RedirectAndDie('profile/' . $username);
    }

    $mask = SITE_DIR . '/files/public/' . $id . '-*.*';
    array_map('unlink', glob($mask));

    $delete = $db->query('DELETE FROM itinerary_city WHERE idItinerary IN (SELECT id FROM itinerary WHERE idUser = ?)', $id);
    $delete = $db->query('DELETE FROM itinerary_like WHERE idItinerary IN (SELECT id FROM itinerary WHERE idUser = ?)', $id);
    $delete = $db->query('DELETE FROM itinerary_like WHERE idUser = ?', $id);
    $delete = $db->query('DELETE FROM itinerary WHERE idUser = ?', $id);

    RegisterLog('Delete account', true);

    unset($_SESSION['auth']);

    $_SESSION['toast'] = 'A sua conta foi apagada.<br>Obrigado por utilizar o MyItinerary :)';
    RedirectTo('login');
?>