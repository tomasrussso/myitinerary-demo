<?php
// ---
// 
// O phpmailer.inc.php faz o seguinte:
//      - oferece a possiblidade do envio de emails atraves da ferramenta PHPMAILER
// 
// Este ficheiro deve ser incluído nas páginas em que este for necessário, 
// depois de ser requirido o settings.inc.php.
// 
// ---

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require SITE_DIR . '/vendor/autoload.php';

function SendEmail($recipiantName, $recipiantEmail, $subject, $bodyHTML) {
    try {
        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = DEBUG_OPTIONS;
        $mail->isSMTP();
        $mail->Host = EMAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->CharSet = 'UTF-8';
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = EMAIL_PORT;   
        $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
    
        //Recipients
        $mail->setFrom(EMAIL_USERNAME, EMAIL_NAME);
        $mail->addAddress($recipiantEmail, $recipiantName);
        $mail->addReplyTo(EMAIL_USERNAME, EMAIL_NAME);
    
        // Content
        $mail->Subject = $subject; 
        $mail->MsgHTML($bodyHTML);
        $mail->AltBody = strip_tags($bodyHTML);
        $mail->isHTML(true);
    
        $mail->send();
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}