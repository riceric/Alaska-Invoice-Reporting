<?php include 'accesscontrol.php'; ?>
<?php
//Data processing and storage functions
include  "functions.php";

$timestamp = new DateTime('Now');
$filename = "P".$timestamp->format('ymd')."03901000v001.837";
$filename = pFileExists($filename,"_pfiles/");
$billReportId = dbInsertBillingReport($filename,$timestamp);
write837toFile($filename,$timestamp,$billReportId);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Download invoice file</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
</head>
<body>
<?php include('invoice-mainnav.php'); ?>
<!-- FORM: BODY SECTION -->
<div class="container">
	<div style="width:90%;height:80px;" class="confirm">
	<p><strong>Download your file:</strong> <br /><em>(Right-click and choose "Save Link As&hellip;")</em></p>
	<p><a href="_pfiles/<?php echo $filename; ?>"><?php echo $filename; ?></a></p>
	</div><!--.status-->
	
	<div id="">
	<p><a href="invoice-medicaidaudit.php?billreport=<?php echo str_pad($billReportId,9,"0",STR_PAD_LEFT); ?>" target="_blank">Audit Report (HTML)</a>
	<p><a href="invoice-medicaidaudit-pdf.php?billreport=<?php echo str_pad($billReportId,9,"0",STR_PAD_LEFT); ?>" target="_blank">Audit Report (PDF)</a>
	</div>
</div><!--.container-->
</body>
</html>