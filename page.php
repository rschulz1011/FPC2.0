<?php

session_start();

class Page
{

  //class Page's attributes
  
  public $content;
  public $title = "Football Picking Championship";
  public $keywords = "Football picking, pick'em";
                        
  //class Page's operations
  public function __set($name,$value)
  {
     $this->$name = $value;
  }
  
  public function Display()
  {
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     $this -> authenticateUser();
     $this -> DisplayMenu();
     echo "<hr>";
     echo $this->content;
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
  }
  
  public function DisplayTitle()
  {
     echo "<title>".$this->title."</title>";
  }
  
  public function DisplayKeywords()
  {
     echo "<meta name=\"keywords\"
            content=\"".$this->keywords."\"/>";
  }
  
  public function DisplayStyles()
  {
   ?>
   <style type="text/css">
   h1 {color:red; font-size:24pt; text-align:center;
       font-family:arial,sans-serif;font-weight:bold}
   .menu {color:red; font-size:12pt; text-align:center;
       font-family:arial,sans-serif; font-weight:bold}
   td.foot {background:red}
   p {color:black; font-size:12pt; text-align:justify;
      font-family:arial,sans-serif}
   p.foot {color:white; font-size:9pt; text-align:center;
       font-family:arial,sans-serif; font-weight:bold}
   g.good {color:green}
   g.bad {color:red}
   a:link,a:visited,a:active {color:red}
   a.normal {color:blue}
   th.vpheader{font-size:70%; font-family:Arial}
   tr.shaded {background:AliceBlue}
   tr.userpick {font-size:60%; font-family:Arial}
   tr.highlight {background:yellow}
   td.goodpick {color:green; font-weight:bold}
   td.pushpick {color:#FFCC33; font-weight:bold}
   td.badpick {color:red; font-weight:bold; text-decoration: line-through}
   td.pendingpick {color:blue; font-weight:bold; font-style: italic}
   td.vppending {background:LightCyan; font-style: italic}
   td.vpcorrect {background:#80CC99}
   td.vppush {background:Yellow}
   td.vpwrong {background:#FF6666}
   td.vphidden {background:Silver}
   td.vpempty {background:LightGrey}
   table.specialcontainer {text-align:center; background-color: #F5F5F5; width:80%}
   table.specialcontainer p {margin: 0 0 0 0; padding: 0 0 0 0; font-size: .7em}
   table.specialcontainer img {margin-top:.1em; padding-top:0}
   table.specialstanding {text-align:center; width:100%; background-color:#EEEEEE}
   td.specialinfo {width:20%}
   </style>
   <?php
   }
  
  public function DisplayHeader()
  {
   ?>
   <table width="100%" cellpadding="12" cellspacing="0" border="0">
   <tr>
   <td align="left"><img src="Logo.png" alt="FPC logo" height="70" width="120"></td>
   <td>
      <h1>The Football Picking Championship</h1>
   </td>
   <td align="right"><img src="Logo.png" alt="FPC logo" height="70" width="120"/></td>
   </tr>
   </table>
   <?php
   }
   
   public function DisplayMenu()
   {
      
      if (isset($_SESSION['adminlev']))
      {
         if ($_SESSION['adminlev']==0)
         {
             $buttons = array("Home"=>"index.php",
                          "Member Home"=>"mhome.php",
                          "Current Standings"=>"overallstandings.php",
                          "Contact Us"=> "contact.php",);
         }
         else
         {
            $buttons = array("Home"=>"index.php",
                          "Member Home"=>"mhome.php",
                          "Current Standings"=>"overallstandings.php",
                          "Contact Us"=> "contact.php",
                          "Admin Home"=>"adminhome.php",);
         }
      
      }
      else
      {
            $buttons = array("Home" => "index.php",
                        "Contact Us" => "contact.php",
                        "Login" => "login.php",);
      }
      
      echo "<table width=\"100%\" bgcolor=\"white\"
            cellpadding=\"4\" cellspacing=\"4\">\n";
      echo "<tr>\n";
      
      //calculate button SIZE
      $width = 100/count($buttons);
      
      while (list($name,$url)=each($buttons)) {
      $this -> DisplayButton($width,$name,$url,!$this->IsURLCurrentPage($url));
      }
      
      echo "</tr>\n";
      echo "</table>\n";
   }
   
   public function IsURLCurrentPage($url)
   {
      if(strpos($_SERVER['PHP_SELF'],$url)==false)
      {
         return false;
      }
      else
      {
          return true;
      }
      
   }

  public function authenticateUser()
   {
        if (isset($_SESSION['username']))
        {
           @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
           $query = "select password, admin from user where username='".$_SESSION['username']."' 
           and  password='".$_SESSION['password']."'";
           
           $result = $db->query($query);
           
           $num_rows = $result->num_rows;
           
           if ($num_rows==1) {
                 echo "<font size=\"2\" color=\"red\">You are logged in as:&nbsp &nbsp ".$_SESSION['username'].
                   "&nbsp &nbsp<a href=\"logout.php\">LOG OUT</a></font>";
                 $row = $result->fetch_assoc();
                 $_SESSION['adminlev'] = $row['admin'];
                 return 1;
            }

        } 
       
   }

   public function DisplayButton($width,$name,$url,$active=true)
   {
      if ($active) {
        echo "<td width=\"".$width."%\">
          <a href=\"".$url."\">
          <img src=\"nothere.png\" alt=\"".$name."\" border=\"0\" /></a>
          <a href=\"".$url."\"><span class=\"menu\">".$name."<span></a>
          </td>";
          }
          else
          {
          echo "<td width=\"".$width."%\">
            <img src=\"here.png\">
            <span class=\"menu\">".$name."</span>
            </td>";
          }
    }
      
    public function DisplayFooter()
    {
    ?>
   <table width="100%" bgcolor="white" cellpadding="12" border="0">
   <tr>
     <td class="foot">
        <p class="foot">&copy; Football Picking Championship.</p>
     </td>
   </tr>
   </table>
   <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-28438752-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
   <?php
   }
  
}

?>
     
     