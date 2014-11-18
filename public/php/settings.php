<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';
require_once 'includes/Session.php';

$s = Session::CheckSession($db);
if (is_null($s)) outError('Unauthorized', 403);

$body = file_get_contents('php://input');
$post = json_decode($body);

if ($post == null) outError("No data given", 400);

if (isset($post->name)){
    $n = trim($post->name);
    if (strlen($n) < 4) outError("Wrong input", 400);
    $r = $db->q('UPDATE Users SET Name = ? WHERE idUsers = ?', array(trim($post->name), $s->user));
    if ($r->rowCount() == 0) outError('Внутренняя ошибка', 500);
    echo json_encode(array('result' => 'success'));
} elseif (isset($post->password, $post->password1, $post->password2)){
    if ($post->password1 != $post->password2 || strlen($post->password1) < 4) outError("wrong input", 400);
    $r = $db->q('SELECT true FROM Users WHERE idUsers = ? AND md5(concat(Salt, ?)) = Password'
               , array($s->user, $post->password));
    if ($r->rowCount() == 0) outError('Неверный пароль', 403);
    $r = $db->q('UPDATE Users Set Password = md5(concat(Salt, ?)) WHERE idUsers = ?', array($s->user));
    if ($r->rowCount() == 0) outError('Внутренняя ошибка', 500);
    echo json_encode(array('result' => 'success'));
} else {
    outError('Wrong input', 400);
}