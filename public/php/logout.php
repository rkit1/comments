<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

$s = Session::CheckSession($db);
$s->Logout();

echo (json_encode(array('result'=>'success')));