<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';

if (isset($_GET['k']))
{
    $st = $db->q('SELECT idComments, Comment, Name, Time
                  FROM Comments
                  JOIN users WHERE post = ?'
                , array($_GET['k']));
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
