<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php
//Data processing and storage functions
include  "functions.php";

//Define dates
$today = new DateTime("Now");
$strDate = isset($_REQUEST['date']) ? $_REQUEST['date'] : $today->format('Y/m/d');
$reportType = isset($_REQUEST['reportType']) ? $_REQUEST['reportType'] : "month";

setlocale(LC_MONETARY, 'en_US'); //Set currency locale
$date = new DateTime($strDate);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska Medicaid Report</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
</head>
<body>
<?php include('invoice-mainnav.php'); ?>
<div class="container">
<form id="formMedicaidReport" name="formMedicaidReport" method="POST" action="">
<label for="date">Date and range</label>
<input type="text" id="date" name="date" size="8" value="<?php echo $strDate; ?>" />
<select id="reportType" name="reportType">
<?php
	$options = array("Month","Quarter","Year");
	foreach ($options as $option) {
		$strSelect = (strtolower($option) == $reportType) ? "selected=\"selected\"" : "";
		print "STR SELECT".$strSelect;
		print "<option value=\"".strtolower($option)."\" $strSelect>$option</option>";
	}
?>
</select><button type="submit" id="go">Go</button>
<div><a href="invoice-akmedicaidreport-pdf.php?date=<?php echo $strDate; ?>&reportType=<?php echo $reportType; ?>" target="_blank">View as PDF</a></div>
</form>
</div><!--container-->
<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<div class="invoiceheader">
<h1>Alaska Medicaid Report</h1>
<h2>Vision Associates of Rochester</h2>
</div><!--.invoiceheader-->
<!-- FORM: BODY SECTION -->

<?php 
switch($reportType)
{
	case "month":
		dbPrintMedicaidReportVCodesMonth($date);// FIX THIS
		dbPrintMedicaidReportFramesMonth($date);// FIX THIS
		break;
	case "quarter":
		dbPrintMedicaidReportVCodesQtr($date);// FIX THIS
		dbPrintMedicaidReportFramesQtr($date);// FIX THIS
		break;
	case "year":
		dbPrintMedicaidReportVCodesYear($date);// FIX THIS
		dbPrintMedicaidReportFramesYear($date);// FIX THIS
		break;
}
//dbPrintMedicaidReportFrames('2011-05-05');//Print frames billing chart 


?>
</div><!--.container-->
</body>
</html>