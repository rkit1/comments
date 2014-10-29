<?php
if (isset($_GET['adminToken'])) setcookie('adminToken', $_GET['adminToken'], 0, '/');
?>