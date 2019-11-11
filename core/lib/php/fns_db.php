<?php
require_once("core.php");
require_once("fns_errorHandler.php");

function dbi_connect($databaseName="rayarcca_admin")
{
	$host="localhost";
	$user="rayarcca_root";
	$pass="P@$$!0n";

	$result=mysqli_connect($host,$user,$pass,$databaseName);

	if (!$result)
	{
	 	$errMsg="Error: Unable to connect to MySQL DB=".$databaseName;
	 	$errMsg.=";Debugging error: ".mysqli_connect_error();
	 	errorHandler(mysqli_connect_errno(),$errMsg,1,"dbi_connect","fns_db.php");
		return false;
	}

	return $result;
}

function dbi_connect_x($databaseName=null,$host=null,$user=null,$pass=null,$caller=null)
{
	$caller=print_r(debug_backtrace(),true);

	if($host==null and $user== null and $pass==null)
	{
		$host="localhost";
		$user="rayarcca_root";
		$pass="P@$$!0n";
	}
	
	if(is_null($databaseName))
	 {
	 	$databaseName=getDatabaseName();
	 }
	 else if($databaseName===0){
	 	$databaseName="rayarcca_admin";
	 }

	$result=mysqli_connect($host,$user,$pass,$databaseName);

	if (!$result)
	{
		$errMsg="Error: caller-".$caller." Unable to connect to MySQL DB=".$databaseName;
		$errMsg.="<BR>Debugging error: ".mysqli_connect_error();
		errorHandler(0,$errMsg,0,"dbi_connect_x","fns_db.php");
		return false;
	} 	
	 		
	 	return $result;
}


function db_connect_x($databaseName=null,$host=null,$user=null,$pass=null)
{

	if($host==null and $user== null and $pass==null)
	{
		$host="localhost";
		$user="rayarcca_root";
		$pass="P@$$!0n";
	}
	
	$result=mysql_pconnect($host,$user,$pass);

	if (!$result)
	 return false;
 
	if(is_null($databaseName))
	{
		$databaseName=getDatabaseName();		
	}
	else if($databaseName===0){
		$databaseName="rayarcca_admin";	
	}

	echo "<BR>databaseName=".$databaseName."<BR>";
 	if (!mysql_select_db($databaseName))
 		return false;
 		
 	return $result;
}
function getDatabaseName()
{
	if(!is_null(getsubDomainName()))
		$subDomain=getsubDomainName();
	else
		$subDomain="admin";		
			
 	return "rayarcca_".$subDomain;
}
 
?>