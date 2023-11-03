<?php
    require_once 'include/_settings.inc.php';
    RegisterLog('Visit:Policies');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Política de Privacidade e Cookies - MyItinerary</title>
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

    <div class="general">
        <div class="container">
            <div class="row title">
                <h1>Política de Privacidade e Cookies</h1>
                <h2>Conheça a Política de Privacidade e Cookies do MyItinerary</h2>
            </div>
            <div class="row section" id="privacy">
                <h3>Política de Privacidade</h3>
                <p>Your privacy is important to us. It is MyItinerary's policy to respect your privacy regarding any information we may collect from you across our website, <a href="<?= SITE_URL ?>" class="link">MyItinerary</a>, and other sites we own and operate.</p> <p> We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used. </p> <p> We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we’ll protect within commercially acceptable means to prevent loss and theft, as well as unauthorised access, disclosure, copying, use or modification.</p> <p> We don’t share any personally identifying information publicly or with third-parties, except when required to by law. </p> <p> Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies. </p> <p> You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with some of your desired services. </p> <p>Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information. If you have any questions about how we handle user data and personal information, feel free to contact us. </p>
            </div>
            <div class="row section" id="cookies">
                <h3>Política de Cookies</h3>
                <p>This is the Cookie Policy for <a href="<?= SITE_URL ?>" class="link">MyItinerary</a>.</p> <h5>What Are Cookies</h5> <p> As is common practice with almost all professional websites this site uses cookies, which are tiny files that are downloaded to your computer, to improve your experience. This page describes what information they gather, how we use it and why we sometimes need to store these cookies. We will also share how you can prevent these cookies from being stored however this may downgrade or 'break' certain elements of the sites functionality. </p> <h5>How We Use Cookies</h5> <p> We use cookies for a variety of reasons detailed below. Unfortunately in most cases there are no industry standard options for disabling cookies without completely disabling the functionality and features they add to this site. It is recommended that you leave on all cookies if you are not sure whether you need them or not in case they are used to provide a service that you use. </p> <h5>Disabling Cookies</h5> <p> You can prevent the setting of cookies by adjusting the settings on your browser (see your browser Help for how to do this). Be aware that disabling cookies will affect the functionality of this and many other websites that you visit. Disabling cookies will usually result in also disabling certain functionality and features of the this site. Therefore it is recommended that you do not disable cookies. </p>
            </div>
            <div class="row section">
                <p> This policy is effective as of June 2021. </p><p>If you have any questions about our policies, please contact us by sending an email to <a href="mailto:geral.myitinerary@gmail.com" class="link">geral.myitinerary@gmail.com</a></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>
    
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