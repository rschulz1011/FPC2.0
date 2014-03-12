<?php

	$daytime = time()-3600*8;
	$month =  date('m',$daytime);
	$day = date('d',$daytime);
	
	$contents = file_get_contents('http://gd2.mlb.com/components/game/mlb/year_2013/month_'.$month.'/day_'.$day.'/master_scoreboard.xml');

	echo $contents;

?>