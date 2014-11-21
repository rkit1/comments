<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

$s = Session::CheckSession($db);
if ($s->IsAdmin()) {
    if (!is_numeric($_GET['id'])) JSON::outError('wrong input', 400);
    $r = $db->q('DELETE FROM Comments WHERE idComments = ?', array($_GET['id']))->fetch();
    if (!$r) JSON::outError ('Такого комментария не существует', 500);
    echo json_encode(array('result'=>'success'));
} else {
    JSON::outError('access denied', 403);
}
?>