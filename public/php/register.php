<?php
require_once '../../includes/setup.php';
$data = json_decode(file_get_contents('php://input'));
if ($data == null || !isset($data->email, $data->name, $data->password)) outError("no data", 400);
require_once 'includes/db.php';

$db->beginTransaction();
if($db->q('SELECT idUsers FROM Users WHERE Email = ?', array($data->email))->columnCount() == 1)
    outError("Такой e-mail уже зарегистрирован. Попробуйте восстановить пароль", 400);
$db->q('INSERT INTO Users (Email, Name, Role, Password, Salt)
        VALUES (:email, :name, -1, md5(:salt + :password), :salt)'
       , array(':email'=>$data->email, ':name'=>$data->name, ':password'=>$data->password, ':salt'=>uniqid()));
$db->commit();
echo json_encode(array('result' => 'success'));