<?php
    require 'include/_settings.inc.php';

    if (!isset($_GET['token'])) {
        include SITE_DIR . '/404.php';
        die;
    }

    $token = htmlspecialchars($_GET['token']);

    $user = $db->query('SELECT id FROM user WHERE verifyToken = ?', $token)->fetchAll();

    if (empty($user)) {
        include SITE_DIR . '/404.php';
        die;
    }

    $update = $db->query('UPDATE user SET verifiedAt = now(), status = 1 WHERE id = ? AND createdAt >= TIMESTAMPADD(DAY, -1, now()) AND status = 0', $user[0]['id']);

    if ($update->affectedRows() > 0) {
        // Sucesso
        include SITE_DIR . '/include/verify-account-success.inc.php';
    } else {
        // Erro
        include SITE_DIR . '/include/verify-account-error.inc.php';
    }
?>