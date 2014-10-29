<?php
require_once '../../includes/setup.php';
if (isset($_GET['k'], $_GET['id']) && is_numeric($_GET['id']))
{
    require_once 'includes/db.php';
    if (Admins::exists($_COOKIE['adminToken']))
    {
        Comments::delete_all(array('conditions' => array('idComments = ?', $_GET['id']) ));
        $out['result'] = 'success';
    } else {
        $out['result'] = 'error';
        $out['message'] = 'access denied';
        header(':', true, 403);
    }
} else {
    $out ['result'] = 'error';
    $out ['message'] = 'wrong input';
    header(':', true, 400);
}

echo json_encode($out);
//echo Comments::table()->last_sql;
?>