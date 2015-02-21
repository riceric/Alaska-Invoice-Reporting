<?php
include 'functions.php'; //Calls functions-db.php
include 'functions-db.php';

if (isset($_REQUEST["recipientID"]) && isset($_REQUEST["patient_gender"]))
{
	if (!isEligibleForFrame($_REQUEST["recipientID"],$_REQUEST["patient_gender"])) { print "false"; }
	else { print "true"; }
}
?>