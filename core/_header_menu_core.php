<div id="header" class="header clearfix">
	<div class="header-area" style="min-height: 0px; height:0px;">
		<div class="logo">
			<a href="<?php echo DOMAINHOST;?>" title="<?php echo $title;?>">
				<img  src="images/logos/domain_logo_flat.png" alt="<?php echo $title;?>" />
			</a>
		</div>
		<!-- /logo -->
		
		<div class="primarymenu menuwrap">
			<ul id="atp_menu" class="sf-menu">
				<!-- <li id="menu-item-10343" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo DOMAINHOST;?>/djmixlist.php">DJ Mixes</a></li>
				<li id="menu-item-10343" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="selectsource.php">Share | Upload</a></li>			
				 
				<li id="menu-item-10342" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="myprofile.php">My Profiles</a></li> 
				 
				<li id="menu-item-10342" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="arena.php">Arena</a></li>
				
				<li id="menu-item-10342" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="artists.php">Artists</a></li>
				
				<li id="menu-item-10343" class="menu-item menu-item-type-post_type menu-item-object-page"><a class="no-ajaxy" href="signup.php">Sign-up</a></li>
				
				<?php if(isset($_SESSION["loginSession_memberID"])) {?>
          			<li id="menu-item-10344" class="menu-item menu-item-type-post_type menu-item-object-page"><a class="no-ajaxy" href="logout.php">Logout</a></li>
          		<?php } else {?>
            		<li id="menu-item-10344" class="menu-item menu-item-type-post_type menu-item-object-page"><a class="no-ajaxy" href="login.php">Login</a></li>
           		<?php } ?>  
           		
		<!-- 	<li id="menu-item-10318" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children"><a href="http://www.aivahthemes.com/musicplay/">Home</a>
<ul class="sub-menu">
	<li id="menu-item-10346" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=113">Home Page 2</a></li>
</ul>
</li>
<li id="menu-item-10333" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=59">Albums</a>
<ul class="sub-menu">
	<li id="menu-item-10356" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9911">Albums  3 columns</a></li>
	<li id="menu-item-10355" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9913">Albums  4 columns</a></li>
	<li id="menu-item-10354" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9914">Albums  5 columns</a></li>
	<li id="menu-item-10353" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9915">Albums  6 columns</a></li>
	<li id="menu-item-10358" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=10102">5 Cols Filterable</a></li>
	<li id="menu-item-10359" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=10099">4 Cols Filterable</a></li>
	<li id="menu-item-10360" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=10096">3 Cols Filterable</a></li>
	<li id="menu-item-10322" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=deep-house">Deep House</a></li>
	<li id="menu-item-10325" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=electro-house">Electro House</a></li>
	<li id="menu-item-10323" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=pop-rock">Pop / Rock</a></li>
	<li id="menu-item-10324" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=trance">Trance</a></li>
	<li id="menu-item-10326" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=techno">Techno</a></li>
</ul>
</li>
<li id="menu-item-10332" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=33">Artists</a>
<ul class="sub-menu">
	<li id="menu-item-10352" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9919">Artists  3 column</a></li>
	<li id="menu-item-10351" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9921">Artists  4 column</a></li>
	<li id="menu-item-10350" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9922">Artists  5 columns</a></li>
	<li id="menu-item-10349" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9923">Artists  6 columns</a></li>
	<li id="menu-item-10319" class="menu-item menu-item-type-taxonomy menu-item-object-artist_cat"><a href="<?php echo COREHOST;?>/cms/wp/?artist_cat=rock-band">Rock Band</a></li>
	<li id="menu-item-10320" class="menu-item menu-item-type-taxonomy menu-item-object-artist_cat"><a href="<?php echo COREHOST;?>/cms/wp/?artist_cat=boom-band">Boom Band</a></li>
	<li id="menu-item-10321" class="menu-item menu-item-type-taxonomy menu-item-object-artist_cat"><a href="<?php echo COREHOST;?>/cms/wp/?artist_cat=rockstar-band">Rockstar Band</a></li>
</ul>
</li>
<li id="menu-item-10335" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=192">DJ Mixes</a>
<ul class="sub-menu">
	<li id="menu-item-10348" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9906">DJ Mix Shortcode</a></li>
</ul>
</li>
<li id="menu-item-10339" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8831">Features</a>
<ul class="sub-menu">
	<li id="menu-item-10345" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8907">Video Tutorials</a></li>
	<li id="menu-item-10336" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8328">Shortcodes</a></li>
	<li id="menu-item-10340" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8845">Theme Options</a></li>
	<li id="menu-item-10342" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8882">Artist Post Type</a></li>
	<li id="menu-item-10343" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8898">Album Post Type</a></li>
	<li id="menu-item-10344" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8902">Video Post Type</a></li>
	<li id="menu-item-10341" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8878">Home Page Teaser</a></li>
	<li id="menu-item-10347" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9681">FAQ</a></li>
</ul>
</li>
<li id="menu-item-10337" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8369">Photos</a></li>
<li id="menu-item-10338" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8478">Videos</a></li>
<li id="menu-item-10334" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=131">Blog</a></li>
<li id="menu-item-10357" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9241">Events</a>
<ul class="sub-menu">
	<li id="menu-item-10327" class="menu-item menu-item-type-taxonomy menu-item-object-events_cat"><a href="<?php echo COREHOST;?>/cms/wp/?events_cat=clubs">Clubs</a></li>
	<li id="menu-item-10328" class="menu-item menu-item-type-taxonomy menu-item-object-events_cat"><a href="<?php echo COREHOST;?>/cms/wp/?events_cat=festivals">Festivals</a></li>
	<li id="menu-item-10329" class="menu-item menu-item-type-taxonomy menu-item-object-events_cat"><a href="<?php echo COREHOST;?>/cms/wp/?events_cat=open-air">Open Air</a></li>
</ul>
</li>
-->
</ul>			<a href="#" class="iva-mobile-dropdown"><span></span><span></span><span></span><span></span></a>
		</div>

	</div>

	<!-- /inner-->
  
	<div class="iva-mobile-menu">
	<ul id="menu-primary-menu" class="iva_mmenu">
<!--  <li id="mobile-menu-item-10337" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo DOMAINHOST;?>/djmixlist.php">DJ Mixes</a></li>
<li id="mobile-menu-item-10336" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="arena.php">Arena</a></li>
<li id="mobile-menu-item-10336" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="artists.php">Arists</a></li>
<li id="mobile-menu-item-10338" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="signup.php">Sign-up</a></li>


				<?php if(isset($_SESSION["loginSession_memberID"])) {?>
          			<li id="mobile-menu-item-10337" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="logout.php">Logout</a></li>
          		<?php } else {?>
            		<li id="mobile-menu-item-10337" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="login.php">Login</a></li>
           		<?php } ?> 

<!-- 
	<li id="mobile-menu-item-10318" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children"><a href="http://www.aivahthemes.com/musicplay/">Home<span class="iva-children-indenter"><i class="fa fa-angle-down"></i></span></a>
<ul class="sub-menu">
	<li id="mobile-menu-item-10346" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=113">Home Page 2</a></li>
</ul>
</li>
<li id="mobile-menu-item-10333" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=59">Albums<span class="iva-children-indenter"><i class="fa fa-angle-down"></i></span></a>
<ul class="sub-menu">
	<li id="mobile-menu-item-10356" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9911">Albums  3 columns</a></li>
	<li id="mobile-menu-item-10355" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9913">Albums  4 columns</a></li>
	<li id="mobile-menu-item-10354" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9914">Albums  5 columns</a></li>
	<li id="mobile-menu-item-10353" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9915">Albums  6 columns</a></li>
	<li id="mobile-menu-item-10358" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=10102">5 Cols Filterable</a></li>
	<li id="mobile-menu-item-10359" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=10099">4 Cols Filterable</a></li>
	<li id="mobile-menu-item-10360" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=10096">3 Cols Filterable</a></li>
	<li id="mobile-menu-item-10322" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=deep-house">Deep House</a></li>
	<li id="mobile-menu-item-10325" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=electro-house">Electro House</a></li>
	<li id="mobile-menu-item-10323" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=pop-rock">Pop / Rock</a></li>
	<li id="mobile-menu-item-10324" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=trance">Trance</a></li>
	<li id="mobile-menu-item-10326" class="menu-item menu-item-type-taxonomy menu-item-object-genres"><a href="<?php echo COREHOST;?>/cms/wp/?genres=techno">Techno</a></li>
</ul>
</li>
<li id="mobile-menu-item-10332" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=33">Artists<span class="iva-children-indenter"><i class="fa fa-angle-down"></i></span></a>
<ul class="sub-menu">
	<li id="mobile-menu-item-10352" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9919">Artists  3 column</a></li>
	<li id="mobile-menu-item-10351" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9921">Artists  4 column</a></li>
	<li id="mobile-menu-item-10350" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9922">Artists  5 columns</a></li>
	<li id="mobile-menu-item-10349" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9923">Artists  6 columns</a></li>
	<li id="mobile-menu-item-10319" class="menu-item menu-item-type-taxonomy menu-item-object-artist_cat"><a href="<?php echo COREHOST;?>/cms/wp/?artist_cat=rock-band">Rock Band</a></li>
	<li id="mobile-menu-item-10320" class="menu-item menu-item-type-taxonomy menu-item-object-artist_cat"><a href="<?php echo COREHOST;?>/cms/wp/?artist_cat=boom-band">Boom Band</a></li>
	<li id="mobile-menu-item-10321" class="menu-item menu-item-type-taxonomy menu-item-object-artist_cat"><a href="<?php echo COREHOST;?>/cms/wp/?artist_cat=rockstar-band">Rockstar Band</a></li>
</ul>
</li>
<li id="mobile-menu-item-10335" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=192">DJ Mixes<span class="iva-children-indenter"><i class="fa fa-angle-down"></i></span></a>
<ul class="sub-menu">
	<li id="mobile-menu-item-10348" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9906">DJ Mix Shortcode</a></li>
</ul>
</li>
<li id="mobile-menu-item-10339" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8831">Features<span class="iva-children-indenter"><i class="fa fa-angle-down"></i></span></a>
<ul class="sub-menu">
	<li id="mobile-menu-item-10345" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8907">Video Tutorials</a></li>
	<li id="mobile-menu-item-10336" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8328">Shortcodes</a></li>
	<li id="mobile-menu-item-10340" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8845">Theme Options</a></li>
	<li id="mobile-menu-item-10342" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8882">Artist Post Type</a></li>
	<li id="mobile-menu-item-10343" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8898">Album Post Type</a></li>
	<li id="mobile-menu-item-10344" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8902">Video Post Type</a></li>
	<li id="mobile-menu-item-10341" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8878">Home Page Teaser</a></li>
	<li id="mobile-menu-item-10347" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9681">FAQ</a></li>
</ul>
</li>
<li id="mobile-menu-item-10337" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8369">Photos</a></li>
<li id="mobile-menu-item-10338" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=8478">Videos</a></li>
<li id="mobile-menu-item-10334" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=131">Blog</a></li>
<li id="mobile-menu-item-10357" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?php echo COREHOST;?>/cms/wp/?page_id=9241">Events<span class="iva-children-indenter"><i class="fa fa-angle-down"></i></span></a>
<ul class="sub-menu">
	<li id="mobile-menu-item-10327" class="menu-item menu-item-type-taxonomy menu-item-object-events_cat"><a href="<?php echo COREHOST;?>/cms/wp/?events_cat=clubs">Clubs</a></li>
	<li id="mobile-menu-item-10328" class="menu-item menu-item-type-taxonomy menu-item-object-events_cat"><a href="<?php echo COREHOST;?>/cms/wp/?events_cat=festivals">Festivals</a></li>
	<li id="mobile-menu-item-10329" class="menu-item menu-item-type-taxonomy menu-item-object-events_cat"><a href="<?php echo COREHOST;?>/cms/wp/?events_cat=open-air">Open Air</a></li>
</ul>
</li>
-->
</ul></div>

</div><!-- /header -->
