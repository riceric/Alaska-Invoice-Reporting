<?php
include 'functions.php'; //Calls functions-db.php
include 'functions-db.php';


if (isset($_REQUEST["patientDOB"]))
{
	if(!validTimeSpan($_REQUEST["patientDOB"],0,200,"years")){ print "false"; } 
	else { print "true"; } 
}
?>