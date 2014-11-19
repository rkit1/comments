<?php
require_once 'includes/db.php';
require_once 'includes/mail.php';
if(isset($_POST['email'], $_POST['password'])){
    require_once '../../includes/setup.php';
    $db->beginTransaction();
    $id = $db->q('SELECT idUsers, Name, ConfirmKey FROM Users WHERE Email = :email'
        , array(':email' => $_POST['email']))
        ->fetch();
    if ($id[3]) outError("Указан неверный адрес электронной почты", 400);
    $key = uniqid('', true);
    $db->q('INSERT INTO ResetPassword (ConfirmKey, User, NewPassword) VALUES (:k, :id, :pass)',
            array(':k'=>$key, ':ud'=>$id[0], ':pass'=>$_POST['password']));
    resetMail($_POST['email'], $id[1], $key);
    $db->commit();
    echo json_encode(array('result'=>'success'));
} elseif (isset($_GET['k'], $_GET['email'])){
    header('Content-type: text/html; charset=utf8');
    echo '<h1>';
    $db->beginTransaction();
    $r = $db->q('SELECT NewPassword FROM ResetPassword JOIN Users WHERE Email = :email',
        array(':email'=>$_GET['email']))->fetch();
    if (!$r) die ('Указаны некорректные данные');
    $r = $db->q('UPDATE Users SET Password = md5(concat(Salt, :newPass)) WHERE Email = :email',
        array(':newPass'=>$r[0], ':email'=>$_GET['email']));
    if ($r->columnCount() != 1) die ('Внутренняя ошибка');
    echo 'Пароль сменен успешно';
}
