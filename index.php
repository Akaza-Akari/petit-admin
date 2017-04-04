<?php
$config = require_once 'config.php';

$rows = array();

$db_host = $config['db_host'];
$db_user = $config['db_user'];
$db_pass = $config['db_pass'];
$db_name = $config['db_name'];
$db_table = $config['db_table'];

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
	die('Connection failed: ' . $conn->connect_error);
}

$sql = "SELECT * FROM `".$db_table."`;";
$result = $conn->query($sql);
if($result) {
	$sqlarray = $result->fetch_array(MYSQLI_ASSOC);
} else {
	echo $conn->error;
	die();
}

while($row = $sqlarray)
	$rows[] = $row;
foreach($sqlarray as $row) {
	var_dump($row);
}