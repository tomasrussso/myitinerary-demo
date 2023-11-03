<?php
    require_once 'include/_settings.inc.php';
    RegisterLog('Visit:Help');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajuda - MyItinerary</title>
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

    <div class="help">
        <div class="container">
            <div class="row title">
                <h1>Centro de Ajuda</h1>
                <h2>Encontra aqui toda a ajuda que precisa</h2>
            </div>
            <div class="row options justify-content-start">
                <div class="col-12 col-md-4">
                    <a href="#about" class="link">
                        <div class="card green">
                            <div class="card-body d-flex">
                                <div class="align-self-end">    
                                    <h3>O que é o MyItinerary?</h3>
                                    <p>Saiba mais sobre esta nova plataforma.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <a href="#how-it-works" class="link">
                        <div class="card blue">
                            <div class="card-body d-flex">
                                <div class="align-self-end">    
                                    <h3>Como funciona o&nbsp;MyItinerary?</h3>
                                    <p>Aprenda a como criar o seu primeiro itinerário!</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <a href="#faq" class="link">
                        <div class="card">
                            <div class="card-body d-flex">
                                <div class="align-self-end">    
                                    <h3>FAQ</h3>
                                    <p>Consulte algumas perguntas frequentes.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <p class="mb-4">AVISO: O conteúdo desta página encontra-se em desenvolvimento. Pedimos desculpa pelo incómodo.</p>
            <div class="row about" id="about">
                <h3>O que é o MyItinerary?</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt, sapiente fugit alias, deserunt nisi debitis aspernatur ullam maiores voluptatem aut voluptate, optio voluptatibus! Quidem provident excepturi iusto dolore aspernatur libero optio a quibusdam, nihil aliquid! Debitis earum voluptate reiciendis, alias saepe distinctio qui fugit perspiciatis nisi, fuga inventore quam illum. Sint at odio aliquid, non rem necessitatibus a consectetur! Ut sed qui assumenda quae quasi excepturi esse, necessitatibus dolorum eligendi, facere facilis commodi quas officia molestiae perferendis illum? Minus dicta aliquam nostrum veniam sit iste eum adipisci consequatur corporis. Aspernatur hic illum tenetur obcaecati corporis vitae dolorem error eum beatae?</p>
            </div>
            <div class="row how-it-works" id="how-it-works">
                <h3>Como funciona o MyItinerary?</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt, sapiente fugit alias, deserunt nisi debitis aspernatur ullam maiores voluptatem aut voluptate, optio voluptatibus! Quidem provident excepturi iusto dolore aspernatur libero optio a quibusdam, nihil aliquid! Debitis earum voluptate reiciendis, alias saepe distinctio qui fugit perspiciatis nisi, fuga inventore quam illum. Sint at odio aliquid, non rem necessitatibus a consectetur! Ut sed qui assumenda quae quasi excepturi esse, necessitatibus dolorum eligendi, facere facilis commodi quas officia molestiae perferendis illum? Minus dicta aliquam nostrum veniam sit iste eum adipisci consequatur corporis. Aspernatur hic illum tenetur obcaecati corporis vitae dolorem error eum beatae?</p>
            </div>
            <div class="row faq" id="faq">
                <h3>FAQ - Perguntas Frequentes</h3>
                <h4 class="question" id="question"><i class="fas fa-chevron-down close" id="icon"></i><a onclick="ChangeArrow(this)" class="link question-link" data-bs-toggle="collapse" href="#answer-1" aria-controls="answer-1" role="button" aria-expanded="false">Aparece um erro quando troco a minha foto de perfil. Porquê?</a></h4>
                <div class="collapse" id="answer-1">
                    <p>O MyItinerary limita o tamanho e o tipo de ficheiros que podem ser utilizados como foto de perfil.<br>A sua imagem deve seguir as seguintes regras:</p>
                    <p>- Ter tamanho menor que 10 MB;<br>- Ser uma imagem do tipo JPEG, GIF ou PNG.</p>
                    <p>Para resolver o problema, tente cortar a sua fotografia ou convertê-la para um formato válido.</p>
                </div>
                <h4 class="question" id="question"><i class="fas fa-chevron-down close" id="icon"></i><a onclick="ChangeArrow(this)" class="link question-link" data-bs-toggle="collapse" href="#answer-2" aria-controls="answer-2" role="button" aria-expanded="false">A minha foto de perfil/capa fica esticada ou não mantém a sua proporção original. O que fazer?</a></h4>
                <div class="collapse" id="answer-2">
                    <p>As suas fotos de perfil e de capa são ajustadas automaticamente ao espaço disponível no ecrã.</p>
                    <p>Se a fotografia que escolheu ficou com um formato diferente do desejado, tente cortá-la para que fique no formato de um quadrado, no caso da foto de perfil, ou de um retângulo, no caso de ser a foto de capa.</p>
                </div>
                <h4 class="question" id="question"><i class="fas fa-chevron-down close" id="icon"></i><a onclick="ChangeArrow(this)" class="link question-link" data-bs-toggle="collapse" href="#answer-3" aria-controls="answer-3" role="button" aria-expanded="false">Alterei a minha foto de perfil mas ela não mudou. O que aconteceu?</a></h4>
                <div class="collapse" id="answer-3">
                    <p>Se a sua foto de perfil não mudar automaticamente, tente recarregar a página.<br>Se ainda assim ela não trocar, termine sessão e volte a iniciar.</p>
                </div>
                <h4 class="question" id="question"><i class="fas fa-chevron-down close" id="icon"></i><a onclick="ChangeArrow(this)" class="link question-link" data-bs-toggle="collapse" href="#answer-4" aria-controls="answer-4" role="button" aria-expanded="false">Encontrei uma problema/bug no site e gostaria de o reportar. Como proceder?</a></h4>
                <div class="collapse" id="answer-4">
                    <p>Antes de mais gostaríamos de agradecer a sua disponibilidade para melhorar o MyItinerary.<br>Para reportar um problema, pedimos que envie um email para <a href="mailto:geral.myitinerary@gmail.com" class="link">geral.myitinerary@gmail.com</a> e, se possível, com a seguinte informação:</p>
                    <p>- Descrição do problema;<br>- Browser e sistema operativo que utilizava;<br>- Data e hora em que aconteceu a falha (se aplicável);<br>- Outros detalhes relevantes, como capturas de ecrã, página em que se encontrava, etc.</p>
                    <p>Iremos analisar o problema reportado, e dar-lhe-emos feedback o mais depressa possível.</p>
                </div>
                <h4 class="question" id="question"><i class="fas fa-chevron-down close" id="icon"></i><a onclick="ChangeArrow(this)" class="link question-link" data-bs-toggle="collapse" href="#answer-5" aria-controls="answer-5" role="button" aria-expanded="false">Como devo contactar a equipa do MyItinerary?</a></h4>
                <div class="collapse" id="answer-5">
                    <p>Para nos contactar, pedimos que envie um email para <a href="mailto:geral.myitinerary@gmail.com" class="link">geral.myitinerary@gmail.com</a>, expondo com clareza o motivo do seu contacto.<br>Tentaremos responder o mais depressa possível.</p>
                </div>
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