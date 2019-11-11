<?php   
   require_once("../core/lib/php/cls_domainManager.php");
   $obj_domain = new domain(getDomainIDByDomainName(getDomainName()));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error 404 (Not Found)</title>
</head>

<body>
	<div><a href="<?php echo DOMAINHOST?>/artists" title="<?php echo $obj_domain->domainTitle;?>">
					<img width="" height="" src="<?php echo DOMAINHOST?>/images/logos/domain_logo_flat.png" alt="<?php echo $obj_domain->domainTitle;?> &#8211; wp" />
				</a></div>
                
<b>404.</b> <ins>That’s an error.</ins>
  <p>The requested URL <code><?php echo $_SERVER['QUERY_STRING'];?></code> was not found on this server.  <ins>That’s all we know.</ins>
</body>
</html>