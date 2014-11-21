<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

$db->beginTransaction();
$s = Session::CheckSession($db);
if (!$s) JSON::outError("unauthorized", 403);

$n = $db->q('SELECT Name, RoleName FROM Users JOIN Roles ON Role = idRoles
             WHERE idUsers = ?', array($s->user))->fetch();

$out = array('result'=>'success', 'name'=>$n[0]);
if ($n[1] == 'Admin') $out['isAdmin'] = true;
$db->commit();
echo (json_encode($out));