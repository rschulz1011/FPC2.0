<?php

class Db
{
	
	private $db;
	
	function __construct() {		
		@ $db_temp = new mysqli('fpcdata.db.8807435.hostedresource.com',
				'fpcdata','bB()*45.ab','fpcdata');
		
		// @ $db_temp = new mysqli('fpctest.db.8807435.hostedresource.com',
		//		'fpctest','j8@!KODs','fpctest');
		
		$this->db = $db_temp;
	}
	
	function authenticateUser($username,$password) {
		$query = "select password, admin from user where username='".$username."'
           and  password='".$password."'";
		$result = $this->db->query($query);
		
		if ($result->num_rows>0) {
			$query = "update user set lastlogin = '".date("Y-m-d H:i:s",now_time())."'
			where username = '".$username."'";
			$this->db->query($query);
		}
		
		return $result;
	}
	
	function getPosts($maxposts)
	{
		$query = "select * from post order by posttime desc limit ".$maxposts;
		$result = $this->db->query($query);
		return $result;
	}
	
	function getTeamName($teamId)
	{
		$query = "select location from team where teamID='".$teamId."'";
		$result = $this->db->query($query);
		$row = $result->FETCH_ASSOC();
		$teamName = $row['location'];
		return $teamName;
	}
	
	function gameExists($ateam,$hteam,$timeval)
	{
		$gameExists = false;
		$query = "select KOtime from game where ateamID='".$ateam."' and hteamID='".
				$hteam."'";
		$result = $this->db->query($query);
		$num_rows = $result->num_rows;
		if ($num_rows>0)
		{
			for ($j=0;$j<$num_rows;$j++)
			{
				$row = $result->FETCH_ASSOC();
				if (($timeval)==strtotime($row['KOtime']))
				{$gameExists = true;}
			}
	    }
	    return $gameExists;
	}
	
	function addGame($hteam,$ateam,$timeval,$spread,$weeknum)
	{
		if (strlen($spread)>0)
		{
			$query = "insert into game (hteamID, ateamID, KOtime, weeknum, spread) values
                            ('".$hteam."','".$ateam."','".date("Y-m-d H:i:s",$timeval)."','".$weeknum."','".$spread."')";
		}
		else
		{
			$query = "insert into game (hteamID, ateamID, KOtime, weeknum) values
                            ('".$hteam."','".$ateam."','".date("Y-m-d H:i:s",$timeval)."','".$weeknum."')";
		}
		$result = $this->db->query($query);
		$num_rows = $this->db->affected_rows;
		if ($num_rows>0) {return true;} else {return false;}
	}
	
	function getAllTeams($league)
	{
		$query = "select location, nickname, teamID from team where league = \"".$league."\"
		       order by location";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getDefaultPick($compId)
	{
		$query = "select league,defaultpick from competition where competitionID=\"".$compId."\"";
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();
		return $row['defaultpick'];
	}
	
	function getLeague($compId)
	{
		$query = "select league,defaultpick from competition where competitionID=\"".$compId."\"";
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();
		return $row['league'];
	}
	
	function getCurrentCompetitions()
	{
		$query = "select * from competition where active = 1";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getGamesForWeek($weekNum,$league)
	{
		$query = "select a.location, h.location, game.gameID from team as a, team as h, game where
           game.weeknum=".$weekNum." and a.teamID=game.ateamID and h.teamID=game.hteamID and a.league=\"".$league."\"";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getPickDataAts($gameId)
	{
		$query = "select ateamID,hteamID,KOtime from game where gameID='".$gameId."'";
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();
		
		$pickData['optA'] = $row['ateamID'];
		$pickData['optB'] = $row['hteamID'];
		$pickData['locktime'] = $row['KOtime'];
		return $pickData;
	}
	
	function getPickDataSurvivor($weeknum,$league)
	{
		$query = "select max(game.KOtime) from game, team where team.teamID=game.hteamID
               and team.league='".$league."' and game.weeknum='".$_GET['weeknum']."'";
		$result = $this->db->query($query);
		$row = $result->fetch_array(); 
		
		$pickData['optA'] = '';
		$pickData['optB'] = '';
		$pickData['locktime'] = $row[0];
		
		return $pickData;
	}
	
	function addQuestion($compId,$pickData,$game,$name,$picktype,$weeknum,$bonus)
	{
		echo $compId." ".$game." ".$name." ".$picktype." ".$weeknum;
		$query = "insert into question (competitionID,gameID,pickname,picktype,weeknum,option1,
                  option2,bonusmult,locktime) values (".$compId.",".$game.",'".$name."','".
		          $picktype."',".$weeknum.",'".$pickData['optA']."','".$pickData['optB'].
				  "',".$bonus.",'".$pickData['locktime']."')";
		$result = $this->db->query($query);
		
		$query = "select max(questionID) from question";
		$result = $this->db->query($query);
		$row = $result->FETCH_ASSOC();
		$questionID = $row['max(questionID)'];
		
		return $questionID;
	}
	
	function addPicks($questionId,$compId,$locktime)
	{
		$query = "select username from whoplays where competitionID = '".$compId."'";
		$result = $this->db->query($query);
		$num_results = $result->num_rows;
		 
		$query = "insert into pick (questionID, username, locktime) values ";
		 
		for ($j=0;$j<$num_results;$j++)
		{
		$row = $result->FETCH_ASSOC();
		$query = $query."(".$questionId.",'".$row['username']."','".$locktime."')";
		if ($j<($num_results-1)) {$query=$query.",";}
		}
		 
		$result = $this->db->query($query);
	}
	
	function getTeam($teamId)
	{
		$query = "select location, nickname, league, conference, division
           from team where teamID=".$teamId;
		$result = $this->db->query($query);
		$num_results = $result->num_rows;
		$row = $result->FETCH_ASSOC();
		return $row;
	}
	
	function updateTeam($loc,$nick,$league,$conf,$div,$teamId)
	{
		$query = "update team set location='" . $loc . "', nickname='" . $nick . "', league='" .
				$league . "', conference='" . $conf . "', division='" . $div . "' where teamID=" .
				$teamId;
		$result = $this->db->query($query);
		$num_rows = $this->db->affected_rows;
		if ($num_rows>0) {return true;} else {return false;}	
	}
	
	function addTeam($loc,$nick,$league,$conf,$div)
	{
		$query = "insert into team (location,nickname,league,conference,division)
        values ('" . $loc . "','" . $nick . "','" . $league . "','" . $conf . "','" . $div . "')";
		$result = $this->db->query($query);
		$num_rows = $this->db->affected_rows;
		if ($num_rows>0) {return true;} else {return false;}
	}
	
	function getConferences()
	{
		$query = "select distinct conference from team";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getTeams($league,$conf)
	{
		$query = "select teamID, location, nickname, league, conference, division from team where
                 league='".$league."' and conference='".$conf."'";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getTeamsByLeague($league)
	{
		$query = "select location, nickname, teamID from team where league = '".$league."'
                   order by location";
		$result = $this->db->query($query);
		return $result;
	}
	
	function query($query)
	{
		$result = $this->db->query($query);
		return $result;
	}
	
	function deleteGame($gameId)
	{
		$query = "delete from game where gameID='".$gameId."'";	
		$result = $this->db->query($query);
	}
	
	function deleteTeam($teamId)
	{
		$query = "delete from team where teamID='".$teamId."'";
		$result = $this->db->query($query);
	}
	
	function getGames($league,$week)
	{
		$query = "select game.gameID, a.location as hloc, b.location as aloc, game.KOtime, game.spread,
              a.league, game.weeknum, game.ascore, game.hscore
              from game, team as a, team as b where game.hteamID=a.teamID 
		      and game.ateamID=b.teamID
              and a.league='".$league."' and game.weeknum='".$week."'
              order by game.KOtime, game.gameID";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getQuestion($questionId)
	{
		$query = "select * from (select a.location as aloc, h.location as hloc, game.gameID from game,
           team as a, team as h where a.teamID=game.ateamID and h.teamID=game.hteamID) as g right join
           question on question.gameID=g.gameID where question.questionID = '".$questionId."'";	 
		$result = $this->db->query($query);
		return $result;
	}
	
	function getQuestions($week,$compId)
	{
		$query = "select * from (select a.location as aloc, h.location as hloc, game.gameID from game,
           team as a, team as h where a.teamID=game.ateamID and h.teamID=game.hteamID) as g right join
           question on question.gameID=g.gameID where question.weeknum='".$week."'
           and question.competitionID = '".$compId."'";
		$result = $this->db->query($query);
		return $result;
	}
	
	function deleteQuestion($questionId)
	{
		$query = "delete from question where questionID='".$questionId."'";
		$result = $this->db->query($query);
		 
		$query = "delete from pick where questionID='".$questionId."'"; 
		$result = $this->db->query($query);
	}
	
	function getCompetition($compId)
	{
		$query = "select * from competition where competitionID='".$compId."'";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getSimpleStandings($compId)
	{
		$query = "select username, totalpoints from whoplays where competitionID='".$compId."'
    	order by totalpoints desc";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getDetailedStandings($compId)
	{
		$query = "select sum(pick.pickpts), question.weeknum, pick.username,whoplays.totalpoints from
                pick, question,whoplays where pick.questionID = question.questionID and
                question.competitionID = '".$compId."' and whoplays.competitionID = '"
		                		.$compId."' and whoplays.username = pick.username group by pick.username,
                question.weeknum order by whoplays.totalpoints desc, pick.username, question.weeknum";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getPointsByWeek($username,$compId)
	{
		$query = "select question.weeknum, sum(pick.pickpts), count(pick.pickpts) as numpicks from
    	pick,question where pick.username='".$username."'and question.competitionID='".$compId."'
    	and question.questionID=pick.questionID group by question.weeknum order by question.weeknum";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getOverallStandings()
	{
		$currentComps = $this->getCurrentCompetitions();
		$num_comps = $currentComps->num_rows;
		$selectstr = "";
		$fromstr = "";
		$wherestr = "";
		
		for ($i=0;$i<$num_comps;$i++)
		{
			$row = $currentComps->fetch_assoc();
			$selectstr = $selectstr.", a".$i.".totalpoints as tp".$i;
     
         	$fromstr = $fromstr.",(select totalpoints,username from whoplays where competitionID='".
               	$row['competitionID']."') as a".$i;
        
		    if ($i>0) {$wherestr=$wherestr." and ";}
		               		 
		    $wherestr = $wherestr." a".$i.".username=whoplays.username";
        }
		$query = "select whoplays.username,
                tp.totalpoints ".$selectstr." from whoplays,
                (select sum(whoplays.totalpoints) as totalpoints, username from whoplays, competition
                where competition.competitionID=whoplays.competitionID and competition.active=1 group by username) as tp ".
		                $fromstr." where ".$wherestr." and tp.username=whoplays.username group by whoplays.username order by tp.totalpoints desc";
		$result = $this->db->query($query);
		return $result;		
	}
	
	function getGame($gameId)
	{
		$query = "select game.KOtime, game.weeknum, game.spread, game.hscore,
           game.ascore, a.location as hloc, b.location as aloc, a.league, game.hteamID, game.ateamID
				from game, team as a, team as b where
           game.hteamID=a.teamID and game.ateamID=b.teamID and gameID='".$gameId."'";		 

		$result = $this->db->query($query);
		return $result;	 
	}
	
	function getUser($username)
	{
		$query = "select * from user where username='".$username."'";
		$result = $this->db->query($query);
		if ($result->num_rows>0)
		{
			$row = $result->fetch_assoc();
		}
		else
		{
			$row = null;
		}
		return $row;
	}
	
	function addUser($username,$password,$email,$fname,$lname,$emailshare)
	{
		$query = "insert into user values (\"".$username."\",\"".$password."\",\"".
				$email."\",\"".$fname."\",\"".$lname."\",".
				$emailshare.",\"".date('c')."\",0,\"".date('c')."\")";
		$this->db->query($query);
		if ($this->db->affected_rows>0)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	function getUserByEmail($email)
	{
		$query = "select * from user where email ='".$email."'";
		$result = $this->db->query($query);
		return $result;
	}
	
	function updatePassword($email,$password)
	{
		$query = "update user set password ='".$password.
		"' where email = '".$email."'";
		$this->db->query($query);
	}
	
	function getAffectedRows()
	{
		return $this->db->affected_rows;
	}
	
	function newPost($post,$user)
	{
		$query = "insert into post set posttext='".$post."', username ='".
				$user."', posttime = '".date("c",now_time())."'";	 
		$this->db->query($query);
	}
	
	function getWhoPlays($username)
	{
		$query = "select * from (select * from whoplays where username='".$username."') as w right join
          competition on competition.competitionID=w.competitionID where competition.active=1";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getPicks($compId,$weeknum,$username)
	{
		$query = "select pick.pickID, pick.pick, pick.confpts, pick.locktime, pick.pickpts, q.correctans, 
   			q.bonusmult, q.questionID, q.picktype, q.aloc, q.hloc, q.option1, q.option2, q.pickname, q.spread, q.picktype 
   			from (select question.pickname, question.competitionID, question.weeknum, question.picktype, 
   			question.questionID, question.option1, question.option2, question.bonusmult, question.correctans,
   			g.aloc, g.hloc, g.spread from (select a.location as aloc, h.location as hloc, game.gameID, game.spread 
   			from game, team as a, team as h where a.teamID=game.ateamID and h.teamID=game.hteamID) as g right join question on 
   			question.gameID=g.gameID) as q, pick where pick.questionID=q.questionID and pick.username='".
   		
		$username."' and q.competitionID='".$compId."' and q.weeknum='".$weeknum."' order by pick.pickID";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getSurvivorOpponent($weeknum,$teamId)
	{
		$query = "select * from game where weeknum=".$weeknum." and (ateamID=".$teamId." or hteamID=".$teamId.")";
		$result = $this->db->query($query);
		if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			if ($row['hteamID']==$teamId) {return $row['ateamID'];}
			if ($row['ateamID']==$teamId) {return $row['hteamID'];}
		}
		else { return null;}
	}
	
	function getTeamIds()
	{
		$query = "select teamID, location from team";
		$result = $this->db->query($query);
		return $result;
	}
	
	function getAvailableCollegeSurvivorTeams($username,$compId,$weeknum)
	{
		$query = "select * from (select t.location, t.teamID, t.conference, p.pickID from (select pick.pick, pick.pickID
   		from pick,question where username='".$username."' and pick.questionID=question.questionID
   		and question.competitionID='".$compId."' and (question.weeknum<>'".$weeknum."' or pick.locktime<'".date("Y-m-d H:i:s",now_time()).
		   "')) as p right join  (select game.gameID, team.teamID, team.location, team.conference from team,
   		game where (team.teamID=game.ateamID or team.teamID=game.hteamID) and team.league='NCAA' and
   		game.weeknum='".$weeknum."' and game.KOtime>'".date("Y-m-d H:i:s",now_time()).
		   "') as t  on p.pick=t.teamID) as a where a.pickID is null and a.conference != 'FCS' order by location";
		
		$result = $this->db->query($query);
		$numTeams = $result->num_rows;
		
		$teamIds = array();
		
		for ($index=0;$index<$numTeams;$index++)
		{
			$row = $result->fetch_assoc();
			$teamIds[$index] = (int)$row['teamID'];
		}
		
		return $teamIds;	
	}
	
	function getAvailableProSurvivorTeams($username,$compId,$weeknum)
	{
		$query = "select * from (select t.location, t.teamID, t.conference, p.pickID from (select pick.pick, pick.pickID
   		from pick,question where username='".$username."' and pick.questionID=question.questionID
   		and question.competitionID='".$compId."' and (question.weeknum<>'".$weeknum."' or pick.locktime<'".date("Y-m-d H:i:s",now_time()).
	   		"')) as p right join  (select game.gameID, team.teamID, team.location, team.conference from team,
   		game where (team.teamID=game.ateamID or team.teamID=game.hteamID) and team.league='NFL' and
   		game.weeknum='".$weeknum."' and game.KOtime>'".date("Y-m-d H:i:s",now_time()).
	   		"') as t  on p.pick=t.teamID) as a where a.pickID is null order by location";
	
		$result = $this->db->query($query);
		$numTeams = $result->num_rows;
	
		$teamIds = array();
	
		for ($index=0;$index<$numTeams;$index++)
		{
		$row = $result->fetch_assoc();
		$teamIds[$index] = (int)$row['teamID'];
		}
	
		return $teamIds;
	}
	
	function getPickLockStatus($pickIds) {
		
		$pickIdString = '';
		for ($index=0;$index<sizeof($pickIds);$index++) {
			if ($index>0) {$pickIdString = $pickIdString.",";}
			$pickIdString = $pickIdString.$pickIds[$index];
		}
		$query = "select pickID,locktime from pick where pickID in (".$pickIdString.")";
		
		$result = $this->db->query($query);
		
		$status = array();
		
		for ($index=0;$index<sizeof($pickIds);$index++) {
			$row = $result->fetch_assoc();
			if (strtotime($row['locktime'])<now_time()) {
				$status[$index] = true;
			} else {
				$status[$index] = false;
			}
		}
		
		return $status;
	}
	
	function updatePicks($pickIds,$pickValues,$pickConfPts,$pickLocks){
		
		for ($index=0;$index<sizeof($pickIds);$index++){
			$setString = '';
			if ($pickLocks[$index]==false) {
				$setString = $setString."set pick = ".$pickValues[$index];
				if (isset($pickConfPts[$index])) {
					$setString = $setString.", confpts = ".$pickConfPts[$index];
				}
				$setString = $setString." where pickID=".$pickIds[$index];
				$query = "update pick ".$setString;
				$this->db->query($query);	
			}
			$this->updatePick($pickIds[$index]);

		}
	}
	
	function updatePick($pickId)
	{
	    $query = "select * from pick,question where pick.questionID=question.questionID and pick.pickID = '".$pickId."'";
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();

		// update ATS-C type picks
		if ($row['picktype']=="ATS-C" & !is_null($row['correctans']))
		{
			if ($row['pick']==$row['correctans'])
			{
				$pickpts = $row['confpts']*$row['bonusmult'];
			}
			elseif ($row['correctans']==-1)
			{
				$pickpts = $row['confpts']*$row['bonusmult']/2;
			}
			else
			{
				$pickpts = 0;
			}
		
			$query = "update pick set pickpts='".$pickpts."' where pickID='".$pickId."'";		 
			$this->db->query($query);
		}
		
		// update ATS type picks
		if ($row['picktype']=="ATS" & !is_null($row['correctans']))
		{
			if ($row['pick']==$row['correctans'])
			{
				$pickpts = 2*$row['bonusmult'];
			}
			elseif ($row['correctans']==-1)
			{
				$pickpts = 0;
			}
			else
			{
				$pickpts = -1*$row['bonusmult'];
			}

			$query = "update pick set pickpts='".$pickpts."' where pickID='".$pickId."'";
			$this->db->query($query);
		}

		//update S-COL type picks
		if ($row['picktype']=="S-COL")
		{
			if ( (is_null($row['pick']) | $row['pick']==0) & strtotime($row['locktime'])<now_time())
			{
				$query = "update pick set pickpts='-3' where pickID='".$pickId."'";
				$this->db->query($query);
			}
			else
			{
				//find relevant game
				$query = "select * from game where game.weeknum='".$row['weeknum']."' and (game.hteamID='".$row['pick'].
				"' or game.ateamID='".$row['pick']."')";
				$gameresult = $this->db->query($query);
				$gamerow=$gameresult->FETCH_ASSOC();
					 
				if (is_null($row['pick']) | $row['pick']==0)
				{
					$query = "select game.KOtime from game, team where team.teamID = game.hteamID and team.league=\"NCAA\" 
						and weeknum = ".$row['weeknum']." order by KOtime desc limit 1";
					$result = $this->db->query($query);
					$thisRow = $result->fetch_assoc();
					
					$query = "update pick set locktime = '".$thisRow['KOtime']."' where pickID='".$pickId."'";
					$this->db->query($query);
					
				}
				else
				{
					$query = "update pick set locktime = '".$gamerow['KOtime']."' where pickID='".$pickId."'";
					$this->db->query($query);
				}
					 
			}
		
		}
		
    	//update S-PRO type picks
    	if ($row['picktype']=="S-PRO" & is_null($row['pick'])==0 & $row['pick']<>0)
    	{
        	//find relevant game
        	$query = "select * from game where game.weeknum='".$row['weeknum']."' and (game.hteamID='".$row['pick'].
            	"' or game.ateamID='".$row['pick']."')";
        	$gameresult = $this->db->query($query);
        	$gamerow=$gameresult->FETCH_ASSOC();
        
        	$query = "update pick set locktime = '".$gamerow['KOtime']."' where pickID='".$pickId."'";
        	$this->db->query($query);  
    	}
        
     
	}
	
	function getScoreUpdates()
	{
		// get all pending games (in last 7 days, ordered by ko time)	
		$query = "select game.KOtime,game.gameID, game.hteamID, game.ateamID, a.league, a.location as hloc, b.location as aloc  from game,team as a, team as b where game.KOtime between '"
         .date("Y-m-d H:i:s",now_time()-604800)."' and '".date("Y-m-d H:i:s",now_time())."' 
         and (hscore is null or ascore is null) and game.hteamID = a.teamID 
         and game.ateamID=b.teamID order by game.KOtime";
		
		$openGames = $this->db->query($query);
		$numOpenGames = $openGames->num_rows;

		// get all unique picks for college survivor (in last 7 days)
		$query = "select distinct pick.pick from pick,question where pick.locktime between '"
				.date("Y-m-d H:i:s",now_time()-604800)."' and '".date("Y-m-d H:i:s",now_time())."'
				and pick.questionID = question.questionID and question.picktype = 'S-COL' ";

		$uniquePicks = $this->db->query($query);
		$numUniquePicks = $uniquePicks->num_rows;
		
		// get all Pick Six games in last 7 days
		$query = "select gameID from question where question.locktime between '"
				.date("Y-m-d H:i:s",now_time()-604800)."' and '".date("Y-m-d H:i:s",now_time())."'
				and question.picktype = 'ATS-C'";
		
		$pickSixGames = $this->db->query($query);
		$numPickSixGames = $pickSixGames->num_rows;
		
		
		$uniquePicksArray = array();
		
		for ($index=0;$index<$numUniquePicks;$index++)
		{
			$row = $uniquePicks->fetch_assoc();
			$uniquePicksArray[$index] = $row['pick'];
		}
		
		$pickSixGamesArray = array();
		
		for ($index=0;$index<$numPickSixGames;$index++)
		{
			$row = $pickSixGames->fetch_assoc();
			$pickSixGamesArray[$index] = $row['gameID'];
		}
		
		$pendingGamesArray = array();
		
		for ($index=0;$index<$numOpenGames;$index++)
		{
			$row = $openGames->fetch_assoc();
			if ($row['league']=="NFL" 
				|| in_array($row['hteamID'],$uniquePicksArray) 
				|| in_array($row['ateamID'],$uniquePicksArray)
				|| in_array($row['gameID'],$pickSixGamesArray))
			{
				array_push($pendingGamesArray,$row);
			}
		}
		
		return $pendingGamesArray;
	}
	
	function joinCompetition($username,$compId)
	{
		$query = "insert into whoplays (username,competitionID,totalpoints) values
             ('".$username."',".$compId.",0)";
		$result = $this->db->query($query);
		
		$query = "select questionID, locktime from question where competitionID='".$compId."' and locktime>'".
				date("Y-m-d H:i:s",now_time())."'";
		
		$result = $this->db->query($query);
		$num_results = $result->num_rows;
		
		$query = "insert into pick (questionID, username, locktime) values ";
		 
		for ($j=0;$j<$num_results;$j++)
		{
		$row = $result->FETCH_ASSOC();
		$query = $query."(".$row['questionID'].",'".$username."','".$row['locktime']."')";
				if ($j<($num_results-1)) {$query=$query.",";}
		}
		 
		$result = $this->db->query($query);
	}
	
}


?>