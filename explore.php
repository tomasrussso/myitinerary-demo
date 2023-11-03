<?php
    require_once 'include/_settings.inc.php';
    RegisterLog('Visit:Explore');

    // Itinerários

    $itinerariesPerPage = 12;

    $query = '';

    if (isset($_GET['q']) && !empty($_GET['q'])) {
        $query = urldecode($_GET['q']);

        // if (strpos($query, '=')) {
        //     if (strpos($query, ';')) {
        //         $options = explode(';', $query);
        //         $options = array_map('trim', $options);
        //     } else {
        //         $options[0] = trim($query);
        //     }

        //     foreach ($options as $option) {
        //         if (!strpos($option, '=')) continue;
               
        //         $name = explode('=', $option)[0];
        //         $value = explode('=', $option)[1];

        //         switch ($name) {
        //             case 'city':

        //                 break;
        //             case 'author':
                        
        //                 break;
        //             case 'duration':
        //                 break;
        //         }
        //     }
        // }
    }

    if (empty($query)) {
        $count = $db->query('SELECT COUNT(id) AS count FROM itinerary WHERE status = 1 AND isPrivate = 0')->fetchAll()[0]['count'];
        $pagesCount = ceil($count / $itinerariesPerPage);

        $currentPage = 1;

        if (isset($_GET['p']) && $_GET['p'] <= $pagesCount) {
            $currentPage = $_GET['p'];
        }

        $itineraries = $db->query('SELECT i.*, u.id AS userId, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.status = 1 AND i.isPrivate = 0 AND u.status = 1 ORDER BY i.createdAt DESC LIMIT ' . $itinerariesPerPage * ($currentPage - 1)  . ',' . $itinerariesPerPage)->fetchAll();
    } else {
        $count = $db->query('SELECT COUNT(i.id) AS count FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.status = 1 AND i.isPrivate = 0 AND (i.title LIKE ? OR u.name LIKE ? OR u.username LIKE ?)', '%' . $query . '%', '%' . $query . '%', '%' . $query . '%')->fetchAll()[0]['count'];
        $pagesCount = ceil($count / $itinerariesPerPage);

        $currentPage = 1;

        if (isset($_GET['p']) && $_GET['p'] <= $pagesCount) {
            $currentPage = $_GET['p'];
        }

        $itineraries = $db->query('SELECT i.*, u.id AS userId, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.status = 1 AND i.isPrivate = 0 AND u.status = 1 AND (i.title LIKE ? OR u.name LIKE ? OR u.username LIKE ?) ORDER BY i.createdAt DESC LIMIT ' . $itinerariesPerPage * ($currentPage - 1)  . ',' . $itinerariesPerPage, '%' . $query . '%', '%' . $query . '%', '%' . $query . '%')->fetchAll();
    }
    
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
    <title>Explorar - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="description" content="Explore os itinerários do MyItinerary e inspire-se para a sua próxima viagem">
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

    <div class="explore">
        <div class="container">
            <div class="row title">
                <h1>Explorar</h1>
                <h2>Navegue pelos itinerários criados por outros Exploradores</h2>
            </div>
            <div class="row search">
                <div class="col-12">
                    <form action="<?= SITE_URL ?>/explore" name="explore-search" method="get" autocomplete="off">
                        <div class="d-flex">
                            <label for="search" hidden>Pesquisar</label>
                            <input class="flex-grow-1" spellcheck="false" autocomplete="off" type="text" name="q" id="search" placeholder="Pesquise por cidade, nome, autor..." <?php if (isset($_GET['q'])): ?>value="<?= htmlspecialchars(urldecode($_GET['q'])) ?>"<?php endif; ?>>
                            <button type="submit" class="btn btn-secondary" title="Pesquisar"><i class="fas fa-search"></i><span class="d-none d-md-inline">Pesquisar</span></button>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (!empty($query) && !empty($itineraries)): ?>
            <div class="row p-0 mb-3" style="margin-top: -8px">
                <h5 class="p-0">Resultados da pesquisa para <b><?= urldecode($_GET['q']) ?></b>:</h5>
            </div>
            <?php endif; ?>
            <?php if (empty($itineraries)): ?>
            <div class="row p-0 mb-3 text-center" style="margin-top: 16px">
                <h5 class="p-0">A pesquisa para <b><?= urldecode($_GET['q']) ?></b> não retornou qualquer resultado :(</h5>
            </div>
            <?php endif; ?>
        </div>
        <div class="container result">
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
            <?php if (!empty($itineraries)): ?>
            <div class="row navigation">
                <nav aria-label="Navegação">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage == 1): ?>
                            <li class="page-item disabled"><a class="page-link" tabindex="-1" href="#">Anterior</a></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?= SITE_URL ?>/explore<?php echo (isset($_GET['q']) ? '?q=' . urlencode($_GET['q']) . '&' : '?') ?>p=<?= $currentPage - 1 ?>">Anterior</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pagesCount; $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <li class="page-item active"><a class="page-link" href="#"><?= $i ?></a></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?= SITE_URL ?>/explore<?php echo (isset($_GET['q']) ? '?q=' . urlencode($_GET['q']) . '&' : '?') ?>p=<?= $i ?>"><?= $i ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($currentPage == $pagesCount): ?>
                            <li class="page-item disabled"><a class="page-link" tabindex="-1" href="#">Seguinte</a></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?= SITE_URL ?>/explore<?php echo (isset($_GET['q']) ? '?q=' . urlencode($_GET['q']) . '&' : '?') ?>p=<?= $currentPage + 1 ?>">Seguinte</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>

    <?php if(!isset($_SESSION['auth'])): include SITE_DIR . '/include/register-modal.inc.php'; endif; ?>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>

    <?php include SITE_DIR . '/include/itinerary-modal.inc.php'; ?>

    <?php include SITE_DIR . '/include/toast.inc.php'; ?>
</body>
</html>
<?php
    unset($_SESSION['errors']);
?>