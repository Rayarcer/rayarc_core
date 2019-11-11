<!DOCTYPE html>
<html>
<head>
<?php include("_header_core.php");?>
</head>

<body class="albums-template-default single single-albums postid-72 jp-radio-top">
<div class="iva_page_loader"></div>	<div class="layoutoption" id="stretched">
	
	<div class="bodyoverlay"></div><!-- .bodyoverlay -->
		<div id="sticky">Enter the content which will be displayed in sticky bar</div><!-- #sticky -->
	
	<div id="wrapper">
		<?php include("_header_topbar_core.php");?>
<div class="clear"></div>
<?php include("_header_menu_core.php");?>
		<div id="ajaxwrap">
		<script type="text/javascript">
jQuery(document).ready(function($) {

	 console.log("jquery ready: content_album.php");

		// Scroll to a certain element
		//document.querySelector('#playlist').scrollIntoView({ 
		//  behavior: 'smooth' 
		//});
	 function getCookie(cname) {
			    var name = cname + "=";
			    var ca = document.cookie.split(';');
			    for(var i = 0; i < ca.length; i++) {
			        var c = ca[i];
			        while (c.charAt(0) == ' ') {
			            c = c.substring(1);
			        }
			        if (c.indexOf(name) == 0) {
			            return c.substring(name.length, c.length);
			        }
			    }
			    return "";
			}
	
	var likedItemsString = getCookie("MID-0_LKITMS");
			
			//console.log("likedItemsString="+likedItemsString);
			if(likedItemsString!="")
			{
				likedItemsArray=likedItemsString.split(".");
				//console.log("liked items Count:"+ likedItemsArray.length);
				
				var found=false;	
				var i;
				for (i = 0; i < likedItemsArray.length; i++) {
					if($( "#like_thumps_up-"+likedItemsArray[i]).length)
					{
						console.log("playlist item:"+likedItemsArray[i]+" already liked");
						$( "#like_thumps_up-"+likedItemsArray[i]).removeClass( "fa-thumbs-o-up" );
						$( "#like_thumps_up-"+likedItemsArray[i]).addClass( "fa-thumbs-up" );
						$( "#like_thumps_up_link-"+likedItemsArray[i]).removeAttr( "href" );
					}
						
				} 
				
			}
		

		$('html,body').animate({scrollTop:$("#playlist").offset().top}, 
			    Math.abs(window.scrollY - $("#playlist").offset().top) * 5);
	 
	// self.location.href = '#playlist'; 
	 
	
});	
</script>
		
		
		<div class="iva_page_bg"></div>	
		<div id="main" class="rightsidebar">
	<div id="primary" class="pagemid">
	<div class="inner">
		<div class="content-area">
						<div class="custompost-single post-72 albums type-albums status-publish has-post-thumbnail hentry genres-hip-hop label-ovum-recordings label-phethouse-records" id="post-72">
							
				<div class="custompost_entry">
					<div class="custompost_details">
						<div class="col_fourth album_bio">
														<div class="custompost_thumb port_img">
								<figure><img  width="470"  height="470"   alt="<?php echo $obj_item->title;?>" src="<?php echo $obj_item->getImage();?>"></figure><div class="hover_type"><a data-rel="prettyPhoto" class="hoverimage" href="<?php echo $obj_item->getImage();?>" title="<?php echo $obj_item->title;?>"></a></div><span class="imgoverlay"></span>							</div>
													
							<div class="album-details">
							<div class="album-meta">
								<span class="album-label">Title</span><div class="album-mdata"><?php echo $obj_item->title;?></div>
								</div>
								<div class="album-meta">
									<?php 
									$obj_profile=new profile($obj_item->profileId);	
									?>
									<span class="album-label">Artist</span><div class="album-mdata">
									<?php echo $obj_profile->name;?>
								</div>
							</div>
							
								<div class="album-meta"><span class="album-label">Release Date</span><div class="album-mdata"><?php echo $obj_item->dateAdded;?></div></div>
					
								<div class="album-meta"><span class="album-label">Label</span><div class="album-mdata"><?php echo $obj_domain->domainTitle;?></div></div>
								<!--  <div class="album-meta"><span class="album-label">Catalog ID</span><div class="album-mdata">MP054R549</div></div>-->
								<div class="album-meta"><span class="album-label">Genres</span>
									<div class="album-mdata">
										<?php echo implode(", ", $obj_item->genres); ?>
										
										<!-- 
										<a href="/?genres=hip-hop">Reggae</a>
										<a href="/?genres=hip-hop">Oldies</a>
										-->
									</div>
								</div>
								<!--  
								<div class="album-meta">
									<span class="album-label">Contact</span><div class="album-mdata"><a href="mailto:djtrigger8@gmail.com">send email</a></div>
								</div>
								-->
							</div><!-- .album-details -->

							
						</div><!-- .col_fourth -->
					
						<?php
						
						
							include("content_playlist.php");
						
						
						?>
						
					</div><!-- .custompost_details -->
				</div><!-- .custompost_entry -->
				
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