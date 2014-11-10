<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';
require_once 'includes/Session.php';

$s = Session::CheckSession($db);
$s->Logout();