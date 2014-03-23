<?php

class Db
{
	
	private $db;
	
	function __construct() {		
		@ $db_temp = new mysqli('fpcdata.db.8807435.hostedresource.com',
				'fpcdata','bB()*45.ab','fpcdata');
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
	
}


?>