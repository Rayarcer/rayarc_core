<?php
require_once("fns_db.php");
//require_once("cls_itemManager.php");
require_once("fns_pointsManager.php");
require_once("fns_errorHandler.php");
//require_once("core.php");
class profile
{
	var $id;
	var $key;
	var $name;
	var $shortBio;
	var $status;
	var $imagePath;
	var $created;
	var $memberId;
	var $roleId;
	var $roleTitle;
	var $domainId;
	var $points=0;
	
	//initialize item using an existing item ID or if null make a new item;
	function init_class_object($row,&$obj_profile)
	{
			//$obj_eblast= new eblast();
			//echo "row profile_id=".$row->profile_id;
			$obj_profile->id=$row->profile_id;
			$obj_profile->key=$row->profile_key;
			$obj_profile->name=$row->profile_name;
			$obj_profile->shortBio=$row->short_bio;
			$obj_profile->status=$row->status;
			$obj_profile->imagePath=$row->image_path;
			$obj_profile->created=$row->created;
			$obj_profile->memberId=$row->member_id;
			$obj_profile->roleId=$row->role_id;
			$obj_profile->roleTitle=$row->role_title;	
			$obj_profile->points=$row->points;			
	}	
	
	
	function profile($id=null)
	{
		if ($id==null)
		{
			$this->id=null;
			$this->key=randString(8);
			$this->status="N";
			$this->shortBio=NULL;	
			return;
		}

		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$query="select p.*,r.role_title from profile p JOIN role r ON p.role_id=r.role_id where profile_id=$id";		
		
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;	
	    $row_count=mysqli_num_rows($result);
		
		if($row_count==1)
		{
			$row = mysqli_fetch_object($result);	
			$this->init_class_object($row,$this);			
		 }
		 else
		{
			$this->id=null;	
			return;
		}					 
	}
	function getByName($name)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$query="select p.*,r.role_title from profile p JOIN role r ON p.role_id=r.role_id where REPLACE(profile_name,' ','')='".$name."'";		
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;	
	    $row_count=mysqli_num_rows($result);
		//echo "profile row_count=".$row_count;

		if($row_count>=1)
		{
			$row = mysqli_fetch_object($result);	
			$this->init_class_object($row,$this);			
		 }
		 else
		{
			$this->id=null;	
			return;
		}					 
	}
	function getByKey($key)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$query="select p.*,r.role_title from profile p JOIN role r ON p.role_id=r.role_id where profile_key='".$key."'";
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
		$row_count=mysqli_num_rows($result);
		//echo "profile row_count=".$row_count;
		
		
		if($row_count>=1)
		{
			$row = mysqli_fetch_object($result);
			$this->init_class_object($row,$this);
		}
		else
		{
			$this->id=null;
			return;
		}		
	}
	
	function getVanityName()
	{
		return str_replace(" ","",$this->name);	
	}
	
	function getItemUploadCount($includePendingStatus=false)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return false;
			
		$pendingStatusOption=",'PENDING'";	
			
		$query="select count(*) from item where profile_id=".$this->id." AND item_status in ('ACCEPTED','PUBLISHED'".$pendingStatusOption.")";
		//$query="select count(*) from item where profile_id=".$this->id;
		
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysqli_fetch_array($result);
			return $row[0]; 
		}
		return 0;
	}
	
	function calculatePoints()
	{
		$initialUploadPoints=0;
		$profilePicPoints=0;
		$profileBioPoints=0;
		
		if($this->getItemUploadCount()>0)
			$initialUploadPoints=POINTFACTOR_INITIAL_CONTENT;
		
		if($this->imagePath)
			$profilePicPoints=POINTFACTOR_PIC;	
		
		$totalPoints=$initialUploadPoints+$profilePicPoints;
		
		if($this->shortBio)
			$profileBioPoints=POINTFACTOR_BIO;	
		
		$totalPoints=$initialUploadPoints+$profilePicPoints+$profileBioPoints;
		
		return $totalPoints;	
	}
	
	function setPoints($points)
	{
		$this->points=$points;
	}
	
	
	function getPoints()
	{	/*
		$initialUploadPoints=0;
		$profilePicPoints=0;
		$profileBioPoints=0;
		//TODO: move to item get Points;
		
		//if ($this->getItemUploadCount()>0)
			$initialUploadPoints= POINTFACTOR_CONTRIBUTE_ITEM *$this->getItemUploadCount();
		
		if($this->imagePath)
			$profilePicPoints=POINTFACTOR_PIC;	
		
		$totalPoints=$initialUploadPoints+$profilePicPoints;
		
		if($this->shortBio)
			$profileBioPoints=POINTFACTOR_BIO;	
		
		$totalPoints=$initialUploadPoints+$profilePicPoints+$profileBioPoints;
		
		return $totalPoints;
		*/
		//$this->updatePoints();
		//$this->updatePoints();
		return $this->points;
			
	}
	function updatePoints()
	{
		return PointsManager::updateProfilePoints($this);
		//$this->points=$this->calucatePoints();
		/*
		$conn=dbi_connect();if (!$conn)
 			return false;
		$query="UPDATE profile
				SET points=".$this->points." 
				WHERE profile_id=".$this->id;
		
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;	
		*/
	}
	
	function loadFirstProfileByMemberId($memberId)
	{
	$conn=dbi_connect();
	if (!$conn)
 			return false;
	 	
		$query="select min(profile_id) as first_profile_id from profile where 
			member_id=$memberId";
		//echo $query;	

		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
	    		
		$row_count=mysqli_num_rows($result);
		
		if($row_count==1)
		{
			$row =mysqli_fetch_object($result);	
	 		$this->profile($row->first_profile_id);
		 }
		  else
		{
			$this->id=null;	
			return;
		}			
		 				 
	}
	
	function loadLastProfileByMemberId($memberId)
	{
	$conn=dbi_connect();
	if (!$conn)
 			return false;
	 	
		$query="select max(profile_id) as last_profile_id from profile where 
			member_id=$memberId";
		//echo $query;	
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
	    		
		$row_count=mysqli_num_rows($result);
		
		if($row_count==1)
		{
			$row = mysqli_fetch_object($result);	
	 		$this->profile($row->last_profile_id);
		 }
		  else
		{
			$this->id=null;	
			return;
		}			
		 				 
	}
 function exist()
   {
   		
		if($this->id===Null)
			return true;
  		else
			return false;
   }



 function save()
	{
		//if( getProfileIdbyName($this->name)!=false)
		//	return false;
		$conn=dbi_connect();
		if (!$conn)
 			return  false;
		//echo "Profile ID=".$this->id;
		if ($this->id===null)
		{	
		
			//$roleId = empty($this->roleId) ? "NULL" : mysql_real_escape_string($this->roleId);
			$query="INSERT INTO `profile`(`profile_id`, `profile_key`, `profile_name`, `short_bio`, `image_path`, `status`, `created`, `member_id`, `role_id`) 
					VALUES (Null,'".$this->key."','".$this->name."','".$this->shortBio."','".$this->imagePath."','".$this->status."',now(),".$this->memberId.",".$this->roleId.")";
			
		
			//echo $query;
		}	
		//echo $query;
		
			$result=mysqli_query($conn,$query);
			
			if($result)
			{	
				$id = mysqli_insert_id($conn);
				if($id==0)
					return false;
				
				$this->id=$id;	
				return true;
        	}
        	echo mysqli_error($conn);
        	return false;
	}  
	function setImagePath($imagePath)
	{
		$this->imagePath=$imagePath;

		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$query="UPDATE profile
				SET image_path='".$imagePath."'
				WHERE profile_id=".$this->id;
		
		$result=mysqli_query($conn,$query);
		if($result)
		{
			$this->updatePoints();
			return true;
		}
		else
			return false;	
	
	}
	function updateStatus($statusCode)
	{
		$this->status=$statusCode;

		$conn=dbi_connect();
		if (!$conn)
			return false;
			$query="UPDATE profile
				SET status='".$this->status."'
				WHERE profile_id=".$this->id;
		
			$result=mysqli_query($conn,$query);
			if($result)
			{
				//$this->updatePoints();
				return true;
			}
			else
				return false;
	}
	function isComplete()
	{
		if($this->status=='C')
			return true;
		else
			return false;
	}
	
	function setBio($strBio)
	{
		$this->shortBio=$strBio;

		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$query="UPDATE profile
				SET short_bio='".addslashes($strBio)."'
				WHERE profile_id=".$this->id;
		
		$result=mysqli_query($conn,$query);
		if($result)
		{
			$this->updatePoints();
			return true;
		}
		else
			return false;	
	
	}
	
	
// end of profile class	
}

class profileManager
{
	var $profileMgr = array();
	var $totalCount;
	
	//initialize item using an existing item ID or if null make a new item;
	function profileManager($filter=null,$domainID=null,$limit=null,$page=1,$imageOnly=false,$rand=false,$orderByLatest=false)
	{
		$whereConditionExist=false;
		$domainSpecified=false;
		$conn=dbi_connect();
		if (!$conn)
 			return false;

		switch ($filter)
		{
			case "ALL":
			$query="select * from profile p"; 
			$subQuery=" JOIN role r ON p.role_id=r.role_id";
			$whereConditionExit=false;
			if(!is_null($domainID))
			{
				$subQuery.=" JOIN domain_profile dp ON p.profile_id=dp.profile_id";
				$domainSpecified=true;
			}	
			if($domainSpecified==true)
			{
				$subQuery.=" WHERE dp.domain_id =".$domainID;
				$whereConditionExist=true;	
			}
			
			if($imageOnly)
			{	
				if(!$whereConditionExist)
					$subQuery.=" WHERE image_path is NOT NULL AND image_path!=''";
				else
					$subQuery.=" AND image_path is NOT NULL AND image_path!=''";
				$whereConditionExist=true;
			}
		
			 $this->getTotalCount($subQuery);
			
			if($rand)
				$subQuery.=" ORDER BY RAND()";
			if($orderByLatest)
				$subQuery.=" ORDER BY p.points DESC,created DESC";
			
			$offset=($page-1)*$limit;
			if(!is_null($limit))
				$subQuery.=" LIMIT ".$offset.",".$limit;
				
				
				$query.=$subQuery;
				
				
			break;
			default:
				$query="";
				return;		
		}//end of switch statement
				//exit;	
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;	
	    $row_count=mysqli_num_rows($result);
		
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);
			$obj_profile = new profile();
			$obj_profile->init_class_object($row,$obj_profile);
			$this->profileMgr[]=$obj_profile;
		}				 
	} //end of constructor class
	
	function loadProfilesByMemberID($ID)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$query="select p.*,r.role_title from profile p JOIN role r ON p.role_id=r.role_id WHERE member_id=".$ID;
		
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$errMsg=";Debugging error: ".mysqli_connect_error();
			errorHandler(mysqli_connect_errno(),$errMsg,1,"getProfilesByMemberID","cls_profileManager.php");
			return false;
		}
		
		
			$row_count=mysqli_num_rows($result);
		
			for($i=0;$i<$row_count; $i++)
			{
				$row = mysqli_fetch_object($result);
				$obj_profile = new profile();
				$obj_profile->init_class_object($row,$obj_profile);
				$this->profileMgr[]=$obj_profile;
			}
		
	}
	

	
	
	function addSearchProfiles($searchWords,$limit=null,$page=1)
	{	
		$conn=dbi_connect();
		if (!$conn)
 			return false;
								
	$query="select p.*,r.role_title from profile p JOIN role r ON p.role_id=r.role_id";
	
	$subQuery=" WHERE profile_name LIKE '%".$searchWords."%' OR short_bio LIKE '%".$searchWords."%'";	
	
	$this->getTotalCount($subQuery);
	$offset=($page-1)*$limit;
			if(!is_null($limit))
				$subQuery.=" LIMIT ".$offset.",".$limit;
	$query.=$subQuery;
	 
	$result=mysqli_query($conn,$query);
	
		if(!$result)
			return false;
			
	    $row_count=mysqli_num_rows($result);
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);
			$obj_profile = new profile();
			$obj_profile->init_class_object($row,$obj_profile);
			$this->profileMgr[]=$obj_profile;
		}							
	
	
	}
	
	function getCount()
	{
 		return count($this->profileMgr);
	}
	function getTotalCount($subQuery)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		
		$query="select count(*)  from profile p"; 
		$query.=$subQuery;	
			
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;	
	    $row_count=mysqli_num_rows($result);
		
		$row = mysqli_fetch_row($result);
		$this->totalCount=$row[0];
		
	
	}
	function getProfiles(&$profileItems)
	{
		for($i=0;$i<$this->getCount(); $i++)
			$profileItems[]=$this->profileMgr[$i];	 
	}
	
static function cmp_profileCreated($a, $b)
    {
        $al = strtolower($a->created);
        $bl = strtolower($b->created);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
static function cmp_profileImage($a, $b)
    {
        $al = strtolower($a->imagePath);
        $bl = strtolower($b->image_path);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

} // end of commentManager class


function getProfileIdByName($name)
{
	$conn=dbi_connect();
	if (!$conn)
 		return  false;
 
	$query="select profile_id from profile where profile_name='$name'";
	$result=mysqli_query($conn,$query);
	if(!$result)
		return false;
	$rowCount=mysqli_num_rows($result);
	//echo "RowCount=".$rowCount;
	if ($rowCount==1)
	{	
		$row = mysqli_fetch_object($result);	
		return $row->profile_id;
	}
	else
	{
		return false;  
	}
}


?>
