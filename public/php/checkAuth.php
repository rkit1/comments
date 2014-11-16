<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';
require_once 'includes/Session.php';
$db->beginTransaction();
$s = Session::CheckSession($db);
$st = $db->prepare('SELECT Name FROM Users WHERE idUsers = ?');
$st->execute(array($s->user));
if(!$n = $st->fetch()) outError("unauthorized", 403);
$out = array('result'=>'success', 'name'=>$n[0]);
if (!$s->IsConfirmed()) $out['emailUnconfirmed'] = true;
$db->commit();
echo (json_encode($out));