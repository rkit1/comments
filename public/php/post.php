<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';
if (isset($_GET['k']) && $s = Session::CheckSession($db))
{
    $key = $_GET['k'];

    $body = file_get_contents('php://input');
    $post = json_decode($body);

    if ($post == null) outError("No data given", 400);
    $post->comment = trim($post->comment);
    if (strlen($post->comment)<5) outError("Комментарий должен содержать, как минимум, 5 букв.", 400);

    $st = $db->prepare('INSERT INTO Comments (post, comment, user) VALUES (?, ?, ?)');
    $st->execute(array($key, $post->author, $s->user));
}