<?php
require_once '../../includes/setup.php';

if (isset($_GET['k']))
{
    $key = $_GET['k'];
    require_once 'includes/db.php';
    $body = file_get_contents('php://input');
    $post = json_decode($body);


    if ($post == null) outError("No data given");
    $post->author = trim($post->author);
    if (strlen($post->author)==0) outError("Не указано имя");
    $post->comment = trim($post->comment);
    if (strlen($post->comment)<5) outError("Комментарий должен содержать, как минимум, 5 букв.");

    $arr = array(
        'author' => $post->author,
        'comment' => $post->comment,
        'post' => $key
    );
    if (Comments::create($arr)->save())
        $out['result'] = 'ok';
}
echo json_encode($out);
