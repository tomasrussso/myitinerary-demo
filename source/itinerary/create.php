<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('create-itinerary', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    }

    // Não está autenticado? Então vai embora
    if (!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])) {
        RedirectAndDie('BACK');
    }

    $redirect = (isset($_POST['redirect']) ? htmlspecialchars($_POST['redirect']) : 'BACK');
 
    // Validação se existem parametros POST
    if (!isset($_POST['title'], $_POST['duration'])) {
        RedirectAndDie($redirect);
    }

    // Validação

    $id = $_SESSION['auth']['user']['id'];

    $errors = array();
    $hasErrors = false;

    $errors['modal'] = 'createItinerary';

    if (strlen($_POST['title']) <= 0 || strlen($_POST['title']) > 100) {
        $hasErrors = true;
        $errors['title'] = 'O título deve ser menor que 100 caracteres.'; 
    }

    if (strlen($_POST['duration']) <= 0 || !is_numeric($_POST['duration']) || $_POST['duration'] > 30) {
        $hasErrors = true;
        $errors['duration'] = 'A duração deve ser entre 1 e 30 dias.'; 
    }

    if (!isset($_POST['locations'])) {
        $hasErrors = true;
        $errors['locations'] = 'Selecione, pelo menos, 1 cidade.'; 
    }

    if (count($_POST['locations']) > 8) {
        $hasErrors = true;
        $errors['locations'] = 'Deve selecionar, no máximo, 8 cidades.'; 
    }
    
    if ($hasErrors) {
        $_SESSION['title'] = htmlspecialchars($_POST['title']);
        $_SESSION['duration'] = htmlspecialchars($_POST['duration']);
        $_SESSION['locations'] = $_POST['locations'];
        RedirectAndDie($redirect, $errors);
    }

    $slug = SanitizeString($_POST['title']);

    $insert = $db->query('INSERT INTO itinerary (idUser, title, slug, duration, status) VALUES (?, ?, ?, ?, ?)', $id, $_POST['title'], $slug, $_POST['duration'], 1);
    
    if ($insert->affectedRows() <= 0) {
        $_SESSION['title'] = htmlspecialchars($_POST['title']);
        $_SESSION['duration'] = htmlspecialchars($_POST['duration']);
        $_SESSION['locations'] = $_POST['locations'];
        RedirectAndDie($redirect, [
            'modal' => 'createItinerary',
            'locations' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    $idItinerary = $insert->lastInsertID();

    foreach ($_POST['locations'] as $city) {
        $insert2 = $db->query('INSERT INTO itinerary_city (idItinerary, idCity) VALUES (?, ?)', $idItinerary, $city);
    }

    $firstCity = $db->query('SELECT c.district FROM city AS c INNER JOIN itinerary_city AS ic ON ic.idCity = c.id WHERE ic.idItinerary = ? ORDER BY c.name ASC LIMIT 0,1', $idItinerary)->fetchArray()['district'];

    $wallpaper = 'files/default/' . SanitizeString($firstCity) . '.jpg';

    $insert3 = $db->query('UPDATE itinerary SET wallpaperPath = ? WHERE id = ?', $wallpaper, $idItinerary);

    RegisterLog('Create itinerary-' . $idItinerary, true);

    RedirectTo('itinerary/' . $idItinerary . '/' . $slug);
?>