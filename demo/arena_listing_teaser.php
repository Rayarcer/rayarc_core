<?php
require_once("../core/lib/php/cls_domainManager.php");
require_once("../core/lib/php/cls_itemManager.php");
require_once("../core/lib/php/cls_profileManager.php");
$obj_domain= new domain();
$obj_domain->getDomainByName(getsubDomainName());
		


$page=1;
$limit=6;

$ip=$_SERVER['REMOTE_ADDR'];
$hour=date("H");
$day=date("j");
$month=date("n");
$ip=str_replace(".","",$ip);
$seed=($ip+$hour+$day+$month);
//$seed=null;
?>
<div class="one_fourth">
	<h2>Latest <strong>Music</strong></h2>
	<p>
	Check it out the latest emerging talent in our Demo Music Arena.
	</p>
	<p>
		<a href="arena.php">&#8211; View All</a><br />
	</p>
</div>
<div class="three_fourth last">
<?php
$databaseName=$obj_domain->databaseName;
$obj_itemManager= new itemManager("RANDOM",NULL,$databaseName,NULL,NULL,$limit,$page,"PUBLISHED",$seed);
$items=array();
$obj_itemManager->getItems($items);
for($i=0;$i<count($items);$i++)
{

	
	if(parse_url($items[$i]->getImage(), PHP_URL_SCHEME) != '')
		$artwork=$items[$i]->getImage(0);
	else
		$artwork="http://".$obj_domain->domainName.".".SLDH."/".$items[$i]->image;
?>
	<div class="col_third <?php if(($i+1)%3==0) echo "end";?>  item element electro-house  techno axtone-records  spinnin-records">
		<div class="custompost_entry album-list">
			<div class="custompost_thumb port_img">
				<figure>
					<img  style="width:100%" width="470"  height="470"   alt="Listen to Roger" src="<?php echo $artwork; ?>">
				</figure>
				<div class="hover_type">
					<a class="hoveraudio"  href="content.php?ikey=<?php echo $items[$i]->ikey;?>" title="<?php echo  $items[$i]->title; ?>"></a>
				</div>
				<span class="imgoverlay"></span>
			</div>
			<div class="album-desc">
					<h2 class="entry-title"><a href="http://templates.rayarc.localhost:8080/musicplay/?post_type=albums&p=20"><?php echo  $items[$i]->title; ?></a></h2>
					<span><?php echo $obj_domain->domainTitle;?></span>
			</div>
		</div>
	</div>
<?php 
}
?>
<!--  
	<div class="col_third  item element hip-hop ovum-recordings  phethouse-records">
		<div class="custompost_entry album-list">
			<div class="custompost_thumb port_img">
				<figure><img  width="470"  height="470"   alt="Leaving You Zouk" src="http://templates.rayarc.localhost:8080/musicplay/wp-content/uploads/2013/10/8294799-470x470.jpg"></figure>
				<div class="hover_type"><a class="hoveraudio"  href="http://templates.rayarc.localhost:8080/musicplay/?post_type=albums&p=72" title="Leaving You Zouk"></a></div>
				<span class="imgoverlay"></span>
			</div>
			<div class="album-desc">
				<h2 class="entry-title"><a href="http://templates.rayarc.localhost:8080/musicplay/?post_type=albums&p=72">Leaving You Zouk</a></h2>
				<span>Ovum Recordings, PhetHouse Records</span>
			</div>
		</div>
	</div>
	<div class="col_third end  item element deep-house  techno axtone-records  snatch-recordings">
		<div class="custompost_entry album-list">
			<div class="custompost_thumb port_img">
				<figure><img  width="470"  height="470"   alt="Adjustments Cannon" src="http://templates.rayarc.localhost:8080/musicplay/wp-content/uploads/2013/10/8222926-470x470.jpg"></figure>
				<div class="hover_type">
					<a class="hoveraudio"  href="http://templates.rayarc.localhost:8080/musicplay/?post_type=albums&p=79" title="Adjustments Cannon"></a>
				</div>
				<span class="imgoverlay"></span>
			</div>
			<div class="album-desc">
				<h2 class="entry-title"><a href="http://templates.rayarc.localhost:8080/musicplay/?post_type=albums&p=79">Adjustments Cannon</a></h2><span>Axtone Records, Snatch Recordings</span>
			</div>
		</div>
	</div>
	-->
	<div class="clear"></div>
</div> <!-- end of three_fourth last -->
<div class="clear"></div>
