<?php
    
    session_start();
    
    require("edit_profile_page.php");
    
    $page = new Edit_Profile_Page;
    
    $page->Display();
    
?>