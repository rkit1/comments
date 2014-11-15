<h1>
<?php
require_once 'includes/db.php';
if (isset($_GET['k'], $_GET['email'])){
    $r = $db->q('UPDATE Users SET ConfirmKey = NULL WHERE email = :email AND ConfirmKey = :cKey'
               , array(':email' => $_GET['email'], ':cKey' => $_GET['k']));
    if ($r->columnCount() == 0) echo "Неправильный код активации";
    else echo "Электронный адрес подтвержден. Теперь вы можете оставлять комментарии.";
} else echo "Не указан код активации или электронный адрес";