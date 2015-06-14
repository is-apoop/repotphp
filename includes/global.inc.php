<?php
require_once 'classes/User.class.php';
require_once 'classes/UserTools.class.php';
require_once 'classes/DB.class.php';
// Подключаем класс для работы с excel
require_once('classes/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('classes/PHPExcel/Writer/Excel5.php');
//connect to the database

$db = new DB();
$db->connect() or die("FUCK YOU");
//initialize UserTools object
$userTools = new UserTools();

//start the session
session_start();

//refresh session variables if logged in
if(isset($_SESSION['logged_in'])) {
	$user = unserialize($_SESSION['user']);
	$_SESSION['user'] = serialize($userTools->get($user->user_id));
}
?>