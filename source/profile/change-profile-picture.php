<?php
    require '../../include/_settings.inc.php';

    // Verificação do CSRF
    if (!IsCsrfTokenValid('change-profile-picture', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    $id = $_SESSION['auth']['user']['id'];
    $username = $_SESSION['auth']['user']['username'];

    $okFileType = array('image/jpeg', 'image/jpg', 'image/gif', 'image/png');
    $fileName = UploadFile($_FILES['picture'], $okFileType, $id . '-profile');

    if (!$fileName) {
        RedirectAndDie('profile/' . $username, [
            'modal' => 'changeProfilePicture',
            'picture' => 'Não foi possível carregar a sua foto. Tente usar uma foto mais leve ou de outro formato. Visite o Centro de Ajuda para mais informações.'
        ]);
    }

    $lastPhoto = $db->query('SELECT profilePicturePath FROM user WHERE id = ? AND status = 1', $id)->fetchAll()[0]['profilePicturePath'];
    
    if (!is_null($lastPhoto)) {
        unlink(SITE_DIR . '/' . $lastPhoto);
    }

    $update = $db->query('UPDATE user SET profilePicturePath = ?, lastUpdateAt = NOW() WHERE id = ? AND status = 1', $fileName, $id);

    if ($update->affectedRows() <= 0) {
        unlink(SITE_DIR . '/' . $fileName);

        RedirectAndDie('profile/' . $username, [
            'modal' => 'changeProfilePicture',
            'picture' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    } 

    RegisterLog('Change profile picture', true);

    $_SESSION['auth']['user']['profilePicturePath'] = $fileName;

    $_SESSION['toast'] = 'Foto de perfil atualizada!';
    RedirectTo('profile/' . $username);
?>