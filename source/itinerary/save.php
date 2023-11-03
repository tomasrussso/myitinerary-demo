<?php
    require_once '../../include/_settings.inc.php';

    if (!isset($_POST['json']) || empty($_POST['json']) || is_null($_POST['json'])) {
        echo '0';
        die;
    }

    if (!isset($_SESSION['auth']['itinerary-edit-id']) || empty($_SESSION['auth']['itinerary-edit-id']) || is_null($_SESSION['auth']['itinerary-edit-id'])) {
        echo '0';
        die;
    }

    $itineraryInfo = $db->query('SELECT id, duration FROM itinerary WHERE id = ?', $_SESSION['auth']['itinerary-edit-id'])->fetchAll()[0];
    
    $json = urldecode($_POST['json']);
    $json = strip_tags($json, '<br>');
    $arrayJSON = json_decode($json, true);

    $jsonImgsDelete = urldecode($_POST['imagesToDelete']);
    $arrayImgsDelete = json_decode($jsonImgsDelete, true);

    $jsonImgsMove = urldecode($_POST['imagesToMove']);
    $arrayImgsMove = json_decode($jsonImgsMove, true);

    foreach ($arrayImgsDelete as $image) {
        $image = str_replace('%SITE_URL%', SITE_DIR, $image);
        unlink($image);
    }

    foreach ($arrayImgsMove as $image) {
        $image = str_replace('%SITE_URL%', SITE_DIR, $image);
        $newLocation = str_replace('/temp/', '/public/', $image);
        rename($image, $newLocation);
    }

    $content = $arrayJSON['content'];

    $isEmpty = IsItineraryEmpty($content, $itineraryInfo['duration']);

    if ($isEmpty) {
        $update = $db->query('UPDATE itinerary SET contentHTML = NULL, contentJSON = NULL WHERE id = ?', $itineraryInfo['id']);
    } else {
        $html = JsonToHtmlView($content, $itineraryInfo['duration']);

        $update = $db->query('UPDATE itinerary SET contentHTML = ?, contentJSON = ? WHERE id = ?', $html, $json, $itineraryInfo['id']);
    }

    if ($update->affectedRows() <= 0) {
        echo '0';
        die;
    }

    echo '1';
?>