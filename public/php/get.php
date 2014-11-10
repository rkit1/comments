<?php
require_once '../../includes/setup.php';

if (isset($_GET['k']))
{
    require_once 'includes/db.php';
    $st = $db->prepare('SELECT idComments, comment, Name, time FROM Comments JOIN users WHERE post = ?');
    $st->execute(array($_GET['k']));
    $out = array();
    while($row = $st->fetch())
    {
        $out[] = array(
            'idComments' => $row[0],
            'comment' => $row[1],
            'author' => $row[2],
            'time' => $row[3]
        );
    };

    echo json_encode($out);
}
?>
