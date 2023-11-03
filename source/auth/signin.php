<?php
    require '../../include/_settings.inc.php';

    // Está autenticado? Então vai embora
    if (isset($_SESSION['auth']) || isset($_SESSION['auth-bo'])) {
        RedirectAndDie('signin');
    }

    // Verificação do CSRF
    if (!IsCsrfTokenValid('signin', $_POST['_token'])) {
        RegisterLog('CSRF not valid:' . $_SERVER['PHP_SELF'], true);
        RedirectAndDie('BACK');
    } 

    include SITE_DIR . '/include/_phpmailer.inc.php';

    // Validação se existem parametros POST
    if (!isset($_POST['name'], $_POST['email'], $_POST['username'], $_POST['password'], $_POST['password_confirm'])) {
        RedirectAndDie('signin');
    }

    $_SESSION['name'] = htmlspecialchars($_POST['name']);
    $_SESSION['email'] = htmlspecialchars($_POST['email']);
    $_SESSION['username'] = htmlspecialchars($_POST['username']);

    $errors = array();
    $hasErrors = false;

    // Sanitização dos valores
    if (strlen($_POST['name']) <= 0 || strlen($_POST['name']) > 50) {
        $hasErrors = true;
        $errors['name'] = 'O seu nome deve ser menor que 50 caracteres.'; 
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $hasErrors = true;
        $errors['email'] = 'O email que inseriu não é válido.'; 
    }

    if (strlen($_POST['password']) <= 5) {
        $hasErrors = true;
        $errors['password'] = 'A sua password deve ser maior que 5 caracteres.'; 
    }

    if (strlen($_POST['password']) >= 50) {
        $hasErrors = true;
        $errors['password'] = 'A sua password deve ser menor que 50 caracteres.'; 
    }

    if ($_POST['password'] != $_POST['password_confirm']) {
        $hasErrors = true;
        $errors['password_confirm'] = 'As passwords que inseriu não coincidem.'; 
    }

    if (!preg_match('/^([\w.](?!\.(com|net|html?|js|jpe?g|png|php)$)){6,}$/', $_POST['username'])) {
        $hasErrors = true;
        $errors['username'] = 'O seu username deve conter apenas letras, números, underscores e pontos, e ser maior que 5 caracteres.'; 
    }

    if ($hasErrors) {
        RedirectAndDie('signin', $errors);
    }

    // Tudo OK, siga para a frente: verifica se o email já está registado

    $countUsersWithSameEmail = $db->query('SELECT COUNT(id) as users FROM user WHERE email = ? AND (createdAt >= TIMESTAMPADD(DAY, -1, now()) OR status > 0)', $_POST['email'])->fetchAll();

    if($countUsersWithSameEmail[0]['users'] > 0) {
        RedirectAndDie('signin', [
            'email' => 'Já existe um utilizador registado com este email.'
        ]);
    }

    $countUsersWithSameUsername = $db->query('SELECT COUNT(id) as users FROM user WHERE username = ? AND (createdAt >= TIMESTAMPADD(DAY, -1, now()) OR status > 0)', $_POST['username'])->fetchAll();

    if($countUsersWithSameUsername[0]['users'] > 0) {
        RedirectAndDie('signin', [
            'username' => 'Já existe um utilizador registado com este username. Tente adicionar alguns números, como o seu ano de nascimento!'
        ]);
    }

    $verifyToken = GenerateToken(52);

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $passwordHashed = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $insert = $db->query('INSERT INTO user (name, email, username, password, verifyToken) VALUES (?, ?, ?, ?, ?)', $name, $email, $username, $passwordHashed, $verifyToken);

    if ($insert->affectedRows() <= 0) {
        RedirectAndDie('signin', [
            'password_confirm' => 'Ocorreu um erro inesperado a processar o seu pedido. Tente novamente.'
        ]);
    }

    $message = file_get_contents(SITE_DIR . '/files/templates/email-welcome-template.html'); 
    $message = str_replace('%name%', $name, $message); 
    $message = str_replace('%link%', SITE_URL . '/verify-account?token=' . $verifyToken, $message); 

    $mail = SendEmail($name, $email, 'Confirme a sua conta', $message);

    $_SESSION['signin-complete'] = true;

    if ($mail) RegisterLog('Email to signin:' . $email, true);
    RegisterLog('Signin:' . $email, true);
    RedirectTo('signin');
?>