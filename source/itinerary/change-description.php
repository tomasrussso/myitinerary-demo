<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-description', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    // Não está autenticado? Então vai embora
    if (!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])) {
        RedirectAndDie('BACK');
    }

    $id = $_SESSION['auth']['user']['id'];

    if (!isset($_POST['itinerary'], $_POST['description']) || !strpos($_POST['itinerary'], '/')) {
        RedirectAndDie('BACK');
    }

    $description = trim($_POST['description']);

    $idItinerary = explode('/', $_POST['itinerary'])[0];

    $check = $db->query('SELECT COUNT(id) AS count FROM itinerary WHERE idUser = ? AND id = ? AND status = 1', $id, $idItinerary)->fetchAll()[0]['count'];
    
    if (!$check) {
        RedirectAndDie('BACK');
    }

    if (strlen($description) >= 512) {
        RedirectAndDie('itinerary/' . $_POST['itinerary'], [
            'modal' => 'changeDescription',
            'description' => 'A descrição deve ser menor que 512 caracteres.'
        ]);
    }

    if (strlen($description) == 0) {
        $update = $db->query('UPDATE itinerary SET description = NULL WHERE id = ? AND status = 1', $idItinerary);
    } else {
        $update = $db->query('UPDATE itinerary SET description = ? WHERE id = ? AND status = 1', $description, $idItinerary);
    }

    if ($update->affectedRows() <= 0) {
        RedirectAndDie('itinerary/' . $_POST['itinerary'], [
            'modal' => 'changeDescription',
            'description' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    RegisterLog('Change itinerary description-' . $_POST['itinerary'], true);

    $_SESSION['toast'] = 'Descrição do itinerário atualizada!';
    RedirectTo('itinerary/' . $_POST['itinerary']);
?>