<?php 
	require_once("../core/_init_core.php");
require_once("../core/lib/php/cls_itemManager.php");
require_once("../core/lib/php/cls_domainManager.php"); 
require_once("../core/lib/php/cls_genreManager.php");

$obj_domain= new domain();
$obj_domain->getDomainByName(getsubDomainName());
?>
<!DOCTYPE html>
<html>
<head>
<?php 
$title="Music Arena  &#8211;".$obj_domain->domainTitle;
	include("_header_core.php");
?>
</head>

<body class="home page-template-default page page-id-9196 page-child parent-pageid-113 jp-radio-top" 

>
<div class="iva_page_loader"></div>	
	<div class="layoutoption" id="stretched">
	<div class="bodyoverlay"></div><!-- .bodyoverlay -->
		<!-- 	<div id="trigger" class="tarrow"></div>
		<div id="sticky">Enter the content which will be displayed in sticky bar</div> --><!-- #sticky -->
	
	<div id="wrapper">
		<?php //include("_header_topbar_core.php");?>
<div class="clear"></div>
<?php include("_header_menu_core.php");?>
		<div id="ajaxwrap">
		<script>
		var fapOpened=false;
		//console.log("set default fapOpened="+fapOpened);
		
		</script>
		<div id="subheader" style="padding:20px 0;background-color:#6699ff; ">
			<div class="inner"><div class="subdesc">
				<h1 class="page-title" style="color:#FFFFFF;">Music Arena</h1>
			</div>
			<div class="breadcrumbs" style="color:#CCCCCC"><!-- Breadcrumb NavXT 6.0.2 -->
				<span property="itemListElement" typeof="ListItem">
					<a property="item" typeof="WebPage" title="Go to RMC home" href="index.php" style="color:#CCCCCC" class="home">
						<span property="name"><?php echo $obj_domain->domainTitle;?></span>
					</a>
					<meta property="position" content="1">
				</span> &gt; 
				<span property="itemListElement" typeof="ListItem">
					<a property="item" typeof="WebPage" title="Go to Share Your Music" href="#" style="color:#CCCCCC" class="taxonomy genres">
						<span property="name">Music Arena</span>
					</a>
					<meta property="position" content="2">
				</span>  	
			</div>
		</div>
	  </div>
	  <div class="iva_page_bg"></div>
<div id="main" class="fullwidth">
					<div id="primary" class="pagemid">
						<div class="inner">
							<div class="content-area">
							
							<?php include("arena_listing.inc.php");?>		
							</div><!-- .content-area -->
						</div><!-- inner -->
					</div><!-- #primary.pagemid -->

				</div><!-- #main --> 
			</div> <!-- #Ajax wrap -->


<div class="clear"></div>
</div></div></div>

<div class="iva_page_bg"></div>		
		<?php include("_footer_core.php");?>
	</div><!-- #wrapper -->
</div><!-- #layout -->
<?php include("_trailer_core.php");?>
	</body>
</html>