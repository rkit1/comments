<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();
$auth = json_decode(file_get_contents('php://input'));
if ($auth == null || !isset($auth->email, $auth->password)) JSON::outError("No input", 400);

$db->beginTransaction();
$user = $db->q('SELECT idUsers, Name, ConfirmKey IS NULL, RoleName  FROM Users JOIN Roles ON Role = idRoles
                WHERE Email = ? AND Password = md5(concat(Salt, ?))',
                array($auth->email, $auth->password))->fetch();
if (!$user) JSON::outError("Неправильно введено имя пользователя или пароль", 403);
if (!$user[2]) JSON::outError('Сперва подтвердите адрес электронной почты', 403);
Session::NewSession($user[0], $auth->remember, $db);
$out = array('result'=>'success', 'name' => $user[1]);
if ($user[3] == 'Admin') $out['isAdmin'] = true;
$db->commit();

echo json_encode($out);
?>