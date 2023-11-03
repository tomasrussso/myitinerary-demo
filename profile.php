<?php
    require_once 'include/_settings.inc.php';

    if (!isset($_GET['u'])) {
        include SITE_DIR . '/404.php';
        die;
    }

    $username = htmlspecialchars($_GET['u']);

    $users = $db->query('SELECT id, name, profilePicturePath, wallpaperPath, createdAt, isVerified FROM user WHERE username = ? AND status = 1', $username)->fetchAll();

    if (empty($users)) {
        include SITE_DIR . '/404.php';
        die;
    }

    SetDefaultPictures($users[0]);

    $user = $users[0];
    $id = $user['id'];

    RegisterLog('Visit:Profile-' . $id . ' ' . $username);

    // Itinerários do user

    if (isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id) {
        $itineraries = $db->query('SELECT i.*, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.idUser = ? AND i.status = 1 ORDER BY i.createdAt DESC', $id)->fetchAll();
    } else {
        $itineraries = $db->query('SELECT i.*, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.idUser = ? AND i.status = 1 AND i.isPrivate = 0 ORDER BY i.createdAt DESC', $id)->fetchAll();
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

    // Itinerários gostados

    if (isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id) {
        $itinerariesLiked = $db->query('SELECT i.*, u.name, u.username, u.isVerified FROM itinerary AS i INNER JOIN user AS u ON i.idUser = u.id WHERE i.status = 1 AND i.isPrivate = 0 AND i.id IN (SELECT il.idItinerary FROM itinerary_like AS il WHERE il.idUser = ?) ORDER BY i.createdAt DESC', $id)->fetchAll();
        
        foreach ($itinerariesLiked as &$itineraryTemp) {
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
    }
    
    $itineraryLikeToken = GetCSRFToken('itinerary-like');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil de <?= htmlspecialchars($user['name']) ?> - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="description" content="Explore os itinerários que <?= htmlspecialchars($user['name']) ?> criou no MyItinerary">
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

    <div class="profile">
        <div class="header" style="background-image: url(<?= SITE_URL ?>/images/background-dark.png), url(<?= SITE_URL ?>/<?= htmlspecialchars($user['wallpaperPath']) ?>);">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-auto">
                        <img src="<?= SITE_URL ?>/<?= htmlspecialchars($user['profilePicturePath']) ?>" alt="<?= htmlspecialchars($user['name']) ?>">
                    </div>
                    <div class="col-auto">
                        <h1 class="d-flex"><?= htmlspecialchars($user['name']) ?><?php if ($user['isVerified']): ?><i style="font-size: 1.4rem; margin-left: 12px;" class="fas fa-check-circle mt-2" title="Perfil verificado"></i><?php endif;?></h1>
                        <h4>Explorador desde <?= htmlspecialchars(substr($user['createdAt'], 0, 4)) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-content">
            <div class="container d-lg-flex">
                <div class="col-12 justify-content-start <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?>col-lg-9<?php endif; ?>">
                    <div class="row itin-cards justify-content-start<?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): echo ' pr'; endif; ?>" id="itineraries" style="margin-bottom: 8px">
                        <h3><?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?>Os meus itinerários<?php else: ?>Itinerários públicos<?php endif; ?></h3>
                        <?php if (empty($itineraries)): ?>
                            <p class="placeholder-empty mb-0"><?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?>Parece que ainda não criou nenhum itinerário...<br><a href="#createItinerary" data-bs-toggle="modal" data-bs-target="#createItinerary" class="link">Crie o seu primeiro itinerário!</a><?php else: ?>Parece que este explorador ainda não criou nenhum itinerário...<?php endif; ?></p>
                        <?php else: 
                        foreach ($itineraries as $itinerary):
                            if(isset($_SESSION['auth'])): $isLiked = $db->query('SELECT COUNT(idItinerary) AS count FROM itinerary_like WHERE idUser = ? AND idItinerary = ?', $_SESSION['auth']['user']['id'], $itinerary['id'])->fetchAll()[0]['count']; endif;
                        ?>
                        <div class="col-12 col-md-6 col-xl-<?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?>4<?php else: ?>3<?php endif; ?>">
                            <div class="card">
                                <!-- Link para itinerário -->
                                <a href="<?= SITE_URL ?>/itinerary/<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>" class="a-card" title="<?= $itinerary['title'] ?>"> 
                                    <div class="card-img-top" style="background-image: url('<?= SITE_URL ?>/<?= $itinerary['wallpaperPath'] ?>');"></div>
                                </a>
                                <div class="card-img-overlay d-flex justify-content-end">
                                    <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?><a href="<?= SITE_URL ?>/itinerary/<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>?edit=1" title="Editar"><i class="fas fa-pen"></i></a><?php else: ?><button class="btn shadow-none<?php if(isset($_SESSION['auth']) && $isLiked): ?> liked" title="Não gosto deste itinerário"<?php else: ?>" title="Gosto deste itinerário"<?php endif; ?><?php if(isset($_SESSION['auth'])): ?> onclick="ItineraryLike('<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>', '<?= $itineraryLikeToken ?>', this)"<?php else: ?> data-bs-toggle="modal" data-bs-target="#register"<?php endif; ?>><i class="fas fa-heart"></i></button><?php endif; ?>
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
                                        <div class="author-name"><?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?><?php if ($itinerary['isPrivate']): ?><i class="fas fa-lock"></i>Privado<?php else: ?><i class="fas fa-users"></i>Público<?php endif; else: ?><?= $itinerary['name'] ?><?php if ($itinerary['isVerified']): ?><i style="font-size: 0.75rem; margin-left: 5px; margin-right: 2px !important; color: #212529;" class="fas fa-check-circle" title="Perfil verificado"></i><?php endif;?><?php endif; ?></div>
                                        <div class="date">&nbsp;&middot;&nbsp;<?= TimeElapsedString($itinerary['createdAt']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?>
                    <div class="row itin-cards justify-content-start pr" id="favourites">
                        <h3>Favoritos</h3>
                        <?php if (empty($itinerariesLiked)): ?>
                            <p class="placeholder-empty">Ainda não gostou de nenhum itinerário...<br><a href="<?= SITE_URL ?>/explore" class="link">Comece já a explorar!</a></p>
                        <?php else: 
                        foreach ($itinerariesLiked as $itinerary):
                        ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card">
                                <!-- Link para itinerário -->
                                <a href="<?= SITE_URL ?>/itinerary/<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>" class="a-card" title="<?= $itinerary['title'] ?>"> 
                                    <div class="card-img-top" style="background-image: url('<?= SITE_URL ?>/<?= $itinerary['wallpaperPath'] ?>');"></div>
                                </a>
                                <div class="card-img-overlay d-flex justify-content-end">
                                    <button class="btn shadow-none liked" title="Não gosto deste itinerário" onclick="ItineraryLike('<?= $itinerary['id'] ?>/<?= $itinerary['slug'] ?>', '<?= $itineraryLikeToken ?>', this)"><i class="fas fa-heart"></i></button>
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
                        <?php endforeach; endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?>
                    <div class="col-12 col-lg-3" id="settings">
                        <div class="row text-right">
                            <h3>Definições</h3>
                        </div>
                        <div class="row options-menu">
                            <ul>
                                <a href="#changeProfilePicture" data-bs-toggle="modal" data-bs-target="#changeProfilePicture" class="option"><li><i class="fas fa-user-circle"></i>Atualizar foto de perfil</li></a>
                                <a href="#changeWallpaper" data-bs-toggle="modal" data-bs-target="#changeWallpaper" class="option"><li><i class="fas fa-image"></i>Trocar foto de capa</li></a>
                                <a href="#changePassword" data-bs-toggle="modal" data-bs-target="#changePassword" class="option"><li class="mt-4"><i class="fas fa-key"></i>Alterar a palavra-passe</li></a>
                                <a href="#deleteAccount" data-bs-toggle="modal" data-bs-target="#deleteAccount" class="option"><li class="mt-4 del"><i class="fas fa-trash-alt"></i>Apagar a minha conta</li></a>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <?php if(!isset($_SESSION['auth'])): include SITE_DIR . '/include/register-modal.inc.php'; endif; ?>

    <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['user']['id'] == $id): ?>                
    <!-- Modal - Trocar foto perfil -->
    <div class="modal fade" id="changeProfilePicture" tabindex="-1" aria-labelledby="changeProfilePicture" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Atualizar foto de perfil</h1>
                        <p>Selecione a imagem que pretende usar como foto de perfil.</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="<?= SITE_URL ?>/source/profile/change-profile-picture.php" class="form" method="post" enctype="multipart/form-data" onsubmit="DisableButton(document.getElementById('btn-submit-picture'))">
                                <?php csrf('change-profile-picture'); ?>
                                <label for="picture">Foto de perfil</label>
                                <input <?php if (isset($_SESSION['errors']['picture'])): ?> class="input-error" <?php endif; ?> type="file" name="picture" id="picture" accept="image/*" onclick="RemoveError(this)" required>
                                <?php if (isset($_SESSION['errors']['picture'])): ?>
                                    <p class="error" style="margin-top: -8px"><?= $_SESSION['errors']['picture'] ?></p>
                                <?php endif; ?>
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button id="btn-submit-picture" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('picture'))">Atualizar foto</button>
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
                            <form action="<?= SITE_URL ?>/source/profile/change-wallpaper.php" class="form" method="post" enctype="multipart/form-data" onsubmit="DisableButton(document.getElementById('btn-submit-wallpaper'))">
                                <?php csrf('change-wallpaper'); ?>
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

    <!-- Modal - Trocar password -->
    <div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="changePassword" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Alterar palavra-passe</h1>
                        <p>Utilize uma palavra-passe forte, difícil de adivinhar.</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="<?= SITE_URL ?>/source/profile/change-password.php" class="form" method="post" onsubmit="DisableButton(document.getElementById('btn-submit-password'))">
                                <?php csrf('change-password'); ?>
                                <label for="pw-current">Palavra-passe atual</label> <br>
                                <input <?php if (isset($_SESSION['errors']['pw-current'])): ?> class="input-error" <?php endif; ?> type="password" id="pw-current" name="pw-current" placeholder="Password atual" onclick="RemoveError(this)" required> <br>
                                <?php if (isset($_SESSION['errors']['pw-current'])): ?>
                                    <p class="error mb-3" style="margin-top: -8px"><?= $_SESSION['errors']['pw-current'] ?></p>
                                <?php endif; ?>
                                <label for="pw-new">Nova palavra-passe</label> <br>
                                <input <?php if (isset($_SESSION['errors']['pw-new'])): ?> class="input-error" <?php endif; ?> type="password" id="pw-new" name="pw-new" placeholder="Password nova" onclick="RemoveError(this)" required> <br>
                                <?php if (isset($_SESSION['errors']['pw-new'])): ?>
                                    <p class="error mb-3" style="margin-top: -8px"><?= $_SESSION['errors']['pw-new'] ?></p>
                                <?php endif; ?>
                                <label for="pw-new-confirm">Confirme a nova palavra-passe</label> <br>
                                <input <?php if (isset($_SESSION['errors']['pw-new-confirm'])): ?> class="input-error" <?php endif; ?> type="password" id="pw-new-confirm" name="pw-new-confirm" placeholder="Confirme a password" onclick="RemoveError(this)" required>
                                <?php if (isset($_SESSION['errors']['pw-new-confirm'])): ?>
                                    <p class="error mb-3" style="margin-top: -8px"><?= $_SESSION['errors']['pw-new-confirm'] ?></p>
                                <?php endif; ?>
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button id="btn-submit-password" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('pw-current'), document.getElementById('pw-new'), document.getElementById('pw-new-confirm'))">Alterar password</button>
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

    <!-- Modal - Apagar conta -->
    <div class="modal fade" id="deleteAccount" tabindex="-1" aria-labelledby="deleteAccount" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Eliminar a sua conta?</h1>
                        <p class="mb-0">Esta ação eliminará <b>todas</b> as informações relativas à sua conta.<br>Depois de continuar, não poderá voltar atrás. Pretende prosseguir?</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="<?= SITE_URL ?>/source/profile/delete.php" class="form" method="post" onsubmit="DisableButton(document.getElementById('btn-submit-delete'))">
                                <?php csrf('delete-account'); ?>
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button id="btn-submit-delete" type="submit" class="btn btn-danger d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this)">Sim, apagar a minha conta :(</button>
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