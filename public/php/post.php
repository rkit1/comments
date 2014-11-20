<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

if (isset($_GET['k']))
{
    $s = Session::CheckSession($db);
    if (is_null($s)) JSON::outError('Unauthorized', 403);
    if(!$s->IsConfirmed()) JSON::outError("Сперва подтвердите e-mail", 403);

    $key = $_GET['k'];

    $post = JSON::ReadInput();
    $post->comment = trim($post->comment);
    if (strlen($post->comment)<5) JSON::outError("Комментарий должен содержать, как минимум, 5 букв.", 400);

    $db->q('INSERT INTO Comments (post, comment, user) VALUES (?, ?, ?)'
          , array($key, $post->comment, $s->user));

    echo(json_encode(array('result'=>'success')));
}