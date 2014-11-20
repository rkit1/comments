<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

$db->beginTransaction();
$s = Session::CheckSession($db);
$st = $db->prepare('SELECT Name FROM Users WHERE idUsers = ?');
$st->execute(array($s->user));
if(!$n = $st->fetch()) JSON::outError("unauthorized", 403);
$out = array('result'=>'success', 'name'=>$n[0]);
if (!$s->IsConfirmed()) $out['emailUnconfirmed'] = true;
$db->commit();
echo (json_encode($out));