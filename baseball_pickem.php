<html>
<head>

<style type="text/css">
img.logo
{
	float: left;
	width: 30px;
}

td.team p
{
	font-size: 14px;
	text-align: center;
	margin-top: 8px;
	font-weight: bold;
}

td.team
{
	background-color: #DDDDDD;
				border-radius: 15px;
	-moz-border-radius: 15px;
	width: 200px;
	margin-left: 1em;
}

div.playertable
{
	background-color: #EEEEEE;
    border-radius: 25px;
	-moz-border-radius: 25px;
	padding-left: 1em;
}

table
{
	text-align: center;
}

h1
{
	margin-bottom: .5em;
	font-size: 22px;
	margin-left: 2em;
	padding-top: .7em;
}

td.gamestatus
{
	text-align: left;
	position: relative;
	width: 300px;
}

td.total
{
	font-size: 20px;
	font-weight: bold;
}

h1 img
{
	margin-bottom: 4px;
	margin-right:5px;
}


p.opp
{
	position: absolute;
	left: 5px;
	bottom: 10px;
	width: 65px;
	font-size: 15px;
	margin: 0px;
}

p.opp img
{
	margin-bottom: 2px;
	margin-left: 2px;
}

p.gamestr
{
	position: absolute;
	left: 75px;
	bottom: 10px;
	width: 200px;
	font-size: 15px;
	margin: 0px;
}

p.gamestr img
{
	width: 20px;
	margin-right: 4px;
	margin-bottom: -3px;
}

p.matchup
{
	position: absolute;
	left: 150px;
	font-size: 10px;
	bottom: -1px;
	width: 150px;
	text-align: center;
	line-height: 8px;
}

span.gb
{
	font-size: 10px;
}


</style>

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>

<script>

$(document).ready( function() {

	window.setInterval(function(){
	
	

	$.ajax({
		type: 'GET',
		url: 'get_mlb_xml.php',
		dataType: 'xml',
		async: false,
		success: function (xmldata) {
			
			$(xmldata).find('game').each(function () {
				
				var $item = $(this);	
	
				$item.find('r').each(function () {
					
					var $runs = $(this);

					
					
				});
				
				parsegame($item);
			
			});

		}
		
		
	});
	
	console.log('updating');
	
	},5000);

});

function parsegame($game) {
	
	var $gametime = $game.attr('time');
	var $aID = $game.attr('away_team_id');
	var $hID = $game.attr('home_team_id');
	var $aAbbr = $game.attr('away_name_abbrev');
	var $hAbbr = $game.attr('home_name_abbrev');
	
	var $status = $game.find('status');
	
	var $gamestatus = $status.attr('status');
	var $top = $status.attr('top_inning');
	var $inning = $status.attr('inning');
	var $outs = $status.attr('o');
	var $balls = $status.attr('b');
	var $strikes = $status.attr('s');
	
	var $rob = $game.find('runners_on_base');
	var $rob_num = $rob.attr('status');
	
	var $r = $game.find('r');
	var $aruns = $r.attr('away');
	var $hruns = $r.attr('home');
	
	var $gs = '';
	
	if ($top=="Y")
	{
		var $hrs = ' '+$hruns+'-<strong>'+$aruns+'</strong> ';
		var $ars = ' <strong>'+$aruns+'</strong>-'+$hruns+' ';
	}
	else
	{
		var $hrs = ' <strong>'+$hruns+'</strong>-'+$aruns+' ';
		var $ars = ' '+$aruns+'-<strong>'+$hruns+'</strong> ';
	}
	
	if ($gamestatus=="Final")
	{
		$gs = $gs.concat('F');
	}
	else if ($gamestatus=="Postponed")
	{
		$gs = $gs.concat('P');
				$ars = '';
		$hrs = '';

	}
	else if ($gamestatus=="Preview")
	{
		$gs = $gs.concat($gametime," ET");
				$ars = '';
		$hrs = '';

	}
	else if ($gamestatus=="Pre-Game")
	{
		$gs = $gs.concat('WARMUP');
				$ars = '';
		$hrs = '';

	}
	else
	{
		if ($top=="Y") {$gs = $gs.concat('T');} else {$gs =$gs.concat('B');}
		$gs = $gs.concat($inning,' ');
		
		if ($rob_num>=0)
		{
		$gs = $gs.concat("<img src=\"mlblogos/b",$rob_num,".png\">");
		}
		//if ($rob_num=="0") {$gs = $gs.concat(" Bases Empty ");}
		//else if ($rob_num=="1") {$gs = $gs.concat(" Runner on 1st ");}
		//else if ($rob_num=="2") {$gs = $gs.concat(" Runner on 2nd ");}
		//else if ($rob_num=="3") {$gs = $gs.concat(" Runner on 3rd ");}
		//else if ($rob_num=="4") {$gs = $gs.concat(" Runners on 1st & 2nd ");}
		//else if ($rob_num=="5") {$gs = $gs.concat(" Runners on 1st & 3rd ");}
		//else if ($rob_num=="6") {$gs = $gs.concat(" Runners on 2nd & 3rd ");}
		//else if ($rob_num=="7") {$gs = $gs.concat(" Bases Loaded ");}
		
		$gs = $gs.concat($outs,' outs ',$balls,'-',$strikes);
		
		$('#'+$hID+' p.matchup').html(' ');
		
	}
	
	
	//$('#'+$aID).html('at ' + $hAbbr + $ars + ' ' + $gs);
	//$('#'+$hID).html('vs ' + $aAbbr + $hrs + ' ' + $gs);
	
		
	$('#'+$aID+' p.gamestr').html($ars + ' ' + $gs);
	$('#'+$hID+' p.gamestr').html($hrs + ' ' + $gs);
	
};

</script>


</head>
<body>

<h2>Still Unnamed Baseball Team Pick'em Challenge Standings</h2>
<p>Scoring updates live, no refresh needed</p>
<p>Your team's score is listed first, <strong>bold</strong> score represents team currently batting</p>

<?php

	function print_team($team)
	{
		echo "<tr><td class=\"team\"><img class=\"logo\" src=\"mlblogos/".$team['abbr'].".png\"><p>"
		.$team['name'].'</p></td><td>'.$team['W'].'-'.$team['L'].
		'</td><td>'.($team['PO']*10+$team['WC']*5).' <span class="gb">'.$team['place'].' '.$team['gb'].' GB</span>'.
		'</td><td>'.$team['POM'].'</td><td>'.$team['AS'].
		'</td><td>'.$team['GG'].'</td><td>'.($team['TC']*2).'</td><td>'.($team['MVP']*3).'</td>
		<td>'.$team['totalpts'].'</td>
		<td id="'.$team['ID'].'" class="gamestatus">'.$team['gamestr']."</td></tr>";
	}
	

	$daytime = time()-3600*8;
	$month =  date('m',$daytime);
	$day = date('d',$daytime);
	
	
	$teamID = array(108,109,110,111,112,113,114,115,116,117,118,119,120,121,133,134,
				135,136,137,138,139,140,141,142,143,144,145,146,147,158);
				
	$player = array();
	
	$player[0] = array( 'pname' => 'HART', 'teams' => array('TB','CLE','OAK','WSH','PIT','SF') );
	$player[1] = array( 'pname' => 'MATT', 'teams' => array('TOR','CWS','TEX','PHI','CIN','SD') );
	$player[2] = array( 'pname' => 'JOSH', 'teams' => array('BAL','KC','SEA','ATL','MIL','LAD') );
	$player[3] = array( 'pname' => 'RYAN', 'teams' => array('BOS','DET','LAA','NYM','STL','ARI') );
	$player[4] = array( 'pname' => 'FARM', 'teams' => array('NYY','MIN','HOU','MIA','CHC','COL') );
	
	$division = array();
	$division[0] = array('TB','TOR','BAL','BOS','NYY');
	$division[1] = array('MIN','DET','KC','CWS','CLE');
	$division[2] = array('OAK','TEX','SEA','LAA','HOU');
	$division[3] = array('MIA','NYM','ATL','PHI','WSH');
	$division[4] = array('PIT','CIN','MIL','STL','CHC');
	$division[5] = array('COL','ARI','LAD','SD','SF');
	
	$teamArray = array();
	
	foreach ($teamID as $ID)
	{
	   
	   $xml = simplexml_load_file("http://gd2.mlb.com/components/team/stats/".$ID."-stats.xml");
	   $abbr = (string) $xml['team_abbrev'];
	   $childs = $xml->children();
	   
	   $name = $xml['team_name'];
	   $wins = $childs[1]['W'];
	   $losses = $childs[1]['L'];
	   

	   
	   $teamArray[$abbr] = array();
	   $teamArray[$abbr]['abbr'] = $abbr;
	   $teamArray[$abbr]['name'] = $name;
	   $teamArray[$abbr]['W'] = $wins;
	   $teamArray[$abbr]['L'] = $losses;
	   $teamArray[$abbr]['gamestr'] = '';
	   $teamArray[$abbr]['ID'] = $ID;
	   $teamArray[$abbr]['PO'] = 0;
	   $teamArray[$abbr]['WC'] = 0;
	   $teamArray[$abbr]['POM'] = 0;
	   $teamArray[$abbr]['AS'] = 0;
	   $teamArray[$abbr]['GG'] = 0;
	   $teamArray[$abbr]['TC'] = 0;
	   $teamArray[$abbr]['MVP'] = 0;
	   
	   foreach ($player as $key => $p)
	   {
	   		if (in_array($abbr,$p['teams']))
	   		{
	   			$teamArray[$abbr]['player'] = $key;
	   		}
	   }	
	}
	
	foreach ($division as $d)
	{
		
	
		$go5 = array();
		$gb = array();
	
		foreach ($d as $t)
		{
			$go5[$t] = $teamArray[$t]['W'] - $teamArray[$t]['L'];
		}
		
		$best_wins = max($go5);
		
		echo $best_wins.' ';
		
		foreach ($d as $t)
		{
			$teamArray[$t]['gb'] = ($best_wins - $go5[$t])/2;
			$gb[$t] = ($best_wins - $go5[$t])/2;
			
			$place=1;
			
			foreach ($d as $tt)
			{
				if ($go5[$tt] > $go5[$t])
				{
					$place++;
				}
			}
			
			if ($place == 1) {$placestr = '1st';}
			elseif ($place == 2) {$placestr = '2nd';}
			elseif ($place == 3) {$placestr = '3rd';}
			else {$placestr = $place.'th';}
			
			if ($best_wins == $go5[$t])
			{
				$gb[$t] = 999;
			}
			
			$teamArray[$t]['place'] = $placestr;
			
			
		}
		
		$secondplace = min($gb);
		
		foreach($d as $t)
		{
			if ($teamArray[$t]['gb'] == 0)
			{
				$teamArray[$t]['gb'] = $secondplace *(-1);
			}
		}
		
		
	}
	
	// Load in Bonus Points
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
			
			$teamArray[$team][$type] = $teamArray[$team][$type] + 1;
				
		}	
	}

	
	$xml = simplexml_load_file('http://gd2.mlb.com/components/game/mlb/year_2013/month_'.$month.'/day_'.$day.'/master_scoreboard.xml');


	foreach($xml->children() as $game)
	{
		$gs = '';
		
		$gamestate = $game->children();
		
		foreach ($gamestate as $i)
		{
			if ($i->getName()=="runners_on_base") {$onbase=$i;} 
		}

		
		$linescore = $gamestate[1]->children();
		
		foreach($linescore as $entry)
		{
			if ($entry->getName()=="r")
			{
				$gs = $gs.$entry['away']."-".$entry['home']." ";
			}
		}
		
		$home_pitcher = $gamestate[1]["name_display_roster"].' ('.$gamestate[1]["wins"].'-'.$gamestate[1]["losses"].' '.$gamestate[1]["era"].')';
		$away_pitcher =  $gamestate[2]["name_display_roster"].' ('.$gamestate[2]["wins"].'-'.$gamestate[2]["losses"].' '.$gamestate[2]["era"].')';
		
		if ($gamestate[0]["status"]=="Final") 
		{$gs = $gs."F";}
		elseif ($gamestate[0]["status"]=="Postponed") 
		{$gs = $gs."P";}
		elseif 
		($gamestate[0]["status"]=="Preview") {$gs = $gs.$game["time"]." ET <p class=\"matchup\">".$away_pitcher."<br> vs. <br>".$home_pitcher."</p>";}
		elseif 
		($gamestate[0]["status"]=="Pre-Game") {$gs = $gs.$game["time"]." ET"." Warmups";}
		else
		{
			if ($gamestate[0]["top_inning"]=="Y") {$gs = $gs."T";} else {$gs = $gs."B";}
			$gs = $gs.$gamestate[0]["inning"];
			
			if (isset($onbase["status"]))
			{$gs = $gs."<img src=\"mlblogos/b".$onbase["status"].".png\">";}
			
			//if ($onbase["status"]==0) {$gs = $gs." Bases Empty ";}
			//elseif ($onbase["status"]==1) {$gs = $gs." Runner on 1st ";}
			//elseif ($onbase["status"]==2) {$gs = $gs." Runner on 2nd ";}
			//elseif ($onbase["status"]==3) {$gs = $gs." Runner on 3rd ";}
			//elseif ($onbase["status"]==4) {$gs = $gs." Runners on 1st & 2nd ";}
			//elseif ($onbase["status"]==5) {$gs = $gs." Runners on 1st & 3rd ";}
			//elseif ($onbase["status"]==6) {$gs = $gs." Runners on 2nd & 3rd ";}
			//elseif ($onbase["status"]==7) {$gs = $gs." Bases Loaded ";}
			
			
			$gs = $gs.$gamestate[0]["o"]." outs ".$gamestate[0]["b"]."-".$gamestate[0]["s"];			
			
		}
		
		$home_ab = (string) $game['home_name_abbrev'];
		$away_ab = (string) $game['away_name_abbrev'];
		$home_player_id = $teamArray[$home_ab]['player'];
		$away_player_id = $teamArray[$away_ab]['player'];
		
		$teamArray[$home_ab]['gamestr'] = "<p class=\"opp\">vs ".$away_ab."<img src=\"mlblogos\p".($away_player_id+1)."\"></p> <p class=\"gamestr\">".$gs."</p>";
		$teamArray[$away_ab]['gamestr'] = "<p class=\"opp\">at ".$home_ab."<img src=\"mlblogos\p".($home_player_id+1)."\"></p> <p class=\"gamestr\">".$gs."</p>";
			
	}
	
	foreach ($player as $key => $p)
	{
		echo "<br/>";
		echo "<div class=\"playertable\"><h1><img src=\"mlblogos/p".($key+1).".png\">".$p['pname']."</h1><table><tr><th>Team</th><th>W-L</th><th>PO</th><th>POM</th><th>AS</th><th>GG</th><th>TC</th><th>MVP</th><th>Total</th><th>Today</th></tr>";
		
		$pts = 0;
		
		foreach ($p['teams'] as $t)
		{
			$teampts = 0;
			
			$teampts = $teampts + $teamArray[$t]['W'];
			$teampts = $teampts + $teamArray[$t]['PO']*10;
			$teampts = $teampts + $teamArray[$t]['WC']*5;
			$teampts = $teampts + $teamArray[$t]['POM'];
			$teampts = $teampts + $teamArray[$t]['AS'];
			$teampts = $teampts + $teamArray[$t]['GG'];
			$teampts = $teampts + $teamArray[$t]['TC']*2;
			$teampts = $teampts + $teamArray[$t]['MVP']*3;
			
			$pts = $pts + $teampts;
			$teamArray[$t]['totalpts'] = $teampts;
			
			print_team($teamArray[$t]);
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class=\"total\">".$pts."</td></tr>";
		echo "</table></div>";
	}
	
?>
<br><br>
<a href="baseball_bonus.php">Add Bonus Points</a>
</body>