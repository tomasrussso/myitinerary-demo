<div class="container">
    <div class="row justify-content-start">
        <div class="col-12 col-md-6 col-lg-3">
            <h5>A Minha Conta</h5>
            <?php if ((!isset($_SESSION['auth']) && !isset($_SESSION['auth-bo'])) || (!isset($_SESSION['auth']) && isset($_SESSION['auth-bo']))): ?>
            <ul>
                <li><a href="<?= SITE_URL ?>/login">Iniciar Sessão</a></li>
                <li><a href="<?= SITE_URL ?>/signin">Registar-me</a></li>
            </ul>
            <?php else: ?>
            <ul>
                <li><a href="<?= SITE_URL ?>/profile/<?= $_SESSION['auth']['user']['username'] ?>">Perfil</a></li>
                <li><a href="<?= SITE_URL ?>/profile/<?= $_SESSION['auth']['user']['username'] ?>#itineraries">Os Meus Itinerários</a></li>
                <li><a href="<?= SITE_URL ?>/profile/<?= $_SESSION['auth']['user']['username'] ?>#favourites">Favoritos</a></li>
            </ul>
            <?php endif; ?>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h5>Explorar</h5>
            <ul>
                <li><a href="<?= SITE_URL ?>/explore">Explorar Itinerários</a></li>
            </ul>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h5>Centro de Ajuda</h5>
            <ul>
                <li><a href="<?= SITE_URL ?>/help#about">O que é o MyItinerary?</a></li>
                <li><a href="<?= SITE_URL ?>/help#how-it-works">Como funciona o MyItinerary?</a></li>
                <li><a href="<?= SITE_URL ?>/help#faq">FAQ</a></li>
            </ul>
        </div>
    </div>
    <div class="row logo">
        <div class="col align-self-center">
            <p>&copy; <?= date('Y'); ?> MyItinerary &middot; <a href="<?= SITE_URL ?>/terms" class="link">Termos e Condições</a> &middot; <a href="<?= SITE_URL ?>/policies" class="link">Política de Privacidade e Cookies</a></p>
        </div>
    </div>
</div>