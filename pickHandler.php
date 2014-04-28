<?php

require("db.php");
require("get_weeknum.php");

if (isset($_POST['username'])) {
	$compId = $_POST['compId'];
	$username = $_POST['username'];
	$weeknum = $_POST['weeknum'];
	$password = $_POST['password'];
	$picks = json_decode(str_replace("\\\"","\"",$_POST['picks']),true);
	
	
	//echo str_replace("\\\"","\"",$_POST['picks']);
	//echo $picks;
}
else {
	$compId = $_GET['compId'];
	$username = $_GET['username'];
	$weeknum = $_GET['weeknum'];
	$password = $_GET['password'];
}

$db = new Db();

$result = $db->authenticateUser($username,$password);

if ($result->num_rows > 0) {
	
	if (isset($picks)){
		$pickIds = array();
		$pickValues = array();
		$pickConfPts = array();
		
		for ($index=0;$index<sizeof($picks);$index++)
		{
			$pickIds[$index] = $picks[$index][pickId];
			$pickValues[$index] = $picks[$index][pick];
			$pickConfPts[$index] = $picks[$index][confpts];
		}
		
		$pickLocks = $db->getPickLockStatus($pickIds);
		
		$db->updatePicks($pickIds,$pickValues,$pickConfPts,$pickLocks);
		
		
	}

	$pickResults = $db->getPicks($compId,$weeknum,$username);
	$pickJson = array();

	for ($index=0;$index<$pickResults->num_rows;$index++){	
		$row = $pickResults->fetch_assoc();
		$pickJson[$index] = $row;
	}

	echo json_encode($pickJson);
}
else {
	echo "{}";
}

?>