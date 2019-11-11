<?php
require_once("core.php");

//var $errFilePath="/home/rayarcca/public_html/err/error.log";
$domainName=getsubDomainName();
require_once('fns_email.php');

if(strrpos($_SERVER['SERVER_NAME'],"localhost")!=false)
{
	if(is_null($domainName))
		$domainName="";
	else 
		$domainName.="\\";
	define( 'ERRFILEPATH', "C:\\wwwroot\\rayarc\\".$domainName."logs\\err\\error_".date('Y-m-d').".log" );
}
else
{
	if(is_null($domainName))
		$domainName="";
	else
		$domainName.="/";
	define( 'ERRFILEPATH', "/home/rayarcca/public_html/".$domainName."logs/err/error_".date('Y-m-d').".log" );
}
	
function errorHandler($errNo,$errMsg,$emailflag=0,$fileName=null,$functionName="")
{
	if(is_null($fileName))
		$fileName=curPageName();
	
	$dt = date('Y-m-d H:i:s (T)');
	
	if(!is_null(getsubDomainName()))
		$domainPath="/".getsubDomainName();
	else
		$domainPath="";
		
	$retval=error_log($dt."\t".$errNo."\t".$fileName." | ".$errMsg."\n",3,CENTRAL_DOCUMENT_ROOT.$domainPath."/logs/err/error_".date('Y-m-d').".log");
	if($emailflag==1)
	{
	 	$headers = 'From: system@rayarc.ca' . "\r\n" .
    'Reply-To: system@rayarc.ca' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

		//mail("info@rayarc.ca","Critical Error",$errMsg,$headers);
	}
	
	return $retval;
}

function errorDisplay()
{
	if(isset($_SESSION['e']))
	{
	
		$errMsg=$_SESSION['e'];
  		unset($_SESSION['e']);
		
		for($i=0;$i<count($errMsg);$i++)
			echo "<div>".$errMsg[$i]."</div>";
	
	}
	

}

?>
