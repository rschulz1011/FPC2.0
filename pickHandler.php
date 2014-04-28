<?php

require("db.php");

if (isset($_POST['username'])) {
	$compId = $_POST['compId'];
	$username = $_POST['username'];
	$weeknum = $_POST['weeknum'];
}
else {
	$compId = $_GET['compId'];
	$username = $_GET['username'];
	$weeknum = $_GET['weeknum'];
}

$db = new Db();

$pickResults = $db->getPicks($compId,$weeknum,$username);
$pickJson = array();

for ($index=0;$index<$pickResults->num_rows;$index++){
	
	$row = $pickResults->fetch_assoc();
	$pickJson[$index] = $row;
}

echo json_encode($pickJson);

?>