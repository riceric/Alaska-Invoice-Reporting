<?php include 'accesscontrol.php'; //Calls functions-db.php ?>
<?php

/** 
 * Return all frames that have an in_stock value of 1 
 */
function dbSelectAllFrames()
{
	$sql = "SELECT id, name FROM frame WHERE in_stock=1 ORDER BY name ASC";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}
$rows = dbSelectAllFrames();
echo json_encode($rows);

?>