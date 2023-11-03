<?php if(isset($_SESSION['auth'])): ?>
<div class="container">
    <div class="row justify-content-between">
        <div class="col-auto logo align-self-center">
            <a href="<?= SITE_URL ?>"><img src="<?= SITE_URL ?>/images/logo-preto.svg" alt="MyItinerary" height="36px"></a>
        </div>
        <div class="col-auto options">
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
        </div>
    </div>
</div>
<?php else: ?>
<div class="container">
    <div class="row justify-content-between">
        <div class="col-auto logo align-self-center">
            <a href="<?= SITE_URL ?>"><img src="<?= SITE_URL ?>/images/logo-preto.svg" alt="MyItinerary" height="36px"></a>
        </div>
        <div class="col-auto options">
            <nav>
                <ul>
                    <li class="d-flex align-items-center">
                        <a href="<?= BO_URL ?>" class="btn btn-link btn-link-black" style="padding-right: 0">Voltar a MIGest</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php endif; ?>