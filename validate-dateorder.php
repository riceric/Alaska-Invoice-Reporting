<?php
include 'functions.php'; //Calls functions-db.php
include 'functions-db.php';

if (isset($_REQUEST["dateOrder"]))
{
	if(!validTimeSpan($_REQUEST["dateOrder"],0,3650,"days")){ print "false"; } 
	else { print "true"; }
}
?>