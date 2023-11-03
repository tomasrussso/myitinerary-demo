<?php
    require '../../include/_settings.inc.php';

    // Não está autenticado? Então vai embora
    if (!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])) {
        exit;
    }

    $id = $_SESSION['auth']['user']['id'];

    if (!isset($_GET['i']) || !strpos(urldecode($_GET['i']), '/')) {
        exit;
    }

    $idItinerary = explode('/', urldecode($_GET['i']))[0];

    $check = $db->query('SELECT COUNT(id) AS count FROM itinerary WHERE idUser = ? AND id = ? AND status = 1', $id, $idItinerary)->fetchAll()[0]['count'];

    if ($check) {
        exit;
    }

    $isPrivate = $db->query('SELECT isPrivate FROM itinerary WHERE id = ? AND status = 1', $idItinerary)->fetchAll()[0]['isPrivate'];

    if ($isPrivate) {
        exit;
    }

    $hasLike = $db->query('SELECT COUNT(idItinerary) AS count FROM itinerary_like WHERE idUser = ? AND idItinerary = ?', $id, $idItinerary)->fetchAll()[0]['count'];

    if ($hasLike) {
        $db->query('DELETE FROM itinerary_like WHERE idUser = ? AND idItinerary = ?', $id, $idItinerary);
        RegisterLog('Like itinerary-' . $idItinerary, true);
        echo 'disliked';
    } else {
        $db->query('INSERT INTO itinerary_like (idUser, idItinerary) VALUES (?, ?)', $id, $idItinerary);
        RegisterLog('Dislike itinerary-' . $idItinerary, true);
        echo 'liked';
    }
?>