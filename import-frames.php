<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php
//Data processing and storage functions
include  "functions-db.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska - Import Frames</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="./js/jquery.validate.js"></script>
<script type="text/javascript" src="./js/jquery.validate.mod.js"></script>
</head>
<body>
<?php include('invoice-mainnav.php'); ?>
<!-- FORM: BODY SECTION -->
<div class="container">
<div class="defaultWidth">
<h1>Alaska - Import Frames</h1>
<!-- FORM: BODY SECTION -->

<fieldset id="importForm" class="">
<legend>Import frames data</legend>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
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
	parseFramePricesCSV($csvFile);
}
?>
</fieldset>
</div>

</div><!--.container-->

<?php
// TEST AREA TEST AREA TEST AREA TEST AREA TEST AREA 
//print "<pre>".var_dump(dbSelectAllOrders())."</pre>";
?>
</body>
</html>