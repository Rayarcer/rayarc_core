<?php
require_once("../core/lib/php/cls_domainManager.php");
require_once("../core/lib/php/cls_itemManager.php");
require_once("../core/lib/php/cls_profileManager.php");
$obj_domain= new domain();
$obj_domain->getDomainByName(getsubDomainName());
		

if(isset($_GET["page"]))
	$page=$_GET["page"];
else
	$page=1;
$limit=16;

$ip=$_SERVER['REMOTE_ADDR'];
$hour=date("H");
$day=date("j");
$month=date("n");
$ip=str_replace(".","",$ip);
$seed=($ip+$hour+$day+$month);
//$seed=null;

$databaseName=$obj_domain->databaseName;
$obj_itemManager= new itemManager("RANDOM",NULL,$databaseName,NULL,NULL,$limit,$page,"PUBLISHED",$seed);
$items=array();
$obj_itemManager->getItems($items);
for($i=0;$i<count($items);$i++)
{
?>							
					<div class="album-list  col_fourth  <?php if(($i+1)%4==0) echo "end";?>  " >
						<div class="custompost_entry">
						
						<div class="custompost_thumb port_img">
								<figure><a 
                                onClick="fn_viewcontent('<?php echo $items[$i]->ikey;?>',
                                '<?php echo $obj_domain->domainName;?>');">
                               
                                <?php
                                if(parse_url($items[$i]->getImage(), PHP_URL_SCHEME) != '')
                                	$artwork=$items[$i]->getImage(0);
                                else
                                	$artwork="http://".$obj_domain->domainName.".".SLDH."/".$items[$i]->image;
                                ?>
                                <img style="border:3px #CCC solid;width:100%;" src="<?php 
											echo $artwork;?>" alt="" /></a></figure>
                                <div class="hover_type">
                                <a class="hoveraudio" href="<?php echo DOMAINHOST?>/content.php?ikey=<?php echo $items[$i]->ikey;?>" title="<?php echo $items[$i]->title; ?>"></a>
                                </div>
                                <span class="imgoverlay"></span>							
                        </div>
														
							<div class="album-desc">
							<?php 
								if($items[$i]->title!=""){
									$title=$items[$i]->title;
									if(strlen($items[$i]->title)>35)
										$title=substr($items[$i]->title,0,30)."...";
								}
								else 
									$title="Untitled";	
							?>
							
								<h2 class="entry-title"><a href="<?php echo DOMAINHOST?>/content.php?ikey=<?php echo $items[$i]->ikey;?>" rel="bookmark" title="Permanent Link to <?php echo $title;?>"><?php echo $title;?></a></h2>
								
								<?php
								  $profile= new profile($items[$i]->profileId);
								  if($profile->name!="")
								  	$name=$profile->name;
								  else
								  	$name="Unnamed";
								  
								?>
                                <span class="label-text"><a href="profile.php?pkey=<?php echo $profile->key; ?>"><?php echo $name; ?></a></span>
                                <span class="label-text"><?php echo $obj_domain->domainTitle;?></span>
                                <span style="font-size:12px;">
											<?php if (round($items[$i]->getUnifiedRating(),0)> 19 )
												{	
													$starCount=$items[$i]->getRatingStarCount($items[$i]->getUnifiedRating());
													for($x=0;$x<$starCount;$x++)
													{
													?>
												 		<img src="images/star_icon.png" width="24" height="24" 
                                                  		<?php if($x==$starCount-1) echo "title=\"".round($items[$i]->getUnifiedRating(),0)."%\"";?>
                                                  		/>
                                    			 		<?php                               
									  				}
													for($x=$starCount;$x<5;$x++)
													{
														?>
                                                		<img src="images/star_blank_icon.png" width="24" height="24" /> 
                                                		<?php
													}	
										  		}
										  		else
										  			echo "<span title='value is below our rating theshold>".round($items[$i]->getUnifiedRating(),0)."'><I>Not Rated</I></span>";
												
											?> 
                  </span>
							</div><!-- .album-desc-->

						</div><!-- .custompost_entry -->
					</div><!-- .album_list -->
                    
                    
<?php 
if(($i+1)%4==0) {?>
<div class="clear"></div>
<?php } } ?>			

				<div class="clear"></div>
				 <div class="pagination pagination2">
            	<?php
			$totalPages=ceil($obj_itemManager->totalCount/$limit);
			//echo $obj_profiles->totalCount;
			?>
            
            <?php 
			echo "<span class='pages extend'>Page ".$page." of ".$totalPages."</span>";			
			for($i=1;$i<=$totalPages;$i++){
			if($i==$page)   
            	echo "<span class='current'>".$page."</span>";
            else
				echo "<a href='".DOMAINHOST.getCurrentScriptName()."?orderby=".$orderby."&page=".$i."' class='inactive' >".$i."</a>";
             } ?>
            </div>
								
				
								
			