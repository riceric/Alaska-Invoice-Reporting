<?php include 'accesscontrol.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Print Orders</title>
<link rel="stylesheet" href="invoice.css" type="text/css" media="all" />
</head>
<body>
<?php include('invoice-mainnav.php'); ?>
<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<form method="post" action="invoice-837.php">
<h1>Alaska - Print Orders</h1>
<fieldset id="PatientInformati" class="">
<legend>Select a date range</legend>
<div id="startDate-D" class="oneField">
<label class="preField" for="startDate">Start date (YYYYMMDD) <span class="req">*</span></label> <input type="text" id="startDate" name="startDate" value="" size="8" class="validate-custom /^[0-9]{8}$/ required" />
</div>
<div id="endDate-D" class="oneField">
<label class="preField" for="endDate">End date (YYYYMMDD) <span class="req">*</span></label> <input type="text" id="endDate" name="endDate" value="" size="8" class="validate-custom /^[0-9]{8}$/ required" />
</div>
</fieldset>
<div class="actions">
<input type="submit" class="primaryAction" id="submit" name="submitAction" value="Generate .837" />
</div>
</form></div>
</div>
</body>
</html>