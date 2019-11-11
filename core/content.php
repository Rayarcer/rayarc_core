<?php 
require_once("../core/_init_core.php");
require_once("../core/lib/php/cls_itemManager.php");
require_once("../core/lib/php/cls_profileManager.php");

//echo "checking content";

if(isset($_GET["domain"]))
	$_SESSION['loginSession_domain']=$_GET["domain"];
 else
 	$_SESSION['loginSession_domain']=getsubDomainName();

 	$obj_domain= new domain();
 	//echo "get domain";
 	$obj_domain->getDomainByName(getsubDomainName());
 	$databaseName=$obj_domain->databaseName;
 	//echo "databaseName=".$databaseName;
 	$obj_item= new item(null,$databaseName);
 	$getItemKey=false;

 	//$_GET["ikey"]="CHRSMIX"; /* all content data gets pulled from here */
	 
 	if(isset($_GET["ikey"]))
 	{
 		
 		$obj_item->getItemByKey($_GET["ikey"]);
 		$getItemKey=true;
 		if(!$obj_item->id)
 		{
 			header('HTTP/1.1 404 Not Found');
 			//echo "404 file Not found";
 			include("404.php");
 			die;
 		}
 	}
 	else
 	{
 		header('HTTP/1.1 404 Not Found');
 		//echo "404 file Not found";
 		include("404.php");
 		die;
 	}
	$itemTitle=$obj_item->title." | ".$obj_domain->domainTitle;
	$title=$itemTitle;
	$desc=$obj_item->desc;
	$image=DOMAINHOST."/".$obj_item->image;
	$domainTitle=$obj_domain->domainTitle;

	if ($obj_item->isPlayList() || $obj_item->isAudio())
		include("content_album.php");
	else if($obj_item->isVideo() || $obj_item->isEmbed())
		include("content_video.php");
?>
						
				