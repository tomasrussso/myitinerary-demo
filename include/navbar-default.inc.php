<?php
    $url = substr($_SERVER['REQUEST_URI'], 1);
    $urlArray = explode('myitinerary.pt/', $url);
    array_shift($urlArray);
    $redirect = implode('/', $urlArray);
?>
<div class="container">
    <div class="row justify-content-between">
        <div class="col-auto logo align-self-center">
            <a href="<?= SITE_URL ?>"><img src="<?= SITE_URL ?>/images/logo-preto.svg" alt="MyItinerary" height="36px"></a>
        </div>
        <div class="col-auto options">
            <nav>
                <ul>
                    <li><a href="<?= SITE_URL ?>/signin" class="btn btn-link btn-link-black menu-md-hide">Registar-me</a></li>
                    <li><a href="<?= SITE_URL ?>/login?redirect=<?= htmlspecialchars(urlencode($redirect)) ?>" class="btn btn-primary">Iniciar sessão</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>