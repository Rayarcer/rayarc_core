<!DOCTYPE html>
<html>
<head>
<?php include("_header_core.php");?>
</head>
<body class="video-template-default single single-video postid-8476 jp-radio-top">
	<div class="iva_page_loader"></div>	
	<div class="layoutoption" id="stretched">
		<div class="bodyoverlay"></div><!-- .bodyoverlay -->
		<div id="sticky">Enter the content which will be displayed in sticky bar</div><!-- #sticky -->	
		<div id="wrapper">
			<?php include("_header_topbar_core.php");?>
			<div class="clear"></div>
			<?php include("_header_menu_core.php");?>
			<div id="ajaxwrap">
			<script>
			var ikey="<?php echo $obj_item->ikey; ?>";
			loadscript("<?php echo COREHOST;?>/lib/js/fb.js");
			loadscript("<?php echo COREHOST;?>/lib/js/content.js");
		var fapOpened=false;
		//console.log("set default fapOpened="+fapOpened);
		
		</script>
				<div class="iva_page_bg"></div>	
				<div id="main" class="rightsidebar">
					<div id="primary" class="pagemid">
						<div class="inner">
							<div class="content-area">
								<div class="custompost-single post-8476 video type-video status-publish has-post-thumbnail hentry video_type-tour-2010" id="post-8476">
									<div class="custompost_entry">
										<div class="custompost_details">
											<h2 class="album-title ">
												<span><?php echo $obj_item->title;?></span>
											</h2>
											<!--<div class="video-stage"> -->
												<div>
													<!--
													<video controls>
  														<source src="<?php echo "http://".$obj_domain->domainName.".".SLDH.$obj_item->contentSource;?>" type="video/mp4">
  														Your browser does not support the video tag.
													</video>
													-->
													<?php
														$itemCount=1;
														$domain=null;
														$likeCount=$obj_item->getLikeCount();
														$viewCount=$obj_item->getViewCount("S");
														$dataId=$obj_item->ikey;
														$database=$obj_domain->databaseName;
														$itemID=$obj_item->id;
														$width="100%";
														$height=360;
														$positionID=0;
														$autoplay=true;
														$imagePath=$obj_item->getImage();
														include("../core/lib/php/fns_mediaplayer.php");
														$handlerID=show_MediaPlayerJW($obj_item->id,"100%",360);
														include("content_event.inc.php");
														include("content_metric_info.inc.php");
													?>
													<?php 
														$likeCount=$obj_item->getLikeCount();
														$viewCount=$obj_item->getViewCount("S");
														$dataId=$obj_item->ikey;
													?> 
													
												</div>
											<!--  </div>-->					
										</div><!-- .custompost_details -->
									</div><!-- .custompost_entry -->
									<div class="demospace" style="height:20px;"></div>
									<p><?php echo $obj_item->desc;?>.<?php echo $obj_item->longDesc;?></p>
									<div class="demospace" style="height:20px;"></div>
								</div><!-- #post-72 -->						
							</div><!-- .content-area -->
							<div id="sidebar">
								<div class="sidebar-inner widget-area">
								<!--  sidebar content here -->
								<?php include("content_sidebar.php");?>
								</div><!-- .widget-area -->
							</div><!-- #sidebar -->
						</div><!-- inner -->
					</div><!-- #primary.pagemid -->
				<?php include("content_fromlabel.php");?>
				
				</div><!-- #Ajax wrap -->
			</div><!-- #main -->
			<?php include("_footer_core.php");?>
		</div><!-- #wrapper -->
	</div><!-- #layout -->
	<?php include("_trailer_core.php");?>
</body>
</html>