<?php

	if (isset($_POST['team']))
	{
		$team = $_POST['team'];
		$type = $_POST['bonustype'];
		$note = $_POST['note'];
		
		$fid = fopen("baseball_bonus_data.txt",'a');
		
		fprintf($fid,$team.",".$type.",".$note."\n");
		
		fclose($fid);
		
	}

	echo "<table><tr><th>Team</th><th>Type</th><th>Note</th></tr>";
	
	$fid = fopen("baseball_bonus_data.txt",'r');
	
	$line = "b";
	
	while ($line != false)
	{
		$line = fgets($fid);
		
		if ($line != false)
		{
			$firstcomma = strpos($line,',');
			$team = substr($line,0,$firstcomma);
			$secondcomma = strpos($line,',',$firstcomma+1);
			$type = substr($line,$firstcomma+1,$secondcomma-$firstcomma-1);
			$note = substr($line,$secondcomma+1);
			
			echo "<tr><td>".$team."</td><td>".$type."</td><td>".$note."</td></tr>";	
		}	
	}
	
	
	fclose($fid);	
	echo "</table><br><br>";
	


?>

<form id="bonusform" action="baseball_bonus.php" method="post">
	<select name="team">
	<option value="ARI">ARI</option>
	<option value="ATL">ATL</option>
	<option value="BAL">BAL</option>
	<option value="BOS">BOS</option>
	<option value="CHC">CHC</option>
	<option value="CIN">CIN</option>
	<option value="CLE">CLE</option>
	<option value="COL">COL</option>
	<option value="CWS">CWS</option>
	<option value="DET">DET</option>
	<option value="HOU">HOU</option>
	<option value="KC">KC</option>
	<option value="LAA">LAA</option>
	<option value="LAD">LAD</option>
	<option value="MIA">MIA</option>
	<option value="MIL">MIL</option>
	<option value="MIN">MIN</option>
	<option value="NYM">NYM</option>
	<option value="NYY">NYY</option>
	<option value="OAK">OAK</option>
	<option value="PHI">PHI</option>
	<option value="PIT">PIT</option>
	<option value="SD">SD</option>
	<option value="SEA">SEA</option>
	<option value="SF">SF</option>
	<option value="STL">STL</option>
	<option value="TB">TB</option>
	<option value="TEX">TEX</option>
	<option value="TOR">TOR</option>
	<option value="WSH">WSH</option>
	</select>
	
	<select name="bonustype">
	<option value="PO">Division Winner</option>
	<option value="WC">Wild Card</option>
	<option value="POM">Player of the Month</option>
	<option value="AS">All-Star</option>
	<option value="GG">Golden Glove</option>
	<option value="TC">Triple Crown</option>
	<option value="MVP">MVP/Cy Young</option>
	</select>
	
	<input type="text" name="note" size="75"/>
	
	<input type="submit" />
	
</form>

<br><br>
<a href="baseball_pickem.php">Back To Standings</a>