<?php
require_once '../../includes/setup.php';
if (isset($_GET['k']))
{
    $body = file_get_contents('php://input');
    $data = json_decode($body);
    require_once 'includes/db.php';
    $s = Session::CheckSession($db);

    $data->id = trim($data->id);
    if (!is_numeric($data->id)) outError('wrong input', 400);

    if ($s->IsAdmin()) {
        $db->prepare('DELETE FROM comments WHERE idComments = ?')->execute($data['id']);
        echo json_encode(array('result'=>'success'));
    } else {
        outError('access denied', 403);
    }
}

?>