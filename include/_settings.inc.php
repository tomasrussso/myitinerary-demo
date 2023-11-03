<?php
// ---
// 
// O settings.inc.php faz o seguinte:
//      - inicia uma session
//      - define as constantes do projeto (diretório do site, url do site, ...)
//      - inclui os ficheiros myfunctions.inc.php e db.inc.php
//      - inicia uma conexão à DB ($db)
//      - verifica se o site está em manutenção
//      - verifica se o user autenticado ainda está válido na DB
// 
// Este ficheiro deve ser requerido no ínicio de todos os outros (excluíndo os .inc).
// 
// ---

session_name('myitin_session_id');
@session_start();

// ---

$wwwDirectory = '';
$domain = '';
$projectName = 'myitinerary.pt';
$dbName = '';
$dbUsername = '';
$dbPassword = '';

// Não utilizado neste momento
// default: PT
define('LANG_DEFAULT', 'PT');

// Mostrar texto de debug
// default: 0
define('DEBUG_OPTIONS', 0);

define('SITE_DIR', $wwwDirectory . $projectName);
define('SITE_URL', $domain . '/' . $projectName);
define('SITE_DOMAIN', $domain);

define('BO_DIR', $wwwDirectory . $projectName . '/gest');
define('BO_URL', $domain . '/' . $projectName . '/gest');

define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', $dbUsername);
define('DB_PASSWORD', $dbPassword);
define('DB_NAME', $dbName);

define('EMAIL_USERNAME', '');
define('EMAIL_PASSWORD', '');
define('EMAIL_NAME', 'MyItinerary');
define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_PORT', 465);

define('GMAPS_API_KEY', '');

// ---

include SITE_DIR . '/include/_db.inc.php';
$db = new db(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

include SITE_DIR . '/include/_myfunctions.inc.php';

// Define se o site está em modo de manutenção
// default: 0
define('MAINTENANCE', $db->query("SELECT value FROM setting WHERE name = 'MAINTENANCE'")->fetchAll()[0]['value']);

// Registo de estatísticas
// default: 1
define('REGISTER_LOGS', $db->query("SELECT value FROM setting WHERE name = 'REGISTER_LOGS'")->fetchAll()[0]['value']);

if (MAINTENANCE && !isset($_SESSION['auth-bo']) && $_SERVER['PHP_SELF'] != '/myitinerary.pt/login.php' && $_SERVER['PHP_SELF'] != '/12itm30/myitinerary.pt/login.php' && $_SERVER['PHP_SELF'] != '/myitinerary.pt/source/auth/login.php' && $_SERVER['PHP_SELF'] != '/12itm30/myitinerary.pt/source/auth/login.php') {
    unset($_SESSION['auth']);
    unset($_SESSION['auth-bo']);
    RegisterLog('Visit:MAINTENANCE-' . $_SERVER['PHP_SELF']);
    include SITE_DIR . '/include/maintenance.inc.php';
    die;
}

IfAuthUserNotValidLogout();
?>
