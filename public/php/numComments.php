<?php
require_once '../../includes/setup.php';

if (isset($_GET['hrefs']))
{
    require_once 'includes/db.php';
    $hrefs = $_GET['hrefs'];

    $options = array(
        'readonly' => true,
        'conditions' => array('post in ?', $hrefs),
        'select' => 'count(post) as count, post'
    );

    $rows = Comments::find('all', $options);

    $out = array();
    foreach($rows as $row)
        $out[] = array(
            'k' => $row->post,
            'count' => $row->count
        );


}
echo json_encode($out);
?>