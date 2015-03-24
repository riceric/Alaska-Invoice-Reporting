<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php
//Data processing and storage functions
include  "functions.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Main Menu</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
</head>
<body>
<?php include('invoice-mainnav.php'); ?>

<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<h1>Alaska - Order History</h1>
<!-- FORM: BODY SECTION -->


<fieldset id="fldsetNewOrder" class="">
<legend>Order History</legend>
<form method="post" action="invoice-entry.php">
<div id="submitOrder-D" class="oneField">
<input type="submit" class="primaryAction" id="submitNewOrder" name="submitNewOrder" value="Add a new order&hellip;" />
<?php 
//Print last 10 orders in the database
$numRecords = 0;
print "<h3>Incomplete entries</h3>";
dbPrintOrdersTable($numRecords,true,-1);//Print only incomplete orders

print "<h3>All entries</h3>";
dbPrintOrdersTable($numRecords);//Print all remaining

//Alert if import is needed
if ($numBadRecords = incompleteRecordFound()) {
	print "<div class=\"status\">$numBadRecords incomplete record(s) were found. Please import sales data.</div><!--.status-->";
}
else {
	print "<div class=\"confirm\">All order records are complete and reading for printing!</div><!--.confirm-->";
}
?>
</div>
</form><!--FORM: Add new order-->
<form method="post" action="invoice-entry.php">
<input type="hidden" name="mode" value="edit" />
<div id="searchByInvoice-D">
<label for="txtInvoiceNum">Search by Invoice Number</label>
<input type="text" id="invoicenum" name="invoicenum" maxlength="9" value="" />
<input type="submit" class="primaryAction" id="searchByInvoice" name="searchByInvoice" value="Search" />
</div>
</form><!--FORM: Search for order by invoice-->
</fieldset>

</div><!--.container-->
</body>
</html>