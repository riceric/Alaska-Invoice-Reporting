<?php
include 'functions.php'; //Calls functions-db.php
include 'functions-db.php';

$result = "true";
if (isset($_REQUEST["newid"]))
{
	if(dbUserExists($_REQUEST["newid"])){ $result = "false"; } 
	else { $result = "true"; }
}
if (isset($_REQUEST["dateOrder"]))
{
	if(!validTimeSpan($_REQUEST["dateOrder"],0,3650,"days")){ $result = "false"; } 
	else { $result = "true"; }
}
if (isset($_REQUEST["patientDOB"]))
{
	if(!validTimeSpan($_REQUEST["patientDOB"],0,200,"years")){ $result = "false"; } 
	else { $result = "true"; } 
}
if (isset($_REQUEST["recipientID"]) && ($_REQUEST["recipientID"] != "") && isset($_REQUEST["dateOrder"]) && ($_REQUEST["dateOrder"] != "") && isset($_REQUEST["sex"]) && ($_REQUEST["sex"] != ""))
{
	if (isset($_REQUEST["mode"]) && ($_REQUEST["mode"] == "edit"))
	{
		if (!isEligibleForFrame($_REQUEST["recipientID"],$_REQUEST["dateOrder"],$_REQUEST["sex"],"edit")) { $result = "false"; }
		else { $result = "true"; }
	} 
	else {
		if (!isEligibleForFrame($_REQUEST["recipientID"],$_REQUEST["dateOrder"],$_REQUEST["sex"])) { $result = "false"; }
		else { $result = "true"; }	
	}
}
print $result;
?>