<?php // accesscontrol.php
error_reporting (E_ALL ^ E_NOTICE);

include_once 'common.php';
include_once 'functions-db.php';

session_start();


$unm = isset($_POST['unm']) ? $_POST['unm'] : $_SESSION['unm'];
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : $_SESSION['pwd'];

if(!isset($unm)) {
?>
<!DOCTYPE html PUBLIC "-//W3C/DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>Alaska Invoice Processing | Please login</title>
<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="./js/jquery.validate.js"></script>
<script type="text/javascript" src="./js/jquery.validate.mod.js"></script>
<script type="text/javascript">
$(function() {
	//Form validation
    $("#loginForm").validate({
		rules: {
			unm: {
				required: true
			}
		},
		messages: {
			unm: {
				required: "Please enter a username."
			},
			pwd: {
				required: "Please enter a password."
			}
		}
		
	});
});

</script>
</head>
<body>
<div class="container">
<h1>Alaska Invoice Processing</h1>
<p>Please login to access the Alaska invoice entry form. Contact <a href="mailto:eric.hui@rochesteroptical.com">eric.hui@rochesteroptical.com</a> if you need an account.</p>
<?php 
if (isset($_REQUEST['msg']))
{
	switch ($_REQUEST['msg']) {
		case "1":
			print "<div class=\"status\">Sorry, we did not recognize that username and/or password. Please try again.</div>";
			break;
	}
} 
?>
<p><form method="post" action="<?=$_SERVER['PHP_SELF']?>" id="loginForm">
<label for="unm" class="preField">Username:</label> <input type="text" name="unm" class="required" style="width:175px;" /><br />
<label for="pwd" class="preField">Password:</label> <input type="password" name="pwd" class="required" style="width:175px;"  /><br />
<input type="submit" value="Log in" />
</form></p>
</div>
</body>
</html>
<?php
  exit;
}
$_SESSION['unm'] = $unm;
$_SESSION['pwd'] = $pwd;

if (dbCheckAuth($unm,$pwd) == 0) {
  unset($_SESSION['unm']);
  unset($_SESSION['pwd']);
  header('Location: '.$_SERVER['PHP_SELF'].'?msg=1');
exit;
}
$curAuthLevel = dbCheckAuthLevel($unm);
?>
