<?php
require_once '../../includes/setup.php';
require_once 'includes/db.php';
require_once 'includes/mail.php';

$data = json_decode(file_get_contents('php://input'));
if ($data == null || !isset($data->email, $data->name, $data->password)) outError("no data", 400);

$db->beginTransaction();
if($db->q('SELECT idUsers FROM Users WHERE Email = ?', array($data->email))->rowCount() == 1)
    outError("Такой e-mail уже зарегистрирован. Попробуйте восстановить пароль", 400);
$key = uniqid();
$db->q('INSERT INTO Users (Email, Name, Role, Password, Salt, ConfirmKey)
        VALUES (:email, :name, 0, md5(concat(:salt, :password)), :salt, :cKey)'
       , array( ':email'=>$data->email, ':name'=>$data->name, ':password'=>$data->password
              , ':salt'=>uniqid("", true), ':cKey' => $key));
confirmMail($data->email, $data->name, $key);
$db->commit();
echo json_encode(array('result' => 'success'));