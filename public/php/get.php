<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();
if (isset($_GET['k']))
{
    $st = $db->q('SELECT idComments, Comment, Name, Time
                  FROM Comments
                  JOIN Users ON User = idUsers
                  WHERE post = ?'
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
