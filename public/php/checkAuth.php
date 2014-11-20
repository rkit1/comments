<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

$db->beginTransaction();
$s = Session::CheckSession($db);
$n = $db->q('SELECT Name, RoleName FROM Users JOIN Roles ON Role = idRoles
             WHERE idUsers = ?', array($s->user))->fetch();
if(!$n) JSON::outError("unauthorized", 403);

$out = array('result'=>'success', 'name'=>$n[0]);
if ($n[1] == 'Admin') $out['isAdmin'] = true;
$db->commit();
echo (json_encode($out));