<?php
require_once("fns_db.php");
class genre
{
	var $id;
	var $name;
	var $title;
	var $itemCount;
	
	function init_class_object($row,&$obj_genre)
	{
		$obj_genre->id=$row->genre_id;
		$obj_genre->name=$row->genre_name;
		$obj_genre->title=$row->genre_title;
		if (array_key_exists('item_count', $row))
			$obj_genre->itemCount=$row->item_count;		
	}
	
	function genre($id=null)
	{
		if(is_null($id))
			return;
		
		
		$link=dbi_connect();
		if (!$link)
		{
			echo "no database connection";
 			return false;
		}
	
		$query="SELECT genre_id,genre_name,genre_title FROM genre WHERE genre_id=".$id;
		
		$result=mysqli_query($link,$query);
		if(!$result)
		{
			echo "(" . mysqli_errno($link) . ") " .mysqli_error($link);	
			return false;
		}
		$row_count=mysqli_num_rows($result);
		if($row_count!=1)
		{
			echo "unexpected rowcount of ".$row_count." for query for id=".$id;
			return false;	
		}
	
		$row = mysqli_fetch_object($result);	
		$this->init_class_object($row,$this);				
	}
	function getByName($genreName)
	{	
		$conn=dbi_connect();
		if (!$conn)
 			return false;
			
		$query="SELECT genre_id,genre_name,genre_title FROM genre WHERE genre_name='".$genreName."'";		
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
			$this->init_class_object($row,$this);
		}
		 else
		 	$this->id=null;				 
	}
} //end of genre class

class genreManager
{
	var $genres=array();
	var $databaseName=null;
	 
	function genreManager($databaseName=NULL,$withItemOnly=false)
	{
		if(!is_null($databaseName))	
				$this->databaseName=$databaseName;
		$conn=dbi_connect_x($this->databaseName,null,null,null,"genreManager");
		if (!$conn)
		{
 			echo "no database connection";
			return false;
		}
		
		if($withItemOnly)
			$query="SELECT g.genre_id, genre_name,genre_title, COUNT( * ) AS item_count
					FROM  `item_genre` ig
					JOIN rayarcca_admin.genre g ON ig.genre_id = g.genre_id
					GROUP BY genre_id, genre_name,genre_title";
		else
			$query="SELECT rag.genre_id, rag.genre_name, rag.genre_title 
					FROM rayarcca_admin.genre rag
					JOIN genre g ON rag.genre_id=g.genre_id";
			
		$result=mysqli_query($conn,$query);
		if(!$result)
			return false;
			
	    $row_count=mysqli_num_rows($result);
		for($i=0;$i<$row_count; $i++)
		{
			$row = mysqli_fetch_object($result);
			$obj_genre = new genre();
			$obj_genre->init_class_object($row,$obj_genre);	
			$this->genres[]=$obj_genre;						
		}   //end of for loop			
	
	}// end of genre manager constructor
	function getCount()
	{
 		return count($this->genres);
	}
	function getGenres(&$genres)
	{
		for($i=0;$i<$this->getCount(); $i++)
			$genres[]=$this->genres[$i];
	}
	
	
} //end of genre manager class

?>