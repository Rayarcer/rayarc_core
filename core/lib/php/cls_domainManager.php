<?php
require_once("fns_db.php");
require_once("fns_errorHandler.php");
class domain
{
	var $id;
	var $domainName;
	var $domainTitle;
	var $shortDesc;
	var $status;
	var $databaseName;
	var $parentId=0;
	var $clientId;
	var $planId;
	
	function init_class_object($row,&$obj_domain)
	{
		$obj_domain->id=$row->domain_id;
		$obj_domain->domainName=$row->domain_name;
		$obj_domain->domainTitle=$row->domain_title;
		$obj_domain->shortDesc=$row->domain_short_desc;
		$obj_domain->status=$row->status;
		$obj_domain->databaseName= $row->database_name;	
		$obj_domain->parentId= $row->domain_parent_id;
		$obj_domain->clientId= $row->client_id;
		$obj_domain->planId= $row->plan_id;
	}	
	

	function domain($id=null)
	{	
	
		if ($id===null)
		{
			$this->id=null;
			$this->status="PENDING";	
			return;
		}
		$conn=dbi_connect();
		if (!$conn)
 			return false;
			
		$query="select *,CONCAT('rayarcca_',domain_name) as database_name from domain where domain_id=$id";		
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$errMsg="Error: construct domain id:".$id." | ".mysqli_error($conn);
			errorHandler(0,$errMsg,0,"domain()","cls_domainManager.php");
			$this->id=null;
			return false;	
	    }
		$row_count=mysqli_num_rows($result);
		if($row_count)
		{
			$row = mysqli_fetch_object($result);	
			$this->init_class_object($row,$this);
		}
		 else
		 	$this->id=null;				 
	}
	static function getDatabaseNameById($id)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return false;
			
		$query="select CONCAT('rayarcca_',domain_name) as database_name from domain where domain_id=$id";		
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return null;	
	 	$row = mysqli_fetch_object($result);	
		return $row->database_name;	
	}
	static function hasDomainName($domainName)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$query="select count(*) As count from domain where domain_name='".$domainName."'";
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$errMsg="Error: construct domain id:".$id." | ".mysqli_error($conn);
			errorHandler(0,$errMsg,0,"hasDomainName()","cls_domainManager.php");
			return false;
		}
		$rowCount=mysqli_num_rows($result);
		//echo "rowCount=".$rowCount;
		
		if ($rowCount)
		{
			$row = mysqli_fetch_row($result);
			
			if($row[0]==1)
				return true;
			else 
				return false;
		}
		else
			return false;
	}
	
	function getDomainByName($domainName)
	{	
		$conn=dbi_connect();
		if (!$conn)
 			return false;
			
		$query="select *,CONCAT('rayarcca_',domain_name) as database_name from domain where domain_name='".$domainName."'";		
		//echo $query;
		//exit;
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$this->id=null;
			return false;	
	    }
		$row_count=mysqli_num_rows($result);
		if($row_count)
		{
			$row = mysqli_fetch_object($result);	
			$this->init_class_object($row,$this);
			return $this->id;
		}
		 else
		 	$this->id=null;				 
	}
	
	function hasParent()
	{
		if(is_null($this->parentId))
			return false;
		else 
			return true;
	}
	
	
	function save()
	{
		$conn=dbi_connect();
		if (!$conn)
 			return false;

		//$query="INSERT INTO `domain`(`domain_id`, `domain_name`, `status`) VALUES (NULL,'".$this->domainName."','".$this->status."')";
		$query="INSERT INTO `domain`(`domain_id`, `domain_name`, `domain_title`, `plan_id`, `client_id`) VALUES (NULL,'".$this->domainName."','".$this->domainTitle."',".$this->planId.",".$this->clientId.")";
		//echo $query;
		//exit;
		
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$errMsg="Error: save domain name:".$this->domainName." | ".mysqli_error($conn);
			errorHandler(0,$errMsg,0,"save()","cls_domainManager.php");
			$this->id=null;
			return false;
		}
		$id = mysqli_insert_id($conn); 
		$this->id=$id;
		return $this->id;	
	}
	function addMember($memberID,$clientFlag=0,$adminFlag=0,$temp=false)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$tablename="domain_member";
		if($temp)
			$tablename="domain_member_temp";

		$query="INSERT INTO $tablename (`domain_id`,`member_id`,`admin_flag`, `client_flag`) VALUES (".$this->id.",".$memberID.",".$clientFlag.",".$adminFlag.")";
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;
	}
	
	
	
} //end of class domain

function getDomainIDByDomainName($domainName)
{
	$conn=dbi_connect();
	if (!$conn)
 		return  false;
		
	$query="select domain_id from domain where domain_name='".trim($domainName)."'";
	//echo $query;
		
	$result=mysqli_query($conn,$query);
	if(!$result)
		return false;
	$rowCount=mysqli_num_rows($result);
	
	if ($rowCount==1)
	{	
		$row = mysqli_fetch_object($result);	
		return $row->domain_id;
	}
	else
	{
		return null;  
	}
}
class domainManager
{
	var $domainMgr = array();
	
	function domainManager($clientId=Null)
	{
		if(is_null($clientId))
			return true;
		
		$conn=dbi_connect();
		if (!$conn)
 			return false;

 			$query="select *, CONCAT('rayarcca_',domain_name) as database_name from domain WHERE client_id=".$clientId;
			
		//echo $query;
		//exit;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;	
	    $row_count=mysqli_num_rows($result);
		
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);
			$obj_domain = new domain();
			$obj_domain->init_class_object($row,$obj_domain);
			$this->domainMgr[]=$obj_domain;
		}				 
	} //end of constructor class

	function loadByMemberId($memberId,$temp=false)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$tablename="domain_member";
		if($temp)
			$tablename="domain_member_temp";
		
		$query="SELECT d.*, CONCAT('rayarcca_',domain_name) as database_name FROM domain d
			JOIN domain_member_temp dm ON d.domain_id=dm.domain_id
			WHERE member_id=".$memberId;
			
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			$row_count=mysqli_num_rows($result);
		
			for($i=0;$i<$row_count; $i++)
			{
				$row = mysqli_fetch_object($result);
				$obj_domain = new domain();
				$obj_domain->init_class_object($row,$obj_domain);
				$this->domainMgr[]=$obj_domain;
			}
	}
	
	function getCount()
	{
 		return count($this->domainMgr);
	}
	
	function getDomains(&$domains)
	{
		for($i=0;$i<$this->getCount(); $i++)
			$domains[]=$this->domainMgr[$i];	 
	}	
}

function isDomainMember($domainID,$memberID)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
	
		$query="SELECT member_id FROM domain_member where domain_id=$domainID AND member_id=$memberID";
		$result=mysqli_query($conn,$query);
		if(!$result)
				return false;	
	    
		$rowCount=mysqli_num_rows($result);
		if ($rowCount==1)
		{	
			$row = mysqli_fetch_object($result);	
			return $row->member_id;
		}
		else
			return false;  
	
	
	}