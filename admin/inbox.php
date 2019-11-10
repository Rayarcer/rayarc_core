<?php 
require_once("../core/lib/php/cls_itemManager.php");
require_once("../core/lib/php/cls_domainManager.php");
require_once("../core/lib/php/cls_profileManager.php");
require_once("../core/lib/php/fns_time.php");

if(isset($_GET["domain"]))
	$domainName=$_GET["domain"];
else 
	$domainName="demo";

	
$_SESSION['loginSession_domain']=$domainName;
$obj_domain= new domain();
$obj_domain->getDomainByName($domainName);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Music Inbox | Rayarc Control Center</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script type='text/javascript' src='<?php echo COREHOST?>/cms/wp/wp-includes/js/jquery/jquery.js'></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
	<script type="text/javascript" src="lib/js/inbox.js"></script> 
</head>

<body>

    <div id="wrapper" class="gray-bg">
    	
    
        <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <!--
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="search_results.html">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                </div>
            </form>
            -->
        </div>
        
            <ul class="nav navbar-top-links navbar-right">
            <?php 
                $obj_newContentItems= new itemManager("STATUS","NEW");
            ?>    <li>
                    <span class="m-r-sm text-muted welcome-message">Welcome to Rayarc Control Center.</span>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i>  <span class="label label-success"><?php echo $obj_newContentItems->getCount();?></span>
                    </a>

                </li>
                <!--  
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>  <span class="label label-primary">8</span>
                    </a>
                    
                </li>
				-->

                <li>
                    <a href="login.html">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
            </ul>

        </nav>
        </div>
            <div class="row wrapper border-bottom white-bg page-heading" style="background-color:#0e9aef;color:#FFFFFF;padding:15px 15px;">
                <div class="col-lg-10" >
                    <h1 style="color:#FFFFFF;">Music Inbox</h1>
                    <ol class="breadcrumb" style="background-color:#0e9aef;color:#FFFFFF;">
                        <li>
                            <a href="index.html">Home</a>
                        </li>
                        <li class="active">
                            <strong>Music Inbox</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content">
            <div class="row animated fadeInRight">
                <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-content mailbox-content">
                        <div class="file-manager">
                            <h5>Status</h5>
                            <ul class="folder-list m-b-md" style="padding: 0">
                                <li><a href="javascript:update_inbox_activity('NEW','<?php echo $domainName;?>');"> <i class="fa fa-inbox "></i> New <span id="new-count" class="label label-success pull-right">0</span> </a></li>
                                <li><a href="javascript:update_inbox_activity('ACCEPTED','<?php echo $domainName;?>');"> <i class="fa fa-envelope-o"></i> Accepted <span id="accepted-count" class="label label-warning pull-right">0</span></a></li>
                                <li><a href="javascript:update_inbox_activity('PUBLISHED','<?php echo $domainName;?>');"> <i class="fa fa-certificate"></i> Published<span id="published-count" class="label label-primary pull-right">0</a></li>
                                <li><a href="javascript:update_inbox_activity('DECLINED','<?php echo $domainName;?>');"> <i class="fa fa-file-text-o"></i> Declined <span id="declined-count" class="label label-danger pull-right">0</span></a></li>
                                <li><a href="javascript:update_inbox_activity('ARCHIVED','<?php echo $domainName;?>');"> <i class="fa fa-trash-o"></i> Archived <span id="archived-count" class="label  pull-right">0</span></a></li>
                            </ul>
                            <h5>Categories</h5>
                            <ul class="category-list" style="padding: 0">
                                <li><a href="#"> <i class="fa fa-circle text-navy"></i> Top Viewed </a></li>
                                <li><a href="#"> <i class="fa fa-circle text-danger"></i> Top Liked</a></li>
                                <li><a href="#"> <i class="fa fa-circle text-primary"></i> Top Rated</a></li>
                            </ul>
							<!--
                            <h5 class="tag-title">Labels</h5>
                            <ul class="tag-list" style="padding: 0">
                                <li><a href=""><i class="fa fa-tag"></i> Family</a></li>
                                <li><a href=""><i class="fa fa-tag"></i> Work</a></li>
                                <li><a href=""><i class="fa fa-tag"></i> Home</a></li>
                                <li><a href=""><i class="fa fa-tag"></i> Children</a></li>
                                <li><a href=""><i class="fa fa-tag"></i> Holidays</a></li>
                                <li><a href=""><i class="fa fa-tag"></i> Music</a></li>
                                <li><a href=""><i class="fa fa-tag"></i> Photography</a></li>
                                <li><a href=""><i class="fa fa-tag"></i> Film</a></li>
                            </ul>
                           -->
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>    
                
                </div>
                <script>
                $(document).ready(function(){
	console.log("jQuery inbox.js ready");
	update_inbox_activity("NEW","<?php echo $domainName;?>");
	getItemCountByStatus("ACCEPTED","<?php echo $domainName;?>");
	getItemCountByStatus("PUBLISHED","<?php echo $domainName;?>");
	getItemCountByStatus("DECLINED","<?php echo $domainName;?>");
	getItemCountByStatus("ARCHIVED","<?php echo $domainName;?>");
	
});
                </script>
				<div class="col-md-8" id="inbox_activity">					               
					<?php 
					include("inbox_activity.php");
					?>
				</div>
 </div>




                                        

  <?php include ("_footer.php")?>        

       
        <!--
        <div class="footer">
            <div class="pull-right">
                10GB of <strong>250GB</strong> Free.
            </div>
            <div>
                <strong>Copyright</strong> Example Company &copy; 2014-2015
            </div>
        </div>
-->
        </div>
        </div>



    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>

    <!-- Peity -->
    <script src="js/demo/peity-demo.js"></script>

</body>

</html>
