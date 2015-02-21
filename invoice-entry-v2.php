<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php
//Data processing and storage functions
include  "functions.php";

//Form mode
if (isset($_REQUEST["mode"])) { $formMode = "edit"; } else { $formMode = "add"; }		//"edit" = fill values from database
if (isset($_REQUEST["jobid"])) { $jobid = $_REQUEST["jobid"]; } else { $jobid = ""; }	//"edit" mode requires job id
if (isset($_REQUEST["invoicenum"])) { $invoiceNum = $_REQUEST["invoicenum"]; } else { $invoiceNum = ""; }	//"edit" mode requires invoice number

//Default values
$lname = "";
$fname = "";
$recipientID = "";
$patientDOB = "";
$sex = "";
$amount = "";
$dateOrder = "";
$priorAuthNum = "";
$drchange = "";
//$isEligibile = "";
$odSph = "";
$odCyl = "";
$odPsm = "";
//$odAxis = "";
$odMulti = "";
//$odAdd1 = "";
$osSph = "";
$osCyl = "";
$osPsm = "";
//$osAxis = "";
$osMulti = "";
//$osAdd1 = "";
$frameSupplied = true;	//Checked by default
$frameName = "";	//Checked by default
$miscService = false;
$tint = false;
$slaboff = false;
$miscServiceCost = "";
$miscServiceDesc = "";
$miscServiceType = "";
$bal = 0;

if ($formMode == "edit")
{
	//User is searching by job id
	if ($jobid != "") {
		//Get order information from the database; assign resultset values to form
		$rs = dbSelectOrderByJobID($jobid);
	}
	//User is searching by invoice num
	if ($invoiceNum != "") {
		//Get order information from the database; assign resultset values to form
		$rs = dbSelectOrderByInvoice($invoiceNum);
	}
	$jobid = $rs[0]["job_id"];
	$lname = $rs[0]["patient_lname"];
	$fname = $rs[0]["patient_fname"];
	$recipientID = $rs[0]["recipient_id"];
	$patientDOB = $rs[0]["patient_dob"];
	$sex = $rs[0]["patient_gender"];
	$amount = $rs[0]["amount"];
	$dateOrder = $rs[0]["service_date"];
	$priorAuthNum = $rs[0]["prior_auth_num"];
	$drchange = $rs[0]["dr_change"];
	$odSph = $rs[0]["od_sph"];
	$odCyl = $rs[0]["od_cyl"];
	$odPsm = $rs[0]["od_psm"];
	$odMulti = $rs[0]["od_multi"];
	$osSph = $rs[0]["os_sph"];
	$osCyl = $rs[0]["os_cyl"];
	$osPsm = $rs[0]["os_psm"];
	$bal = $rs[0]["bal"];
	
	$osMulti = $rs[0]["os_multi"];
	$frameSupplied = $rs[0]["frame"];
	$frameName = $rs[0]["frame_id"];
	$tint = $rs[0]["tint"];
	$slaboff = $rs[0]["slab_off"];
	$miscService = $rs[0]["misc_service"];
	$miscServiceCost = $rs[0]["misc_service_cost"];
	$miscServiceDesc = $rs[0]["misc_service_desc"];
	$miscServiceType = $rs[0]["misc_service_type"];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Invoice Entry</title>
<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="./js/jquery-ui-1.8.11.custom.min.js"></script>
<script type="text/javascript" src="./js/jquery.maskedinput.js"></script>
<script type="text/javascript" src="./js/jquery.validate.js"></script>
<script type="text/javascript" src="./js/jquery.validate.mod.js"></script>
<script type="text/javascript" src="./js/invoice-entry-v2.js"></script>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
<link rel="stylesheet" href="./css/pepper-grinder/jquery-ui-1.8.11.custom.css" type="text/css" media="all" />
</head>
<body>
<div id="dialog-delete" title="Confirm order deletion">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure that you want to delete this order?</p>
</div>
<?php include('invoice-mainnav.php'); ?>
<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<form method="post" action="invoice-process.php" id="invoiceForm">
<input type="hidden" name="mode" id="mode" value="<?php echo $formMode ?>" />
<?php if($formMode =="add") { ?>
<h1>New Invoice Entry</h1>
<?php } else if ($formMode =="edit") { ?>
<h1>Edit Invoice Entry</h1>
<?php } ?>
<fieldset id="PatientInformati" class="" style="float:left; width:300px; height:415px;">
<legend>Patient Information</legend>
<div id="jobid-D" class="oneField">
<?php if ($formMode == "add") { ?>
	<label class="preField" for="jobid">Job ID<span class="req">*</span></label> <input type="text" id="jobid" name="jobid" value="<?php echo $jobid ?>" maxlength="6" class="required" />
<?php } else { ?>
	<input type="hidden" id="jobid" name="jobid" value="<?php echo $jobid ?>" />
<?php } ?>
</div>
<div id="lname-D" class="oneField">
<label class="preField" for="lname">Patient Last Name <span class="req">*</span></label> <input type="text" id="lname" name="lname" value="<?php echo $lname ?>" size="40" class="required" />
</div>
<div id="fname-D" class="oneField">
<label class="preField" for="fname">Patient First Name <span class="req">*</span></label> <input type="text" id="fname" name="fname" value="<?php echo $fname ?>" size="40" class="required" />
</div>
<div id="recipientID-D" class="oneField">
<label class="preField" for="recipientID">Recipient ID Number <span class="req">*</span></label> <input type="text" id="recipientID" name="recipientID" value="<?php echo $recipientID ?>" maxlength="10" class="required" /></div>
<div id="patientDOB-D" class="oneField">
<label class="preField" for="patientDOB">Date Of Birth <span class="minihelp">(YYYY/MM/DD)</span> <span class="req">*</span></label> <input type="text" id="patientDOB" name="patientDOB" value="<?php if ($patientDOB != "") { $date = new DateTime($patientDOB);echo $date->format('Ymd'); } ?>" size="10" class="required" />
</div>
<div id="Sex1-D" class="oneField">
<label class="preField" for="sex">Gender Code <span class="req">*</span></label> <input type="text" id="sex" name="sex" maxlength="4" class="required" value="<?php echo $sex ?>" />
</div>
<div id="dateOrder-D" class="oneField">
<label class="preField" for="dateOrder">Date Of Service <span class="minihelp">(YYYY/MM/DD)</span> <span class="req">*</span></label> <input type="text" id="dateOrder" name="dateOrder" value="<?php if ($dateOrder != "") { $date = new DateTime($dateOrder);echo $date->format('Ymd'); } ?>" size="10" class="required" />
</div>
<div id="priorAuthNum-D" class="oneField">
<label class="preField" for="priorAuthNum">Prior Authorization Number</label> <input type="text" id="priorAuthNum" name="priorAuthNum" value="<?php echo $priorAuthNum ?>"  maxlength="8" class="" /> <input type="checkbox" id="drchange" name="drchange" <?php if ($drchange) echo "checked=\"checked\""; ?> /><label for="drchange" class="postField">Doctor change?</label>
</div>
<div id="amount-D" class="oneField">
<?php if ($formMode == "add") { ?>
	<input type="hidden" id="amount" name="amount" value="<?php echo $amount ?>" />
<?php } else { ?>
	<label class="preField" for="amount">Amount</label> $<input type="text" id="amount" name="amount" value="<?php echo $amount ?>" />
<?php } ?>
</div>
</fieldset>
<fieldset id="RXInformation" class="" style="float:left; width:500px; height:415px;">
<legend>Order/RX Details</legend>
<table>
<tr class="headerRow">
<th> </th>
<th>Sphere </th>
<th>Cylinder </th>
<th>Prism</th>
<th>Lens Style </th>
<th>&nbsp;</th>
<!--<th>Axis </th>-->
</tr>
<tr id="right" class="alternate-0">
<th>Right </th>
<td><div id="Sphere1-D" class="oneField"> <input type="text" id="odsph" name="odsph" value="<?php echo $odSph ?>" size="5" class="required rxsphere" /> 
</div></td>
<td><div id="Cylinder1-D" class="oneField"> <input type="text" id="odcyl" name="odcyl" value="<?php echo $odCyl ?>" size="5" class="" /> 
</div></td>
<td><div id="Prism1-D" class="oneField"> <input type="text" id="odpsm" name="odpsm" value="<?php echo $odPsm ?>" size="5" class="" /> 
</div></td>
<td><div id="MultifocalType1-D" class="oneField">
<div class="ui-widget">
	<input id="odmulti" name="odmulti" value="<?php echo $odMulti ?>" class="required" />
</div>
</div></td>
<td><div id="Add1-D" class="oneField"> <input type="checkbox" id="odbal" name="odbal" value="1" <?php if ($bal == 1) echo "checked=\"checked\""; ?> /><label for="odbal" class="postField">BAL</label>
</div></td>
</tr>
<tr id="left" class="alternate-1">
<th>Left </th>
<td><div id="Sphere-D" class="oneField"> <input type="text" id="ossph" name="ossph" value="<?php echo $osSph ?>" size="5" class="required rxsphere"/> 
</div></td>
<td><div id="Cylinder-D" class="oneField"> <input type="text" id="oscyl" name="oscyl" value="<?php echo $osCyl ?>" size="5" class="" /> 
</div></td>
<td><div id="Prism-D" class="oneField"> <input type="text" id="ospsm" name="ospsm" value="<?php echo $osPsm ?>" size="5" class="" /> 
</div></td>
<td><div id="MultifocalType-D" class="oneField"> <div class="ui-widget">
	<input id="osmulti" name="osmulti" value="<?php echo $osMulti ?>" class="required" />
</div>
</div></td>
<td><div id="Add2-D" class="oneField"> <input type="checkbox" id="osbal" name="osbal" value="2" <?php if ($bal == 2) echo "checked=\"checked\""; ?> /><label for="osbal" class="postField">BAL</label></div></td>
</tr>
</table>
<div id="slaboff-D" class="oneField">
<span class="oneChoice"><input type="checkbox" value="true" id="tint" name="tint" <?php if ($tint) echo "checked=\"checked\""; ?>  /><label for="tint" class="postField">Tint?</label></span>
<span class="oneChoice"><input type="checkbox" value="true" id="slaboff" name="slaboff" <?php if ($slaboff) echo "checked=\"checked\""; ?> /><label for="slaboff" class="postField">Slab-off? (outsourced)</label></span>
</div>
<div id="miscService-D" class="oneField">
<span class="oneChoice"><input type="checkbox" value="true" id="miscService" name="miscService" <?php if ($miscService) echo "checked=\"checked\""; ?> /><label for="miscService" class="postField">Misc. vision service?</label></span>
<blockquote style="margin:0 0 0 25px;"><span class="oneChoice"><label for="miscServiceCost" class="postField">If yes, provide cost and description here: $</label><input type="text" id="miscServiceCost" name="miscServiceCost" value="<?php if ($miscServiceCost != 0) echo $miscServiceCost; ?>" size="8" class="required number" /><br /><label for="miscServiceDesc" class="postField">Description: </label><input type="text" id="miscServiceDesc" name="miscServiceDesc" value="<?php if ($miscServiceDesc != "") echo $miscServiceDesc; ?>" class="required" /><label for="miscServiceType" class="postField">&nbsp;Service type: </label><select id="miscServiceType" name="miscServiceType" class="required"><option value="FRAME" <?php if ($miscServiceType == "FRAME") { echo "selected=\"selected\""; } ?>>FRAME</option><option value="LENS" <?php if ($miscServiceType == "LENS" || $miscServiceType == "") { echo "selected=\"selected\""; } ?>>LENS</option><option value="MISC" <?php if ($miscServiceType == "MISC") { echo "selected=\"selected\""; } ?>>MISC</option></select></span></blockquote>
</div>
<div id="frameSupplied-D" class="oneField"><span class="oneChoice"><input type="checkbox" value="true" class="" id="frameSupplied" name="frameSupplied" <?php if ($frameSupplied) echo "checked=\"checked\""; ?> /><label for="frameSupplied" class="postField">Frame supplied by Rochester?</label></span>
</div>
<div id="frameName-D" class="oneField">
	<label for="frameName" class="postField">Frame name:</label>
	<select id="frameName" name="frameName" class="required"></select>
</div>
<!--<div id="frameName-D" class="offstate-a oneField">
<label class="preField" for="frameName">Frame name <span class="req">*</span></label> <input type="text" id="frameName" name="frameName" value="" size="15"  />
</div>-->
</fieldset>
<div style="clear:both"></div>
<div class="actions">
<input type="submit" class="primaryAction" id="submit" name="submitAction" value="Submit" />
<button type="button" class="secondaryAction" id="cancel" name="cancelAction" onclick="location.href='index.php'">Cancel</button>
</div>
</form></div>
</div>
</body>
</html>