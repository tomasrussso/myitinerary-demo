<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-title', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    // Não está autenticado? Então vai embora
    if (!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])) {
        RedirectAndDie('BACK');
    }

    $id = $_SESSION['auth']['user']['id'];

    if (!isset($_POST['itinerary'], $_POST['title']) || !strpos($_POST['itinerary'], '/')) {
        RedirectAndDie('BACK');
    }

    $idItinerary = explode('/', $_POST['itinerary'])[0];

    $check = $db->query('SELECT COUNT(id) AS count FROM itinerary WHERE idUser = ? AND id = ? AND status = 1', $id, $idItinerary)->fetchAll()[0]['count'];
    
    if (!$check) {
        RedirectAndDie('BACK');
    }

    if (strlen($_POST['title']) >= 100 || strlen($_POST['title']) <= 0) {
        RedirectAndDie('itinerary/' . $_POST['itinerary'], [
            'modal' => 'changeTitle',
            'title-itinpage' => 'O título deve ser menor que 100 caracteres.'
        ]);
    }

    $slug = SanitizeString($_POST['title']);
    $update = $db->query('UPDATE itinerary SET title = ?, slug = ? WHERE id = ? AND status = 1', $_POST['title'], $slug, $idItinerary);

    if ($update->affectedRows() <= 0) {
        RedirectAndDie('itinerary/' . $_POST['itinerary'], [
            'modal' => 'changeTitle',
            'title-itinpage' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    RegisterLog('Change itinerary title-' . $idItinerary . '/' . $slug, true);

    $_SESSION['toast'] = 'Título do itinerário atualizado!';
    RedirectTo('itinerary/' . $idItinerary . '/' . $slug);
?>