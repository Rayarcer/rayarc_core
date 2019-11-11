<?php
require_once("fns_db.php");
require_once("cls_clientManager.php");
require_once("fns_client.php"); 
require_once("fns_errorHandler.php"); 
require_once("fns_logManager.php");
require_once("fns_pointsManager.php"); 
class item
{
	var $id;
	var $ikey;
	var $title;
	var $desc;
	var $longDesc;
	var $image;
	var $duration;
	var $contentSource;
	var $downloadSource;
	var $status;
	var $access;
	var $onFeature;
	var $downloadable;
	var $dateAdded;
	var $expires;
	var $parentId;
	var $profileId;
	var $contentTypeId;
	var $contentProviderId;
	var $contentTypeName;
	var $tag;
	var $ratingValue=-1;
	var $domainId=null;
	var $databaseName=null;
	var $genres= array();
	var $genresId=array();
	var $points=0;
	var $sequenceId=0;
	//initialize item using an existing item ID or if null make a new item;
	
	function init_class_object($row,&$obj_item)
	{
			//$obj_eblast= new eblast();
			$obj_item->id=$row->item_id;
			$obj_item->ikey=$row->item_key;	
	 		$obj_item->title=$row->item_title;
			$obj_item->desc=$row->item_desc;
			$obj_item->longDesc=$row->item_long_desc;
			$obj_item->image=$row->item_image;
			$obj_item->duration=$row->item_duration;
			$obj_item->contentSource=$row->item_content_source;
			$obj_item->access=$row->item_access;
			$obj_item->status=$row->item_status;
			$obj_item->onFeature=$row->item_on_feature;
	 		$obj_item->downloadable=$row->item_downloadable;
			$obj_item->dateAdded=$row->item_date_added;
			$obj_item->expires=$row->item_expires;
			$obj_item->parentId=$row->item_parent_id;
			$obj_item->profileId=$row->profile_id;
			$obj_item->contentTypeId=$row->item_content_type_id;
			$obj_item->contentProviderId=$row->item_content_provider_id;
			$obj_item->tag=$row->item_tag;
			$obj_item->points=$row->points;
			if (array_key_exists('sequence_id', $row))
				$obj_item->sequenceId=$row->sequence_id;
			if (array_key_exists('domain_id', $row))
				$obj_item->domainId=$row->domain_id;	
			$obj_item->loadGenres($obj_item->id,$obj_item->domainId);	
		    $obj_item->loadGenreIds($obj_item->id,$obj_item->domainId);
		
	}	
	
	function item($id=null,$databaseName=null,$adminItemDomainId=null)
	{
		
		if(!is_null($databaseName))
		{	
			$this->databaseName=$databaseName;
		}
		
		if(!is_null($adminItemDomainId))
		{
			$this->domainId=$adminItemDomainId;
		}
		if ($id===null)
		{
			$this->id=null;	
			$this->status="PENDING";
			$this->ikey=randString(8);
			$this->duration=0;
			$this->onFeature=0;
			$this->downloadable=0;
			$this->parentId="NULL";
			return;
		}
		$conn=dbi_connect_x($databaseName,null,null,null,"item");
		if (!$conn)
		{
 			echo "no database connection";
			return false;
		}
		
		$query="Select * from item where item_id=".$id;
		if(!is_null($adminItemDomainId))
			$query.=" and domainID=".$this->domainId;
	
		
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$this->id=null;	
			return;
		}
		$row_count=mysqli_num_rows($result);
		$row = mysqli_fetch_object($result);	
		$this->init_class_object($row,$this);
					
	}
	private function loadGenres($id,$domainId=null)
	{	
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
		$query="SELECT distinct g.genre_title as genre_title
				FROM `item_genre` ig
				JOIN rayarcca_admin.genre g
				ON g.genre_id=ig.genre_id
				WHERE item_id=".$id;
		
		if(!is_null($domainId))
			$query.=" and domain_id=".$this->domainId;
		
		
		$result=mysqli_query($conn,$query);

		if(!$result)
		{
			errorHandler(session_id(),"failed result for query:".$query,0,"cls_itemManager.php","loadGenres");
			return false;
		}
		$row_count=mysqli_num_rows($result);

	 	for($i=0;$i<$row_count; $i++)
	 	{
			$row = mysqli_fetch_object($result);	

	 		$this->genres[]=$row->genre_title;
	 	}
	}
	
	private function loadGenreIds($id,$domainId=null)
	{
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
		$query="SELECT distinct g.genre_id as genre_id
				FROM `item_genre` ig
				JOIN rayarcca_admin.genre g
				ON g.genre_id=ig.genre_id
				WHERE item_id=".$id;
		
		if(!is_null($domainId))
			$query.=" and domain_id=".$this->domainId;
		
		$result=mysqli_query($conn,$query);
		
		if(!$result)
		{
			errorHandler(session_id(),"failed result for query:".$query,0,"cls_itemManager.php","loadGenreIds");
			return false;
		}
		$row_count=mysqli_num_rows($result);
	 	for($i=0;$i<$row_count; $i++)
	 	{
			$row = mysqli_fetch_object($result);	
	 		$this->genresId[]=$row->genre_id;
	 	}
	}
	
	function getItemByContentSource($uploadFilePath)
	{
		$conn=!dbi_connect_x($this->databaseName);
		if($conn)
			return false;
			$query="Select * from item where item_content_source='".$uploadFilePath."'";
			//echo $query;
			//exit;
			$result=mysqli_query($conn,$query);
		
			if(!$result)
			{
				$this->id=null;
				$this->ikey=null;
				return;
			}
			$row_count=mysqli_num_rows($result);
			if($row_count==1)
			{
				$row = mysqli_fetch_object($result);
				$this->init_class_object($row,$this);
			}
			else
			{
				$this->id=null;
				$this->ikey=null;
			}
	}
	
	function delete()
	{
		$conn=!dbi_connect_x($this->databaseName);
		if($conn)
			return false;
		$query="Delete from item where item_id=".$this->id;
		$result=mysqli_query($conn,$query);

	
		if (!$result) {
			return false;
		}
		else
			return true;
		
	}
	function getItemByKey($itemKey)
	{
		$conn=dbi_connect_x($this->databaseName);
		//echo $this->databaseName; 
		if(!$conn)
 			return false;
		$query="Select * from item where item_key='".$itemKey."'";
		//echo $query;
		//exit;
		$result=mysqli_query($conn,$query);
		
		if(!$result)
		{
			$this->id=null;
			$this->ikey=null;
			return;
		}
		$row_count=mysqli_num_rows($result);
		//echo "ROW COUNT=".$row_count;
		if($row_count==1)
		{
			$row = mysqli_fetch_object($result);	
			//$this->item(null,$this->dataSource);
			$this->init_class_object($row,$this);
		}
		else
		{	
			$this->id=null;
			$this->ikey=null;
		}
	}
	
	function add()
	{
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 		return false;	
		$this->dateAdded=date("Y-m-d H:i:s", time());
		$query="INSERT INTO `item`(`item_id`, `item_key`, `item_title`, `item_desc`, `item_long_desc`, `item_image`, `item_duration`, `item_content_source`, `item_download_source`, `item_status`, `item_access`, `item_on_feature`, `item_downloadable`, `item_date_added`, `item_expires`, `item_parent_id`, `profile_id`, `item_content_type_id`,`item_content_provider_id`) 
VALUES (NULL,'".$this->ikey."',
'".mysqli_real_escape_string($conn,$this->title)."','".mysqli_real_escape_string($conn,$this->desc)."','".mysqli_real_escape_string($conn,$this->longDesc)."','".$this->image."',".$this->duration.",'".$this->contentSource."','".$this->downloadSource."','".$this->status."','".$this->access."',".$this->onFeature.",".$this->downloadable.",'".$this->dateAdded."',NULL,".$this->parentId.",".$this->profileId.",'".$this->contentTypeId."','".$this->contentProviderId."')";
		//echo $query;
		//exit;
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			errorHandler(session_id(),"failed result for query:".$query,0,"cls_itemManager.php","add");
			$this->id=null;
			return false;
		}
		$id = mysqli_insert_id($conn); 
		$this->id=$id;
		//echo "saveMemberInfo=".$this->id;
		return $this->id;
	}
	function addGenre($genreId)
	{
		$conn=dbi_connect_x();
		if (!$conn)
			return false;
		
			$query="INSERT INTO `item_genre`(`item_id`, `genre_id`) 
					VALUES (".$this->id.",".$genreId.")";
		
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			errorHandler(session_id(),"failed result for query:".$query,0,"cls_itemManager.php","addGenre");
			return false;
		}
		return true;	
	}
	
	function setImage($imagePath,$temp=false)
	{	
		$conn=dbi_connect_x();
		if (!$conn)
			return false;
					
		$query="UPDATE item
		SET item_image='".$imagePath."'
		 WHERE item_id=".$this->id."";
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;
	}

	function getContentSource_youtubeEmbedURL()
	{
		if($this->contentTypeId=="E")
		{	
			$image_url = parse_url($this->contentSource);
			if($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com')
			{
        		$array = explode("&", $image_url['query']);
				return "http://www.youtube.com/embed/".substr($array[0], 2);
			}
			else if ($image_url['host'] == 'youtu.be')
			{
				$array=explode("/",$this->contentSource);
				return "http://www.youtube.com/embed/".end($array);
			}
			return null;
		}
		else
			return null;	
	}
	function getContentSource_youtubeID()
	{
		if($this->contentProviderId=="yt")
		{
			$image_url = parse_url($this->contentSource);
			if($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com')
			{
        		$array = explode("&", $image_url['query']);
				return substr($array[0], 2);
			}
			else if ($image_url['host'] == 'youtu.be')
			{
				$array=explode("/",$this->contentSource);
				return end($array);
			}
			return null;
		}
		else
			return null;	
	}
	
	function getContentSource($ext=null)
	{
		if($ext===null)
			return $this->contentSource;
			
		if($this->contentTypeId=="V" or $this->contentTypeId=="A")
		{
			$info = pathinfo($this->contentSource);
			if($info['extension']==$ext)
				return $this->contentSource;
				
			$contentSourceEXT=$info['dirname']."/".$info['filename'].".".$ext;
			
			//if(file_exists($contentSourceEXT))
				return $contentSourceEXT;
			//else
				//return null;
		}
		return null;
	}
	
	function getContentSource_asFLV()
	{}
	
	
	function getImage($imageSize=-1)
	{
		if($this->contentTypeId=="E" and $this->contentProviderId=="yt")
		{
			if(!is_null($this->image) && $this->image!="")
			{
			
				return $this->image;
			} 
			
			$image_url = parse_url($this->contentSource);
			 //var_dump($image_url);
			 
    		if($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com')
			{
				$array = explode("&", $image_url['query']);
				$yuKey=substr($array[0], 2);
			}
			else if ($image_url['host'] == 'youtu.be')
			{
				//echo "host=youtu.be";
				$array=explode("/",$image_url['path']);
				$yuKey=end($array);
				//echo "yuKey=".$yuKey;
			}
			else
				return null;	
				
			
				
				
				switch ($imageSize) 
				{
    			case 0:
       				return "http://img.youtube.com/vi/".$yuKey."/0.jpg";
        			break;
    			case 1:
        			return "http://img.youtube.com/vi/".$yuKey."/1.jpg";
        			break;
    			case 2:
       				return "http://img.youtube.com/vi/".$yuKey."/2.jpg";
        			break;
				case 3:
       				return "http://img.youtube.com/vi/".$yuKey."/3.jpg";
        			break;	
    			default:
       				return "http://img.youtube.com/vi/".$yuKey."/default.jpg";
				}
			
   		}
		//else if ($this->contentProviderId=="sc" and (is_null($this->image) or trim($this->image)==""))
		 //	return "/images/icons/wave_icon.png";
		//else	
		 return $this->image;
	}
	
	function isPlayList()
	{
		if($this->contentTypeId=='P')
			return true;
		else
			return false;
	}
	function isAudio()
	{
		if($this->contentTypeId=='A')
			return true;
			else
				return false;
	}
	function isVideo()
	{
		if($this->contentTypeId=='V')
			return true;
		else
			return false;
	}
	function isEmbed()
	{
		if($this->contentTypeId=='E')
			return true;
		else
			return false;
	}
	
	function setItemAccess($session_memberId,$accessType)
	{
		  $conn=dbi_connect_x($this->databaseName);
		  if(!$conn)
 			return false;
	
		$query="INSERT INTO `member_item_access`(`access_id`, `item_id`, `access_type`, `access_time`, `session_id`, `ip_address`, `session_member_id`) VALUES (NULL,".$this->id.",'".$accessType."',NULL,'".session_id()."','".get_real_IP_address()."',$session_memberId)";
		//echo $query;
		$result=mysqli_query($conn,$query);
	  	
		
	  	if(!$result)
			return false;
	  	else
		{  /* performance risk */
	  		//$this->updatePoints();
			$id = mysqli_insert_id($conn);
			return $id;	
		}
	
	}
	function setStatusAsAccepted()
	{
		return $this->setStatus("ACCEPTED");
	}
	function setStatusAsArchived()
	{
		return $this->setStatus("ARCHIVED");
	}
	function setStatusAsPublished()
	{
		return $this->setStatus("PUBLISHED");
	}
	function setStatusAsDeclined()
	{
		return $this->setStatus("DECLINED");
	}
	
	
	function setStatus($newStatus)
	{

		$this->status=$newStatus;
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
			
		$query="UPDATE item
		SET item_status='".$this->status."'
		 WHERE item_id=".$this->id."";
		//echo $query;
		//exit;
		$result=mysqli_query($conn,$query);
		if($result)
		{
			//$this->updatePoints();
			return true;
		}
		else
			return false;
		
	}
	function setTagValue($name,$newValue)
	{
		$currentValue=$this->getTagValue($name);
		if(is_null($currentValue))
			if(trim($this->tag)=="")
				$this->tag=trim($name)."=".trim($newValue);
			else
				$this->tag=$this->tag.";".trim($name)."=".trim($newValue);
		else
			$this->tag=str_replace($name."=".trim($currentValue),$name."=".$newValue,$this->tag);
			
		if (!db_connect_x())
			return false;
			
			$query="UPDATE item
		SET item_tag='".$this->tag."'
		 WHERE item_id=".$this->id."";
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;	
			
	}
	function getTagValue($name)
	{
		$tags=explode(";",trim($this->tag));
		for($i=0;$i<count($tags);$i++)
		{
			$tag=explode("=",$tags[$i]);
			if(count($tag)>0)
				if($tag[0]==$name)
					return $tag[1];
		}
		
		return null;
	}
	
	function setTitle($newTitle)
	{
		if($this->title!=trim($newTitle))
		{
			$this->title=$newTitle;
			$conn=dbi_connect_x();
			if (!$conn)
			return false;
			
			$query="UPDATE item
		SET item_title='".$this->title."'
		 WHERE item_id=".$this->id."";
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;
		
		}
		return true;
	}
	function setDesc($newDesc)
	{
		if($this->desc!=trim($newDesc))
		{
			$this->desc=$newDesc;
			$conn=dbi_connect_x();
			if (!$conn)
			return false;
			
			$query="UPDATE item
		SET item_desc='".$this->desc."'
		 WHERE item_id=".$this->id."";
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;
		
		}
		return true;
	}
	
	
	
	function trackItemLike($memberId)
	{
	  if (!db_connect_x())
 			return false;
	  
	  $query = "INSERT INTO member_item_like values (NULL,$memberId,".$this->id.",NOW(),'".session_id()."','".get_real_IP_address()."')";
	//echo $query;
	  $result=mysqli_query($conn,$query);
	  if(!$result)
		return false;
	  else
	  	return true;
	}
	
	function setLike($sessionMemberID)
	{
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
	$query="INSERT INTO `member_item_like`(`like_id`, `item_id`, `like_value`, `like_time`, `session_id`, `ip_address`, `session_member_id`) VALUES (NULL,".$this->id.",1,NULL,'".session_id()."','".get_real_IP_address()."',$sessionMemberID)";
	//echo $query;
	$result=mysqli_query($conn,$query);
	
	  if(!$result)
		return false;
	 $this->updatePoints();	
	  $likeId =mysqli_insert_id($conn);
	  	return $likeId;
	}

	function likedByMember($sessionMemberID)
	{
	
	 	if(isset($_COOKIE['MID:'.$sessionMemberID.'_LKITMS']))
	 	{ 
	 		$likeItemString=$_COOKIE['MID:'.$sessionMemberID.'_LKITMS'];
	 		$likeStringArray=explode(".",$likeItemString);
			for($i=0;$i<count($likeStringArray);$i++)
			{
				if($this->id==$likeStringArray[$i])
					return true;
			}
	 	}
		if(isset($_COOKIE['MID:'.$sessionMemberID.'_DSLKITMS']))
	 	{ 
	 		$dislikeItemString=$_COOKIE['MID:'.$sessionMemberID.'_DSLKITMS'];
	 		//echo "likeString=".$likeItemString.";";
			//echo "itemID=".$this->id.";";
			$dislikeStringArray=explode(".",$dislikeItemString);
			for($i=0;$i<count($dislikeStringArray);$i++)
			{
				if($this->id==$dislikeStringArray[$i])
					return true;
			}
	 	}
		
	 	// check server side cookie file
	   	if (!db_connect_x())
 			return false;
		if($sessionMemberID==0)
			$query="SELECT DISTINCT item_id FROM `member_item_like` WHERE ip_address = '".get_real_IP_address()."' AND  session_member_id =".$sessionMemberID;		
	 	else
			$query="SELECT DISTINCT item_id FROM `member_item_like` WHERE session_member_id =".$sessionMemberID;
		$result=mysqli_query($conn,$query);
	 	if(!$result)
			return false;
	 	$row_count=mysqli_num_rows($result);
	 	for($i=0;$i<$row_count; $i++)
	 	{
			$row = mysqli_fetch_object($result);	
	 		if($this->id==$row->item_id)
				return true;		
	 	}
	 	return false;
	}
	
	function isAccepted()
	{
		if(($this->status=='ACCEPTED')||($this->status=='PUBLISHED'))
			return true;
		else
			return false;
	}
	function isPublished()
	{
		if($this->status=='PUBLISHED')
			return true;
		else
			return false;
	}
	
	function getPointsForItem()
	{
		$totalPoints=0;
		if($this->isAccepted())
		{
			$totalPoints= POINTFACTOR_INITIAL_UPLOAD;
			//echo "status=".$this->status;
		}
		return $totalPoints;
	}
	function calculatePoints()
	{
		$totalPoints=0;
		$totalWeightedViews=0;
		//$query="select count(*) from item where profile_id=".$this->id." AND item_status in ('ACCEPTED','PUBLISHED')";
		//$query="select count(*) from item where profile_id=".$this->id;
	
		//if($this->isAccepted())
		//	$totalPoints=POINTFACTOR_CONTRIBUTE_ITEM;
		//else
		//	return 0;
			
		$totalWeightedViews=$this->getViewCount("S","IP") * 0.64;
		$totalWeightedViews=$totalWeightedViews + $this->getViewCount("E","IP") * 0.16;
		$totalWeightedViews=$totalWeightedViews + $this->getViewCount("S","SESSION") * 0.12;
		$totalWeightedViews=$totalWeightedViews + $this->getViewCount("E","SESSION") * 0.03;
		$totalWeightedViews=$totalWeightedViews + $this->getViewCount("S","ALL") * 0.04;
		$totalWeightedViews=$totalWeightedViews + $this->getViewCount("E","ALL") * 0.01;
		

		
		$totalPoints=$totalPoints + $totalWeightedViews * POINTFACTOR_VIEWS;
		$totalPoints=$totalPoints + $this->getSumOfLikes() * POINTFACTOR_LIKES;
			
		return round($totalPoints);
	}
	
	
	function getPoints()
	{

		return $this->points;
	}
	function updatePoints()
	{
		return PointsManager::updateItemPoints($this);	
	}
	
	static function cmp_urating($a, $b)
    {
        $al = strtolower($a->getUnifiedRating());
        $bl = strtolower($b->getUnifiedRating());
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
	static function cmp_dateAdded($a, $b)
    {
        $al = strtolower($a->dateAdded);
        $bl = strtolower($b->dateAdded);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
	function getSumOfLikes($toDate=Null)
	{
		if(is_null($toDate))
			$toDate = date('Y-m-d H:i:s');
		//echo "at sum of likes";
		$conn=!dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
		$query = "SELECT  IFNULL( sum( like_value ),0  )
				  FROM member_item_like
				  WHERE item_id=".$this->id." and like_time<='".$toDate."'";
		
		if(!is_null($this->domainId))
			$query.=" and domain_id=".$this->domainId;
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}
	}
	
	function bayesianRatingAverage($c,$avgRating,$count,$score)
	{
		$br= (($c*$avgRating) + ($count*$score))/($c + $count);
		return $br;		
	}
	
	function getLikeRating()
	{
		
		$obj_itemStats= new itemStats(null,$this->databaseName);
		$likeAvg=$obj_itemStats->avgLikes;
		if(($likeAvg * 0.50) <3)
			$c=3;
		else
			$c=$likeAvg * 0.10;
		$c=0.05;
		$itemLikeSumCount=$this->getSumOfLikes($obj_itemStats->processingTime);
		if($itemLikeSumCount<3)
			return 0;
		
		$maxItemLikeSumCount=$obj_itemStats->maxLikes;
		if ($maxItemLikeSumCount<3)
			return 0;
		
		$likeRatingScore=($itemLikeSumCount/$maxItemLikeSumCount);
		$avgRating=(getLikeAverage())/$maxItemLikeSumCount;
		$br=$this->bayesianRatingAverage($c,$avgRating,$itemLikeSumCount,$likeRatingScore);
		return $br * 100;
	}
	function getViewRating()
	{	
		//********IP**************
		//get this item view count by IP
		$viewFactor=3;
		$viewRating=0;

		$obj_itemStats= new itemStats(null,$this->databaseName);
		if ($obj_itemStats->id===null)
			return 0;
		 
		if ($obj_itemStats->maxViewStart==0 or $obj_itemStats->maxViewEnd==0)
			return 0;
		 
		 $viewAvg=$obj_itemStats->avgViewStartByIP;
		if(($viewAvg * 0.25) <$viewFactor)
			$c=$viewFactor;
		else
			$c=$viewAvg * 0.25;
                                   		
		$itemViewStartCountByIP=$this->getViewCount("S","IP",$obj_itemStats->processingTime);
		$maxItemViewStartCountByIP=$obj_itemStats->maxViewStartByIP;
		$score=$itemViewStartCountByIP/$maxItemViewStartCountByIP;

		$avgViewRating=$viewAvg/$maxItemViewStartCountByIP;
	
		$rate_viewStartsByIP=$this->bayesianRatingAverage($c,$avgViewRating,$itemViewStartCountByIP,$score);
		
		$rate_viewStartsByIP=$rate_viewStartsByIP * 64;
		$viewRating=$rate_viewStartsByIP;
		
		$viewAvg=$obj_itemStats->avgViewEndByIP;
		if(($viewAvg * 0.25) <$viewFactor)
			$c=$viewFactor;
		else
			$c=$viewAvg * 0.25;
		
		$itemViewEndCountByIP=$this->getViewCount("E","IP",$obj_itemStats->processingTime);
		
		$maxItemViewEndCountByIP=$obj_itemStats->maxViewEndByIP;
		$score=$itemViewEndCountByIP/$maxItemViewEndCountByIP;
		$avgViewRating=$viewAvg/$maxItemViewEndCountByIP;
		$rate_viewEndsByIP=$this->bayesianRatingAverage($c,$avgViewRating,$itemViewEndCountByIP,$score);
		
		$rate_viewEndsByIP=$rate_viewEndsByIP * 16;
		$viewRating=$viewRating+$rate_viewEndsByIP;
		//*********Session**********************
		// get this item view count by Session
		$viewFactor=3;
		
		$viewAvg=$obj_itemStats->avgViewStartBySession;
		if(($viewAvg * 0.25) <$viewFactor)
			$c=$viewFactor;
		else
			$c=$viewAvg * 0.25;
		
		$itemViewStartCountBySession=$this->getViewCount("S","SESSION",$obj_itemStats->processingTime);
        
        $maxItemViewStartCountBySession=$obj_itemStats->maxViewStartBySession;
		
		$score=$itemViewStartCountBySession/$maxItemViewStartCountBySession;
		$avgViewRating=$viewAvg/$maxItemViewStartCountBySession;
		$rate_viewStartBySession=$this->bayesianRatingAverage($c,$avgViewRating,$itemViewStartCountBySession,$score);
		
		$rate_viewStartBySession=$rate_viewStartBySession * 12;
		$viewRating=$viewRating+$rate_viewStartBySession;
		
		$viewAvg=$obj_itemStats->avgViewEndBySession;
		if(($viewAvg * 0.25) <$viewFactor)
			$c=$viewFactor;
		else
			$c=$viewAvg * 0.25;
		
		$itemViewEndCountBySession=$this->getViewCount("E","SESSION",$obj_itemStats->processingTime);
		
		$maxItemViewEndCountBySession=$obj_itemStats->maxViewEndBySession;
		$score=$itemViewEndCountBySession/$maxItemViewEndCountBySession;
		$avgViewRating=$viewAvg/$maxItemViewEndCountBySession;
		$rate_viewEndBySession=$this->bayesianRatingAverage($c,$avgViewRating,$maxItemViewEndCountBySession,$score);
		
		$rate_viewEndBySession=$rate_viewEndBySession * 3;
		$viewRating=$viewRating+$rate_viewEndBySession;
		
		$viewFactor=5;
		
		$viewAvg=$obj_itemStats->avgViewStart; 
		if(($viewAvg * 0.25) <$viewFactor)
			$c=$viewFactor;
		else
			$c=$viewAvg * 0.25;
			
		$itemViewStartCountAll=$this->getViewCount("S","ALL",$obj_itemStats->processingTime);
		
		$maxItemViewStartCountAll=$obj_itemStats->maxViewStart;
		$score=$itemViewStartCountAll/$maxItemViewStartCountAll;
		$avgViewRating=$viewAvg/$maxItemViewStartCountAll;
		$rate_viewStartAll=$this->bayesianRatingAverage($c,$avgViewRating,$maxItemViewStartCountAll,$score);
		
		$rate_viewStartAll=$rate_viewStartAll * 4;
		$viewRating=$viewRating+$rate_viewStartAll;
		
		$viewAvg=$obj_itemStats->avgViewEnd;
		if(($viewAvg * 0.25) <3)
			$c=5;
		else
			$c=$viewAvg * 0.25;
		
		$itemViewEndCountAll=$this->getViewCount("E","ALL",$obj_itemStats->processingTime);
		
		$maxItemViewEndCountAll=$obj_itemStats->maxViewEnd;
		$score=$itemViewEndCountAll/$maxItemViewEndCountAll;
		$avgViewRating=$viewAvg/$maxItemViewEndCountAll;
		$rate_viewEndAll=$this->bayesianRatingAverage($c,$avgViewRating,$maxItemViewEndCountAll,$score);
		
		$rate_viewEndAll=$rate_viewEndAll * 1;
		$viewRating=$viewRating+$rate_viewEndAll;
		
		if($viewRating>100)
			return 100;
		else
		return $viewRating;
	}
	

	function getUnifiedRating()
	{	
		if($this->ratingValue!=-1)
			return $this->ratingValue;
			
		$likeRating=$this->getLikeRating();
		$viewRating=$this->getViewRating();

		if($likeRating!=0 and $viewRating!=0)
			$this->ratingValue=(($likeRating/100) * 60) + (($viewRating/100) * 40);
		else
			$this->ratingValue= 0;
	
		return $this->ratingValue;
	}
	
	public static function getRatingStarCount($ratingValue)
	{	
		$intRatingValue=round($ratingValue,0);
		if ($intRatingValue >= 90)
			return 5;
		else if ($intRatingValue >= 80)
			return 4;
		else if ($intRatingValue >= 60)
			return 3;
		else if ($intRatingValue >= 40)
			return 2;
		else if ($intRatingValue >= 20)
			return 1;
		else
			return 0;			
	}
	
	function setDislike($sessionMemberID)
	{
	
		  if (!db_connect_x())
 			return false;
	$query="INSERT INTO `member_item_like`(`like_id`, `item_id`, `like_value`, `like_time`, `session_id`, `ip_address`, `session_member_id`) VALUES (NULL,".$this->id.",-1,NULL,'".session_id()."','".get_real_IP_address()."',$sessionMemberID)";
	
	$result=mysqli_query($conn,$query);
	  if(!$result)
		return false;
		
	  $likeId = mysql_insert_id(); 
	  	return $likeId;	
	}	
	
	function getLikeCount($toDate=Null)
	{
		if(is_null($toDate))
			$toDate = date('Y-m-d H:i:s');
		
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
		
 			$query="SELECT SUM(like_count)
 			FROM
 			(
 					SELECT item_id, count(*), 1 as like_count FROM `member_item_like` GROUP BY item_id,ip_address
 					) x
 					WHERE item_id=".$this->id."
 					GROUP BY item_id";
 			
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysqli_fetch_array($result);
			return $row[0]; 
		}
		else 
			return 0;
   }
   
   	function getDislikeCount()
	{
		if (!db_connect_x())
 			return false;
		$query = "select count(*) from member_item_like where item_id=".$this->id." and like_value<0";
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}
   }
   function getItemVoteCountByIP()
	{
		if (!db_connect_x())
 			return false;
		$query = "select like_id from member_item_like where item_id=".$this->id." group by ip_address";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
	
			return $row_count; 
	
   }
   
   function isVoteForIP($IpAddress)
   {
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
		$query = "select * from member_item_like where item_id=".$this->id." and ip_address='".$IpAddress."'";
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count>=1)
			return true;
		else
			return false;
   }	
	
	function getViewCountBySession($accessType)
	{

		$conn=!dbi_connect_x($this->databaseName);if($conn)
 			return false;
		$query = "select count(*) from member_item_access where item_id=".$this->id." and access_type='".$accessType."' group by session_id";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
	
			return $row_count; 
		
	}
	
	function getViewCountByIP($accessType)
	{
		$conn=!dbi_connect_x($this->databaseName);if($conn)
 			return false;
		$query = "select count(*) from member_item_access where item_id=".$this->id." and access_type='".$accessType."' group by ip_address";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
	
			return $row_count; 
	}
	
	function getViewCount($accessType,$viewBy="All",$toDate=Null)
	{
		
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
			return false;
		
		if(is_null($toDate))
			$toDate = date('Y-m-d H:i:s');
		
		switch ($viewBy)
		{
			case "IP":
				$sqlparm="distinct(ip_address)";
				break;
			case "SESSION":
				$sqlparm="distinct(session_id)";
				break;
			default:
				$sqlparm="*";
		}
		
		$query = "select count(".$sqlparm.") from member_item_access where item_id=".$this->id." and access_type='".$accessType."'
				and access_time<='".$toDate."'";
		if(!is_null($this->domainId))
			$query.=" and domain_id=".$this->domainId;		
				
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
		
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysqli_fetch_array($result);
			return $row[0];
		}
	}
	
	function loadFirstItemByProfileID($profileID)
	{
		$conn=dbi_connect_x();
		if (!$conn)
 			return false;
	 	
		$query="select min(item_id) as first_item_id from item where 
			profile_id=$profileID";
		//echo $query;	
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
	    		
		$row_count=mysqli_num_rows($result);
		
		if($row_count==1)
		{
			$row = mysqli_fetch_object($result);	
	 		$this->item($row->first_item_id);
		 }
		  else
		{	
			$this->id=null;	
			return;
		}			 				 
	}

}// end of item class	

class itemManager
{
	var $itemMgr=array();
	var $totalCount=0;
	var $databaseName=null;
	
	function itemManager($filter=NULL,$parameter=NULL,$databaseName=NULL,$domainID=NULL,$parentItemOnly=NULL,$limit=null,$page=1,$status=null,$seed=null)
	{
		
		$this->databaseName=$databaseName;
		$conn=dbi_connect_x($this->databaseName,NULL,NULL,NULL,"itemManager");
		if(!$conn)
			{
 				echo "no database connection";
				return false;
			}
	    
		if(isset($filter))
			switch($filter)
			{
				case "CONTENT_TYPE":
				$query="Select * from item i where i.item_content_type_id='".$parameter."'";
				$query.=" Order by item_id desc";
				break;
				case "STATUS":
				$query="Select * from item i where i.item_status='".$parameter."' AND  i.item_parent_id IS NULL  AND item_content_type_id in ('A','E','P','V') AND item_category_id IS NULL";
				$query.=" Order by item_id desc";
				break;
				case "PROFILE":
				$query="Select * from item i where i.profile_id='".$parameter."'";
				//$query.=" Order by item_id desc";
				break;
				case "PREMIUM":
		        $query="SELECT i.* FROM item i JOIN item_premium pi ON i.item_id=pi.item_id AND i.domain_id=pi.domain_id ORDER BY rating desc";
				break;
				case "FEATURED":
		        $query="SELECT i.* FROM item i JOIN item_featured_stack ifs ON i.item_id=ifs.item_id AND i.domain_id=ifs.domain_id ORDER BY created desc";
				break;
				
				case "GENRE":
				$query="SELECT * FROM item i JOIN item_genre ig ON i.item_id = ig.item_id ";
				if(is_null($domainID))
					$query.="AND i.domain_id=ig.domain_id ";
				$query.="WHERE ig.genre_id =".$parameter." AND  i.item_parent_id IS NULL ";
				break;
				
				case "LATEST":
					$query="SELECT * FROM item i WHERE i.item_parent_id is NULL";
					if(!is_null($status))
						$query.=" AND item_status='".$status."'";
					$query.=" ORDER BY i.item_date_added desc";
					break;
			
				case "RATED":
					$query="SELECT * FROM item i WHERE i.item_parent_id is NULL";
					if(!is_null($status))
						$query.=" AND item_status='".$status."'";
					$query.=" ORDER BY i.points desc";
					break;
					
				case "ALL":
					$query="SELECT * FROM item i WHERE i.item_parent_id is NULL";
					if(!is_null($status))
						$query.=" AND item_status='".$status."'";
					break;
				
				case "RANDOM":
					$query="SELECT * FROM item i WHERE i.item_parent_id is NULL";
					if(!is_null($status))
						$query.=" AND item_status='".$status."'";
					$query.=" ORDER BY RAND(".$seed.")";
					break;
					
				default:
					$query="SELECT * FROM item i WHERE i.item_parent_id is NULL";
					if(!is_null($status))
						$query.=" AND item_status='".$status."'";
					
				}// end of switch statement
		   else
		   {
					 return; 
		   }
		   
		   $this->setTotalCount($query);
		   $subQuery="";
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
			$obj_item = new item(null,$this->databaseName);
			$obj_item->init_class_object($row,$obj_item);	
			$this->itemMgr[]=$obj_item;				
		
		 }	//end of for loop							
	
	}
	
	function setTotalCount($query)
	{
		
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
		{
 			echo "no database connection";
			return false;
		}
		
		$query=str_replace("*","count(*)",$query);	
		$result=mysqli_query($conn,$query);
		
		if(!$result)
			return false;
		
		$row_count=mysqli_num_rows($result);
		$row = mysqli_fetch_row($result);
		$this->totalCount=$row[0];
		
	}
	
	function addSearchItems($searchWords)
	{	
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
			{
 				echo "no database connection";
				return false;
			}
						
	$query="SELECT * FROM item WHERE item_title LIKE   '%".$searchWords."%'  OR item_desc LIKE  '%".$searchWords."%' OR item_long_desc LIKE  '%".$searchWords."%'";	
	
	$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			
	    $row_count=mysqli_num_rows($result);
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);
			$obj_item = new item(null,$this->databaseName);
			$obj_item->init_class_object($row,$obj_item);	
			$this->itemMgr[]=$obj_item;							
		 }	//end of for loop							
	
	
	}

	function loaditem($itemId)
	{
		$item = new item($itemId);
		$this->itemMgr[]=$item;	
	}
	
	function loadItems($itemIDs,$domainID=NULL)
	{
	
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;

		if(count($itemIDs)>0)
		{
			$query="Select * from item where item_id in (";
			for($i=0;$i<count($itemIDs);$i++)
			{
				$query.=$itemIDs[$i];
				if($i!=count($itemIDs)-1)
					$query.=",";
			}
			$query.=")";
			//echo $query;
			$result=mysqli_query($conn,$query);
			if(!$result)
				return false;
			
			$row_count=mysqli_num_rows($result);
			for($i=0;$i<$row_count; $i++)
			{
				$row = mysqli_fetch_object($result);	
	 			//$item = new item($row->item_id);
				$obj_item = new item();
				$obj_item->init_class_object($row,$obj_item);	
				$this->itemMgr[]=$obj_item;			
		 	}	//end of for loop				
		}
		else
			return false;
	}
	function loadItemChildren($parentId)
	{
		$conn=dbi_connect_x($this->databaseName,null,null,null,"loadItemChildren");
		if(!$conn)
			return false;
		
		$query="Select * from item_child ic where ic.item_id=$parentId  order by ic.sequence_id";
			//echo $query;
			
		$result=mysqli_query($conn,$query);
			if(!$result)
				return false;
			$row_count=mysqli_num_rows($result);
			for($i=0;$i<$row_count; $i++)
			{
				$row = mysqli_fetch_object($result);
				$item = new item($row->item_child_id,$this->databaseName);
				$this->itemMgr[]=$item;
			}	//end of for loop
			
	}
	
	function loadItemsByCategoryId($categoryId,$isChildItem=null)
	{
		$conn=dbi_connect_x(null,null,null,null,"loadItemsByCategoryId");
		if(!$conn)
			return false;
		
		$query="SELECT * FROM item WHERE item_category_id=".$categoryId; 	
			
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
		$row_count=mysqli_num_rows($result);
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);
			$item = new item($row->item_id,$this->databaseName);
			$this->itemMgr[]=$item;
		}	//end of for loop
			
	}
	function loadItemByParentIdAndFilter($parentId,$filter=NULL,$parameter=NULL)
	{
		$conn=dbi_connect_x($this->databaseName,null,null,null,"loadItemByParentIdAndFilter");
		if(!$conn)
 			return false;
	    if (isset($filter))
			switch($filter)
			{
				case "CONTENT_TYPE":
				$query="Select * from item i where i.item_parent_id=$parentId and i.item_content_type_id='".$parameter."' order by i.item_id";
			}// end of switch statement
		else
			$query="Select * from item i where i.item_parent_id=$parentId  order by i.item_id";
	//echo $query;
	
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
	    $row_count=mysqli_num_rows($result);
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);	
	 		$item = new item($row->item_id,$this->databaseName);
			$this->itemMgr[]=$item;			
		 }	//end of for loop											
	} //end of function
	function loadItemByFilter($filter=NULL,$parameter=NULL)
	{
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
	    if (isset($filter))
			switch($filter)
			{
				case "CONTENT_TYPE":
				$query="Select * from item i where i.item_content_type_id='".$parameter."'";
				break;
				case "STATUS":
				$query="Select * from item i where i.item_status='".$parameter."'";
				break;
			}// end of switch statement
		else
			$query="Select * from item";
	
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
	    $row_count=mysqli_num_rows($result);
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);	
	 		$obj_item = new item(null,$this->databaseName);
			//$this->databaseName=0;
			if(!is_null($this->databaseName))
				$obj_item->domainId=$row->domain_id;
			$obj_item->init_class_object($row,$obj_item);	
			$this->itemMgr[]=$obj_item;			
		 }	//end of for loop											
	} //end of function
	
	
	
	function getCount()
	{
 		return count($this->itemMgr);
	}
	
	function clear()
	{
		$this->itemMgr=array();
	}
	function getLikeCount()
	{
		$totalLikeCount=0;
		for($i=0;$i<$this->getCount(); $i++)
			$totalLikeCount=$totalLikeCount+$this->itemMgr[$i]->getLikeCount();
	
		return $totalLikeCount;
	}
	function getViewCount()
	{
		$totalViewCount=0;
		for($i=0;$i<$this->getCount(); $i++)
			$totalViewCount=$totalViewCount+$this->itemMgr[$i]->getViewCount("S");
	
		return $totalViewCount;
	}
	

	function getItems(&$items,$status=Null)
	{
		for($i=0;$i<$this->getCount(); $i++)
		{
			if(is_null($status))
				$items[]=$this->itemMgr[$i];	 		
			else
			{
				if($this->itemMgr[$i]->status==$status)
					$items[]=$this->itemMgr[$i];
			}
				
		}
	
	}
	
	function getItemsByContentType(&$items,$contentTypeId=Null)
	{
		for($i=0;$i<$this->getCount(); $i++)
		{
			if(is_null($contentTypeId))
				$items[]=$this->itemMgr[$i];
			else
			{
				if($this->itemMgr[$i]->contentTypeId==$contentTypeId && is_null($this->itemMgr[$i]->parentId))
					$items[]=$this->itemMgr[$i];
			
			}
	
		}
	}
	
	function remove($itemId)
	{
		for($i=0;$i<$this->getCount(); $i++)
		{
			if($this->itemMgr[$i]->id==$itemId)
			{
				unset($this->itemMgr[$i]);
				$this->itemMgr= array_values($this->itemMgr);
				break;
			}
		}
	}
	
	
	function getItem($id)
	{
		for($i=0;$i<$this->getCount(); $i++)
	  	{
	    	$item=new item();
			$item=$this->itemMgr[$i]; 
			if($item->id==$id)
		   		return $item;
	  	}
	}
	function isItem($id)
	{
		for($i=0;$i<$this->getCount(); $i++)
	  	{
	    	$item=new item();
			$item=$this->itemMgr[$i]; 
			if($item->id==$id)
		   		return true;
	  	}
		return false;
	}	

	static public function getItemCountByStatus($status=NULL,$database)
	{
		$conn=dbi_connect_x($database,NULL,NULL,NULL,"getItemCountByStatus");
		if(!$conn)
		{
			echo "no database connection";
			return -1;
		}
		 
		$query="Select count(*) from item i where i.item_status='".$status."' AND  i.item_parent_id IS NULL  AND item_content_type_id in ('A','E','P','V') AND item_category_id IS NULL";
		$query.=" Order by item_id desc";
		
			
			//echo $query;
			$result=mysqli_query($conn,$query);
			if(!$result)
				return -1;
					
				$row = mysqli_fetch_row($result);
				return $row[0];
	}
	

}// end of item Manager class

class itemStats
{	
	var $id;
	var $processingTime;	
	var $avgLikes;
	var $maxLikes;
	var $avgViewStartByIP; 
	var $maxViewStartByIP;
  	var $avgViewStartBySession;
  	var $maxViewStartBySession;
  	var $avgViewStart;
  	var $maxViewStart;  	   
  	var $avgViewEndByIP;
  	var $maxViewEndByIP;
  	var $avgViewEndBySession;
  	var $maxViewEndBySession;
  	var $avgViewEnd;
  	var $maxViewEnd;
	var $databaseName=null;
	var $domainId=null;

	function init_class_object()
	{
		if(isset($_SESSION['obj_itemStats']))
		{	
			$thisTemp=unserialize($_SESSION["obj_itemStats"]);
			$this->id=$thisTemp->id;
			errorHandler(0,"retrieving data from session registered for".$this->id,0,"cls_itemManager.php","itemStats");
			$this->processingTime=$thisTemp->processingTime;	
			$this->avgLikes=$thisTemp->avgLikes;
			$this->maxLikes=$thisTemp->maxLikes;
			$this->avgViewStartByIP=$thisTemp->avgViewStartByIP; 
			$this->maxViewStartByIP=$thisTemp->maxViewStartByIP;
  			$this->avgViewStartBySession=$thisTemp->avgViewStartBySession;
  			$this->maxViewStartBySession=$thisTemp->maxViewStartBySession;
  			$this->avgViewStart=$thisTemp->avgViewStart;
  			$this->maxViewStart=$thisTemp->maxViewStart;  	   
  			$this->avgViewEndByIP=$thisTemp->avgViewEndByIP;
  			$this->maxViewEndByIP=$thisTemp->maxViewEndByIP;
  			$this->avgViewEndBySession=$thisTemp->avgViewEndBySession;
  			$this->maxViewEndBySession=$thisTemp->maxViewEndBySession;
  			$this->avgViewEnd=$thisTemp->avgViewEnd;
			$this->maxViewEnd=$thisTemp->maxViewEnd;		
		}
		else
		{	
			$conn=dbi_connect_x($this->databaseName);
			if(!$conn)
 				return false;
			$query = "SELECT *
					FROM `item_stats`
					WHERE stat_id = (
					SELECT max( stat_id )
					FROM item_stats
					)";
			$result=mysqli_query($conn,$query);	
			if(!$result)
			{	
				$this->id=null;	
				return false;
			}
			
			$row_count=mysqli_num_rows($result);
			if($row_count==1)
			{
				$row = mysqli_fetch_object($result);	
				$this->id=$row->stat_id;
				$this->processingTime=$row->processing_time;	
				$this->avgLikes=$row->avg_item_sum_like_value;
				$this->maxLikes=$row->max_item_sum_like_value;
				$this->avgViewStartByIP=$row->avg_item_view_start_by_ip_count; 
				$this->maxViewStartByIP=$row->max_item_view_start_by_ip_count;
  				$this->avgViewStartBySession=$row->avg_item_view_start_by_session_count;
  				$this->maxViewStartBySession=$row->max_item_view_start_by_session_count;
  				$this->avgViewStart=$row->avg_item_view_start_all_count;
  				$this->maxViewStart=$row->max_item_view_start_all_count;  	   
  				$this->avgViewEndByIP=$row->avg_item_view_end_by_ip_count;
  				$this->maxViewEndByIP=$row->max_item_view_end_by_ip_count;
  				$this->avgViewEndBySession=$row->avg_item_view_end_by_session_count;
  				$this->maxViewEndBySession=$row->max_item_view_end_by_session_count;
  				$this->avgViewEnd=$row->avg_item_view_end_all_count;
  				$this->maxViewEnd=$row->max_item_view_end_all_count;						
				errorHandler(0,"retrieving item stats from db for".$this->id,0,"cls_itemManager.php","itemStats");
				errorHandler(0,"session_register for".$this->id,0,"cls_itemManager.php","itemStats");
			}
			else
			{
				 $this->id=null;	
				return false;
			}
		}//end of else session is registered	
		
		if(!is_null($this->id))
		{
			errorHandler(0,"is_null".$this->id,0,"cls_itemManager.php","itemStats");
			$time = strtotime($this->processingTime);
			$time = time() - $time;
			errorHandler(0,"elapsed time=".$time,0,"cls_itemManager.php","itemStats");
			if($time>604800)
			{
				errorHandler(0,"calling regenerate",0,"cls_itemManager.php","itemStats");
				$id=$this->generate();
				unset($_SESSION["obj_itemStats"]);
				$this->itemStats();
				return $id;
			}
		}
		
	}

	function itemStats($domainID=NULL,$databaseName=NULL)
	{
		if(!is_null($domainID))
			$this->domainId=$domainID;	
	
		if(!is_null($databaseName))
			$this->databaseName=$databaseName;
		else
			$this->databaseName=0;
			
		$this->init_class_object(); 

	}
	function generate()
{
		$conn=dbi_connect_x($this->databaseName);
		if(!$conn)
 			return false;
		$query ="INSERT INTO `item_stats`
				(
					stat_id,processing_time,
					avg_item_sum_like_value,
					max_item_sum_like_value,
					avg_item_view_start_by_ip_count,
					max_item_view_start_by_ip_count,
					avg_item_view_start_by_session_count,
					max_item_view_start_by_session_count,
					avg_item_view_start_all_count,
					max_item_view_start_all_count,
					avg_item_view_end_by_ip_count,
					max_item_view_end_by_ip_count,
					avg_item_view_end_by_session_count,
					max_item_view_end_by_session_count,
					avg_item_view_end_all_count,
					max_item_view_end_all_count
				)
				SELECT 
	   				NULL,
	   				now() as processing_time,	
       				avg(itemSumLikeValue) as avgItemSumLikeValue,	   
       				max(itemSumLikeValue) as maxItemSumLikeValue,
	   				avg(itemViewStartBySessionCount) as avgItemViewStartBySessionCount,
	   				max(itemViewStartBySessionCount) as maxItemViewStartBySessionCount,
	   				avg(itemViewStartByIPCount) as avgItemViewStartByIPCount,
       				max(itemViewStartByIPCount) as maxItemViewStartByIPCount,
	   				avg(itemViewStartAllCount) as avgItemViewStartAllCount,
       				max(itemViewStartAllCount) as maxItemViewStartAllCount,
	   				avg(itemViewEndBySessionCount) as avgItemViewEndBySessionCount,
       				max(itemViewEndBySessionCount) as maxItemViewEndBySessionCount,
	   				avg(itemViewEndByIPCount) as avgItemViewEndByIPCount,
       				max(itemViewEndByIPCount) as maxItemViewEndByIPCount,
	   				avg(itemViewEndAllCount) as avgItemViewEndAllCount,
       				max(itemViewEndAllCount) as maxItemViewEndAllCount
				FROM
				(
					SELECT 
						count(distinct(m1.session_id)) as itemViewStartBySessionCount, 
						count(distinct(m1.ip_address)) as itemViewStartByIPCount,
						count(distinct(m1.access_id)) as itemViewStartAllCount,
						count(distinct(m2.session_id)) as itemViewEndBySessionCount, 
						count(distinct(m2.ip_address)) as itemViewEndByIPCount,
						count(distinct(m2.access_id)) as itemViewEndAllCount,
						m3.sumItemLikeValue as itemSumLikeValue
					FROM `member_item_access` m1
					LEFT JOIN `member_item_access` m2
					ON m1.item_id=m2.item_id
					LEFT JOIN
					(
						SELECT item_id,sum(like_value) as sumItemLikeValue 
						FROM `member_item_like`
						GROUP BY item_id
					) m3
					ON m2.item_id=m3.item_id
					WHERE m1.access_type='S'
					AND m2.access_type='E'
					GROUP BY m1.item_id
				) item_benchmark_stats_temp";

		$result=mysqli_query($conn,$query);
	  	if(!$result)
			return false;
		
		$statId = mysqli_insert_id();
		$this->init_class_object(); 
		if(!is_null($statId))
			return $statId;
		else
			return false;	
	} // end of setItemStats
}// end of item statistics class

function cmp_obj($a, $b)
    {
        $al = strtolower($a->trackNo);
        $bl = strtolower($b->trackNo);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

function getVoteCount()
	{
		$conn=db_connect_x();
		if (!$conn)
 			return false;
		$query = "select count(*) from member_item_vote";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}
   }
	
function getItemContentTypes(&$contentTypeArray)
{
	$conn=db_connect_x();
	if (!conn)
 			return false;

	$query="select * from item_content_type";
	$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
	    $row_count=mysqli_num_rows($result);
	for($i=0;$i<$row_count; $i++)
	{
		$row = mysqli_fetch_object($result);	
	 	$contentTypeArray[$i][0]=$row->item_content_type_id;
		$contentTypeArray[$i][1]=$row->item_content_type_name;	
	}
}
function getItemContentId($itemContentName)
{
	
	$conn=db_connect_x();	
	if (!$conn)
 			return false;

	$query="select item_content_type_id from item_content_type where item_content_type_name='".$itemContentName."'";
	$result=mysqli_query($conn,$query);
		if(!$result)
			return 0;
	    $row_count=mysqli_num_rows($result);
	
	  if($row_count!=0)
	  {
		$row = mysqli_fetch_object($result);	
	 	$itemContentId=$row->item_content_type_id;
		return $itemContentId;	
	  }
	  else
	  	return 0;	  



}

function getLikeAverage()
{
	$conn=db_connect_x();
		if (!$conn)
 			return false;
		$query="Select avg(likeCount)
				FROM
				(SELECT sum(like_value) as likeCount FROM `member_item_like`
				Group by item_id) LikeCount
				WHERE likeCount >=0";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}		
}

function getMaxItemLikeSumCount()
{
	$conn=db_connect_x();
		if (!$conn)
 			return false;
			
		$query = "SELECT max(sumItemLikeValue)
					FROM
					(SELECT sum(like_value) as sumItemLikeValue 
					FROM `member_item_like`
					GROUP BY item_id) Item_Sum_like";
		
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}

}

function getAverageViews($accessType,$viewBy)
{ 
	$conn=db_connect_x();
		if (!$conn)
 		return false;

switch ($viewBy) 
		{
			case "IP":
			$sqlparm="distinct(ip_address)";
			break;
			case "SESSION":
			$sqlparm="distinct(session_id)";
			break;
			default:
			$sqlparm="*";
		}	

	$query="Select avg(uniqueIPviews)
			FROM 
			(SELECT count(".$sqlparm.") as uniqueIPviews 
			FROM `member_item_access`
			where access_type='".$accessType."'
			group by item_id) uniqueIPviews";

	$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}

}
function getMaxItemViewCountAll($accessType)
{
		$conn=db_connect_x();
		if (!$conn)
 			return false;
		$query = "SELECT max(itemCount)
					FROM
					(SELECT count(*) as itemCount
					FROM `member_item_access` 
					WHERE access_type='".$accessType."'
					GROUP BY item_id) itemCount";
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}
}

function getMaxItemViewCountByIP($accessType)
{

		$conn=db_connect_x();
		if (!$conn)
 			return false;
		$query = "SELECT max(itemCount)
					FROM
					(SELECT count(distinct(ip_address)) as itemCount
					FROM `member_item_access` 
					WHERE access_type='".$accessType."'
					GROUP BY item_id) itemCount";
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}
}
function getMaxItemViewCountBySession($accessType)
{

		$conn=db_connect_x();
		if (!$conn)
 			return false;
		$query = "SELECT max(itemCount)
					FROM
					(SELECT count(distinct(session_id)) as itemCount
					FROM `member_item_access` 
					WHERE access_type='".$accessType."'
					GROUP BY item_id) itemCount";
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			 
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			return $row[0]; 
		}
}

function getContentProviderId($providerName)
{
		$conn=dbi_connect();
		if (!$conn)
 			return false;

	$query="select provider_id from item_content_provider where provider_name='".$providerName."'";
	$result=mysqli_query($conn,$query);
		if(!$result)
			return null;
	    $row_count=mysqli_num_rows($result);
	
	  if($row_count!=0)
	  {
		$row = mysqli_fetch_object($result);	
	 	$providerId=$row->provider_id;
		return $providerId;	
	  }
	  else
	  	return null;	  
}
function getContentProviderName($providerId)
{
	$conn=dbi_connect();
		if (!$conn)
		return false;

		$query="select provider_name from item_content_provider where provider_id='".$providerId."'";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return 0;
			$row_count=mysqli_num_rows($result);

	  if($row_count!=0)
	  {
	  	$row = mysqli_fetch_object($result);
	  	$providerName=$row->provider_name;
	  	return $providerName;
	  }
	  else
	  	return 0;
}



function getItemContentName($itemContentId)
{
		$conn=db_connect_x();
		if (!$conn)
 			return false;

	$query="select item_content_type_name from item_content_type where item_content_type_id=$itemContentId";
	$result=mysqli_query($conn,$query);
		if(!$result)
			return 0;
	    $row_count=mysqli_num_rows($result);
	
	  if($row_count!=0)
	  {
		$row = mysqli_fetch_object($result);	
	 	$itemContentName=$row->item_content_type_name;
		return $itemContentName;	
	  }
	  else
	  	return 0;	  
}

function humanTiming ($time)
{

    $time = time() - $time; // to get the time since that moment

    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}

?>