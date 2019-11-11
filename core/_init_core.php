<?php 
require_once("lib/php/core.php");
ini_set('session.cookie_domain', RAWCENTRALHOST);
//ini_set('session.cookie_domain', ".".substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'],".")+1, 100));
//ini_set('session.cookie_domain', ".".SLD);

if (!isset($_SESSION)) 
  session_start(); 

 // echo  "session_id=".session_id()."<BR>";
 // echo  "host=".RAWCENTRALHOST;
  
?>
