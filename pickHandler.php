<?php

require("db.php");
require("get_weeknum.php");
require("db_sync.php");

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
	
	$confPtsOk = true;
	
	if (isset($picks)){
		$pickIds = array();
		$pickValues = array();
		$pickConfPts = array();
		
		for ($index=0;$index<sizeof($picks);$index++)
		{
			$pickIds[$index] = $picks[$index][pickId];
			$pickValues[$index] = $picks[$index][pick];
			$pickConfPts[$index] = $picks[$index][confPts];
			$pickTypes[$index] = $picks[$index][type];
		}
		
		$pickLocks = $db->getPickLockStatus($pickIds);
		
		$confPtsOk = checkConfidencePoints($db,$pickIds,$pickConfPts,$compId,$username,$weeknum);
		
		if ($confPtsOk) {
			$db->updatePicks($pickIds,$pickValues,$pickConfPts,$pickLocks);
		}

	}
	
	if ($confPtsOk) {
		$pickResults = $db->getPicks($compId,$weeknum,$username);
		$pickJson = array();

		for ($index=0;$index<$pickResults->num_rows;$index++){	
			$row = $pickResults->fetch_assoc();
			$pickJson[$index] = $row;
		}
		
		echo json_encode($pickJson);
	}
	else {
		echo '{"error":"Duplicate Confidence Points"}';
	}
}
else {
	echo "{}";
}

function checkConfidencePoints($db,$pickIds,$pickConfPts,$compId,$username,$weeknum)
{
	$result = $db->getPicks($compId,$weeknum,$username);
	$num_picks = $result->num_rows;
	$isGood = true;
	$confPtsArray = Array();
	
	for ($index=0;$index<$num_picks;$index++)
	{
		$row = $result->fetch_assoc();
		if ($row['picktype']=="ATS-C") {
			$confPtsArray[$row['pickID']] = $row['confpts'];
		}	
	}
	
	for ($index=0;$index<sizeof($pickIds);$index++)
	{
		if ($pickConfPts[$index]>0)
		{
			$confPtsArray[$pickIds[$index]] = $pickConfPts[$index];
		}
	}
	
	$countValues = array_count_values($confPtsArray);
	
	for ($index=1;$index<=$num_picks;$index++)
	{
		if ($countValues[$index]>1) {$isGood = false;}
	}
	
	return $isGood;
}


?>