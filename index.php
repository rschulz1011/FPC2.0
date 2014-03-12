<?php

   require("page.php");
   
   $homepage = new Page();
   
   $homepage -> content = "<p>Welcome to the Football Picking Championship </p><br/>
                            <br/><br/>
                            <h3>
                            Signup for 2013 FPC is now open.
                            </h3>
                            <a href=\"login.php\">Login</a> or <a href=\"signup.php\">Sign Up</a> Today.<br/>
                            <br/>The Football Picking Championship is a unique football picking competition that 
                            consists of four different football picking games, each with its own twist:<br/>
                            <br/><b>Pro Football Pick'em:</b> Against the Spread Pick'em with weekly bonus
                            points for great picking performances <br/>
                            <b>Pro Football Run It Up:</b> A Survivor Style game where you get points for margin of victory</br>
                            <b>College Six Pack:</b> Pick six of the weeks biggest college matchups 
                            against the spread (with confidence points!)<br/>
                            <b>College Survivor:</b> Survivor for college football where you pick up to 
                            six teams to win each week. Each team you lose reduces your number of picks for next week.</br>
                            <a href=\"comprules.php\">Read Full Rules Here</a></br></br>
                            Your total points from all four of the games are combined into the overall standings. 
                            The player with the most points at the end of the season is crowned the Football Picking Champion!</br></br>
                            <a href=\"overallstandings.php\">Current Standings</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href=\"previouschamps.php\">Previous Champions</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href=\"aboutus.php\">About Us</a>
                            
                            ";
   
   
   $homepage->Display();
   
?>