<?php
require_once '../../includes/setup.php';

if (isset($_GET['k']))
{
    require_once 'includes/db.php';
    $key = $_GET['k'];

    $options = array(
        'readonly' => true,
        'conditions' => array('post = ?', $key),
    );

    $count = Comments::count($options);

    $out = array(
        'count' => $count
    );

}
echo json_encode($out);
?>