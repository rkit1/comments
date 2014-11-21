<?php
namespace Comments;

error_reporting(E_ALL);
require_once 'vendor/autoload.php';

class Config{
    static function db(){
        return new MyPDO('mysql::host=localhost;dbname=Comments',
            'comments', '01a0f77ff30035771f9e38fb2477d34d',
            array('charset'=>'utf8'));
    }
}


class Mail {
    public static function confirmMail($email, $name, $key){
        $keyu = urlencode($key);
        $emailu = urlencode($email);

        $mail = new \PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->Port = 25;
        $mail->From = 'webmaster@clubvi.ru';
        $mail->FromName = 'Клуб ВИИЯ';
        $mail->addAddress($email, $name);
        $mail->Subject = 'Регистрация на сайте Клуба ВИИЯ';
        $mail->Body = "Ваш адрес электронной почты {$email} был использован для регистрации на сайте клуба ВИИЯ КА http://clubvi.ru .
Если вы этого не делали, то просто проигнорируйте это сообщение.
Если вы подтверждаете, что это ваш адрес электронной почты, то перейдите по следующей ссылке:
http://clubvi.ru/apps/comments/php/confirmMail.php?k={$keyu}&email={$emailu}";
        if(!$mail->send()) throw new \Exception("Ошибка отправки email");
    }
    public static function resetMail($email, $name, $key){
        $keyu = urlencode($key);
        $emailu = urlencode($email);

        $mail = new \PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->Port = 25;
        $mail->From = 'webmaster@clubvi.ru';
        $mail->FromName = 'Клуб ВИИЯ';
        $mail->addAddress($email, $name);
        $mail->Subject = 'Восстановление пароля на сайте Клуба ВИИЯ';
        $mail->Body = "Для вашей учетной записи {$email} была запрошена смна пароля.
Если вы этого не делали, то просто проигнорируйте это сообщение.
Для того, чтобы потвердить смену, перейдите по ссылке
http://clubvi.ru/apps/comments/php/resetPassword.php?k={$keyu}&email={$emailu}";
        if(!$mail->send()) throw new \Exception("Ошибка отправки email");
    }
}