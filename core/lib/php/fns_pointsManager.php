<?php
require_once("fns_db.php");
require_once("cls_profileManager.php");
require_once("cls_domainManager.php");
require_once("cls_itemManager.php");
define("POINTFACTOR_VIEWS", 10);
define("POINTFACTOR_LIKES", 15);
define("POINTFACTOR_SHARES", 25);
define("POINTFACTOR_PIC",300);
define("POINTFACTOR_BIO",200);
define("POINTFACTOR_INITIAL_UPLOAD",100);
define("POINTFACTOR_INITIAL_CONTENT",100);
define("POINTFACTOR_CONTRIBUTE_ITEM",100);
define("POINTFACTOR_SHARE_RESPONSE",10);
class PointsManager
{
	public static function updateItemPoints($item)
	{
		$databaseName=null;
		$item->points=$item->calculatePoints();
		if(!is_null($item->domainId))
			$databaseName=domain::getDatabaseNameById($item->domainId);
		else if(!is_null($item->databaseName))
			$databaseName=$item->databaseName;
		
			$conn=dbi_connect_x($databaseName);
			if (!$conn)
 				return false;
			$query="UPDATE item
					SET points=".$item->points." 
					WHERE item_id=".$item->id;
		
		$result=mysqli_query($conn,$query);
		if($result)
		{
			$obj_profile = new profile($item->profileId);
			
			return PointsManager::updateProfilePoints($obj_profile);
		}
		else
			return false;	
		
		/*performance risk */
		
		
	}
	public static function updateProfilePoints($profile)
	{
		$profilePoints=$profile->calculatePoints();
		
		$obj_itemManager= new itemManager("PROFILE",$profile->id,0);
		$items=array();
		$itemPoints=0;
		$obj_itemManager->getItems($items);
		for($x=0;$x<count($items);$x++)
			$itemPoints=$itemPoints + $items[$x]->getPoints();
		
		$profileItemPoints=$profilePoints+$itemPoints;
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$query="UPDATE profile
				SET points=".$profileItemPoints." 
				WHERE profile_id=".$profile->id;
		
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;	
	}
}


?>