<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';
$s = Session::CheckSession($db);

if ($s->IsAdmin()) {
    $body = file_get_contents('php://input');
    $data = json_decode($body);
    $data->id = trim($data->id);
    if (!is_numeric($data->id)) outError('wrong input', 400);
    $db->prepare('DELETE FROM comments WHERE idComments = ?')->execute($data['id']);
    echo json_encode(array('result'=>'success'));
} else {
    outError('access denied', 403);
}


?>