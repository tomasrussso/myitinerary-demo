<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-visibility', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    // Não está autenticado? Então vai embora
    if (!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])) {
        RedirectAndDie('BACK');
    }

    $id = $_SESSION['auth']['user']['id'];

    if (!isset($_POST['itinerary']) || !strpos($_POST['itinerary'], '/')) {
        RedirectAndDie('BACK');
    }

    $idItinerary = explode('/', $_POST['itinerary'])[0];

    $check = $db->query('SELECT COUNT(id) AS count FROM itinerary WHERE idUser = ? AND id = ? AND status = 1', $id, $idItinerary)->fetchAll()[0]['count'];
    
    if (!$check) {
        RedirectAndDie('BACK');
    }

    $isPrivate = $db->query('SELECT isPrivate FROM itinerary WHERE id = ? AND status = 1', $idItinerary)->fetchAll()[0]['isPrivate'];

    if ($isPrivate) {
        $db->query('UPDATE itinerary SET isPrivate = 0 WHERE id = ? AND status = 1', $idItinerary);
        $_SESSION['toast'] = 'Itinerário definido como público.';
    } else {
        $db->query('UPDATE itinerary SET isPrivate = 1 WHERE id = ? AND status = 1', $idItinerary);
        $_SESSION['toast'] = 'Itinerário definido como privado.';
    }

    RegisterLog('Change itinerary visibility-' . $_POST['itinerary'], true);
    RedirectTo('itinerary/' . $_POST['itinerary']);
?>