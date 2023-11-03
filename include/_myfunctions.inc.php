<?php
// ---
// 
// O myfunctions.inc.php faz o seguinte:
//      - declara funções que podem ser utilizadas em qualquer ficheiro
// 
// Este ficheiro é incluído automaticamente sempre que for requerido o settings.inc.php.
// 
// ---

// Tranforma JSON em HTML para visualização
function JsonToHtmlView($content, $duration) {
    $html = '';

    for ($i = 1; $i <= $duration; $i++) {
        $html .= '<h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#day-' . $i . '" role="button" aria-expanded="false" aria-controls="day-' . $i . '">Dia ' . $i . '</a></h4>
                  <div class="collapse show" id="day-' . $i . '">';
        foreach ($content['day-' . $i] as $element) {
            switch ($element['type']) {
                case 'paragraph':
                    $html .= '<p>' . $element['value'] . '</p>';
                    break;
                case 'local':
                    $html .= '<div class="card local">
                                <div class="row">
                                    <div class="col-md-3 align-self-center">
                                        <a href="' . $element['url'] . '" target="_blank"><img class="img-fluid" src="' . str_replace('%SITE_URL%', SITE_URL, $element['image']) . '" alt="' . $element['title'] . '"></a>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="card-body">
                                            <a href="' . $element['url'] . '" class="link" target="_blank"><h5 class="card-title">' . $element['title'] . '</h5></a>
                                            <p class="card-text review"><span class="stars">' . PrintReviewStars($element['rating']) . '</span><a href="' . $element['url'] . '" class="link" target="_blank">' . $element['rating'] . ' no Google</a></p>
                                            <p class="card-text">' . $element['description'] . '</p>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                    break;
                case 'image':
                    $html .= '<div class="image">
                                <img class="img-fluid" src="' . str_replace('%SITE_URL%', SITE_URL, $element['src']) . '" alt="">
                            </div>';
                    break;
            }
        }
        $html .= '</div>';
    }

    return $html;
}

function IsItineraryEmpty($content, $duration) {
    for ($i = 1; $i <= $duration; $i++) {
        if (!empty($content['day-' . $i][0])) {
            return 0;
        }
    }

    return 1;
}

// Tranforma JSON em HTML para edição
function JsonToHtmlEdit($content, $duration) {
    $html = '';

    for ($i = 1; $i <= $duration; $i++) {
        $html .= '<h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#collapse-day-' . $i . '" role="button" aria-expanded="false" aria-controls="collapse-day-' . $i . '">Dia ' . $i . '</a></h4>
                  <div class="collapse show" id="collapse-day-' . $i . '">
                  <div id="day-' . $i . '">';
        foreach ($content['day-' . $i] as $element) {
            switch ($element['type']) {
                case 'paragraph':
                    $html .= '<div class="card paragraph" id="' . $element['id'] . '">
                                <div class="d-flex flex-column flex-md-row">
                                    <div class="paragraph-text d-flex flex-grow-1">
                                        <textarea onblur="SaveContent(\'' . $element['id'] . '\', this)" class="w-100" name="textarea-' . $element['id'] . '" id="textarea-' . $element['id'] . '" placeholder="Escreva alguma coisa...">' . str_replace('<br>', '&#13;&#10;', $element['value']) . '</textarea>
                                    </div>
                                    <div class="elements-options d-flex flex-row flex-md-column justify-content-start align-items-end">
                                        <button class="btn" onclick="GoUp(\'' . $element['id'] . '\')"><i class="fas fa-chevron-up"></i></button>
                                        <button class="btn" onclick="GoDown(\'' . $element['id'] . '\')"><i class="fas fa-chevron-down"></i></button>
                                        <button class="btn" onclick="RemoveElement(\'' . $element['id'] . '\')"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                              </div>';
                    break;
                case 'local':
                    $html .= '<div class="card local" id="' . $element['id'] . '">
                                    <div class="d-flex flex-column flex-md-row justify-content-start">
                                        <div class="row">
                                            <div class="col-md-3 align-self-center">
                                                <a href="' . $element['url'] . '" target="_blank"><img class="img-fluid" src="' . str_replace('%SITE_URL%', SITE_URL, $element['image']) . '" alt="' . $element['title'] . '"></a>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="card-body">
                                                    <a href="' . $element['url'] . '" class="link" target="_blank"><h5 class="card-title">' . $element['title'] . '</h5></a>
                                                    <p class="card-text review"><span class="stars">' . PrintReviewStars($element['rating']) . '</span><a href="' . $element['url'] . '" class="link" target="_blank">' . $element['rating'] . ' no Google</a></p>
                                                    <p class="card-text">' . $element['description'] . '</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="elements-options d-flex flex-row flex-md-column justify-content-start align-items-end">
                                            <button class="btn" onclick="GoUp(\'' . $element['id'] . '\')"><i class="fas fa-chevron-up"></i></button>
                                            <button class="btn" onclick="GoDown(\'' . $element['id'] . '\')"><i class="fas fa-chevron-down"></i></button>
                                            <button class="btn" onclick="RemoveElement(\'' . $element['id'] . '\')"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </div>
                                </div>';
                    break;
                case 'image':
                    $html .= '<div class="card image" id="' . $element['id'] . '">
                                <div class="d-flex flex-column flex-md-row justify-content-start">
                                    <div class="wrapper-image">
                                        <img class="img-fluid" src="' . str_replace('%SITE_URL%', SITE_URL, $element['src']) . '" alt="">
                                    </div>
                                    <div class="elements-options d-flex flex-row flex-md-column justify-content-start align-items-end">
                                        <button class="btn" onclick="GoUp(\'' . $element['id'] . '\')"><i class="fas fa-chevron-up"></i></button>
                                        <button class="btn" onclick="GoDown(\'' . $element['id'] . '\')"><i class="fas fa-chevron-down"></i></button>
                                        <button class="btn" onclick="RemoveElement(\'' . $element['id'] . '\')"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>';
                    break;
            }
        }
        $html .= '</div>
                  <div class="col buttons" id="buttons-' . $i . '">
                  <button onclick="AddParagraph(\'day-' . $i . '\')" class="btn btn-white"><i class="fas fa-plus"></i>Parágrafo</button>
                  <button onclick="AddLocal(\'day-' . $i . '\')" class="btn btn-white"><i class="fas fa-plus"></i>Local</button>
                  <button onclick="AddImage(\'day-' . $i . '\')" class="btn btn-white"><i class="fas fa-plus"></i>Imagem</button>
                  </div>
                  </div>';
    }

    return $html;
}

// Die Dump
function dd() {
    foreach (func_get_args() as $value) {
        var_dump($value);
    }
    die;
}

// Gera um token 'criptograficamente' seguro
function GenerateToken($length) {
    $bytes = (function_exists('random_bytes')) ?
            random_bytes($length) :
            openssl_random_pseudo_bytes($length);
    return bin2hex($bytes);
}

// Cross-Site Request Forgery - Gera um token, armazena na variavel $_SESSION['token'][<nome_do_token>],
// e cria um campo de input oculto com o valor do token, para utilização nos formulários de método POST
function csrf($name) {
    $token = GenerateToken(32);
    $_SESSION['token'][$name] = $token;
    echo "
        <input type=\"hidden\" name=\"_token\" value=\"$token\">
    ";
    return;
}

function ToFixed($number, $decimals) {
    return number_format($number, $decimals, '.', "");
}

function PrintReviewStars($rating) {
    $int = substr($rating, 0, 1);
    $dec = substr($rating, 2, 1);

    $stars = '';
    $starsRemaning = 5;

    for ($i = 1; $i <= $int; $i++) {
        $starsRemaning--;
        $stars .= '<i class="fas fa-star"></i>';
    }

    if ($starsRemaning == 0) {
        return $stars;
    } 

    if ($dec > 2) {
        $starsRemaning--;
        $stars .= '<i class="fas fa-star-half-alt"></i>';
    }

    if ($starsRemaning == 0) {
        return $stars;
    } 

    for ($i = 1; $i <= $starsRemaning; $i++) {
        $stars .= '<i class="far fa-star"></i>';
    }

    return $stars;
}

function GetCSRFToken($name) {
    $token = GenerateToken(32);
    $_SESSION['token'][$name] = $token;
    return $token;
}

// Complemento da função csrf(): verifica se o token está de acordo com o token da variável de sessão
// Quando a função for chamada, o argumento token corresponde a $_POST['_token']
function IsCsrfTokenValid($name, $token) {
    if ($token == $_SESSION['token'][$name] && isset($token)) {
        $_SESSION['token'][$name] = 'no_token';
        return true;
    } else {
        $_SESSION['token'][$name] = 'no_token';
        return false;
    }
}

// Função alternativa ao header(), para tornar o código mais limpo
function RedirectTo($route) {
    if ($route == 'home') {
        $route = '';
    } 
    if ($route == 'BACK') {
        header("location:javascript://history.go(-1)");
    } else {
        header('location: ' . SITE_URL . '/' . $route);
    }
}

// Função alternativa ao header(), para tornar o código mais limpo
// Opção de adicionar erros
function RedirectAndDie($route, $errors = "") {
    if (!$errors == "") {
        $_SESSION['errors'] = $errors;
    }
    if ($route == 'home') {
        $route = '';
    } 
    if ($route == 'BACK') {
        header("location:javascript://history.go(-1)");
        die;
    } else {
        header('location: ' . SITE_URL . '/' . $route);
        die;
    }
}

// Regista a ação do utilizador no site
function RegisterLog($action, $acceptRepeated = false) {
    if (!REGISTER_LOGS) return;

    global $db;

    $userInfo = @get_browser(null, true);
    $browserInfo = (!$userInfo ? 'N/A' : $userInfo['browser'] . ' ' . $userInfo['version']);
    $deviceType = (!$userInfo ? 'N/A' : $userInfo['device_type']);
    $platform = (!$userInfo ? 'N/A' : $userInfo['platform']);

    $userIP = $_SERVER['REMOTE_ADDR'];

    if (!$acceptRepeated && $db->query('SELECT COUNT(id) AS count FROM log WHERE session = ? AND action = ? AND createdAt >= TIMESTAMPADD(DAY, -1, now())', session_id(), $action)->fetchAll()[0]['count'] > 0) return;

    if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
        $idUser = isset($_SESSION['auth']) ? $_SESSION['auth']['user']['id'] : $_SESSION['auth-bo']['user']['id'];
        $db->query('INSERT INTO log (idUser, session, ip, browserInfo, deviceType, platform, action) VALUES (?, ?, ?, ?, ?, ?, ?)', $idUser, session_id(), $userIP, $browserInfo, $deviceType, $platform, $action);
    } else {
        $db->query('INSERT INTO log (session, ip, browserInfo, deviceType, platform, action) VALUES (?, ?, ?, ?, ?, ?)', session_id(), $userIP, $browserInfo, $deviceType, $platform, $action);
    }
}

// Esta função move um ficheiro para a pasta de destino indicada.
// Retorna o path do ficheiro em caso de sucesso, caso contrário retorna false.
function UploadFile($arrFile, $arrFileTypes, $description, $maxUploadSize = 10000000, $folder = '/files/public') {
    if(!$arrFile['error']) {
        if($arrFile['size'] <= $maxUploadSize) {
            if(in_array($arrFile['type'], $arrFileTypes)) {
                $arrInfoFile = pathinfo($arrFile['name']);
                $sanitizeName = SanitizeString($arrInfoFile['filename']);
                $fileName = $description . '_' . $sanitizeName . '_' . uniqid() . '.' . $arrInfoFile['extension'];
                move_uploaded_file($arrFile['tmp_name'], SITE_DIR . $folder . '/' . $fileName);
                return $folder . '/' . $fileName;
            }
        }
    }
    return false;
}

// Verifica se o User está válido na DB (tem status diferente de 0)
function IfAuthUserNotValidLogout() {
    if (isset($_SESSION['auth'])) {
        global $db;
        $status = 0;
        $status = $db->query('SELECT status FROM user WHERE id = ?', $_SESSION['auth']['user']['id'])->fetchAll()[0]['status'];
        if (!$status) {
            unset($_SESSION['auth']);
            RedirectAndDie('login');
        }
    }
}

// Verifica se o user está autenticado no backoffice
function CheckAuthBo() {
    if (!isset($_SESSION['auth-bo'])) {
        RegisterLog('Attempt to access backoffice unauthenticated', true);
        include SITE_DIR . '/404.php';
        die;
    }
}

// Se o utilizador/itinerarrio não possuir foto de perfil ou capa, define como default
function SetDefaultPictures(&$array) {
    if (array_key_exists('profilePicturePath', $array) && is_null($array['profilePicturePath'])) {
        $number = ord(substr($array['name'], 0, 1));
        $array['profilePicturePath'] = 'files/default/profileDefault' . ($number % 2 == 0 ? 2 : 1) . '.jpg';
    }
    if (array_key_exists('wallpaperPath', $array) && is_null($array['wallpaperPath'])) {
        $array['wallpaperPath'] = 'files/default/wallpaperDefault' . (substr($array['createdAt'], -1) % 2 == 0 ? 2 : 1) . '.png';    
    }
}

// Devolve uma string segura, tanto para ficheiro, tanto para slug
function SanitizeString($string, $forceLowercase = true, $removeAlphanumeric = false) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $temp = trim(str_replace($strip, "", strip_tags($string)));
    $clean = Unaccent($temp);
    $clean = preg_replace('/[\s-]+/', "-", $clean);
    $clean = ($removeAlphanumeric) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
    return ($forceLowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;
}

// Remove acentos
function Unaccent($string)
{
    if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false) {
        $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
    }
    return $string;
}

// Gera cores aleatórias (para gráficos no BO)
function RandomColorPart() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}
function RandomColor() {
    return RandomColorPart() . RandomColorPart() . RandomColorPart();
}

// Devolve o tempo convertido em string (Ex: Há 4 dias)
function TimeElapsedString($datetime, $full = false) {
    $now = new DateTime('now', new DateTimeZone('Europe/Lisbon'));
    $ago = new DateTime($datetime, new DateTimeZone('Europe/Lisbon'));
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'ano',
        'm' => 'mês',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minuto'
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = 'Há ' . $diff->$k . ' ' . ($k == 'm' && $diff->$k != 1 ? 'mese' : $v) . ($diff->$k > 1 ? 's' : '');
            // $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) : 'Há instantes';
}
?>