<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{

    private static $HOST = 'smtp.gmail.com';
    private static $LOGIN = 'lifecode.smtp@gmail.com';
    private static $PASS = 'flacam81';
    private static $PORT = 587;
    private static $SMTPSecure = 'tls';
    private static $SMTPAuth = true;
    private static $templateMail = BASEURL . 'assets/mail/template.html';

    public static function send(String $toEmail, String $toName, String $subject, String $message, String $replyTo = null, String $replyToName = null, $attachments = null, $copyTo = null)
    {
        if (ENV == 'local') {
            $toEmail = 'rodrigo.werlich@gmail.com';
            $copyTo = ['dwerlich21@gmail.com'];
            $subject = "Teste - {$subject}";
        }

        $templateMail = file_get_contents(self::$templateMail);
        $message = str_replace('HTMLMessage', $message, $templateMail);

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0; //2 para modo debug
        $mail->Host = self::$HOST;
        $mail->Port = self::$PORT;
        $mail->SMTPSecure = self::$SMTPSecure;
        $mail->SMTPAuth = self::$SMTPAuth;
        $mail->Username = self::$LOGIN;
        $mail->Password = self::$PASS;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->setFrom(self::$LOGIN, 'Life Code - SISGG');
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->IsHTML(true);
        if ($replyTo !== null) {
            $mail->addReplyTo(trim($replyTo), $replyToName);
        }
        $mail->addAddress(trim($toEmail), $toName);
        if ($copyTo) {
            for ($i = 0; $i < sizeof($copyTo); $i++) {
                $mail->addCC($copyTo[$i]);
            }
        }
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment[0], $attachment[1]);
            }
        }
        if (!$mail->send()) {
            throw new \Exception($mail->ErrorInfo);
        }
    }


}