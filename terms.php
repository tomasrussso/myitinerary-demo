<?php
    require_once 'include/_settings.inc.php';
    RegisterLog('Visit:Terms');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Termos e Condições - MyItinerary</title>
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
                <h1>Termos e Condições</h1>
                <h2>Saiba mais sobre os Termos e Condições de utilização do MyItinerary</h2>
            </div>
            <div class="row section" id="terms">
                <h5>1. Terms</h5> <p>By accessing the website at <a href="<?= SITE_URL ?>" class="link">MyItinerary</a> you are agreeing to be bound by these terms of service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this website are protected by applicable copyright and trademark law.</p> <h5>2. Use License</h5> <ol> <li> Permission is granted to temporarily download one copy of the materials (information or software) on MyItinerary's website for personal, non-commercial transitory viewing only. This is the grant of a licence, not a transfer of title, and under this licence you may not: <ol> <li>modify or copy the materials;</li> <li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li> <li>attempt to decompile or reverse engineer any software contained on MyItinerary website;</li> <li>remove any copyright or other proprietary notations from the materials; or</li> <li>transfer the materials to another person or 'mirror' the materials on any other server.</li> </ol> </li> <li>This licence shall automatically terminate if you violate any of these restrictions and may be terminated by MyItinerary at any time. Upon terminating your viewing of these materials or upon the termination of this licence, you must destroy any downloaded materials in your possession whether in electronic or printed format. </li> </ol> <h5>3. Disclaimer</h5> <ol> <li> The materials on MyItinerary's website are provided on an 'as is' basis. MyItinerary makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</li> <li> Further, MyItinerary does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.</li> </ol> <h5>4. Limitations</h5> <p>In no event shall MyItinerary or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on MyItinerary's website, even if MyItinerary or a MyItinerary authorised representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p> <h5>5. Accuracy of materials</h5> <p>The materials appearing on MyItinerary's website could include technical, typographical, or photographic errors. MyItinerary does not warrant that any of the materials on its website are accurate, complete or current. MyItinerary may make changes to the materials contained on its website at any time without notice. However MyItinerary does not make any commitment to update the materials.</p> <h5>6. Links</h5> <p>MyItinerary has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by MyItinerary of the site. Use of any such linked website is at the user's own risk.</p> <h5>7. Modifications</h5> <p>MyItinerary may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p> <h5>8. Governing Law</h5> <p>These terms and conditions are governed by and construed in accordance with the laws of MyItinerary and you irrevocably submit to the exclusive jurisdiction of the courts in that State or location.</p>
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