<?php
    require_once '../../include/_settings.inc.php';

    $okFileType = array('image/jpeg', 'image/jpg', 'image/png');
    $fileName = UploadFile($_FILES['file'], $okFileType, $_POST['id_user'] . '-itin_' . $_POST['id_itinerary'] . '-image', 600000000, '/files/temp');

    echo (!$fileName ? 0 : $fileName);
?>