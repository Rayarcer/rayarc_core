<?php
require_once("fns_db.php");
require_once("fns_errorHandler.php");
class prospect
{
	//prospect
	var $id;
	var $firstname;
	var $lastname;
	var $email;
	var $roleId;
	var $planId;
	var $companyName;
	var $website;
	var $contactNumber;
	var $marketChannelId;
	var $project;
	var $status;
	var $verifyKey;
	var $created;
	var $services=array();
		
	function prospect($id=null)
	{
		
		if ($id===null)
		{
			$this->verifyKey=randString(8);
			$this->status="PENDING";
			return true;
		}
		
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$query="select * from prospect where prospect_id=$id";	
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
			$this->id=$row->prospect_id;
			$this->firstname=$row->first_name;
			$this->lastname=$row->last_name;
			$this->email=$row->email;
			$this->roleId=$row->role_id;
			$this->planId=$row->plan_id;
			$this->companyName=$row->company_name;
			$this->website=$row->website;
			$this->contactNumber=$row->contact_number;
			$this->marketChannelId=$row->mc_id;
			$this->project=$row->project;
			$this->status=$row->status;
			$this->verifyKey=$row->verify_key;
			$this->created=$row->created;
		 }
		 else
		 	$this->id=null;				 
	} //end of prospect constructor
	function pullServices()
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
			
			$query="select * from prospect_service where prospect_id=".$this->id;
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
	    	$this->services[]=$row[0];
		}
		 else
		 	$this->id=null;	
	}
	
	function save()
	{	
		$conn=dbi_connect();
		if (!$conn)
			return false;
	
	$query="INSERT INTO `prospect`(`prospect_id`, `first_name`, `last_name`, `email`, `role_id`) VALUES (NULL,'".mysqli_real_escape_string($conn,$this->firstname)."','".mysqli_real_escape_string($conn,$this->lastname)."','".mysqli_real_escape_string($conn,$this->email)."',".$this->roleId.")";	
	//echo $query;
	//exit;
	
		$result=mysqli_query($conn,$query);
	
		if(!$result)
		{
			
			$isValid=false;
			
			$this->id=null;
			return false;
		}
		$id = mysqli_insert_id($conn); 
		$this->id=$id;
		//echo "saveMemberInfo=".$this->id;
		return $this->id;	
	
	}
	
	function update()
	{	
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
	$query="UPDATE `prospect` SET `first_name`='".mysqli_real_escape_string($conn,$this->firstname)."',`last_name`='".mysqli_real_escape_string($conn,$this->lastname)."',`email`='".mysqli_real_escape_string($conn,$this->email)."',`role_id`=".$this->roleId.",`company_name`='".mysqli_real_escape_string($conn,$this->companyName)."',`website`='".mysqli_real_escape_string($conn,$this->website)."',`contact_number`='".mysqli_real_escape_string($conn,$this->contactNumber)."',`mc_id`=".(is_null($this->marketChannelId) ? "NULL" : mysqli_real_escape_string($conn,$this->marketChannelId)).",`project`='".mysqli_real_escape_string($conn,$this->project)."',`status`='".mysqli_real_escape_string($conn,$this->status)."',`verify_key`='".mysqli_real_escape_string($conn,$this->verifyKey)."' WHERE `prospect_id`=".$this->id;	
	//echo $query;
	//exit;
	
		$result=mysqli_query($conn,$query);
	
		if($result)	
			return true;
		else
			return false;
	
	}
	function addService($serviceId)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		//insert into prospect_service table
		$query="INSERT INTO `prospect_service`(`prospect_id`, `service_id`) VALUES (".$this->id.",".$serviceId.")";
		//echo $query.";";
		
	$result=mysqli_query($conn,$query);
	
		if(!$result)
		{
			//$this->id=null;
			return false;
		}
		$this->services[]=$serviceId;
		return $this->id;	
	}
	function getServices(&$serviceId)
	{
		for($i=0;$i<count($this->services); $i++)
			$serviceId[]=$this->services[$i];
	}
	
}
class prospectManager
	{
		var $prospectMgr = array();
		
		function prospectManager($status=null)
		{
			$conn=dbi_connect();
			if (!$conn)
 			return false;

			if ($status===null)
				$query="select * from prospect";
			else
				$query="select * from prospect where status='".$status."'";
			$result=mysqli_query($conn,$query);
			if(!$result)
				return false;	
	    	$row_count=mysqli_num_rows($result);
		
			for($i=0;$i<$row_count; $i++)
			{
				$row = mysqli_fetch_object($result);
				$obj_prospect = new prospect($row->prospect_id,true);
				$this->prospectMgr[]=$obj_prospect;				
			}
		}
		
		
		
		function getCount()
		{
 			return count($this->prospectMgr);
		}
		function getProspect(&$prospects)
		{	
			for($i=0;$i<$this->getCount(); $i++)
				$prospects[]=$this->prospectMgr[$i];	 
		}


	} //end of prospect manager


class client
{
	//client 1
	var $id;
	var $firstname;
	var $lastname;
	var $email;
	var $contactNumber;
	var $companyName;
	var $companySize;
	var $title;
	var $status;
	var $verifyKey;
	var $created;
	var $lastAccessed;
	var $role;
	var $planId;

	//client 2
	var $companyWebsite;
 	var $country;
 	var $region;
 	var $city;
 	var $postalCode; 
 	var $marketChannelId;
	
	//client 3
	var $subDomainName;
	var $username;
	var $password;
	
	function client($id=null,$temp=false)
	{
		//echo "calling member function with id=".$id;
		if ($id===null)
		{
			//echo "member is null";
			$this->verifyKey=randString(8);
			$this->status="PENDING";
			if($temp)
				$this->status="UNVERIFIED";	
			$this->id=null;	
			return;
		}
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$tablename="client1_contact";
		if($temp)
			$tablename="client1_contact_temp";	
			
		$query="select * from $tablename where client_id=$id";		
		//echo $query;
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
			$this->id=$row->client_id;
			$this->firstname=$row->first_name;
			$this->lastname=$row->last_name;
			$this->email=$row->email;
			$this->contactNumber=$row->contact_number;
			$this->companyName=$row->company_name;
			$this->companySize=$row->company_size;
			$this->title=$row->title;
			$this->status=$row->status;
			$this->role=$row->role;
			$this->planId=$row->plan_id;
			$this->verifyKey=$row->verify_key;
			$this->created=$row->created;
			$this->lastAccessed=$row->last_accessed;
		 	$this->client2($id,$temp);
			$this->client3($id,$temp);
		 }
		 else
		 	$this->id=null;				 
	}
	function getClientByVerifyKey($verifyKey,$temp=false)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$tablename="client1_contact";
		if($temp)
			$tablename="client1_contact_temp";	
						
					$query="select * from $tablename where verify_key='".$verifyKey."'";
					//echo $query;
				
					$result=mysqli_query($conn,$query);
					if(!$result)
					{
						$errMsgArray=array();
						$errMsg="Error: clientkey:".$verifyKey." | ".mysqli_error($conn);
						$errMsgArray[]=$errMsg;
						errorHandler(0,$errMsg,0,"getClientByVerifyKey()","cls_clientManager.php");
						return false;
					}
		    $row_count=mysqli_num_rows($result);
		   // echo "row_count:".$row_count;
		   // exit;
		    if($row_count)
		    {
		    $row = mysqli_fetch_object($result);				
			$this->id=$row->client_id;
			$this->firstname=$row->first_name;
			$this->lastname=$row->last_name;
			$this->email=$row->email;
			$this->contactNumber=$row->contact_number;
			$this->companyName=$row->company_name;
			$this->companySize=$row->company_size;
			$this->title=$row->title;
			$this->status=$row->status;
			$this->role=$row->role;
			$this->planId=$row->plan_id;
			$this->verifyKey=$row->verify_key;
			$this->created=$row->created;
			$this->lastAccessed=$row->last_accessed;
		 	$this->client2($this->id,$temp);
			$this->client3($this->id,$temp);
		    		
		    }
	
	}
	
	
	
	function client2($id=NULL,$temp=false)
	{
		if ($id==null)
	    	return;
		$conn=dbi_connect();
		if (!$conn)
 			return false;
			
		$tablename="client2_location";
		if($temp)
			$tablename="client2_location_temp";		
		$query="select * from $tablename where client_id=$id";		

		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;	
	    $row_count=mysqli_num_rows($result);
		if($row_count)
		{
			$row = mysqli_fetch_object($result);	
			$this->country=$row->country;
			$this->region=$row->region;
			$this->city=$row->city;
			$this->companyWebsite=$row->company_website;
			$this->postalCode=$row->postal_code;
		    $this->marketChannelId=$row->mc_id;					
		}				 	
	}
	function client3($id=NULL,$temp=false)
	{
		if ($id==null)
	    	return;
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		$tablename="client3_account";
		if($temp)
			$tablename="client3_account_temp";			
			
		$query="select * from $tablename where client_id=$id";		
		//echo $query;
		
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;	
	    $row_count=mysqli_num_rows($result);
		//echo $row_count;
		
		if($row_count)
		{
			$row = mysqli_fetch_object($result);	
			$this->subDomainName=$row->subdomain_name;
			//echo $row->subdomain_name;
			//echo $this->subDomainName;
			//exit;
			$this->username=$row->username;
			$this->password=$row->password;				
		}				 	
	}
	
	static function getClientIdbyEmail($email)
	{
		$conn=dbi_connect();
		if (!$conn)
			return  false;
	
			$query="select client_id from client1_contact where email='$email'";
			$result=mysqli_query($conn,$query);
			if(!$result)
				return false;
				$rowCount=mysqli_num_rows($result);
				//echo "RowCount=".$rowCount;
				if ($rowCount==1)
				{
					$row = mysqli_fetch_object($result);
					return $row->client_id;
				}
				else
				{
					return false;
				}
	}
	function saveClient1Info($temp=false)
	{ 
		//echo serialize($this);
		$conn=dbi_connect();
		if (!$conn)
 			return false;
		
		$tablename="client1_contact";
		if($temp)
			$tablename="client1_contact_temp";
			
		$query="INSERT INTO `$tablename`(`client_id`, `first_name`, `last_name`, `email`, `contact_number`, `company_name`, `company_size`, `title`, `status`,`role`,`plan_id`,`verify_key`, `created`, `last_accessed`) VALUES (NULL,'".mysqli_real_escape_string($conn,$this->firstname)."','".mysqli_real_escape_string($conn,$this->lastname)."','".mysqli_real_escape_string($conn,$this->email)."','".mysqli_real_escape_string($conn,$this->contactNumber)."','".mysqli_real_escape_string($conn,$this->companyName)."','0','".mysqli_real_escape_string($conn,$this->title)."','".mysqli_real_escape_string($conn,$this->status)."',".mysqli_real_escape_string($conn,$this->role).",".mysqli_real_escape_string($conn,$this->planId).",'".mysqli_real_escape_string($conn,$this->verifyKey)."',NULL,'0000-00-00 00:00:00')";

		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$errMsgArray=array();
			$errMsg="Error: client email:".$this->email." | ".mysqli_error($conn)." for query:".$query;
			$errMsgArray[]=$errMsg;
			errorHandler(0,$errMsg,0,"saveClient1Info()","cls_clientManager.php");
			$this->id=null;
			return false;
		}
		$id = mysqli_insert_id($conn); 
		$this->id=$id;
		//echo "saveMemberInfo=".$this->id;
		return $this->id;	
	
	}
	function saveClient2Info($temp=false)
	{
		//echo serialize($this);
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$tablename="client2_location";
		if($temp)
			$tablename="client2_location_temp";
		
		$query="INSERT INTO `$tablename`(`client_id`, `country`, `region`, `city`, `postal_code`, `mc_id`) VALUES (".$this->id.",'".$this->country."','".$this->region."','".$this->city."','".$this->postalCode."',".$this->marketChannelId.")";

		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$errMsgArray=array();
			$errMsg="Error: client#:".$this->id." | ".mysqli_error($conn);
			$errMsgArray[]=$errMsg;
			errorHandler(0,$errMsg,0,"saveClient2Info()","cls_clientManager.php");
			$this->id=null;
			return false;
		}
		//$id = mysql_insert_id();
		//$this->id=$id;
		//echo "saveMemberInfo=".$this->id;
		return $this->id;
	}
	function existClientIDinfo($temp=false)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$tablename="client3_account";
		if($temp)
			$tablename="client3_account_temp";
		
		$query="Select count(*) from $tablename Where client_id=".$this->id;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
	
		$row_count=mysqli_num_rows($result);
		if($row_count==1)
		{
			$row = mysql_fetch_array($result);
			if ($row[0]==1)
				return true;
		}
		return false;
		
	}
	
	
	
	function saveClient3Info($temp="false")
	{
		//echo serialize($this);
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
		$tablename="client3_account";
		if($temp)
			$tablename="client3_account_temp";
		
		$query="INSERT INTO `$tablename`(`client_id`, `subdomain_name`, `username`, `password`) VALUES (".$this->id.",'".$this->subDomainName."','".$this->username."',password('".$this->password."'))";
		
		//echo $query;
		$result=mysqli_query($conn,$query);
		if(!$result)
		{
			$this->id=null;
			return false;
		}
		//$id = mysql_insert_id();
		//$this->id=$id;
		//echo "saveMemberInfo=".$this->id;
		return $this->id;
	}
	function updateClient1Info($temp=false)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
		
			$tablename="client1_contact";
			if($temp)
				$tablename="client1_contact_temp";
	
			$query="UPDATE `$tablename` SET`company_name`='".mysqli_real_escape_string($conn,$this->companyName)."',`contact_number`='".mysqli_real_escape_string($conn,$this->contactNumber)."',`title`='".mysqli_real_escape_string($conn,$this->title)."',`company_website`='".mysqli_real_escape_string($conn,$this->companyWebsite)."' WHERE `client_id`=".$this->id;
	
	
			$result=mysqli_query($conn,$query);
	
			if($result)
				return true;
			else
			{
				$errMsgArray=array();
				$errMsg="Error: client#:".$this->id." | ".mysqli_error($conn);
				$errMsgArray[]=$errMsg;
				errorHandler(0,$errMsg,0,"updateClient1Info()","cls_clientManager.php");
				return false;
			}
					
	
	}
	
	
	function verified()
	{
		$this->status="VERIFIED";
		$conn=dbi_connect();
		if (!$conn)
			return false;
		$query="UPDATE client1_contact_temp set status='".$this->status."' WHERE client_id=".$this->id;
		$result=mysqli_query($conn,$query);
		if($result)	
			return true;
		else
			return false;
	}
	function addMember($memberID)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;
			
			$tablename="client_member";
	
			
		$query="INSERT INTO $tablename (client_id, member_id) VALUES (".$this->id.",".$memberID.")";
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;
	}
	
	function setStatus($status,$temp=false)
	{
		$this->status=$status;
		$conn=dbi_connect();
		if (!$conn)
			return false;
		$tablename="client1_contact";
		if($temp)
			$tablename="client1_contact_temp";	
			
		$query="UPDATE $tablename set status='".$this->status."' WHERE client_id=".$this->id;
		$result=mysqli_query($conn,$query);
		if($result)	
			return true;
		else
			return false;
	}
	
	function validatePassword($password)
	{
		if ((strlen($password)>=8) && (strlen($password)<=16))
		{
			$this->password=$password;	
			return true;
		}
		else
			return false;
	
	}
	
	function updatePassword($password)
	{
		if(!$this->validatePassword($password))
			return false;	
		if (!db_connect())
 			return false;
		$query="UPDATE client3_account
				SET password=password('$password')
				WHERE client_id=".$this->id."";
		
		$result=mysqli_query($conn,$query);
		if($result)
			return true;
		else
			return false;	
	}

	
} //end of class

	class clientManager
	{
		var $clientMgr = array();
		function clientManager($status=null,$temp=false)
		{
			$conn=dbi_connect();
		if (!$conn)
 			return false;
			
		
		//if($temp)
			//$tablename="client1_contact_temp";
			$tablename="client1_contact";
			
			if ($status===null)
				$query="select * from ".$tablename;
			else
				$query="select * from ".$tablename." where status='".$status."'";
			$result=mysqli_query($conn,$query);
			if(!$result)
				return false;	
	    	$row_count=mysqli_num_rows($result);
			//echo $query."  for  ".$row_count;
			for($i=0;$i<$row_count; $i++)
			{
				$row = mysqli_fetch_object($result);
				$obj_client = new client($row->client_id);
				$this->clientMgr[]=$obj_client;				
			}
		
		}
		function getCount()
		{
 			return count($this->clientMgr);
		}
		
		function getCountNotArchived()
		{
			$clientCountNotArchived=0;
 			for($i=0;$i<count($this->clientMgr);$i++)
				if($this->clientMgr[$i]->status!="ARCHIVE")
					$clientCountNotArchived++;
			
			return $clientCountNotArchived;
		}
		
		
		function getClient(&$clients)
		{	
			for($i=0;$i<$this->getCount(); $i++)
				$clients[]=$this->clientMgr[$i];	 
		}


	} //end of client Manager
	
	
	function getClientIdbyEmail($email)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return  false;
 
		$query="select client_id from client1_contact where email='$email'";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
		$rowCount=mysqli_num_rows($result);
		//echo "RowCount=".$rowCount;
		if ($rowCount==1)
		{	
			$row = mysqli_fetch_object($result);	
			return $row->client_id;
		}
		else
		{
		return false;  
		}
	}
	

	function getClientIdBySubDomainName($subDomainName)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return  false;
		
		$query="select client_id from domain where domain_name='".trim($subDomainName)."'";
		//echo $query;
		
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
		$rowCount=mysqli_num_rows($result);
		//echo "RowCount=".$rowCount;
		if ($rowCount==1)
		{	
			$row = mysqli_fetch_object($result);	
			return $row->client_id;
		}
		else
		{
		return false;  
		}
	
	
	}
	function getClientIdByUsername($username)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return  false;
	
		$query="select client_id from client3_account where username='$username'";
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
		$rowCount=mysqli_num_rows($result);
		//echo "RowCount=".$rowCount;
		if ($rowCount==1)
		{	
			$row = mysqli_fetch_object($result);	
			return $row->client_id;
		}
		else
		{
		return false;  
		}
	
	}
	
	function isClient($email,$password,$domainName=null)
	{
		$conn=dbi_connect();
		if (!$conn)
 			return  false;
	
	
	$query="SELECT c1.client_id
FROM  `client1_contact` c1
JOIN client3_account c3 
ON c1.client_id = c3.client_id
WHERE email = '$email'
AND password=password('$password')";

if(!is_null($domainName))
$query.=" AND subdomain_name='$domainName'";

		//echo $query;
		//exit;
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
		$rowCount=mysqli_num_rows($result);
		//echo "RowCount=".$rowCount;
		if ($rowCount==1)
		{	
			$row = mysqli_fetch_object($result);	
			return $row->client_id;
		}
		else
		{
		return false;  
		}
	
	}
	
	
	
	function isClientMember($clientID,$memberID)
	{
		$conn=dbi_connect();
		if (!$conn)
			return false;

		$query="SELECT member_id FROM client_member where client_id=$clientID AND member_id=$memberID";
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
	