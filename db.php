<?php

class Db
{
	
	private $db;
	
	function __construct() {		
		//@ $db_temp = new mysqli('fpcdata.db.8807435.hostedresource.com',
		//		'fpcdata','bB()*45.ab','fpcdata');
		
		@ $db_temp = new mysqli('fpctest.db.8807435.hostedresource.com',
				'fpctest','j8@!KODs','fpctest');
		
		$this->db = $db_temp;
	}
	
	function authenticateUser($username,$password) {
		$query = "select password, admin from user where username='".$username."'
           and  password='".$password."'";
		$result = $this->db->query($query);
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
	
	function addGame($hteam,$ateam,$timeval,$spread)
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
}


?>