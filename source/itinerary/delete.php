<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('delete-itinerary', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    // Não está autenticado? Então vai embora
    if (!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])) {
        RedirectAndDie('BACK');
    }

    $id = $_SESSION['auth']['user']['id'];
    $username = $_SESSION['auth']['user']['username'];

    if (!isset($_POST['itinerary']) || !strpos($_POST['itinerary'], '/')) {
        RedirectAndDie('BACK');
    }

    $idItinerary = explode('/', $_POST['itinerary'])[0];

    $check = $db->query('SELECT COUNT(id) AS count FROM itinerary WHERE idUser = ? AND id = ? AND status = 1', $id, $idItinerary)->fetchAll()[0]['count'];
    
    if (!$check) {
        RedirectAndDie('BACK');
    }

    $lastPhoto = $db->query('SELECT wallpaperPath FROM itinerary WHERE id = ? AND status = 1', $idItinerary)->fetchAll()[0]['wallpaperPath'];
    $directoryArray = explode('/', $lastPhoto);
    
    if (!is_null($lastPhoto) && $directoryArray[1] != 'default') {
        unlink(SITE_DIR . '/' . $lastPhoto);
    }

    $delete = $db->query('DELETE FROM itinerary WHERE id = ? AND status = 1', $idItinerary);

    if ($delete->affectedRows() <= 0) {
        RedirectAndDie('itinerary/' . $_POST['itinerary']);
    }

    $delete = $db->query('DELETE FROM itinerary_city WHERE idItinerary = ?', $idItinerary);
    $delete = $db->query('DELETE FROM itinerary_like WHERE idItinerary = ?', $idItinerary);

    RegisterLog('Delete itinerary-' . $_POST['itinerary'], true);

    $_SESSION['toast'] = 'O itinerário foi apagado!';
    RedirectTo('profile/' . $username);
?>