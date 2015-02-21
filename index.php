<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php if ($curAuthLevel == 0) { header("Location: invoice-entry.php"); } ?>
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
<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="./js/jquery.validate.js"></script>
<script type="text/javascript" src="./js/jquery.validate.mod.js"></script>
<script type="text/javascript">
$(function() {
	//Form validation
    $("#searchByInvoice").validate({
		rules: {
			invoicenum: {
				required: true,
				minlength: 9
			}
		},
		messages: {
			invoicenum: {
				required: "Enter an invoice number."
			}
		}
		
	});
	
	setInterval(function() {
		$("#fldsetNewOrder").load(location.href+" #fldsetNewOrder>*","");
	}, 5000);
});

</script>
</head>
<body>
<?php include('invoice-mainnav.php'); ?>
<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<h1>Alaska - Main Menu</h1>
<!-- FORM: BODY SECTION -->


<fieldset id="fldsetNewOrder" class="">
<legend>Add a new order</legend>
<form method="post" action="invoice-entry.php">
<div id="submitOrder-D" class="oneField">
<input type="submit" class="primaryAction" id="submitNewOrder" name="submitNewOrder" value="Add a new order&hellip;" />
<?php 
//Print last 10 orders in the database
$numRecords = 10;
print "<p>Displaying last $numRecords record(s) that have not been submitted:</p>";
dbPrintOrdersTable($numRecords,true);//Print only unsubmitted 

//Alert if import is needed
if ($numBadRecords = incompleteRecordFound()) {
	print "<div class=\"status\">$numBadRecords incomplete record(s) were found. Please import sales data.</div><!--.status-->";
}
else if (count(dbSelectAllOrders()) == 0) {
	print "<div class=\"confirm\">All orders have been processed.</div><!--.status-->";
}
else {
	print "<div class=\"confirm\">All order records are complete and ready for printing!</div><!--.confirm-->";
}
?> 
</div>
</form><!--FORM: Add new order-->
<form method="post" id="searchByInvoice" action="invoice-entry.php">
<input type="hidden" name="mode" value="edit" />
<div id="searchByInvoice-D">
<label for="txtInvoiceNum">Search by Invoice Number</label>
<input type="text" id="invoicenum" name="invoicenum" maxlength="9" value="" class="required" />
<input type="submit" class="primaryAction" id="searchByInvoice" name="searchByInvoice" value="Search" />
</div>
</form><!--FORM: Search for order by invoice-->
</fieldset>


<fieldset id="importForm" class="">
<legend>Import sales data</legend>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
<p><strong>IMPORTANT: Before the .837 file can be generated, you need to import the customer, invoice, and job ID's.</strong></p>
<div id="csvFile-D" class="oneField">
<label class="preField" for="startDate">Upload file (.CSV)<span class="req">*</span></label> <input type="file" id="csvFile" name="csvFile" />
<input type="submit" class="primaryAction" id="submitCSV" name="submitCSV" value="Import File" />
</div>
</form>
<?php
//Check to see if file was posted
if(isset($_POST['submitCSV']) && ($_FILES['csvFile']['name'] != ""))
{
	$csvFile = $_FILES['csvFile']['name'];
	parseSalesCSV($csvFile);
}
?>
</fieldset>
</div>


<fieldset id="printOrders" class="">
<legend>Generate Invoice</legend>
<form method="post" <?php if (count(dbSelectAllOrders()) > 0) { ?>
	action="invoice-download.php">
<?php } else { ?>
	action="javascript:alert('There is nothing to download. All orders have been processed.');">
<?php } ?>
<div class="actions" style="clear:both;">
<input type="submit" class="primaryAction" id="submit" name="submitAction" value="Download .837" />
</div>
</form>
</fieldset>

</div><!--.container-->

<?php
// TEST AREA TEST AREA TEST AREA TEST AREA TEST AREA 
//print "<pre>".var_dump(dbSelectAllOrders())."</pre>";
?>
</body>
</html>