<?php
require_once '../../includes/setup.php';
$auth = json_decode(file_get_contents('php://input'));
if ($auth == null || !isset($auth->email, $auth->password)) outError("No input", 400);
require_once 'includes/db.php';
require_once 'includes/Session.php';

$db->beginTransaction();
$st = $db->prepare('SELECT idUsers, Name FROM Users WHERE Email = ? AND Password = md5(concat(Salt, ?))');
$st->execute(array($auth->email, $auth->password));
if (!$user = $st->fetch()) outError ("Неправильно введено имя пользователя или пароль", 403);

$s = Session::NewSession($user[0], $auth->remember, $db);
$out = array('result'=>'success', 'name' => $user[1]);
if (!$s->IsConfirmed()) $out['emailUnconfirmed'] = true;
$db->commit();

echo json_encode($out);
?>