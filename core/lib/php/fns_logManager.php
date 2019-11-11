<?php
//notification when a user has an error uploading a file
//log tracking file upload activities at the subdomain level
require_once("core.php");

if(strrpos($_SERVER['SERVER_NAME'],"localhost")!==false)
{
	if(!is_null(getsubDomainName()))
		$domainName="\\".getsubDomainName();
	else
		$domainName="";
	//echo "domainName=".$domainName;
	define( 'LOGFILEPATH', "C:\\wwwroot\\rayarc".$domainName."\\logs\\activity_".date('Y-m-d').".log" );
}
else
{
	if(!is_null(getsubDomainName()))
		$domainName="/".getsubDomainName();
	else
		$domainName="";
	define( 'LOGFILEPATH', "/home/rayarcca/public_html".$domainName."/logs/activity_".date('Y-m-d').".log" );
}
function logManager($logType,$logMsg,$memberID,$sessionID,$fileName=null)
{
	if(is_null($fileName))
		$fileName=curPageName();
	
	$dt = date('Y-m-d H:i:s (T)');
	//$logEntry="1999-03-28 16:30:01 | INFO | Start upload for video000.avi | memberID=1 | session=999.99999.9999.1111"
	$logEntry=$dt." | ".$logType." | ".$fileName." | ".$logMsg." | memberID=".$memberID." | sessionID=".$sessionID."\n";
	
	$retval=error_log($logEntry,3,LOGFILEPATH);
		
	if($logType=="ERROR")
	{
		$headers = 'From: system@rayarc.ca' . "\r\n" .
    'Reply-To: system@rayarc.ca' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

		mail("info@rayarc.com","Critical Error: Upload Process",$logMsg,$headers);
		require_once("fns_email.php");
		$from="noreply@rayarc.ca";
		$to="mgoode@rayarc.com";
		$subject="Upload Processing error: memberID=".$memberID;
		$message=$logMsg;
		$fromName="Rayarc Log Manager";
		$toName="Rayarc Info";
		$bcc="system@rayarc.ca";
		$emailSent=fn_sendEmail($from,$to,$subject,$message,$fromName,$toName,$bcc);
		if($emailSent==true)
			echo "sent email=true";
		else
			echo "sent email=false";
		return $emailSent;
	}
	return $retval;
	/*if($emailflag==1)
	{
	 	
	}*/
}




?>