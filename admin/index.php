<?php
if (!isset($_SESSION))
	session_start();
//echo "SESSION=".session_id();
	//echo session_id();
	require_once("../_init_core.php");
	require_once("../core/lib/php/core.php");
	require_once("../core/lib/php/cls_memberManager.php");
	require_once("../core/lib/php/cls_domainManager.php");
	require_once("../core/lib/php/cls_clientManager.php");
	require_once("../_authenticate_adminsonly.php");
//ini_set('session.cookie_domain', 'rayarc.localhost');




//echo $obj_newContentItems->totalCount;



?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rayarc | Control Center</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
 <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>

  

<?php if(!isset($_SESSION['loginSession_domainID'])) {?>
    <script type="text/javascript">
    $(window).load(function(){
        $('#myModal6').modal('show');
    });
</script>
<?php } ?>

</head>

<body>

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
        <?php require_once("_header.php"); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>Rayarc Control Center</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index.php">Home</a>
                        </li>
                        
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content animated fadeInRight">

                        <div class="ibox-content m-b-sm border-bottom">
                            <div class="p-xs">
                                <div class="pull-left m-r-md">
                                    <i class="fa fa-globe text-navy mid-icon"></i>
                                </div>
                                <h2>Welcome Home</h2>
                                <span>Select your administration option</span>
                            </div>
                        </div>

                        <div class="ibox-content forum-container">
							<!--  
                            <div class="forum-title">
                                <div class="pull-right forum-desc">
                                    <samll>Total posts: 320,800</samll>
                                </div>
                                <h3>General subjects</h3>
                            </div>
							-->
							<div class="forum-item active">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-sitemap"></i>
                                        </div>
                                        <a href="<?php echo "http://".$obj_domain->domainName.".".SLDH;?>" target="_blank" class="forum-item-title">Go to my Arena (<?php echo $obj_domain->domainTitle;?>)</a>
                                        <div class="forum-sub-title">Your Arena is your rayarc website, you and other artists can upload music content and share with your fans </div>
                                    </div>
                                </div>
                            </div>
                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-thumbs-o-up"></i>
                                        </div>
                                        <a href="review.php" class="forum-item-title">Review New Content Feeds</a>
                                        <div class="forum-sub-title">Review new content uploaded or shared by contributing members at your site. Accept or decline new content for publishing.</div>
                                    </div>
                             
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                           <?php 
                                         
                                           echo $obj_pendingContentItems->getCount();
                                           ?>
                                        </span>
                                        <div>
                                            <small>Posts</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-bolt"></i>
                                        </div>
                                        <a href="publish.php" class="forum-item-title">Publish Your Accepted Content</a>
                                        <div class="forum-sub-title">Publish the accepted content to your music community</div>
                           			</div>
						
									<div class="col-md-1 forum-info">
                                 		<span class="views-number">
                                 		<?php 
                                     		
                                     		echo $obj_acceptedContentItems->getCount();
                                  		?>
                                  		</span>
                            	  	<div>
                                   	<small>Posts</small>
                                </div>
                           </div>
                            
                 	</div>
                 	
                </div>
                 <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                        </div>
                                        <a href="members.php" class="forum-item-title">Manage Your Members</a>
                                        <div class="forum-sub-title">Review and change permissions for members within your community</div>
                           			</div>
						<div class="col-md-1 forum-info">
                                 		<span class="views-number">
                             <?php 
                             require_once("../lib/php/cls_profileManager.php");

$obj_profileMgr= new profileManager("ALL",$obj_domain->id,null,1,false,false,true);
echo $obj_profileMgr->getCount();
?>
                                  		</span>
                            	  	<div>
                                   	<small>members</small>
									</div>
                                   	
                               
                           </div>
                            </div>
                 	</div>             
  <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                        </div>
                                        <a href="ecampaignsend.php" class="forum-item-title">Create Your Email Campaign</a>
                                        <div class="forum-sub-title">Create on your email campaigns to share with your community</div>
                           			</div>
						
									
                                   	
                               
                           </div>
                            
                 	</div>
                 	<div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </div>
                                        <a href="ecampaigninsight.php" class="forum-item-title"> Email Campaign Insight Reporting</a>
                                        <div class="forum-sub-title">Gain insight on the effectiveness of your email campaigns</div>
                           			</div>
						
									
                                   	
                               
                           </div>
                            
                 	</div>
                 	
                       
                
 
                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-tachometer"></i>
                                        </div>
                                        <a href="dashboard.php" class="forum-item-title">Your Site Dashboard</a>
                                      <div class="forum-sub-title">Monitor and Compare of your music website usage,growth statistics and overall site performance to your peers on the Rayarc music network.
                           		</div>
                           </div>
                       </div>
                            <!-- 
                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a href="forum_post.html" class="forum-item-title">Staff Discussion</a>
                                        <div class="forum-sub-title">This forum is for private, staff member only discussions, usually pertaining to the community itself. </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            1450
                                        </span>
                                        <div>
                                            <small>Views</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            652
                                        </span>
                                        <div>
                                            <small>Topics</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            572
                                        </span>
                                        <div>
                                            <small>Posts</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="forum-title">
                                <div class="pull-right forum-desc">
                                    <samll>Total posts: 17,800,600</samll>
                                </div>
                                <h3>Other subjects</h3>
                            </div>

                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <a href="forum_post.html" class="forum-item-title">Lorem Ipsum is simply dummy text. </a>
                                        <div class="forum-sub-title">Various versions have evolved over the years, sometimes by accident, sometimes on purpose passage of Lorem Ipsum (injected humour and the like).</div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            1516
                                        </span>
                                        <div>
                                            <small>Views</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            238
                                        </span>
                                        <div>
                                            <small>Topics</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            180
                                        </span>
                                        <div>
                                            <small>Posts</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-bomb"></i>
                                        </div>
                                        <a href="forum_post.html" class="forum-item-title">There are many variations of passages</a>
                                        <div class="forum-sub-title"> If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the . </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            1766
                                        </span>
                                        <div>
                                            <small>Views</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            321
                                        </span>
                                        <div>
                                            <small>Topics</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            42
                                        </span>
                                        <div>
                                            <small>Posts</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-bookmark"></i>
                                        </div>
                                        <a href="forum_post.html" class="forum-item-title">The standard chunk of Lorem Ipsum</a>
                                        <div class="forum-sub-title">Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            765
                                        </span>
                                        <div>
                                            <small>Views</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            90
                                        </span>
                                        <div>
                                            <small>Topics</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            11
                                        </span>
                                        <div>
                                            <small>Posts</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="forum-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="forum-icon">
                                            <i class="fa fa-ambulance"></i>
                                        </div>
                                        <a href="forum_post.html" class="forum-item-title">Lorem Ipsum, you need to be sure there</a>
                                        <div class="forum-sub-title">Internet tend to repeat predefined chunks as necessary, making this the</div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            2550
                                        </span>
                                        <div>
                                            <small>Views</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            122
                                        </span>
                                        <div>
                                            <small>Topics</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 forum-info">
                                        <span class="views-number">
                                            92
                                        </span>
                                        <div>
                                            <small>Posts</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
-->
                        </div>
                    </div>
                </div>
            </div>
           <?php require_once ("_footer.php")?>

        </div>
        </div>


    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/jquery-ui-1.10.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>


</body>

</html>
