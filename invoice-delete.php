<?php include 'accesscontrol.php'; ?>
<?php
//Data processing and storage functions
include  "functions.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Delete Order</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
<link rel="stylesheet" href="./css/pepper-grinder/jquery-ui-1.8.11.custom.css" type="text/css" media="all" />
</head>
<body>
<?php 
$linkBack = "<a href=\"index.php\">Back to main</a>";
if (isset($_REQUEST["jobid"])) $jobid = $_REQUEST["jobid"];
dbDeleteOrderVCodes($jobid);
if (!dbDeleteOrder($jobid))
{
	print "<p>There was an error while deleting this order. ".$linkBack."</p>";
}
else {
	print "<p>The order was deleted (job id = $jobid). ".$linkBack."</p>";
}
?>
</body>
</html>