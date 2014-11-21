<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();
$s = Session::CheckSession($db);
if (is_null($s)) JSON::outError('Unauthorized', 403);

$post = JSON::ReadInput();
if (isset($post->name)){
    $n = trim($post->name);
    if (strlen($n) < 4) JSON::outError("Wrong input", 400);
    $r = $db->q('UPDATE Users SET Name = ? WHERE idUsers = ?', array(trim($post->name), $s->user));
    if ($r->rowCount() == 0) JSON::outError('Внутренняя ошибка', 500);
    echo json_encode(array('result' => 'success'));
} elseif (isset($post->password, $post->password1, $post->password2)){
    if ($post->password1 != $post->password2 || strlen($post->password1) < 4) JSON::outError("wrong input", 400);
    $r = $db->q('UPDATE Users
                 SET Password = :pass
                 WHERE idUsers = :id
                 AND :oldpass = Password'
        , array(':oldpass' => $post->password, ':pass'=> $post->password1, ':id' => $s->user));
    if ($r->rowCount() == 0) JSON::outError('Неверный пароль', 403);
    echo json_encode(array('result' => 'success'));
} else {
    JSON::outError('Wrong input', 400);
}