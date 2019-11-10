<?php
if (!isset($_SESSION))
	session_start();
require_once("../lib/php/core.php");
require_once("../lib/php/cls_itemManager.php");
require_once("../lib/php/cls_domainManager.php"); 
require_once("../lib/php/cls_genreManager.php");

if(isset($_SESSION['loginSession_domainID']))
{
	$obj_domain= new domain($_SESSION['loginSession_domainID']);
}
else
{
	$obj_domain= new domain();
	$obj_domain->getDomainByName(getsubDomainName());
}
if($obj_domain->domainName=="music")
	$databaseName=0;
else
	$databaseName=$obj_domain->databaseName;

//echo "databaseName=".$databaseName;
//$obj_item= new item(null,$databaseName);

$getItemKey=false;

$title="Music Arena (private) | ".$obj_domain->domainTitle;

?>
<!DOCTYPE html>
<html>
<head>
<?php include("_header_core.php");?>

</head>


<body class="home page page-id-9196 page-template-default">

<?php include("_header_menu_core.php");?>
		<div id="ajaxwrap">	 
		<script>
		 loadscript("<?php echo MUSICCENTRALHOST?>/lib/js/login_private.js");
		 loadscript("<?php echo CENTRALHOST?>/lib/js/fns_formValidation.js");
</script>
<?php include("modal_login_private.php");?>
	<?php include("_header_musicbar.php");
	

if(empty($obj_item->domainId))
	$obj_item->domainId=$obj_domain->id;	

$obj_genres=array();

$domainHostTitle="Rayarc Music Cloud";
	include("_header_titlebread.php"); ?>

<div class="iva_page_bg"></div>	

<div id="main" >	
	<div id="primary" class="pagemid">
	<div class="inner">
		<div class="content-area">
        			
	<?php 
	$status="ACCEPTED";
	include("arena_listing.inc.php");?>
                 
			</div><!-- .content-area -->
			
				
	
    
    </div><!-- inner -->
	
    </div><!-- #primary.pagemid -->


		</div><!-- #Ajax wrap -->
			</div><!-- #main -->

		<?php include("_footer.php"); ?>

	</div><!-- #wrapper -->
</div><!-- #layout -->
<?php include("_trailer.php");?> 

<?php //include("content_static_trailer.php");?>

	<div id="back-top"><a href="#header"><span class="fadeInUp"></span></a></div>

	</body>
</html>