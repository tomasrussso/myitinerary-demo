<?php
    require_once 'include/_settings.inc.php';

    if (!isset($_GET['i'])) {
        include SITE_DIR . '/404.php';
        die;
    }

    $id = htmlspecialchars($_GET['i']);
    if (strpos($id, '/')) {
        $id = explode('/', $id)[0];
    }

    $itineraries = $db->query('SELECT i.*, u.id AS userId, u.name, u.username, u.profilePicturePath, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.id = ? AND i.status = 1 AND u.status > 0', $id)->fetchAll();

    if (empty($itineraries)) {
        include SITE_DIR . '/404.php';
        die;
    }

    $itinerary = $itineraries[0];

    // Verifica se o URL tem o nome do itinerario
    $urlArray = explode('/', $_SERVER['REQUEST_URI']);
    $urlCurrentName = end($urlArray);
    
    $parameters = 0;
    if (strpos($urlCurrentName, '?')) {
        $nameArray = explode('?', $urlCurrentName);
        $parameters = end($nameArray);
        $urlCurrentName = $nameArray[0];
    }

    if ($urlCurrentName != $itinerary['slug']) {
        RedirectTo('itinerary/' . $id . '/' . $itinerary['slug'] . ($parameters ? '?' . $parameters : ''));
    }

    if ($itinerary['isPrivate'] && !(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId'])) {
        include SITE_DIR . '/include/itinerary-private.inc.php';
        die;
    }

    $citiesArray = $db->query('SELECT c.name FROM city AS c INNER JOIN itinerary_city AS ic ON ic.idCity = c.id WHERE ic.idItinerary = ? ORDER BY c.name ASC', $id)->fetchAll();
    
    $cities = '';
    foreach ($citiesArray as $city) {
        $cities .= $city['name'] . ', ';
    }

    $cities = rtrim($cities, ', ');

    SetDefaultPictures($itinerary);

    if(isset($_SESSION['auth']) && !($_SESSION['auth']['user']['id'] == $itinerary['userId'])) {
        $isLiked = $db->query('SELECT COUNT(idItinerary) AS count FROM itinerary_like WHERE idUser = ? AND idItinerary = ?', $_SESSION['auth']['user']['id'], $id)->fetchAll()[0]['count'];
    }

    if (isset($_GET['edit']) && $_GET['edit'] == 1 && isset($_SESSION['auth']['user']['id']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']) {
        include SITE_DIR . '/include/itinerary-edit.inc.php';
        die;
    }

    RegisterLog('Visit:Itinerary-' . $id . ' ' . $itinerary['slug']);

    // Itinerários recomendados

    if(!isset($_SESSION['auth']['user']['id']) || !($_SESSION['auth']['user']['id'] == $itinerary['userId'])) {
        $itinerariesIds = $db->query('SELECT DISTINCT idItinerary FROM itinerary_city WHERE idCity IN (SELECT idCity FROM itinerary_city WHERE idItinerary = ?) AND idItinerary != ?', $id, $id)->fetchAll();

        if (count($itinerariesIds) > 0) {
            $itineraries = $db->query('SELECT i.*, u.id AS userId, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.status = 1 AND i.isPrivate = 0 AND u.status = 1 AND i.id IN (SELECT DISTINCT idItinerary FROM itinerary_city WHERE idCity IN (SELECT idCity FROM itinerary_city WHERE idItinerary = ?) AND idItinerary != ?) ORDER BY RAND() LIMIT 0,2', $id, $id)->fetchAll();
        } else {
            $itineraries = $db->query('SELECT i.*, u.id AS userId, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.status = 1 AND i.isPrivate = 0 AND u.status = 1 AND i.id IN (SELECT il.idItinerary FROM itinerary_like AS il WHERE il.idItinerary != ? GROUP BY il.idItinerary) ORDER BY RAND() LIMIT 0,2', $id)->fetchAll();
        }
        
        foreach ($itineraries as &$itineraryTemp) {
            $citiesArray = $db->query('SELECT c.name FROM city AS c INNER JOIN itinerary_city AS ic ON ic.idCity = c.id WHERE ic.idItinerary = ? ORDER BY c.name ASC', $itineraryTemp['id'])->fetchAll();

            $citiesRec = '';
            foreach ($citiesArray as $city) {
                $citiesRec .= $city['name'] . ', ';
            }
        
            $citiesRec = rtrim($citiesRec, ', ');

            $itineraryTemp['cities'] = $citiesRec;

            SetDefaultPictures($itineraryTemp);
        }

        unset($itineraryTemp);
    }

    $itineraryLikeToken = GetCSRFToken('itinerary-like');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($itinerary['title']) ?> - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="description" content="Explore o itinerário de <?= htmlspecialchars($itinerary['name']) ?> no MyItinerary">
    <meta name="theme-color" content="#40916c">

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;450;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/bootstrap/bootstrap.min.css">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/fontawesome/css/all.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/main.css">

    <?php include SITE_DIR . '/include/itinerary-modal-css.inc.php'; ?>
</head>
<body>
    <!-- Menu -->
    <div class="menu menu-white">
        <?php 
            if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
                include SITE_DIR . '/include/navbar-login.inc.php'; 
            } else {
                include SITE_DIR . '/include/navbar-default.inc.php';
            } 
        ?>
    </div>

    <div class="itinerary-page">
        <div class="header" style="background-image: url(<?= SITE_URL ?>/images/background-dark.png), url(<?= SITE_URL ?>/<?= htmlspecialchars($itinerary['wallpaperPath']) ?>);">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col">
                        <h1><?= htmlspecialchars($itinerary['title']) ?></h1>
                        <h4><?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?><?php if ($itinerary['isPrivate']): ?><i class="fas fa-lock" title="Visibilidade"></i>Privado&nbsp;&nbsp;&nbsp;&nbsp;<?php else: ?><i class="fas fa-users" title="Visibilidade"></i>Público&nbsp;&nbsp;&nbsp;&nbsp;<?php endif; endif; ?><i class="fas fa-calendar-alt" title="Duração"></i><?= htmlspecialchars($itinerary['duration']) ?> dia<?php if ($itinerary['duration'] != 1) echo 's'; ?>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-map-marker-alt" title="Cidades"></i><?= htmlspecialchars($cities) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="itin-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-9 main-content">
                        <div class="row description">
                            <h3>Sobre</h3>
                            <?php if (is_null($itinerary['description'])): ?><p class="placeholder-empty">Sem descrição...</p><?php else: ?><p><?= htmlspecialchars($itinerary['description']) ?></p><?php endif; ?>
                        </div>
                        <div class="row author align-items-center">
                            <div class="col-auto">
                                <a href="<?= SITE_URL ?>/profile/<?= htmlspecialchars($itinerary['username']) ?>" title="<?= htmlspecialchars($itinerary['name']) ?>" class="link"><img src="<?= SITE_URL ?>/<?= htmlspecialchars($itinerary['profilePicturePath']) ?>" alt="<?= htmlspecialchars($itinerary['name']) ?>"></a>
                            </div>
                            <div class="col-auto author-date">
                                <p><a href="<?= SITE_URL ?>/profile/<?= htmlspecialchars($itinerary['username']) ?>" class="link"><span class="name"><?= htmlspecialchars($itinerary['name']) ?><?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?> (Eu)<?php endif; ?></span></a><?php if ($itinerary['isVerified']): ?><i style="font-size: 0.8rem; margin-left: 6px; color: #212529;" class="fas fa-check-circle mt-2" title="Perfil verificado"></i><?php endif;?><br><?= date('d/m/Y, H:i', strtotime($itinerary['createdAt'])) ?></p>
                            </div>
                        </div>
                        <div class="row buttons">
                            <div class="col">
                                <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?><a href="<?= SITE_URL . '/itinerary/' . $id . '/' . $itinerary['slug']?>?edit=1" class="btn btn-primary"><i class="fas fa-pen"></i>Editar</a><?php else: ?><a <?php if (!isset($_SESSION['auth'])): ?>href="#register" data-bs-toggle="modal" data-bs-target="#register"<?php else: ?>onclick="ThisItineraryLike('<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>', '<?= $itineraryLikeToken ?>', this)"<?php endif;?> class="btn btn-primary"<?php if((isset($_SESSION['auth']) && !$isLiked) || !isset($_SESSION['auth'])): ?> title="Gosto deste itinerário"><i class="far fa-heart"></i>Gostar</a><?php else: ?> title="Não gosto deste itinerário"><i class="fas fa-heart"></i>Gostei</a><?php endif; endif; ?>
                                <button onclick="CopyUrlToClipboard()" class="btn btn-secondary"><i class="fas fa-share-alt"></i>Partilhar</button>
                                <!-- <a href="#" class="btn btn-secondary"><i class="fas fa-sync-alt"></i>Personalizar</a> -->
                                <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?><a href="#options" class="btn btn-white d-inline-block d-lg-none"><i class="fas fa-bars"></i>Opções</a><?php endif; ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row itinerary">
                            <?php if (is_null($itinerary['contentHTML']) || empty($itinerary['contentHTML'])): ?>
                                <p class="placeholder-empty mt-2 mb-2">Sem conteúdo...</p>
                            <?php else: ?>
                                <?= str_replace('%SITE_URL%', SITE_URL, $itinerary['contentHTML']) ?>
                            <?php endif; ?>
                            <!-- <h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#day-1" role="button" aria-expanded="false" aria-controls="day-1">Dia 1</a></h4>
                            <div class="collapse show" id="day-1">
                                <p>Vamos comecar por um lugar que eu ADORO: fabrica dos pasteis de belem. LOL sou gorda por isso e que adoro. comam uns 5 ou 6 com uma bica, e levem mais 2 duzias para a viagem ;)</p>
                                <div class="card local">
                                    <div class="row">
                                        <div class="col-md-3 align-self-center">
                                            <a href="https://www.google.com/maps/place/Past%C3%A9is+de+Bel%C3%A9m/@38.6975105,-9.2032276,15z/data=!4m5!3m4!1s0x0:0xffeff6c6b46d9665!8m2!3d38.6975105!4d-9.2032276" target="_blank"><img class="img-fluid" src="<?= SITE_URL ?>/images/pasteis-belem.jpg" alt="Pasteis de Belem"></a>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body">
                                                <a href="https://www.google.com/maps/place/Past%C3%A9is+de+Bel%C3%A9m/@38.6975105,-9.2032276,15z/data=!4m5!3m4!1s0x0:0xffeff6c6b46d9665!8m2!3d38.6975105!4d-9.2032276" class="link" target="_blank"><h5 class="card-title">Pastéis de Belém</h5></a>
                                                <p class="card-text review"><span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></span><a href="https://www.google.com/maps/place/Past%C3%A9is+de+Bel%C3%A9m/@38.6975105,-9.2032276,15z/data=!4m5!3m4!1s0x0:0xffeff6c6b46d9665!8m2!3d38.6975105!4d-9.2032276" class="link" target="_blank">4,6 no Google</a></p>
                                                <p class="card-text">Grande e arejado café pastelaria com pastelaria portuguesa, inclusive tartes e pão.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p>Depois vamos tirar uma foto no miradouro de sta luzia lol</p>
                                <div class="image">
                                    <img class="img-fluid" src="<?= SITE_URL ?>/images/sta-luzia.jpg" alt="">
                                </div>
                            </div>

                            <h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#day-2" role="button" aria-expanded="false" aria-controls="day-2">Dia 2</a></h4>
                            <div class="collapse show" id="day-2">
                                <p>Acabou. Voltem para o hotel e durmam bjs.</p>
                            </div> -->
                        </div>
                        <hr class="d-block d-lg-none">
                    </div>
                    <div class="col-12 col-lg-3 itin-explore"<?php if (isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?> id="options"<?php endif; ?>>
                        <!-- INICIO DO ANUNCIO -->
                        <!-- <div class="row mb-4 mt-2 mt-lg-0">
                            <img src="<?= SITE_URL ?>/images/image.jpg" alt="" class="img-fluid">
                            <p class="m-0 mt-1">Publicidade</p>
                        </div> -->
                        <!-- FIM DO ANUNCIO -->
                        <div class="row">
                            <h3><?php if (isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?>Opções<?php else: ?>Para ver a seguir...<?php endif; ?></h3>
                        </div>
                        <?php if (isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?>
                        <div class="row options-menu">
                            <ul>
                                <a href="#changeTitle" data-bs-toggle="modal" data-bs-target="#changeTitle" class="option"><li><i class="fas fa-edit"></i>Alterar o título</li></a>
                                <a href="#changeDescription" data-bs-toggle="modal" data-bs-target="#changeDescription" class="option"><li><i class="fas fa-align-left"></i><?php if (is_null($itinerary['description'])): ?>Adicionar<?php else: ?>Editar a<?php endif; ?> descrição</li></a>
                                <a href="#changeWallpaper" data-bs-toggle="modal" data-bs-target="#changeWallpaper" class="option"><li><i class="fas fa-image"></i>Trocar foto de capa</li></a>
                                <form action="<?= SITE_URL ?>/source/itinerary/change-visibility.php" method="post">
                                    <?php csrf('change-visibility'); ?>
                                    <input type="hidden" name="itinerary" value="<?= $id . '/' . $itinerary['slug'] ?>">
                                    <button type="submit" class="btn option shadow-none"><li class="mt-3 text-start"><?php if ($itinerary['isPrivate']): ?><i class="fas fa-users"></i>Tornar público</li><?php else: ?><i class="fas fa-lock"></i>Tornar privado</li><?php endif; ?></button>
                                </form>
                                <a href="#deleteItinerary" data-bs-toggle="modal" data-bs-target="#deleteItinerary" class="option"><li class="mt-3 del"><i class="fas fa-trash-alt"></i>Eliminar o itinerário</li></a>
                            </ul>
                        </div>
                        <?php else: ?>
                        <div class="row itin-cards">
                        <?php foreach ($itineraries as $itinerary):
                            if(isset($_SESSION['auth'])): $isLiked = $db->query('SELECT COUNT(idItinerary) AS count FROM itinerary_like WHERE idUser = ? AND idItinerary = ?', $_SESSION['auth']['user']['id'], $itinerary['id'])->fetchAll()[0]['count']; endif;
                        ?>
                        <div class="col-12 col-md-6 col-lg-12">
                            <div class="card">
                                <!-- Link para itinerário -->
                                <a href="<?= SITE_URL ?>/itinerary/<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>" class="a-card" title="<?= $itinerary['title'] ?>"> 
                                    <div class="card-img-top" style="background-image: url('<?= SITE_URL ?>/<?= $itinerary['wallpaperPath'] ?>');"></div>
                                </a>
                                <div class="card-img-overlay d-flex justify-content-end">
                                    <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?><a href="<?= SITE_URL ?>/itinerary/<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>?edit=1" title="Editar"><i class="fas fa-pen"></i></a><?php else: ?><button class="btn shadow-none<?php if(isset($_SESSION['auth']) && $isLiked): ?> liked" title="Não gosto deste itinerário"<?php else: ?>" title="Gosto deste itinerário"<?php endif; ?><?php if(isset($_SESSION['auth'])): ?> onclick="ItineraryLike('<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>', '<?= $itineraryLikeToken ?>', this)"<?php else: ?> data-bs-toggle="modal" data-bs-target="#register"<?php endif; ?>><i class="fas fa-heart"></i></button><?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="info card-text">
                                        <div class="location"><?= mb_strtoupper($itinerary['cities'], 'UTF-8') ?></div>
                                        <div class="duration">&nbsp;&middot;&nbsp;<?= $itinerary['duration'] ?> DIA<?php if ($itinerary['duration'] != 1): ?>S<?php endif; ?></div>
                                    </div>
                                    <!-- Link para itinerário -->
                                    <a href="<?= SITE_URL ?>/itinerary/<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>" class="a-card" title="<?= $itinerary['title'] ?>"> 
                                        <h5 class="card-title"><?= $itinerary['title'] ?></h5>
                                    </a>
                                    <div class="author card-text">
                                        <!-- Link para perfil -->
                                        <div class="author-name"><a href="<?= SITE_URL?>/profile/<?= $itinerary['username'] ?>" class="a-card" title="<?= $itinerary['name'] ?>"><?= $itinerary['name'] ?></a><?php if ($itinerary['isVerified']): ?><i style="font-size: 0.75rem; margin-left: 5px; margin-right: 2px !important; color: #212529;" class="fas fa-check-circle" title="Perfil verificado"></i><?php endif;?></div>
                                        <div class="date">&nbsp;&middot;&nbsp;<?= TimeElapsedString($itinerary['createdAt']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach;?>
                            <!-- <div class="col-12 col-md-6 col-lg-12">
                                <div class="card">
                                    <a href="<?= SITE_URL ?>/itinerary" class="a-card"> 
                                        <div class="card-img-top" style="background-image: url('<?= SITE_URL ?>/images/lisboa3.jpg');"></div>
                                        
                                        <div class="card-img-overlay text-end">
                                            <p class="card-text text-end"><a href="#" class="liked"><i class="fas fa-heart"></i></a></p>
                                        </div>
                                    </a>
                                    <div class="card-body">
                                        <p class="info card-text">LISBOA &middot; 2 DIAS</p>
                                        
                                        <a href="<?= SITE_URL ?>/itinerary" class="a-card"> 
                                            <h5 class="card-title">Lorem ipsum dolor sit amet consectetur.</h5>
                                        </a>
                                        <p class="author card-text">
                                          
                                            <a href="<?= SITE_URL ?>/profile" class="a-card">Joana Gomes</a> &middot; Há 3 meses
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-12">
                                <div class="card">
                                   
                                    <a href="<?= SITE_URL ?>/itinerary" class="a-card"> 
                                        <div class="card-img-top" style="background-image: url('<?= SITE_URL ?>/images/porto.jpg');"></div>
                                        <div class="card-img-overlay text-end">
                                            <p class="card-text text-end"><a href="#"><i class="fas fa-heart"></i></a></p>
                                        </div>
                                    </a>
                                    <div class="card-body">
                                        <p class="info card-text">LISBOA &middot; 2 DIAS</p>
                                       
                                        <a href="<?= SITE_URL ?>/itinerary" class="a-card"> 
                                            <h5 class="card-title">Lorem ipsum dolor sit amet consectetur.</h5>
                                        </a>
                                        <p class="author card-text">
                                           
                                            <a href="<?= SITE_URL ?>/profile" class="a-card">Joana Gomes</a> &middot; Há 3 meses
                                        </p>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <?php if(!isset($_SESSION['auth'])): include SITE_DIR . '/include/register-modal.inc.php'; endif; ?>

    <?php if (isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $itinerary['userId']): ?>
    <!-- Modal - Trocar titulo -->
    <div class="modal fade" id="changeTitle" tabindex="-1" aria-labelledby="changeTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Alterar o título</h1>
                        <p>Escolha o título que será apresentado junto do seu itinerário.</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="<?= SITE_URL ?>/source/itinerary/change-title.php" class="form" method="post" onsubmit="DisableButton(document.getElementById('btn-submit-title'))">
                                <?php csrf('change-title'); ?>
                                <input type="hidden" name="itinerary" value="<?= $id . '/' . $itinerary['slug'] ?>">
                                <label for="title">Título</label>
                                <input <?php if (isset($_SESSION['errors']['title-itinpage'])): ?> class="input-error" <?php endif; ?> type="text" placeholder="Escolha o título do itinerário" name="title" id="title" onclick="RemoveError(this)" required  value="<?= htmlspecialchars($itinerary['title']) . '"'; ?>">
                                <?php if (isset($_SESSION['errors']['title-itinpage'])): ?>
                                    <p class="error" style="margin-top: -8px"><?= $_SESSION['errors']['title-itinpage'] ?></p>
                                <?php endif; ?>
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button id="btn-submit-title" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('title'))">Alterar título</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - Trocar descricao -->
    <div class="modal fade" id="changeDescription" tabindex="-1" aria-labelledby="changeDescription" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1><?php if (is_null($itinerary['description'])): ?>Adicionar<?php else: ?>Editar<?php endif; ?> descrição</h1>
                        <p>Escolha uma descrição que caracterize o seu itinerário!</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="<?= SITE_URL ?>/source/itinerary/change-description.php" class="form" method="post" onsubmit="DisableButton(document.getElementById('btn-submit-description'))">
                                <?php csrf('change-description'); ?>
                                <input type="hidden" name="itinerary" value="<?= $id . '/' . $itinerary['slug'] ?>">
                                <label for="description">Descrição</label>
                                <textarea <?php if (isset($_SESSION['errors']['description'])): ?> class="input-error" <?php endif; ?> cols="26" rows="3" placeholder="Descreva o seu itinerário..." name="description" id="description" onclick="RemoveError(this)"><?php if (!is_null($itinerary['description'])): echo htmlspecialchars($itinerary['description']); endif; ?></textarea>
                                <?php if (isset($_SESSION['errors']['description'])): ?>
                                    <p class="error" style="margin-top: -12px"><?= $_SESSION['errors']['description'] ?></p>
                                <?php endif; ?>
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button id="btn-submit-description" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this)"><?php if (is_null($itinerary['description'])): ?>Adicionar<?php else: ?>Editar<?php endif; ?> descrição</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - Trocar foto capa -->
    <div class="modal fade" id="changeWallpaper" tabindex="-1" aria-labelledby="changeWallpaper" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Trocar foto de capa</h1>
                        <p>Selecione a imagem que pretende usar como foto de capa.</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="<?= SITE_URL ?>/source/itinerary/change-wallpaper.php" class="form" method="post" enctype="multipart/form-data" onsubmit="DisableButton(document.getElementById('btn-submit-wallpaper'))">
                                <?php csrf('change-wallpaper'); ?>
                                <input type="hidden" name="itinerary" value="<?= $id . '/' . $itinerary['slug'] ?>">
                                <label for="wallpaper">Foto de capa</label>
                                <input <?php if (isset($_SESSION['errors']['wallpaper'])): ?> class="input-error" <?php endif; ?> type="file" name="wallpaper" id="wallpaper" accept="image/*" onclick="RemoveError(this)" required>
                                <?php if (isset($_SESSION['errors']['wallpaper'])): ?>
                                    <p class="error" style="margin-top: -8px"><?= $_SESSION['errors']['wallpaper'] ?></p>
                                <?php endif; ?>
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button id="btn-submit-wallpaper" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('wallpaper'))">Trocar foto</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - Apagar itinerario -->
    <div class="modal fade" id="deleteItinerary" tabindex="-1" aria-labelledby="deleteItinerary" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Apagar este itinerário?</h1>
                        <p class="mb-0">Está prestes a apagar um itinerário completo!<br>Depois de continuar, não poderá voltar atrás. Pretende prosseguir?</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="<?= SITE_URL ?>/source/itinerary/delete.php" class="form" method="post" onsubmit="DisableButton(document.getElementById('btn-submit-delete'))">
                                <?php csrf('delete-itinerary'); ?>
                                <input type="hidden" name="itinerary" value="<?= $id . '/' . $itinerary['slug'] ?>">
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button id="btn-submit-delete" type="submit" class="btn btn-danger d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this)">Sim, eliminar o itinerário :(</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>

    <?php include SITE_DIR . '/include/itinerary-modal.inc.php'; ?>

    <?php include SITE_DIR . '/include/toast.inc.php'; ?>

    <?php if (isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] != $itinerary['userId']): ?>
    <script>
        function ThisItineraryLike(itinerary, token, button) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText == 'liked') {
                        button.innerHTML = "<i class=\"fas fa-heart\"></i>Gostei</a>";
                        button.title = "Não gosto deste itinerário"
                        LaunchToast('Itinerário adicionado aos favoritos!');
                    } else if (this.responseText == 'disliked') {
                        button.innerHTML = "<i class=\"far fa-heart\"></i>Gostar</a>";
                        button.title = "Gosto deste itinerário"
                        LaunchToast('Itinerário removido dos favoritos.')
                    }
                }
            };
            xmlhttp.open("GET", "<?= SITE_URL ?>/source/itinerary/like.php?i=" + encodeURIComponent(itinerary) + "&t=" + encodeURIComponent(token), true);
            xmlhttp.send();
        }

        function ItineraryLike(itinerary, token, button) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText == 'liked') {
                        button.classList.add('liked');
                        button.title = "Não gosto deste itinerário"
                        LaunchToast('Itinerário adicionado aos favoritos!');
                    } else if (this.responseText == 'disliked') {
                        button.classList.remove('liked');
                        button.title = "Gosto deste itinerário"
                        LaunchToast('Itinerário removido dos favoritos.')
                    }
                }
            };
            xmlhttp.open("GET", "<?= SITE_URL ?>/source/itinerary/like.php?i=" + encodeURIComponent(itinerary) + "&t=" + encodeURIComponent(token), true);
            xmlhttp.send();
        }
    </script>
    <?php endif; ?>
</body>
</html>
<?php
    unset($_SESSION['errors']);
    unset($_SESSION['auth']['itinerary-edit-id']);
?>