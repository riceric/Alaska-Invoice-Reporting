<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php
//Data processing and storage functions
include  "functions.php";

$billingReportID = isset($_REQUEST['billreport']) ? $_REQUEST['billreport'] : 0;
$batchInfo = dbGetBillingReportBatchNum($billingReportID);
$dateStamp = new DateTime($batchInfo['time']);
$dateStamp = $dateStamp->format('m/d/Y');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Medicaid Audit Report</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
</head>
<body>
<?php include('invoice-mainnav.php'); ?>

<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<div class="invoiceheader">
<h1>Medicaid Audit Report</h1>
<h2>Vision Associates of Rochester</h2>
<p>Jobs submitted to Medicaid - First time jobs</p>
<p>Batch Number: <?php echo $batchInfo['BHT03']; ?> Date: <?php echo $dateStamp; ?></p>
</div><!--.invoiceheader-->
<!-- FORM: BODY SECTION -->

<?php 
dbPrintMedicaidAudit($billingReportID);

?>
</div><!--.container-->
</body>
</html>