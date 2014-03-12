<?php

    session_start();
    
    require("admin_page.php");
    
    $ahome = new Admin_Page();
    
    $ahome->admin_level = 1;
    
    $ahome->content = "Welcome to the Administrator home page. <br/><br/>";
    
    if ($_SESSION['adminlev']>0)
     {$ahome->content = $ahome->content."<a href=\"updatescores.php\">Update Scores</a><br/><br/>";}
     
    if ($_SESSION['adminlev']>1)
    {
      $ahome->content = $ahome->content."<a href=\"add_team.php\">Add Teams</a>";
      $ahome->content = $ahome->content."&nbsp&nbsp<a href=\"viewteams.php\">View Teams</a><br/><br/>"; 
      $ahome->content = $ahome->content."<a href=\"add_game.php?addgameleague=NFL\">Add Games (NFL)</a>";
      $ahome->content = $ahome->content."&nbsp&nbsp<a href=\"add_game.php?addgameleague=NCAA\">Add Games (NCAA)</a>";
      $ahome->content = $ahome->content."&nbsp&nbsp<a href=\"viewgames.php\">View Games</a><br/><br/>"; 
      $ahome->content = $ahome->content."<a href=\"add_questions.php\">Add Questions</a>";
      $ahome->content = $ahome->content."&nbsp&nbsp<a href=\"viewquestions.php\">View Questions</a><br/><br/>"; 
      $ahome->content = $ahome->content."<a href=\"database_update.php\">Database Sync</a>";
    }
    
    $ahome->Display();
    
?>