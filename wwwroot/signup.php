<?php // signup.php
include("common.php");
include("functions-db.php");

if (!isset($_POST['registerUser'])):
    // Display the user signup form
    ?>
<!DOCTYPE html PUBLIC "-//W3C/DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>New User Registration </title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="./css/invoice.css" type="text/css" media="all" />
	<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="./js/jquery.validate.js"></script>
	<script type="text/javascript" src="./js/jquery.validate.mod.js"></script>
	<script type="text/javascript">
	$(function() {
		//Form validation
		$("#newUserForm").validate({
			rules: {
				newid: {
					required: true,
					remote: "validator.php"
				},
				newpass: {
					required: true
				},
				newpass2: {
					equalTo: "#newpass"
				},
				newname: {
					required: true
				},
				newemail: {
					required: true,
					email: true
				}
			},
			messages: {
				newid: {
					required: "Please enter a username.",
					remote: "This username is already taken."
				},
				newpass: {
					required: "Please enter a password."
				},
				newpass2: {
					equalTo: "Your passwords must match."
				}
			}
			
		});
	});

	</script>
</head>
<body>
<div class="container">
<h3>New User Registration Form</h3>
<form id="newUserForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div>
<label for="newid" class="preField">Username</label>
<input id="newid" name="newid" type="text" maxlength="100" size="25" />
</div>
<div>
<label for="newpass" class="preField">Desired password</label>
<input id="newpass" name="newpass" type="password" maxlength="100" size="25" />
</div>
<div>
<label for="newpass2" class="preField">Re-enter password for verification</label>
<input id="newpass2" name="newpass2" type="password" maxlength="100" size="25" />
</div>
<div>
<h5>Contact information</h5>
<label for="newname" class="preField">Full name</label>
<input id="newname" name="newname" type="text" maxlength="100" size="25" />
</div>
<div>
<label for="newemail" class="preField">Email address</label>
<input id="newemail" name="newemail" type="text" maxlength="100" size="25" />
</div>
<div>
<button type="submit" name="registerUser">Submit</button>
<button type="reset">Reset Form</button>
</div>

</form>
</div>
</body>
</html>

<?php
else:
    // Process signup submission
    
    /* Check for existing user with the new id
    $sql = "SELECT COUNT(*) FROM _account WHERE username = '$_POST[newid]'";
    $result = mysql_query($sql);
    if (!$result) {	
        error('A database error occurred in processing your '.
              'submission.\\nIf this error persists, please '.
              'contact eric.hui@rochesteroptical.com.');
    }
    if (mysql_result($result,0,0)>0) {
        error('A user already exists with your chosen username.\\n'.
              'Please try another.');
    }*/
	$salt = "68g@%#^5hg45".$_REQUEST["newid"]."as1(&))4df";
	$hash = $salt.$_REQUEST["newpass"].$salt;
	$md5pwd = MD5($hash);
    dbRegisterNewUser($_REQUEST["newid"],$md5pwd,$salt,$_REQUEST["newname"],$_REQUEST["newemail"]);
    
    // Email the new password to the person.
	$newpass = $_REQUEST["newpass"];
    $message = "Hello!

Your personal account for the AK Invoice Entry System
has been created! To log in, proceed to the
following address:

    http://rochesteroptical/Alaska/Invoices/index.php

Your personal login ID and password are as
follows:

    username: $_REQUEST[newid]
    password: $newpass

You aren't stuck with this password! Your can
change it at any time after you have logged in.

If you have any problems, feel free to contact me at
<eric.hui@rochesteroptical.com>.

-Your Name
 Rochester Optical
";

    mail($_REQUEST['newemail'],"Your Password for the AK Invoice Entry System",
         $message, "From:Eric Hui <eric.hui@rochesteroptical.com>");
         
?>
<!DOCTYPE html PUBLIC "-//W3C/DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Registration Complete </title>
<meta http-equiv="Content-Type"
content="text/html; charset=iso-8859-1" />
</head>
<body>
<p><strong>User registration successful!</strong></p>
<p>Your username and password have been emailed to
<strong><?=$_POST['newemail']?></strong>, the email address
you just provided in your registration form. To log in,
click <a href="index.php">here</a> to return to the login
page, and enter your new personal username and password.</p>
</body>
</html>
<?php
endif;
?>
