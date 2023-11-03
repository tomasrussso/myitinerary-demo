<?php
    require_once 'include/_settings.inc.php';
    RegisterLog('Visit:Homepage');

    $banner = $db->query('SELECT * FROM banner WHERE status = 1 ORDER BY RAND()')->fetchAll();

    // Itinerários
    
    $itineraries = $db->query('SELECT i.*, u.id AS userId, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.status = 1 AND i.isPrivate = 0 AND u.status = 1 AND i.id IN (SELECT il.idItinerary FROM itinerary_like AS il GROUP BY il.idItinerary ORDER BY COUNT(*) DESC) LIMIT 0,4')->fetchAll();

    foreach ($itineraries as &$itineraryTemp) {
        $citiesArray = $db->query('SELECT c.name FROM city AS c INNER JOIN itinerary_city AS ic ON ic.idCity = c.id WHERE ic.idItinerary = ? ORDER BY c.name ASC', $itineraryTemp['id'])->fetchAll();

        $cities = '';
        foreach ($citiesArray as $city) {
            $cities .= $city['name'] . ', ';
        }
    
        $cities = rtrim($cities, ', ');

        $itineraryTemp['cities'] = $cities;

        SetDefaultPictures($itineraryTemp);
    }

    unset($itineraryTemp);

    $itineraryLikeToken = GetCSRFToken('itinerary-like');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyItinerary - Crie e explore centenas de itinerários</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="description" content="Crie o seu itinerário no MyItinerary e partilhe a sua viagem com outros Exploradores">
    <meta name="theme-color" content="#40916c">

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/bootstrap/bootstrap.min.css">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/fontawesome/css/all.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/main.css">

    <?php include SITE_DIR . '/include/itinerary-modal-css.inc.php'; ?>
</head>
<body>
    <!-- Menu e Banner -->
    <div class="menu menu-home">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-auto logo align-self-center">
                    <a href="<?= SITE_URL ?>">
                        <img src="<?= SITE_URL ?>/images/logo-branco.svg" class="logo-shadow" alt="MyItinerary" height="36px">
                    </a>
                </div>

                <?php if (!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])): ?>
                    <!-- Navbar Guest -->
                    <div class="col-auto options">
                        <nav>
                            <ul>
                                <li><a href="<?= SITE_URL ?>/signin" class="btn btn-link menu-md-hide">Registar-me</a></li>
                                <li><a href="<?= SITE_URL ?>/login" class="btn btn-primary">Iniciar sessão</a></li>
                            </ul>
                        </nav>
                    </div>
                <?php else: ?>
                    <!-- Navbar Auth -->
                    <div class="col-auto options">
                        <?php if (isset($_SESSION['auth'])): ?>
                        <nav>
                            <ul>
                                <li class="d-flex align-items-center">
                                    <p class="d-flex d-sm-none"><i class="fas fa-bars"></i></p><p class="d-none d-sm-inline"><?= $_SESSION['auth']['user']['name'] ?></p>
                                    <div class="dropdown">
                                        <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false" title="Opções">
                                            <img class="profile-pic" src="<?= SITE_URL ?>/<?= $_SESSION['auth']['user']['profilePicturePath'] ?>" alt="<?= $_SESSION['auth']['user']['name'] ?>">
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                            <li><a class="dropdown-item" href="#createItinerary" data-bs-toggle="modal" data-bs-target="#createItinerary">Criar um novo itinerário</a></li>
                                            <div class="dropdown-divider"></div>
                                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/profile/<?= $_SESSION['auth']['user']['username'] ?>">Perfil</a></li>
                                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/profile/<?= $_SESSION['auth']['user']['username'] ?>#itineraries">Os meus itinerários</a></li>
                                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/profile/<?= $_SESSION['auth']['user']['username'] ?>#favourites">Favoritos</a></li>
                                            <div class="dropdown-divider"></div>
                                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/help">Ajuda</a></li>
                                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/profile/<?= $_SESSION['auth']['user']['username'] ?>#settings">Definições</a></li>
                                            <div class="dropdown-divider"></div>
                                            <li>
                                                <form action="<?= SITE_URL ?>/source/auth/logout.php" method="post">
                                                    <?php csrf('logout'); ?>
                                                    <button class="dropdown-item" type="submit">Terminar sessão</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </nav>
                        <?php else: ?>
                        <nav>
                            <ul>
                                <li class="d-flex align-items-center">
                                    <a href="<?= BO_URL ?>" class="btn btn-link" style="padding-right: 0">Voltar a MIGest</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="banner">
        <div class="container-fluid">
            <div id="carousel-banner" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-pause="false">
                <div class="carousel-inner">
                    <?php foreach($banner as $k => $slide): ?>
                        <div class="carousel-item <?php if ($k == 0) echo 'active' ?>" style="background-image: url('<?= SITE_URL ?>/images/darken80.png'), url('<?= SITE_URL ?>/<?= $slide['imagePath'] ?>');">
                            <div class="title row align-items-center">
                                <div class="city text-center">
                                    <h1 id="title"><?= $slide['cityName'] ?></h1>
                                    <h3 id="description"><?= $slide['cityDescription'] ?></h3>
                                    <a href="<?= SITE_URL ?>/explore?q=<?= is_null($slide['buttonCityName']) ? urlencode($slide['cityName']) : urlencode($slide['buttonCityName']) ?>" class="btn btn-secondary text-white" id="button">Explorar <?= is_null($slide['buttonCityName']) ? $slide['cityName'] : $slide['buttonCityName'] ?></a>
                                </div>
                            </div>
                            <div class="row credits">
                                <p>Foto de <?= $slide['credits'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Explorar -->
    <div class="explore-home" id="explore">
        <div class="container">
            <div class="row text-right">
                <h3><a href="<?= SITE_URL ?>/explore" class="link">Explorar</a></h3>
            </div>
            <div class="row itin-cards justify-content-start">
                <?php foreach ($itineraries as $itinerary):
                    if(isset($_SESSION['auth'])): $isLiked = $db->query('SELECT COUNT(idItinerary) AS count FROM itinerary_like WHERE idUser = ? AND idItinerary = ?', $_SESSION['auth']['user']['id'], $itinerary['id'])->fetchAll()[0]['count']; endif;
                ?>
                <div class="col-12 col-md-6 col-xl-3">
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
            </div>
        </div>
    </div>

    <!-- Call to action Login -->
    <div class="last">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto text-center">
                    <h3>Pronto para planear a sua próxima viagem?</h3>
                    <a <?php if(isset($_SESSION['auth'])): ?>href="#createItinerary" data-bs-toggle="modal" data-bs-target="#createItinerary"<?php else: ?>href="<?= SITE_URL ?>/login"<?php endif; ?> class="btn btn-primary"><?php if(isset($_SESSION['auth'])): ?>Criar um novo itinerário<?php else: ?>Iniciar sessão<?php endif; ?></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <?php if(!isset($_SESSION['auth'])): include SITE_DIR . '/include/register-modal.inc.php'; endif; ?>
    
    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>

    <?php include SITE_DIR . '/include/itinerary-modal.inc.php'; ?>

    <?php include SITE_DIR . '/include/toast.inc.php'; ?>

    <?php if (isset($_SESSION['auth'])): ?>
    <script>
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
?>