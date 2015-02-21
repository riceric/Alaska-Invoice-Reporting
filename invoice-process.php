<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php
//Data processing and storage functions
include  "functions.php";

//Variables
$formMode = $_REQUEST["mode"]; //must be edit or add
$jobid = $_REQUEST["jobid"];
$lname = $_REQUEST["lname"];
$fname = $_REQUEST["fname"];
$recipientID = $_REQUEST["recipientID"];
$patientDOB = $_REQUEST["patientDOB"];
$sex = $_REQUEST["sex"];
$amount = $_REQUEST["amount"];
$dateOrder = $_REQUEST["dateOrder"];
$priorAuthNum = isset($_REQUEST["priorAuthNum"]) ? $_REQUEST["priorAuthNum"] : null;
if (isset($_REQUEST["drchange"])) { $drchange = true; } else { $drchange = false; }		//Make sure that frame has default value
//$isEligibile = $_REQUEST["isEligibile"];
$odSph = $_REQUEST["odsph"];
$odCyl = $_REQUEST["odcyl"];
$odPsm = $_REQUEST["odpsm"];
//$odAxis = $_REQUEST["odaxis"];
$odMulti = $_REQUEST["odmulti"];
//$odAdd1 = $_REQUEST["odadd1"];
$osSph = $_REQUEST["ossph"];
$osCyl = $_REQUEST["oscyl"];
$osPsm = $_REQUEST["ospsm"];
//$osAxis = $_REQUEST["osaxis"];
$osMulti = $_REQUEST["osmulti"];
//$osAdd1 = $_REQUEST["osadd1"];
$frameSupplied = $_REQUEST["frameSupplied"];
$frameName = $_REQUEST["frameName"];
if (isset($_REQUEST["frameSupplied"])) { $frameSupplied = true; } else { $frameSupplied = false; }		//Make sure that frame has default value
if (isset($_REQUEST["tint"])) { $tint = true; } else { $tint = false; }				//Make sure that tint has default value
if (isset($_REQUEST["slaboff"])) { $slaboff = true; } else { $slaboff = false; }	//Make sure that slaboff has default value
if (isset($_REQUEST["miscService"])) { $miscService = true; $miscServiceType = $_REQUEST["miscServiceType"]; $miscServiceCost = $_REQUEST["miscServiceCost"];  $miscServiceDesc = $_REQUEST["miscServiceDesc"]; } else { $miscService = false; $miscServiceType = ""; $miscServiceCost = 0.00; $miscServiceDesc = ""; }	//Make sure that miscService has default value
$bal = false;
if (isset($_REQUEST["odbal"])) { $bal = 1; }
if (isset($_REQUEST["osbal"])) { $bal = 2; }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Print Orders</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
<link rel="stylesheet" href="./css/pepper-grinder/jquery-ui-1.8.11.custom.css" type="text/css" media="all" />
<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
<script type="text/javascript">
$().ready(function() {
	$('#submitNewOrder').focus();
});
</script>
</head>
<body>
<?php include('invoice-mainnav.php'); ?>
<!-- FORM: BODY SECTION -->
<div class="container">

<?php
//print "------------------------------------------------------<br />";
$customer_num = 0000000000; 	/** TEST DATA **/
$comp_name = 'ACME, Inc.'; 		/** TEST DATA **/

$arrVcodes = getVCodeArray($odSph,$odCyl,$odPsm,$odMulti,$osSph,$osCyl,$osPsm,$osMulti,$bal,$frameSupplied,$tint,$slaboff,$miscService);
$diagnosisCode = getDiagnosisCode($arrVcodes);

if ($formMode == "edit")
{
	print "Updating record...<br />";
	dbUpdateOrder($jobid,$customer_num,$dateOrder,$amount,$recipientID,$fname,$lname,$patientDOB,$sex,$priorAuthNum,$drchange,$diagnosisCode,$odSph,$odCyl,$odPsm,$odMulti,$osSph,$osCyl,$osPsm,$osMulti,$bal,$frameSupplied,$frameName,$tint,$slaboff,$miscService,$miscServiceType,$miscServiceCost,$miscServiceDesc);
	dbDeleteOrderVCodes($jobid);//1.Delete all the existing vcodes for this job id
	dbInsertVCodes($jobid,$arrVcodes);//2.Recalculate and insert new vcodes
	if ($miscService) {	//Insert misc service information into database
		print "Update: INSERT MISC SERVICE<br />";
		dbInsertV2799($jobid,$miscServiceType,$miscServiceDesc,$miscServiceCost);
	}
}
else if ($formMode == "add") {
	print "Adding new record...<br />";
	$success = dbInsertOrder($jobid,$customer_num,$dateOrder,$recipientID,$fname,$lname,$patientDOB,$sex,$priorAuthNum,$drchange,$diagnosisCode,$odSph,$odCyl,$odPsm,$odMulti,$osSph,$osCyl,$osPsm,$osMulti,$bal,$frameSupplied,$frameName,$tint,$slaboff,$miscService,$miscServiceType,$miscServiceCost,$miscServiceDesc);
	if ($success > 0) dbInsertVCodes($jobid,$arrVcodes);
	if ($miscService) {	//Insert misc service information into database
		print "Add: INSERT MISC SERVICE<br />";
		dbInsertV2799($jobid,$miscServiceType,$miscServiceDesc,$miscServiceCost);
	}
}
if ($_REQUEST["submitAction"] == "Save for Later")
{
	dbUpdateOrderStatus($jobid, -1);
}
?>
<form method="post" action="invoice-entry.php">
<div id="submitOrder-D" class="oneField">
<button id="btnBack" name="btnBack" onclick="javascript: history.back();">&lt; Go back</button>
<button type="submit" class="primaryAction" id="submitNewOrder" name="submitNewOrder">Add another order&hellip;</button>
</div>
</form><!--FORM: Add new order-->

<?php
/** DEBUGGING OUTPUT **/
echo "<pre class=\"debug\">"; var_dump($_REQUEST); echo "</pre>\n";
print "<pre class=\"debug\">"; var_dump($arrVcodes); print "</pre>";
$timestamp = new DateTime('Now');
print "<pre class=\"debug\">"; var_dump($timestamp); "</pre>";
//print "<pre>".var_dump($diagnosisCode)."</pre>";
?>
</body>
</html>