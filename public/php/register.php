<?php
namespace Comments;
require_once '../../Config.php';
$db = Config::db();
JSON::Setup();

$data = JSON::ReadInput();
if (!isset($data->email, $data->name, $data->password)) JSON::outError("no data", 400);

$db->beginTransaction();
if($db->q('SELECT idUsers FROM Users WHERE Email = ?', array($data->email))->rowCount() == 1)
    JSON::outError("Такой e-mail уже зарегистрирован. Попробуйте восстановить пароль", 400);
$key = uniqid();
$db->q('INSERT INTO Users (Email, Name, Role, Password, Salt, ConfirmKey)
        VALUES (:email, :name, 0, md5(concat(:salt, :password)), :salt, :cKey)'
       , array( ':email'=>$data->email, ':name'=>$data->name, ':password'=>$data->password
              , ':salt'=>uniqid("", true), ':cKey' => $key));
Mail::confirmMail($data->email, $data->name, $key);
$db->commit();
echo json_encode(array('result' => 'success'));