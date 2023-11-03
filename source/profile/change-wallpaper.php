<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-wallpaper', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    $id = $_SESSION['auth']['user']['id'];
    $username = $_SESSION['auth']['user']['username'];

    $okFileType = array('image/jpeg', 'image/jpg', 'image/png');
    $fileName = UploadFile($_FILES['wallpaper'], $okFileType, $id . '-wallpaper', 600000000);

    if (!$fileName) {
        RedirectAndDie('profile/' . $username, [
            'modal' => 'changeWallpaper',
            'wallpaper' => 'Não foi possível carregar a sua foto. Tente usar uma foto mais leve ou de outro formato. Visite o Centro de Ajuda para mais informações.'
        ]);
    }

    $lastPhoto = $db->query('SELECT wallpaperPath FROM user WHERE id = ? AND status = 1', $id)->fetchAll()[0]['wallpaperPath'];
    
    if (!is_null($lastPhoto)) {
        unlink(SITE_DIR . '/' . $lastPhoto);
    }

    $update = $db->query('UPDATE user SET wallpaperPath = ?, lastUpdateAt = NOW() WHERE id = ? AND status = 1', $fileName, $id);

    if ($update->affectedRows() <= 0) {
        unlink(SITE_DIR . '/' . $fileName);

        RedirectAndDie('profile/' . $username, [
            'modal' => 'changeWallpaper',
            'wallpaper' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    } 

    RegisterLog('Change wallpaper', true);

    $_SESSION['auth']['user']['wallpaperPath'] = $fileName;

    $_SESSION['toast'] = 'Foto de capa atualizada!';
    RedirectTo('profile/' . $username);
?>