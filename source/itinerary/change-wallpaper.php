<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-wallpaper', $_POST['_token'])) {
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

    $okFileType = array('image/jpeg', 'image/jpg', 'image/png');
    $fileName = UploadFile($_FILES['wallpaper'], $okFileType, $id . '-itin_' . $idItinerary . '-wallpaper', 600000000);

    if (!$fileName) {
        RedirectAndDie('itinerary/' . $_POST['itinerary'], [
            'modal' => 'changeWallpaper',
            'wallpaper' => 'Não foi possível carregar a sua foto. Tente usar uma foto mais leve ou de outro formato. Visite o Centro de Ajuda para mais informações.'
        ]);
    }

    $lastPhoto = $db->query('SELECT wallpaperPath FROM itinerary WHERE id = ? AND status = 1', $idItinerary)->fetchAll()[0]['wallpaperPath'];
    $directoryArray = explode('/', $lastPhoto);
    
    if (!is_null($lastPhoto) && $directoryArray[1] != 'default') {
        unlink(SITE_DIR . '/' . $lastPhoto);
    }

    $update = $db->query('UPDATE itinerary SET wallpaperPath = ? WHERE id = ? AND status = 1', $fileName, $idItinerary);

    if ($update->affectedRows() <= 0) {
        unlink(SITE_DIR . '/' . $fileName);

        RedirectAndDie('itinerary/' . $_POST['itinerary'], [
            'modal' => 'changeWallpaper',
            'wallpaper' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    } 

    RegisterLog('Change itinerary wallpaper-' . $_POST['itinerary'], true);

    $_SESSION['toast'] = 'Foto de capa atualizada!';
    RedirectTo('itinerary/' . $_POST['itinerary']);
?>