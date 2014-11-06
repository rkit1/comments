<?php
require_once '../../includes/setup.php';
$auth = json_decode(file_get_contents('php://input'));
if ($post == null || !isset($post->email, $post->password)) outError("No input", 400);
require_once 'includes/db.php';

$db->beginTransaction();
// auth
$st = $db->prepare('SELECT idUsers FROM Users WHERE Email = ? AND Password = md5(Salt + ?)');
$st->execute(array($post->email, $post->password));
if (!$user = $st->fetch(PDO::FETCH_NUM)) outError ("Неправильно введено имя пользователя или пароль", 403);

Session::NewSession($user[0], $post->remember, $db);
$db->commit();


echo json_encode(array('result' => 'success'));
?>