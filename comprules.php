<?php

   require("page.php");
   
   
   $page = new Page();
   
   $page->content = "
  
  <h2> Competition Rules </h2>
  
  
  <h3> NFL Pick'em </h3>
  Each NFL week, participants will pick all NFL games against the spread. The scoring will be as follows:
  <br><br>
  Correct pick: 2 pts<br>
  Incorrect pick: -1 pts<br>
  Push: 0 pts<br>
  Sunday Night and Monday Night games will be worth double points (4,-2,0) <br><br>
  If a participant fails to make a pick, the home team will be selected by default.<br>
  The Superbowl will feature a special week with A series of proposition picks on the game worth 3 points each. 
  
  <h3> NFL Run-it-up Survivor </h3>
  Each week, the participant will select one NFL team. A participant may only select a given team once 
  over the entire season. The scoring for the pick will be as follows: <br><br>
  Picked Team Loses: 0 pts<br>
  Picked Team Wins:  1 pt per current winning streak + 1/2 pt per margin of victory<br><br>
  Example: Team picked wins 28-10. This is your third winning pick in a row. Score 3 points. 
  Your team won by 18 points, score 18/2=9 bonus points. Your total score for the week is 12.
  If a participant fails to make a pick, no points will be awarded and the participants current 
  win streak will be counted as broken. <br>
  
  <h3> NCAA Pick Six </h3>
  The participant will pick six of the weeks best college football matchups against the spread with confidence points. 
The scoring is as follows:<br><br>

Correct pick: Number of Confidence Points (1-6)<br>
Incorrect pick: 0 pts<br>
Push: Half of Confidence Points<br>
<br>
For bowl season, a selecton of 10 games will be picked with confidence points.<br>
If a participant fails to make a pick, the home team will be given as default, and the
confidence points will be assigned from 1-6 in order of the kickoff time of the game.
      
  <h3> College Survivor </h3>
  The participant will pick up to six college football teams every week. The scoring will be as follows:
  <br><br>
  For each winning team: 3 pts<br>
  For each losing team: 0 pts<br>
  For each missed pick: -3 pts<br>
  Point values doubled during bowl week.<br>
  <br>
  The number of picks for the next week will be reduced for each losing team picked. For example,
  if your first week, the participant picks 4 winners and 2 losers, the following week he or she may
  only pick four teams. If a participant fails to make a pick, they will be penalized 3 pts per pick, but will not lose future picks.<br>
  
  <h2> General Rules </h2>
  
  The overall champion will be determined by adding together point totals from each of the four competitions. 
  
  All picks lock at kickoff time of the corresponding game. The kickoff times are usually rounded down to the nearest half-hour.
  
  <h3> Tiebreaking Procedures </h3>
  
  <h4> For Overall Competition: </h4>
  
  1. Number of individual competitions won <br>
  2. Total of finishing place in individual standings <br>
  3. Total number of weeks won or tied for top score in individual competitions<br>
  4. Most recent week won in individual competitions<br>

  <h4> For Individual Competitions: </h4>
  
  1. Total number of weeks won or tied for top score<br>
  2. Most recent week won or tied for top score<br>
  3. Total score in most recent week<br>
  4. Total score in next most recent week until decided<br>
  

";
   
   $page->Display();
   
?>