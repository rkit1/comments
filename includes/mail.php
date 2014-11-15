<?php
function confirmMail($email, $name, $key){
    $subject = "Регистрация на сайте Клуба ВИИЯ";
    $message =
        "Ваш адрес электронной почты {$email} был использован для регистрации на сайте клуба ВИИЯ КА http://clubvi.ru.
Если вы этого не делали, то просто проигнорируйте это сообщение.
Если вы подтверждаете, что это ваш адрес электронной почты, то перейдите по следующей ссылке.
http://clubvi.ru/apps/comments/php/confirmMail?k={$key}";
    if (!mail($email, $subject, $message, "From: webmaster@clubvi.ru\r\n"))
        throw new Exception("Ошибка отправки email");
}