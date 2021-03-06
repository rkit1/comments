<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();

if (isset($_GET['k'], $_GET['email'])){
    header('Content-type: text/html; charset=utf8');
    echo '<h1>';
    $db->beginTransaction();
    $r = $db->q('SELECT NewPassword FROM ResetPassword JOIN Users ON User = idUsers WHERE Email = :email',
        array(':email'=>$_GET['email']))->fetch();
    if (!$r) die ('Указаны некорректные данные');
    $r = $db->q('UPDATE Users SET Password = :newPass WHERE Email = :email',
        array(':newPass'=>$r[0], ':email'=>$_GET['email']));
    if ($r->rowCount() != 1) die ('Внутренняя ошибка');
    echo 'Пароль сменен успешно';
    $db->commit();
} else {
    JSON::Setup();
    $post = JSON::ReadInput();
    $db->beginTransaction();
    $id = $db->q('SELECT idUsers, Name, ConfirmKey FROM Users WHERE Email = :email'
        , array(':email' => $post->email))
        ->fetch();
    if ($id[2]) JSON::outError("Указан неверный адрес электронной почты", 400);
    $key = uniqid('', true);
    $db->q('INSERT INTO ResetPassword (ConfirmKey, User, NewPassword, Time) VALUES (:k, :id, :pass, NOW())',
        array(':k'=>$key, ':id'=>$id[0], ':pass'=>$post->password));
    Mail::resetMail($post->email, $id[1], $key);
    $db->commit();
    echo json_encode(array('result'=>'success'));
}
