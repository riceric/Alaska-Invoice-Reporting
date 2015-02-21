<?php 
function setNavActive($strPage)
{
	$cssClass = (strpos($_SERVER['PHP_SELF'], $strPage) !== FALSE) ? 'active' : '';
	print $cssClass;
}
?>

<div id="mainnav">
	<div id="logo">
	<a href="index.php"><img src="sig-RochesterOptical.png" width="200" height="60" border="0" /></a>
	</div><!--#logo-->
	<ul id="mainnav-ul">
	<?php if ($curAuthLevel > 0) { ?>
		<li class="<?= setNavActive('index.php'); ?>"><a href="index.php">Home</a></li>
	<?php } //end if ?>
		<li class="<?= setNavActive('invoice-entry.php'); ?>"><a href="invoice-entry.php">Add an Order</a></li>
		<li class="<?= setNavActive('invoice-allorders.php'); ?>"><a href="invoice-allorders.php">Order History</a></li>
	<?php if ($curAuthLevel > 0) { ?>
		<li class="<?= setNavActive('invoice-akmedicaidreport.php'); ?>"><a href="invoice-akmedicaidreport.php">View Report</a></li>
	<?php } //end if ?>
	</ul>
	<div id="logout">You're logged in as <strong><?php echo $unm; ?></strong> | <a href="logout.php">Sign out</a></div>
</div><!--#mainnav-->
