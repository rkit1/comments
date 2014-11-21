<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
header('Content-type: text/html; charset=utf8');
echo '<h1>';

if (isset($_GET['k'], $_GET['email'])){
    $r = $db->q('UPDATE Users SET ConfirmKey = NULL WHERE email = :email AND ConfirmKey = :cKey'
               , array(':email' => $_GET['email'], ':cKey' => $_GET['k']));
    if ($r->rowCount() == 0) echo "Неправильный код активации";
    else echo "Электронный адрес подтвержден. Теперь вы можете оставлять комментарии.";
} else echo "Не указан код активации или электронный адрес";