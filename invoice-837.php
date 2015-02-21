<?php include 'accesscontrol.php'; ?>
<?php
//Data processing and storage functions
include  "functions.php";
include  "functions-db.php";

if (isset($_REQUEST["startDate"]) && isset($_REQUEST["endDate"]))
{
	$startDate = new DateTime($_REQUEST["startDate"]);
	$endDate = new DateTime($_REQUEST["endDate"]);
}
else {
	echo "Oops! Please check to make sure you entered both dates correctly.";
}

echo print837_header();
echo print837_ordersInDateRange($startDate,$endDate);
echo print837_footer();
?>