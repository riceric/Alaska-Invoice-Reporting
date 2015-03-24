<?php
//Data processing and storage functions
include  "functions.php";
include  "functions-db.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Print by date</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
</head>
<body>
<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<h1>Alaska - Print by date</h1>
<!-- FORM: BODY SECTION -->
<fieldset id="printOrders" class="">
<legend>Print orders</legend>
<form method="post" action="invoice-837.php">
<p>Specify the date range for all the orders that you want to include in this report.</p>
<div id="startDate-D" class="oneField" style="width:120px;float:left;">
<label class="preField" for="startDate">Start date</label> <input type="text" id="startDate" name="startDate" value="" size="8" class="validate-custom /^[0-9]{8}$/ required" /> to 
</div>
<div id="endDate-D" class="oneField" style="width:120px;float:left;">
<label class="preField" for="endDate">End date</label> <input type="text" id="endDate" name="endDate" value="" size="8" class="validate-custom /^[0-9]{8}$/ required" />
</div>
<div class="actions" style="clear:both;">
<input type="submit" class="primaryAction" id="submitPrint" name="submitAction" value="Generate .837" />
</div>
</form>
</fieldset>

</div><!--.container-->
</body>
</html>