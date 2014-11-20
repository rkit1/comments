<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

JSON::Setup();
$s = Session::CheckSession($db);
$s->Logout();