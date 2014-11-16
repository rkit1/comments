<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';
require_once 'includes/Session.php';
if (isset($_GET['k']))
{
    $s = Session::CheckSession($db);
    if (is_null($s)) outError('Unauthorized', 403);
    if(!$s->IsConfirmed()) outError("Сперва подтвердите e-mail", 403);

    $key = $_GET['k'];

    $body = file_get_contents('php://input');
    $post = json_decode($body);

    if ($post == null) outError("No data given", 400);
    $post->comment = trim($post->comment);
    if (strlen($post->comment)<5) outError("Комментарий должен содержать, как минимум, 5 букв.", 400);

    $db->q('INSERT INTO Comments (post, comment, user) VALUES (?, ?, ?)'
          , array($key, $post->comment, $s->user));

    echo(json_encode(array('result'=>'success')));
}