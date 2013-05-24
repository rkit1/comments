<?php
require_once '../../includes/setup.php';

if (isset($_GET['k']))
{
    require_once 'includes/db.php';

    if (isset($_GET['last']) && is_numeric($_GET['last']))
        $t = $_GET['last'];
    else $t = 0;
    //$time = new ActiveRecord\DateTime();
    //$time->setTimestamp($t);
    $key = $_GET['k'];

    $options = array(
        'readonly' => true,
        'conditions' => array('post = ?', $key),
        'select' => 'idComments, comment, author, time'
    );

    $comments = Comments::find('all', $options);

    foreach($comments as $comment)
    {
        $out[] = array(
            'idComments' => $comment->idcomments,
            'comment' => $comment->comment,
            'author' => $comment->author,
            'time' => $comment->time->format('U')
        );
    };
}

echo json_encode($out);
//echo Comments::table()->last_sql;
?>
