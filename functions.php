<?php
/**
 * Calculate validity of timespan, based on date (string) given and
 * desired range of time (eg.between 0 and 200 years)
 * strDate - should be in the format: 'YYYY/MM/DD'
 * strUnit - "days" or "years"
 */
function validTimeSpan($strDate,$minLength=0,$maxLength=200,$strUnit="years")
{
	//Validate date string
	if (preg_match("/[0-9][0-9][0-9][0-9]\/[0-9][0-9]\/[0-9][0-9]/", "$strDate"))
	{
		//Calculate difference between today's date and DOB
		$dateparts = explode("/",$strDate);
		$today = strtotime("Now");

		if (!checkdate($dateparts[1],$dateparts[2],$dateparts[0]))
		{
			return false;
		}
		else
		{
			switch($strUnit)
			{
				case "years":
					$curYear = new DateTime("Now");
					$curYear = $curYear->format('Y'); //print "curYear: $curYear";
					$timeSpan = $curYear - $dateparts[0]; 
					error_log("yearsOld: $timeSpan");
					break;
				case "days":
					$myDate = strtotime($strDate);
					$timeSpan = floor(($today-$myDate)/60/60/24);  //print "I am $daysOld days old";
					break;
			}
		}
		return (($timeSpan >= $minLength && $timeSpan < $maxLength )); //Return true if date is within range
	}
	else {
		return false;
	}
}
/**
 *Used by isElibibleForFrame
 *From http://snippets.dzone.com/posts/show/1310
 */
function getAge( $pDOB, $srvDate=0 ) {
    list($Y,$m,$d) = explode("-",$pDOB);
	if ($srvDate==0)
	{
		return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
	}
	else
	{
		list($sY,$sm,$sd) = explode("/",$srvDate);
		$age = $sm.$sd < $m.$d ? $sY-$Y-1 : $sY-$Y;
		error_log("Age at time of appointment ($sY-$sm-$sd) is $age");
		return $age;	
	}
}

/**
 * If < 21, then 2 frames per year, if >= 21, then 1 frame per year 
 * Parameters: $pid - unique patient id
 *             $agegroup - determine eligibility based on age
 */
function isEligibleForFrame($pid,$dateOrder,$agegroup,$mode="")
{
	$result = false;

	$curYear = new DateTime();
	$curYear = $curYear->format('Y');
	$sql = "SELECT patient_gender AS agegroup, patient_dob FROM orderinfo WHERE recipient_id='$pid' AND YEAR(service_date) = $curYear";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$pData = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	if ($mode == "edit")
	{
		$numFrames = count($pData)-1;//Disregard 1 frame if record is being edited
		//error_log("numFrames is $numFrames (pData.length = ".count($pData).") and mode is $mode",0);
	}
	else
	{
		$numFrames = count($pData);
	}
	//print "isEligibleForFrame($pid,$agegroup); Year = $curYear";
	
	if ($numFrames >= 2)//if 2 frames, then FALSE
	{
		return false;
		exit;
	}
	else if ($numFrames <= 0)//if 0 frames, then TRUE
	{
		return true;
		exit;
	}
	else if ($numFrames == 1)//if 1 frame, then
	{
		//track how many times the patient was 21 or over this year
		$apptCount = 0; //**REMOVE
		$count21 = 0;
		foreach($pData as $appt)
		{
			$apptCount++; //**REMOVE
			$age = getAge($appt["patient_dob"], $dateOrder);
			if ($age >= 21) {
				$count21++;
			}
		}
		if ($count21 <= 1)
		{
			return true;
			exit;
		}
	/*
		switch($agegroup)
		{
			case "KFM":
			case "KFP":
			case "KMM":
			case "KMP":
			case "T":
				return true; //Under 21
				break;
			case "ATFM":
			case "ATFP":
			case "ATMM":
			case "ATMP":
				//If previous frame was issued to patient "under 21", then patient is still eligible				
				return $firstFrameUnder21 = ((substr($pData[0]['agegroup'],0,1)=="T")||(substr($pData[0]['agegroup'],0,1)=="K"))? true : false;
				break;
		}
	*/
	}	
}
//isEligibleForFrame(date("Now"));

/** 
 * Return vcodes based on prescription
 * VCode Reference:
 * https://www.noridianmedicare.com/dme/coverage/docs/lcds/current_lcds/refractive_lenses.htm
 */
function getPrescriptionVCode($sph, $cyl, $multi){
	$vcode = "V";
	$sph = abs($sph);	//Absolute ranges for SPH and CYL
	$cyl = abs($cyl);

	switch($multi) {
		//if single vision  --> V21##
		case "SV - Single Vision":
			$vcode .= "21";
			break;
		//if bifocal vision  --> V22##
		case "ST28 - Straight Top 28":
		case "ST35 - Straight Top 35":
		case "Round Seg":
			$vcode .= "22";
			break;
		//if trifocal vision  --> V23##
		case "7X28 Trifocal":
		case "7X35 Trifocal":
		case "8X35 Trifocal":
			$vcode .= "23";
			break;
	}

	//if no cyl measurement
	if ($cyl == "" || $cyl == 0)
	{
		//if sphere >= 0 and sphere <=4.00
		if (($sph >= 0) && ($sph <= 4.00)) {
			$vcode .= "00";
		}
		else if (($sph >= 4.12) && ($sph <=7.00)) {
			$vcode .= "01";
		}
		else if (($sph >= 7.12) && ($sph <=20.00)) {
			$vcode .= "02";
		}
	}
	//if sph && cyl measurement
	else 
	{
		//if sphere >= 0 and sphere <=4.00
		if (($sph >= 0) && ($sph <=4.00)) 
		{
			if (($cyl >= 0.12) && ($cyl <= 2.00))
			{
				$vcode .= "03";
			}
			else if (($cyl >= 2.12) && ($cyl <= 4.00))
			{
				$vcode .= "04";
			}
			else if (($cyl >= 4.25) && ($cyl <= 6.00))
			{
				$vcode .= "05";
			}
			else if ($cyl > 6.00)
			{
				$vcode .= "06";
			}
		}
		//if sphere >= 4.25 and sphere <=7.00
		else if (($sph >= 4.25) && ($sph <=7.00)) 
		{
			if (($cyl >= 0.12) && ($cyl <= 2.00))
			{
				$vcode .= "07";
			}
			else if (($cyl >= 2.12) && ($cyl <= 4.00))
			{
				$vcode .= "08";
			}
			else if (($cyl >= 4.25) && ($cyl <= 6.00))
			{
				$vcode .= "09";
			}
			else if ($cyl > 6.00)
			{
				$vcode .= "10";
			}
		}
		//if sphere >= 7.25 and sphere <=12.00
		else if (($sph >= 7.25) && ($sph <=12.00)) 
		{
			if (($cyl >= 0.12) && ($cyl <= 2.00))
			{
				$vcode .= "11";
			}
			else if (($cyl >= 2.12) && ($cyl <= 4.00))
			{
				$vcode .= "12";
			}
			else if (($cyl >= 4.25) && ($cyl <= 6.00))
			{
				$vcode .= "13";
			}
		}
		//if sphere > 12.00
		else if ($sph > 12.00)
		{
			$vcode .= "14";
		}
	}
	error_log("SPH: $sph, CYL: $cyl, MULTI: $multi ==> $vcode");
	return $vcode;
}


/** 
 * Create array of VCodes for an individual order
 * - Takes prescription and frame data as parameters
 * - Requires access pricing info for each VCode (database?)
 *
 * result["vcode"=>"V2020","price"=>10.00]
 *
 */
function getVCodeArray($od_sph,$od_cyl,$od_psm,$od_multi,$os_sph,$os_cyl,$os_psm,$os_multi,$bal=0,$frame,$tint=false,$slaboff=false,$miscService=false){
	$vcode = "V";
	$result = array();
	
	//if frame not blank, then append V2020 to the array
	if ($frame) {
		$result[] = "V2020";
	}
	//get vCode for right & left eye
	$odrx = getPrescriptionVCode($od_sph,$od_cyl,$od_multi);
	$osrx = getPrescriptionVCode($os_sph,$os_cyl,$os_multi);
	
	//get vCode for left eye
	$result[] = $odrx;
	$result[] = $osrx;

	//tint = true, then append V2745
	if ($tint) {
		$result[] = "V2745";
	}
	//slab-off = true, then append V2710
	if ($slaboff) {
		$result[] = "V2710";
		$result[] = "V2710"; // Always charge for 2
	}
	//if prism measurement not blank, then append V2715 to the array
	if ($od_psm != "") {
		$result[] = "V2715";
	}
	if ($os_psm != "") {
		$result[] = "V2715";
	}
	//Misc Service = true, then append V2799
	//See function print837_order for misc_service_cost
	if ($miscService) {
		$result[] = "V2799";
	}
	//If lens BAL > 0, then append V2700
	if ($bal > 0) {
		$result[] = "V2700";
	}
	return $result;
}

/**
 * Returns diagnosis code for myopia or presbyopia, based on the vcodes type
 */
function getDiagnosisCode($arrVcodes)
{
	$result = "V720";
	$cur = 0;
	foreach ($arrVcodes as $key => $value)
	{
		$subcode = substr($value,1,2);
		if (((int)$subcode > 20) && ((int)$subcode < 27)) {
			$cur = $subcode;
		}
	}
	switch($cur) {
		case "21":
			$result = "3671"; //Myopia
			break;
		case "22":
		case "23":
			$result = "3674"; //Presbyopia
			break;
	}
	return $result;
}

/**
 * Convert demographic/frame code to patient gender
 */
function convertDMOToGender($dmoCode)
{
	switch($dmoCode)
	{
		case "ATFM":
		case "ATFP":
		case "KFM":
		case "KFP":
			return "F"; //Female
			break;
		case "ATMM":
		case "ATMP":
		case "KMM":
		case "KMP":
		case "T":
			return "M"; //Male
			break;
	}
}

/** 
 * Print footer of the .837 invoice file; Certain elements must match header
 */
function print837_footer($numTransactions) {
	$result = "";
	
	//Get billing report data from database; don't increment; pad with zeroes
	$row = dbSelectLastBillingReport();
	$isa13 = str_pad($row['ISA13'], 9, "0", STR_PAD_LEFT);
	$gs06 = str_pad($row['GS06'], 3, "0", STR_PAD_LEFT);
		
	//print "<pre>".var_dump($row)."</pre>";
	$result .=  "SE*".$numTransactions."*0001~\r\n";
	$result .=  "GE*1*".$gs06."~\r\n";
	$result .=  "IEA*1*".$isa13."~\r\n";
	
	return $result;
}

/** 
 * Print header of the .837 invoice file; Modified to output string
 * - Accepts timestamp as parameters
 */
function print837_header($timestamp) {
	$result = "";
	
	//Get billing report data from database; don't increment; pad with zeroes
	$row = dbSelectLastBillingReport();
	$isa13 = str_pad($row['ISA13'], 9, "0", STR_PAD_LEFT);
	$gs06 = str_pad($row['GS06'], 3, "0", STR_PAD_LEFT);
	$bht03 = str_pad($row['BHT03'], 3, "0", STR_PAD_LEFT);
		
	//print "<pre>".var_dump($row)."</pre>";
	$result .=  "ISA*00*          *00*          *ZZ*3142           *ZZ*AKMEDICAID FHSC*".$timestamp->format('ymd')."*".$timestamp->format('hi')."*U*00401*".$isa13."*0*P*:~\r\n";
	$result .=  "GS*HC*3142*AKMEDICAID FHSC*".$timestamp->format('Ymd')."*".$timestamp->format('hi')."*".$gs06."*X*004010X098A1~\r\n";
	$result .=  "ST*837*0001~\r\n";
	$result .=  "BHT*0019*00*".$bht03."*".$timestamp->format('Ymd')."*".$timestamp->format('hi')."*CH~\r\n";
	$result .=  "REF*87*004010X098A1~\r\n";
	$result .=  "NM1*41*2*VISION ASSOC OF ROCH*****46*0002503~\r\n";
	$result .=  "PER*IC*Coreen Henning*TE*5852540029~\r\n";
	$result .=  "NM1*40*2*AKMEDICAID FHSC*****46*AKMEDICAID FHSC~\r\n";
	$result .=  "HL*1**20*1~\r\n";
	$result .=  "PRV*BI*ZZ*332H00000X~\r\n";
	$result .=  "NM1*85*2*VISION ASSOC OF ROCH*****XX*1982734463~\r\n";
	$result .=  "N3*1260 LYELL AVENUE*1260 LYELL AVENUE~\r\n";
	$result .=  "N4*ROCHESTER*NY*14606~\r\n";
	$result .=  "REF*1D*OP161NY~\r\n";
	$result .=  "REF*EI*16-1419012~\r\n";
	
	return $result;
}

/** 
 * $result .=  single order for the .837 invoice file; Modified to output string
 */
function print837_order($hlNum,$jobid,$invoiceNum,$invoiceCost,$fname,$lname,$recipientID,$dob,$sex,$dateOrder,$priorAuthNum="",$diagnosisCode="V720",$arrVcodes,$miscServiceCost=0.00) 
{
	$result = "";
	$sex = convertDMOToGender($sex);
	$dob = new DateTime($dob);
	$dateOrder = new DateTime($dateOrder);
	$result .=  "HL*$hlNum*1*22*0~\r\n";
	$result .=  "SBR*P*18*******MC~\r\n";
	$result .=  "NM1*IL*1*".strtoupper($lname)."*".strtoupper($fname)."****MI*".$recipientID."~\r\n";
	$result .=  "N3*Address line 1*Address line 1~\r\n";
	$result .=  "N4*Fairbanks*AK*99701*US~\r\n";
	$result .=  "DMG*D8*".$dob->format('Ymd')."*".$sex."~\r\n";
	$result .=  "NM1*PR*2*Alaska Medicaid*****PI*AKMEDICAID FHSC~\r\n";
	$result .=  "CLM*$invoiceNum*$invoiceCost***81::1*Y*A*Y*Y*C~\r\n";
	if ($priorAuthNum != "")
	{
		$result .=  "REF*G1*".$priorAuthNum."~\r\n";
	}
	$result .=  "HI*BK:".$diagnosisCode."~\r\n";

	/* Display service lines with vCodes and unit counts */
	//$arrSV1 = array_count_values($arrVcodes);
	$i = 1; //line count
	foreach ($arrVcodes as $key)
	{
		//Check for variable misc. service cost
		if ($key['vcode'] == "V2799")
		{
			$key['total'] = $miscServiceCost;
		}
		$result .=  "LX*".$i."~\r\n";
		$result .=  "SV1*HC:".$key['vcode']."*".$key['total']."*UN*".$key['count']."***1~\r\n";
		$result .=  "DTP*472*D8*".$dateOrder->format('Ymd')."~\r\n";
		$i++;
	}
	return $result;
}

/**
 * Prints all records between a specific start and end date in .837 format
 **/
function print837_ordersInDateRange($startDate,$endDate) 
{
	$rows = dbSelectOrdersInDateRange($startDate->format('Ymd'), $endDate->format('Ymd'));
	$hlCounter = 2; //HL = order number in this invoice starts with 2
	
	foreach ($rows as $key=>$value)
	{
		$arrVcodes = dbGetVCodeArray($value["job_id"]);
		//$diagnosisCode = getDiagnosisCode($arrVcodes); //$value["diagnosis_code"]
		/*** DEBUG
		echo "<pre>print837_ordersInDateRange";
		var_dump($result);
		echo "</pre>";**/
		print837_order($hlCounter,$value["job_id"],$value["invoice_num"],$value["amount"],$value["patient_fname"],$value["patient_lname"],$value["recipient_id"],$value["patient_dob"],$value["patient_gender"],$value["service_date"],$value["prior_auth_num"],$value["diagnosis_code"],$arrVcodes,$value["misc_service_cost"]);
		
		$hlCounter++; //Increment HL for next order
	}
}

/**
 * Prints all records that have not been submitted
 **/
function print837_allUnsubmitted($billReportId) 
{
	$result = "";
	$rows = dbSelectAllOrders();
	$hlCounter = 2; //HL = order number in this invoice starts with 2
	
	foreach ($rows as $key=>$value)
	{
		$arrVcodes = dbGetVCodeArray($value["job_id"]);
		//$diagnosisCode = getDiagnosisCode($arrVcodes); //$value["diagnosis_code"]
		/*** DEBUG
		echo "<pre>print837_ordersInDateRange";
		var_dump($result);
		echo "</pre>";**/
		$result .= print837_order($hlCounter,$value["job_id"],$value["invoice_num"],$value["amount"],$value["patient_fname"],$value["patient_lname"],$value["recipient_id"],$value["patient_dob"],$value["patient_gender"],$value["service_date"],$value["prior_auth_num"],$value["diagnosis_code"],$arrVcodes,$value["misc_service_cost"]);
		
		$hlCounter++; //Increment HL for next order
	}
	
	//Mark all orders as submitted
	dbUpdateSubmittedOrders($billReportId);
	
	return $result;
}

/**
 * Parses sales CSV that contains customer number, customer name, invoice number, date, job id and total invoice amount
 */
function parseSalesCSV($file)
{
	$row = 1;
	$chk_ext = explode(".",$file);
	$filename = $_FILES['csvFile']['tmp_name'];
	$handle = fopen($filename, "r");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$row++;
		dbUpdateOrderFromCSV($data[1],$data[2],$data[3],$data[5],$data[6]);
		/** DEBUG 
		echo "<p>customer_num: ".$data[1] . "<br />\n";
		echo "invoice_num: ".$data[3] . "<br />\n";
		echo "job_id: ".$data[5] . "<br />\n";
		echo "total_invoice_cost: ".$data[6] . "<br /></p>\n";
		**/
	}

	fclose($handle);
	echo "<div class=\"confirm\">Successfully imported <strong>$row record(s)</strong> from <strong>$file</strong></div>";
}

/**
 * Checks to see if record is missing invoice information; if so, an import is needed
 */
function incompleteRecordFound() {
	$res = 0;
    $sql = "SELECT count(*) FROM orderinfo WHERE invoice_num IS NULL";
	
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$rs = $stmt->fetch(PDO::FETCH_NUM);
		$res = $rs[0];
		$stmt->closeCursor ();
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
    return $res;
}

/**
 * Checks to see if record exists in database
 */
function recordFound($val, $col, $table) {
	$res = 0;
    $sql = "SELECT * FROM $table WHERE $col = $val";
	
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> rowCount();
		$res = ($result > 0);
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
    return $res;
}

/** 
 * Return all orders that have an order_status of 0 and invoice_num <> NULL
 */
function dbSelectAllOrders()
{
	$sql = "SELECT * FROM orderinfo WHERE order_status=0 AND invoice_num IS NOT NULL";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> fetchAll();
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}

/** 
 * Mark orders as submitted
 */
function dbUpdateSubmittedOrders($billReportId)
{
	$sql = "UPDATE orderinfo SET order_status=:status,billingreport_id=:billreportid WHERE (order_status=0) AND (invoice_num IS NOT NULL)";
	$params = array(
				':status'=>1,
				':billreportid'=>$billReportId
			);
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute($params);
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo "<div class=\"status\">dbUpdateSubmittedOrders(): ".$e->getMessage()."</div><!--.status-->";
	}
}

/** 
 * Return range of orders, based on a start and end date provided by the user
 */
function dbSelectOrdersInDateRange($startDate, $endDate)
{
	$sql = "SELECT * FROM orderinfo WHERE service_date BETWEEN '$startDate' AND '$endDate' ";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> fetchAll();
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}

/** 
 * Get order by job id
 */
function dbSelectOrderByJobID($jobid)
{
	$sql = "SELECT * FROM orderinfo WHERE job_id = '$jobid' ";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> fetchAll();
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}

/** 
 * Get last billing record; use this information to insert next record in sequence
 */
function dbSelectLastBillingReport()
{
	$sql = "SELECT * FROM billingreport ORDER BY id DESC LIMIT 1";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> fetchAll();
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result[0];
}

/** 
 * Get time and batch number from billing record; use this information for audit report
 */
function dbGetBillingReportBatchNum($id)
{
	$sql = "SELECT BHT03,time FROM billingreport WHERE id=$id";
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
	return $result[0];
}


/**
 * Insert new billing record; increment based on last billing record values
 */
function dbInsertBillingReport($pfilename,$timestamp) {

	$row = dbSelectLastBillingReport();
	$id = $row['id'] + 1;
	$isa13 = $row['ISA13'] + 1;
	$gs06 = $row['GS06'] + 1;
	$bht03 = $row['BHT03'] + 1;
	$time = $timestamp->format("Y-m-d H:i:s");

	$sql = "INSERT INTO billingreport(ISA13,GS06,BHT03,pfile,time) VALUES (:ISA13,:GS06,:BHT03,:pfile,:time)";
	$sqlArray = array(':ISA13'=>$isa13,':GS06'=>$gs06,':BHT03'=>$bht03,':pfile'=>$pfilename,':time'=>$time);
	try {
		$stmt = Database :: prepare ( $sql ) ;
		/*** echo a message saying we have connected 
		echo 'Connected to database<br />';***/

		/*** INSERT data ***/
		$count = $stmt->execute($sqlArray);

		/*** echo the number of affected rows 
		echo $count;***/

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		if( $e->getCode() == 23000)
		{
			echo "<div class=\"status\">Duplicate entry: This billing record already exists.</div><!--.status-->";
		}
		else {
			echo "<div class=\"status\">dbInsertBillingReport(): ".$e->getMessage()."</div><!--.status-->";
		}
	}
	return $id;	//Return billing id for updating orderinfo
}

/** 
 * Select Vcode by vcode id; Return string
 */
function dbSelectIDByVCode($vcode)
{
	$sql = "SELECT id FROM vcode WHERE vcode = '$vcode' ";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> fetchAll();
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result[0]["id"];
}

/** 
 * Get order by invoice number
 */
function dbSelectOrderByInvoice($invoiceNum)
{
	$sql = "SELECT * FROM orderinfo WHERE invoice_num = '$invoiceNum' ";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> fetchAll();
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}


/**
 *Calculate Quarter Range
 *Return array containing start and end date
 */
function calcQuarterDates($date)
{
	$dateYear = $date->format('Y');
	$dateMonth = $date->format('m');
	
	switch($dateMonth)
	{
		case '01':
		case '02':
		case '03':
			return array($dateYear."-01-01",$dateYear."-03-31");
			break;
		case '04':
		case '05':
		case '06':
			return array($dateYear."-04-01",$dateYear."-06-30");
			break;
		case '07':
		case '08':
		case '09':
			return array($dateYear."-07-01",$dateYear."-09-30");
			break;
		case '10':
		case '11':
		case '12':
			return array($dateYear."-10-01",$dateYear."-12-31");
			break;
	}
}

/**
 *Print year-end medicaid report (vcodes) based on date given
 *Req: function calcQuarterDates()
 */
function dbPrintMedicaidReportVCodesYear($date)
{
	//Define date ranges
	$dateRange[0] = $date->format('Y')."-01-01";//Start of year
	$dateRange[1] = $date->format('Y')."-12-31";//End of year
	//Define SQL statements
	$sqlYTDFrame = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[1]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='FRAME' GROUP BY description ORDER BY vcode"; 
	$sqlYTDLens = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[1]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='LENS' GROUP BY description ORDER BY vcode"; 
	$sqlYTDMisc = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[1]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='MISC' GROUP BY description ORDER BY vcode"; 

	try {
		//Print HTML table
		print "<h3>Reporting Period: ".$dateRange[0]." to ".$dateRange[1]."</h3>";

		//Print Frames table
		$stmt = Database :: prepare ( $sqlYTDFrame );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		print "<h4>Bill Type: FRAME</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($ytdArray);$i++)
		{
			print "<tr>";
			print 	"<td>".$ytdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$ytdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">&nbsp;</td>";
			print 	"<td class=\"numeric\">&nbsp;</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";

		//Print Lens table
		$stmt = Database :: prepare ( $sqlYTDLens );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Bill Type: LENS</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($ytdArray);$i++)
		{
			print "<tr>";
			print 	"<td>".$ytdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$ytdArray[$i]['description']."</td>";
			print 	"<td>&nbsp;</td>";
			print 	"<td>&nbsp;</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";

		//Print Misc table
		$stmt = Database :: prepare ( $sqlYTDMisc );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Bill Type: MISC</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($ytdArray);$i++)
		{
			print "<tr>";
			print 	"<td>".$ytdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$ytdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">&nbsp;</td>";
			print 	"<td class=\"numeric\">&nbsp;</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";		
		$stmt->closeCursor ();	
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	
}
 
/**
 *Print quarterly medicaid report (frame types) based on date given
 *Req: function calcQuarterDates()
 */ 
function dbPrintMedicaidReportFramesQtr($date)
{
	//Define date ranges
	$dateRange[0] = $date->format('Y')."-01-01";//Start of year
	$dateRange = array_merge($dateRange, calcQuarterDates($date));//QTD
	//Define SQL statements
	$sqlQTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'QTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'QTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') GROUP BY patient_gender"; 
	$sqlQTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'QTD Qty', COUNT(frame_id)*frame.price AS 'QTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id AND (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name"; 
	$sqlYTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'YTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'YTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') GROUP BY patient_gender";
	$sqlYTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'YTD Qty', COUNT(frame_id)*frame.price AS 'YTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id AND (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name";

	try {
		//print "<pre>".var_dump($qtdArray)."</pre>"; 
		//print "<pre>".var_dump($ytdArray)."</pre>";
		//Print HTML table

		//Print Frame Type table
		$stmt = Database :: prepare ( $sqlQTDFrames );
		$stmt->execute();
		$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDFrames );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Total Number of Frames with Lenses</h4>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Frame Type</th><th class=\"colDesc\">Description</th><th class=\"numeric\">QTD Qty</th><th class=\"numeric\">QTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumQTDQty = 0;
		$sumQTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($qtdArray);$i++)
		{
			if ($qtdArray[$i]['Frame Type'] == "") { $rowClass="error"; } else { $rowClass=""; }
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".$qtdArray[$i]['Frame Type']."</td>";
			print 	"<td>".getFrameDescription($qtdArray[$i]['Frame Type'])."</td>";
			print 	"<td class=\"numeric\">".$qtdArray[$i]['QTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$qtdArray[$i]['QTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
			$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumQTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumQTDTotal,2)."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";	

		//Print Frame Name table
		$stmt = Database :: prepare ( $sqlQTDFrameNames );
		$stmt->execute();
		$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDFrameNames );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Total Number of Frames by Style</h4>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colFrame\">Frame Style</th><th class=\"numeric\">QTD Qty</th><th class=\"numeric\">QTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumQTDQty = 0;
		$sumQTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($qtdArray);$i++)
		{
			if ($qtdArray[$i]['Frame Style'] == "") { $rowClass="error"; } else { $rowClass=""; }
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".($qtdArray[$i]['Frame Style'])."</td>";
			print 	"<td class=\"numeric\">".$qtdArray[$i]['QTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$qtdArray[$i]['QTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
			$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumQTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumQTDTotal."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumYTDTotal."</td>";
		print "</tr>";
		
		print "</tbody></table>";			
		$stmt->closeCursor ();	
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}
 
/**
 *Print quarterly medicaid report (vcodes) based on date given
 *Req: function calcQuarterDates()
 */
function dbPrintMedicaidReportVCodesQtr($date)
{
	//Define date ranges
	$dateRange[0] = $date->format('Y')."-01-01";//Start of year
	$dateRange = array_merge($dateRange, calcQuarterDates($date));//QTD
	//Define SQL statements
	$sqlQTDFrame = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'QTD Qty', SUM(COALESCE(count,0))*cost AS 'QTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='FRAME' GROUP BY description ORDER BY vcode";
	$sqlQTDLens = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'QTD Qty', SUM(COALESCE(count,0))*cost AS 'QTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='LENS' GROUP BY description ORDER BY vcode";
	$sqlQTDMisc = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'QTD Qty', SUM(COALESCE(count,0))*cost AS 'QTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='MISC' GROUP BY description ORDER BY vcode";	
	
	$sqlYTDFrame = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='FRAME' GROUP BY description ORDER BY vcode";
	$sqlYTDLens = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='LENS' GROUP BY description ORDER BY vcode";
	$sqlYTDMisc = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='MISC' GROUP BY description ORDER BY vcode";
	try {
		//Print HTML table
		print "<h3>Reporting Period: ".$dateRange[1]." to ".$dateRange[2]."</h3>";

		//Print Frames table
		$stmt = Database :: prepare ( $sqlQTDFrame );
		$stmt->execute();
		$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDFrame );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		print "<h4>Bill Type: FRAME</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">QTD Qty</th><th class=\"numeric\">QTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumQTDQty = 0;
		$sumQTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($qtdArray);$i++)
		{
			print "<tr>";
			print 	"<td>".$qtdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$qtdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">".$qtdArray[$i]['QTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$qtdArray[$i]['QTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
			$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumQTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumQTDTotal,2)."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";

		//Print Lens table
		$stmt = Database :: prepare ( $sqlQTDLens );
		$stmt->execute();
		$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDLens );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Bill Type: LENS</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">QTD Qty</th><th class=\"numeric\">QTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumQTDQty = 0;
		$sumQTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($qtdArray);$i++)
		{
			print "<tr>";
			print 	"<td>".$qtdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$qtdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">".$qtdArray[$i]['QTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$qtdArray[$i]['QTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
			$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumQTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumQTDTotal,2)."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";

		//Print Misc table
		$stmt = Database :: prepare ( $sqlQTDMisc );
		$stmt->execute();
		$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDMisc );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Bill Type: MISC</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">QTD Qty</th><th class=\"numeric\">QTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumQTDQty = 0;
		$sumQTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($qtdArray);$i++)
		{
			print "<tr>";
			print 	"<td>".$qtdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$qtdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">".$qtdArray[$i]['QTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$qtdArray[$i]['QTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
			$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumQTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumQTDTotal,2)."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";		
		$stmt->closeCursor ();	
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	
}
 
/**
 *Print year-end medicaid report (frame types) based on date given
 */ 
function dbPrintMedicaidReportFramesYear($date)
{
	//Define date ranges
	$dateRange[0] = $date->format('Y')."-01-01";//Start of year
	$dateRange[1] = $date->format('Y')."-12-31";//End of year
	//Define SQL statements
	$sqlYTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'YTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'YTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[1]."') GROUP BY patient_gender";
	$sqlYTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'YTD Qty', COUNT(frame_id)*frame.price AS 'YTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id AND (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[1]."') OR (service_date IS NULL) GROUP BY frame.name";

	try {
		//Print HTML table

		//Print Frame Type table
		$stmt = Database :: prepare ( $sqlYTDFrames );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Total Number of Frames with Lenses</h4>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Frame Type</th><th class=\"colDesc\">Description</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($ytdArray);$i++)
		{
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".$ytdArray[$i]['Frame Type']."</td>";
			print 	"<td>".getFrameDescription($ytdArray[$i]['Frame Type'])."</td>";
			print 	"<td>&nbsp;</td>";
			print 	"<td>&nbsp;</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";	

		//Print Frame Name table
		$stmt = Database :: prepare ( $sqlYTDFrameNames );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Total Number of Frames by Style</h4>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colFrame\">Frame Style</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">&nbsp;</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($ytdArray);$i++)
		{
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".($ytdArray[$i]['Frame Style'])."</td>";
			print 	"<td>&nbsp;</td>";
			print 	"<td>&nbsp;</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".number_format($sumYTDTotal,2)."</td>";
		print "</tr>";
		
		print "</tbody></table>";			
		$stmt->closeCursor ();	
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}
 
/** 
 *Print monthly medicaid report (vcodes) based on date given
 */
function dbPrintMedicaidReportVCodesMonth($date)
{
	//Define date ranges
	$dateRange[0] = $date->format('Y')."-01-01";//Start of year
	$dateRange[1] = $date->format('Y-m')."-01";//MTD
	$dateRange[2] = $date->format('Y-m-t');//t = num days of month
	
	$sqlMTDFrame = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='FRAME' GROUP BY description ORDER BY vcode";
	$sqlMTDLens = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='LENS' GROUP BY description ORDER BY vcode";
	$sqlMTDMisc = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='MISC' GROUP BY description ORDER BY vcode";	

	$sqlYTDFrame = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='FRAME' GROUP BY description ORDER BY vcode";
	$sqlYTDLens = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='LENS' GROUP BY description ORDER BY vcode";
	$sqlYTDMisc = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='MISC' GROUP BY description ORDER BY vcode";
	
	try {
		//Print HTML table
		print "<h3>Reporting Period: ".$dateRange[1]." to ".$dateRange[2]."</h3>";

		//Print Frames table
		$stmt = Database :: prepare ( $sqlMTDFrame );
		$stmt->execute();
		$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDFrame );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		print "<h4>Bill Type: FRAME</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">MTD Qty</th><th class=\"numeric\">MTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumMTDQty = 0;
		$sumMTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($mtdArray);$i++)
		{
			if ($mtdArray[$i]['Bill Code'] == "") { $rowClass="error"; } else { $rowClass=""; }
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".$mtdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$mtdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">".$mtdArray[$i]['MTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$mtdArray[$i]['MTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
			$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumMTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumMTDTotal."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumYTDTotal."</td>";
		print "</tr>";
		
		print "</tbody></table>";

		//Print Lens table
		$stmt = Database :: prepare ( $sqlMTDLens );
		$stmt->execute();
		$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDLens );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Bill Type: LENS</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">MTD Qty</th><th class=\"numeric\">MTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumMTDQty = 0;
		$sumMTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($mtdArray);$i++)
		{
			if ($mtdArray[$i]['Bill Code'] == "") { $rowClass="error"; } else { $rowClass=""; }
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".$mtdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$mtdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">".$mtdArray[$i]['MTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$mtdArray[$i]['MTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
			$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumMTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumMTDTotal."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumYTDTotal."</td>";
		print "</tr>";
		
		print "</tbody></table>";

		//Print Misc table
		$stmt = Database :: prepare ( $sqlMTDMisc );
		$stmt->execute();
		$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDMisc );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Bill Type: MISC</h4>";
		print "<table cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Bill Code</th><th class=\"colDesc\">Description</th><th class=\"numeric\">MTD Qty</th><th class=\"numeric\">MTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumMTDQty = 0;
		$sumMTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($mtdArray);$i++)
		{
			if ($mtdArray[$i]['Bill Code'] == "") { $rowClass="error"; } else { $rowClass=""; }
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".$mtdArray[$i]['Bill Code']."</td>";
			print 	"<td>".$mtdArray[$i]['description']."</td>";
			print 	"<td class=\"numeric\">".$mtdArray[$i]['MTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$mtdArray[$i]['MTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
			$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumMTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumMTDTotal."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumYTDTotal."</td>";
		print "</tr>";
		
		print "</tbody></table>";		
		$stmt->closeCursor ();	
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}

}

/**
 *Print monthly medicaid report (frame types) based on date given
 */
function dbPrintMedicaidReportFramesMonth($date)
{
	//Define date ranges
	$dateRange[0] = $date->format('Y')."-01-01";//Start of year
	$dateRange[1] = $date->format('Y-m')."-01";//MTD
	$dateRange[2] = $date->format('Y-m-t');//t = num days of month
	//Define SQL statements

	$sqlMTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'MTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'MTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') GROUP BY patient_gender"; 
	$sqlMTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'MTD Qty', COUNT(frame_id)*frame.price AS 'MTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id AND (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name"; 
	$sqlYTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'YTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'YTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') GROUP BY patient_gender";
	$sqlYTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'YTD Qty', COUNT(frame_id)*frame.price AS 'YTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id AND (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name";
	
	error_log("MYD Frames SQL -> $sqlMTDFrameNames");
	error_log("YTD Frames SQL -> $sqlYTDFrameNames");
	try {
		//print "<pre>".var_dump($mtdArray)."</pre>"; 
		//print "<pre>".var_dump($ytdArray)."</pre>";

		//Print Frame Type table
		$stmt = Database :: prepare ( $sqlMTDFrames );
		$stmt->execute();
		$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDFrames );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Total Number of Frames with Lenses</h4>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colCode\">Frame Type</th><th class=\"colDesc\">Description</th><th class=\"numeric\">MTD Qty</th><th class=\"numeric\">MTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumMTDQty = 0;
		$sumMTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($mtdArray);$i++)
		{
			if ($mtdArray[$i]['Frame Type'] == "") { $rowClass="error"; } else { $rowClass=""; }
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".$mtdArray[$i]['Frame Type']."</td>";
			print 	"<td>".getFrameDescription($mtdArray[$i]['Frame Type'])."</td>";
			print 	"<td class=\"numeric\">".$mtdArray[$i]['MTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$mtdArray[$i]['MTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
			$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumMTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumMTDTotal."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumYTDTotal."</td>";
		print "</tr>";
		
		print "</tbody></table>";	

		//Print Frame Name table
		$stmt = Database :: prepare ( $sqlMTDFrameNames );
		$stmt->execute();
		$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = Database :: prepare ( $sqlYTDFrameNames );
		$stmt->execute();
		$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<h4>Total Number of Frames by Style</h4>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th class=\"colFrame\">Frame Style</th><th class=\"numeric\">MTD Qty</th><th class=\"numeric\">MTD Total</th><th class=\"numeric\">YTD Qty</th><th class=\"numeric\">YTD Total</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$sumMTDQty = 0;
		$sumMTDTotal = 0;
		$sumYTDQty = 0;
		$sumYTDTotal = 0;
		for ($i=0;$i < count($mtdArray);$i++)
		{
			if ($mtdArray[$i]['Frame Style'] == "") { $rowClass="error"; } else { $rowClass=""; }
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".($mtdArray[$i]['Frame Style'])."</td>";
			print 	"<td class=\"numeric\">".$mtdArray[$i]['MTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$mtdArray[$i]['MTD Total']."</td>";
			print 	"<td class=\"numeric\">".$ytdArray[$i]['YTD Qty']."</td>";
			print 	"<td class=\"numeric\">$".$ytdArray[$i]['YTD Total']."</td>";
			print "</tr>";
			
			//Update sums
			$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
			$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
			$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
			$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
		}
		print "<tr>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">".$sumMTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumMTDTotal."</td>";
		print 	"<td class=\"numeric total\">".$sumYTDQty."</td>";
		print 	"<td class=\"numeric total\">$".$sumYTDTotal."</td>";
		print "</tr>";
		
		print "</tbody></table>";			
		$stmt->closeCursor ();	
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}

/**
 * Translates frame code to description
 */
function getFrameDescription($code)
{
	switch($code)
	{
		case "T":
			return "TODDLER";
			break;
		case "KMP":
			return "KIDS MALE PLASTIC";
			break;
		case "KMM":
			return "KIDS MALE METAL";
			break;
		case "KFP":
			return "KIDS FEMALE PLASTIC";
			break;
		case "KFM":
			return "KIDS FEMALE METAL";
			break;
		case "ATMP":
			return "ADULT/TEEN MALE PLASTIC";
			break;
		case "ATMM":
			return "ADULT/TEEN MALE METAL";
			break;
		case "ATFP":
			return "ADULT/TEEN FEMALE PLASTIC";
			break;
		case "ATFM":
			return "ADULT/TEEN FEMALE PLASTIC";
			break;
	}
}

/**
 * Insert statement for new customer (eg. id of medical office)
 */
function dbInsertCustomer($customer_num, $comp_name) {

$sql = "INSERT INTO customer(customer_num, comp_name) VALUES (:customer_num, :comp_name)";
$sqlArray = array(':customer_num'=>$customer_num,
				  ':comp_name'=>$comp_name);
	try {
		$stmt = Database :: prepare ( $sql ) ;
		/*** echo a message saying we have connected 
		echo 'Connected to database<br />';***/

		/*** INSERT data ***/
		$count = $stmt->execute($sqlArray);

		/*** echo the number of affected rows 
		echo $count;***/

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
        if( $e->getCode() == 23000)
        {
            echo "<div class=\"status\">Duplicate entry: This customer is already in our database.</div><!--.status-->";
        }
		else {
			echo $e->getMessage();
		}
	}
}


/**
 * Print Medicaid Audit Report - First time jobs
 */
function dbPrintMedicaidAudit($billingReportID)
{
	$qAuditOrders = "SELECT customer.customer_num, customer.comp_name, job_id, invoice_num, patient_lname, patient_fname, recipient_id, prior_auth_num, frame, amount FROM customer, orderinfo WHERE customer.customer_num = orderinfo.customer_num AND billingreport_id=$billingReportID ORDER BY service_date DESC";
	try {
		$stmt = Database :: prepare ( $qAuditOrders );
		$stmt->execute();
		$numJobs = $stmt->rowCount();
		$tableArray = $stmt->fetchAll(PDO::FETCH_ASSOC);		
	
		//Print HTML table
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bill\" ><thead>";
		print "<tr><th>Customer Number</th><th>Customer Name</th><th>Job ID</th><th>Invoice Number</th><th>Patient Last Name</th><th>Patient First Name</th><th>Recipient ID</th><th>Authorization #</th><th>Amount</th></tr>";
		print "</thead>";
		print "<tbody>";
		
		$subTotal = 0;
		$totalFrames = 0;
		foreach ($tableArray as $row)
		{
			print "<tr>";
			print 	"<td>".$row['customer_num']."</td>";
			print 	"<td>".strtoupper($row['comp_name'])."</td>";
			print 	"<td>".$row['job_id']."</td>";
			print 	"<td>".$row['invoice_num']."</td>";
			print	"<td>".strtoupper($row['patient_lname'])."</td>";
			print 	"<td>".strtoupper($row['patient_fname'])."</td>";
			print 	"<td>".$row['recipient_id']."</td>";
			print 	"<td>".$row['prior_auth_num']."</td>";
			print 	"<td class=\"numeric\">$".$row['amount']."</td>";
			print "</tr>";
			$subTotal += $row['amount']; //Increment sub total
			$totalFrames += $row['frame']; //Increment sub total
		}
		print "<tr>";
		print 	"<td>Sub Total</td>";
		print 	"<td>Number of jobs: ".$numJobs."</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td>&nbsp;</td>";
		print 	"<td class=\"numeric total\">$".$subTotal."</td>";
		print "</tr>";
		print "</tbody></table>";

		//Get number of lenses
		$strQuery = "SELECT SUM(orderinfo_has_vcode.count) AS numLenses FROM orderinfo, orderinfo_has_vcode, vcode WHERE orderinfo.job_id=orderinfo_job_id AND vcode_id=vcode.id AND bill_type='LENS' AND billingreport_id=$billingReportID GROUP BY bill_type";
		$stmt = Database :: prepare ( $strQuery );
		$stmt->execute();
		$numLenses = $stmt->fetchAll();
		$numLenses = $numLenses[0]['numLenses'];
	}
	catch(PDOException $e)
	{
        echo "<div class=\"status\">dbPrintMedicaidAudit(): ".$e->getMessage()."</div><!--.status-->";
	}
	
	//Get number of frames with lenses
	try {
		$qFramesWLenses = "SELECT job_id, vcode.bill_type 
							FROM orderinfo, orderinfo_has_vcode, vcode 
							WHERE billingreport_id=$billingReportID AND orderinfo.job_id=orderinfo_job_id AND vcode_id=vcode.id AND bill_type= 'LENS' 
							GROUP BY job_id";
		$stmt = Database :: prepare ( $qFramesWLenses );
		$stmt->execute();
		$numFramesWLenses = $stmt->rowCount();
			
		print "<p>Total</p>";
		print "<p>Number of Jobs: ".$numJobs."</p>";
		print "<p>Number of Lenses: ".$numLenses."</p>";
		print "<p>Number of Frames with Lenses: ".$numFramesWLenses."</p>";
		print "<p>Number of Frames without Lenses: ".(int)($totalFrames - $numFramesWLenses)."</p>";
		$stmt->closeCursor ();
	}
	catch(PDOException $e)
	{
        echo "<div class=\"status\">int FramesWLenses: ".$e->getMessage()."</div><!--.status-->";
	}	
}

/**
 * Accepts a SQL SELECT statement and returns an array of results
 * 
 *
 */
function dbPrintOrdersTable($numRecords=0,$byOrderStatus=false,$orderStatus=0)
{
	if($byOrderStatus)
	{
		$strQuery = "SELECT service_date, job_id, invoice_num, amount, patient_fname, patient_lname FROM orderinfo WHERE order_status=$orderStatus ORDER BY service_date DESC";
	}
	else
	{
		$strQuery = "SELECT service_date, job_id, invoice_num, amount, patient_fname, patient_lname FROM orderinfo WHERE order_status!=-1 ORDER BY service_date DESC";
	}
	if ($numRecords != 0) {
		$strQuery .= " LIMIT 0,$numRecords";
	}
	$stmt = Database :: prepare ( $strQuery );
	$stmt->execute();
	//var_dump($stmt -> fetchAll( ));
	
	$tableArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (count($tableArray))
	{
		//Print HTML table
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"data\" ><thead>";
		print "<tr><th>Date of order</th><th>Job ID</th><th>Invoice No.</th><th>Amount</th><th>Patient</th><th>&nbsp;</th></tr>";
		print "</thead>";
		print "<tbody>";
		foreach ($tableArray as $row)
		{
			if ($row['invoice_num'] == "") { 
				$rowClass="error"; 
			} 
			else { 
				$rowClass=""; 
			}
			if ($orderStatus < 0) { 
				$rowClass.=" incomplete"; 
			} 
			print "<tr class=\"".$rowClass."\">";
			print 	"<td>".$row['service_date']."</td>";
			print 	"<td><a href=\"invoice-entry.php?mode=edit&amp;jobid=".$row['job_id']."\">".$row['job_id']."</a></td>";
			print 	"<td>".$row['invoice_num']."</td>";
			print 	"<td>$".$row['amount']."</td>";
			print 	"<td>".$row['patient_fname']." ".$row['patient_lname']."</td>";
			print 	"<td><a href=\"invoice-delete.php?jobid=".$row['job_id']."\">Delete</a></td>";
			print "</tr>";
		}
		print "</tbody></table>";
	}
	else
	{
		print "<p><em>No results found.</em></p>";
	}
	$stmt->closeCursor ();
}


/**
 * Insert additional information into order (Invoice number, invoice cost, )
 * This must be associated with a job id and customer number (eg. id of medical office)
 */
function dbUpdateOrderFromCSV($customer_num,$comp_name,$invoice_num,$jobid,$amount) 
{
	//Check to see if customer exists; if not, add to customer table
	if (!recordFound($customer_num, 'customer_num', 'customer'))
	{
		dbInsertCustomer($customer_num, $comp_name);
	}
	
	$sql = "UPDATE orderinfo SET customer_num=:customer_num, invoice_num=:invoice_num, amount=:amount WHERE job_id=:job_id";
	$sqlArray = array(':customer_num'=>$customer_num,
					  ':invoice_num'=>$invoice_num,
					  ':job_id'=>$jobid,
					  ':amount'=>$amount
					  );
	try {
		$stmt = Database :: prepare ( $sql ) ;
		/*** echo a message saying we have connected 
		echo 'Connected to database<br />';***/

		/*** INSERT data ***/
		$count = $stmt->execute($sqlArray);

		/*** echo the number of affected rows ***/
		//echo "<div class=\"confirm\">orderinfo: $count row(s) updated</div><!--.status-->";

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
        echo "<div class=\"status\">".$e->getMessage()."</div><!--.status-->";
	}
}

/**
 * Insert frame and price information; Called by parseFramePriceCSV
 */
function dbInsertFramePrice($name, $material, $price, $in_stock) {

$sql = "INSERT INTO frame(name, material, price, in_stock) VALUES (:name, :material, :price, :in_stock)";
$sqlArray = array(':name'=>$name,
					':material'=>$material,
					':price'=>$price,
				  ':in_stock'=>$in_stock);
	try {
		$stmt = Database :: prepare ( $sql ) ;
		/*** echo a message saying we have connected 
		echo 'Connected to database<br />';***/

		/*** INSERT data ***/
		$count = $stmt->execute($sqlArray);

		/*** echo the number of affected rows 
		echo $count;***/

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
        if( $e->getCode() == 23000)
        {
            echo "<div class=\"status\">Duplicate entry: This frame is already in the database.</div><!--.status-->";
        }
		else {
			echo "<div class=\"status\">dbInsertFramePrice: ".$e->getMessage()."</div><!--.status-->";
		}
	}
}

/**
 * Update data table that contains frame names and prices; Called by parseFramePriceCSV
 */
function dbUpdateFramePrice($framename,$price) 
{
	//Check to see if customer exists; if not, add to customer table
	//recordFound($val, $col, $table)
	if (!recordFound($framename, 'name', 'frame'))
	{
		dbInsertFramePrice($framename,$price);
	}
	else 
	{
		$sql = "UPDATE frame SET name=:name, price=:price WHERE frame.name=':name'";
		$sqlArray = array(':name'=>$framename,
						  ':price'=>$price
						  );
		try {
			$stmt = Database :: prepare ( $sql ) ;
			/*** echo a message saying we have connected 
			echo 'Connected to database<br />';***/

			/*** INSERT data ***/
			$count = $stmt->execute($sqlArray);

			/*** echo the number of affected rows ***/
			//echo "<div class=\"confirm\">orderinfo: $count row(s) updated</div><!--.status-->";

			/*** close the database connection ***/
			$stmt->closeCursor ( ) ;
		}
		catch(PDOException $e)
		{
			echo "<div class=\"status\">dbUpdateFramePrice: ".$e->getMessage()."</div><!--.status-->";
		}
	}
}


/**
 * Parses frame prices CSV that contains frame names and their prices
 */
function parseFramePricesCSV($file)
{
	$row = 1;
	$chk_ext = explode(".",$file);
	$filename = $_FILES['csvFile']['tmp_name'];
	$handle = fopen($filename, "r");
	
	/** DROP TABLE **/
	$sql = "DROP TABLE IF EXISTS `frame`;
			CREATE TABLE IF NOT EXISTS `frame` (
			  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
			  `name` varchar(40) NOT NULL,
			  `material` varchar(7) NOT NULL COMMENT 'PLASTIC or METAL',
			  `price` decimal(10,2) NOT NULL,
			  `in_stock` tinyint(1) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Stores frame price list' AUTO_INCREMENT=0;
			";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo "<div class=\"status\">parseFramePricesCSV: ".$e->getMessage()."</div><!--.status-->";
	}

		
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$row++;
		dbInsertFramePrice($data[0],$data[1],$data[2],$data[3]);
		/** DEBUG 
		echo "<p>price: $".$data[1] . "<br />\n";
		echo "frame: ".$data[0] . "</p>\n";
		**/
	}

	fclose($handle);
	echo "<div class=\"confirm\">Successfully imported <strong>$row record(s)</strong> from <strong>$file</strong></div>";
}

/**
 * Insert new order (patient info, prescription, etc.)
 * This must be associated with a job id and customer number (eg. id of medical office)
 */
function dbInsertOrder($jobid,$customer_num,$dateOrder,$recipientID,$fname,$lname,$patientDOB,$sex,$priorAuthNum,$drchange,$diagnosisCode,$odSph,$odCyl,$odPsm,$odMulti,$osSph,$osCyl,$osPsm,$osMulti,$bal=0,$frameSupplied,$frameName,$tint,$slaboff,$miscService,$miscServiceType,$miscServiceCost,$miscServiceDesc) 
{
$result = 0;
$priorAuthNum = ($priorAuthNum != '') ? $priorAuthNum : NULL; //Force NULL value
$sql = "INSERT INTO orderinfo(job_id, customer_num, service_date,amount,order_status,recipient_id, patient_fname, patient_lname, patient_dob,patient_gender,prior_auth_num,dr_change,diagnosis_code,od_sph,od_cyl,od_psm,od_multi,os_sph,os_cyl,os_psm,os_multi,bal,frame,frame_id,tint,slab_off,misc_service,misc_service_type,misc_service_cost,misc_service_desc) VALUES (:job_id,:customer_num, :service_date,:amount,:order_status,:recipient_id, :patient_fname,:patient_lname,:patient_dob,:patient_gender,:prior_auth_num,:dr_change,:diagnosis_code,:od_sph,:od_cyl,:od_psm,:od_multi,:os_sph,:os_cyl,:os_psm,:os_multi,:bal,:frame,:frame_id,:tint,:slab_off,:misc_service,:misc_service_type,:misc_service_cost,:misc_service_desc)";
$sqlArray = array(':job_id'=>$jobid,
				  ':customer_num'=>$customer_num,
				  ':service_date'=>$dateOrder,
				  ':amount'=>0.00,
				  ':order_status'=>0,
				  ':recipient_id'=>$recipientID,
				  ':patient_fname'=>$fname,
				  ':patient_lname'=>$lname,
				  ':patient_dob'=>$patientDOB,
				  ':patient_gender'=>$sex,
				  ':prior_auth_num'=>$priorAuthNum,
				  ':dr_change'=>$drchange,
				  ':diagnosis_code'=>$diagnosisCode,
				  ':od_sph'=>$odSph,
				  ':od_cyl'=>$odCyl,
				  ':od_psm'=>$odPsm,
				  ':od_multi'=>$odMulti,
				  ':os_sph'=>$osSph,
				  ':os_cyl'=>$osCyl,
				  ':os_psm'=>$osPsm,
				  ':os_multi'=>$osMulti,
				  ':bal'=>$bal,
				  ':frame'=>$frameSupplied,
				  ':frame_id'=>$frameName,
				  ':tint'=>$tint,
				  ':slab_off'=>$slaboff,
				  ':misc_service'=>$miscService,
				  ':misc_service_type'=>$miscServiceType,
				  ':misc_service_cost'=>$miscServiceCost,
				  ':misc_service_desc'=>$miscServiceDesc
				  );
	try {
		$stmt = Database :: prepare ( $sql ) ;
		/*** echo a message saying we have connected 
		echo 'Connected to database<br />';***/

		/*** INSERT data ***/
		$result = $stmt->execute($sqlArray);

		/*** echo the number of affected rows ***/
		echo "<div class=\"confirm\">orderinfo: $result record(s) added</div><!--.status-->";

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
        if( $e->getCode() == 23000)
        {
            echo "<div class=\"status\">function dbInsertOrder(): An order with this job id already exists in the database.</div><!--.status-->";
        }
		else {
			echo "<div class=\"status\">function dbInsertOrder(): ".$e->getMessage()."</div><!--.status-->";
		}
	}
	return $result;
}

/**
 * Insert customer V2799 - Miscellaneous Service
 */
function dbInsertV2799($jobid,$miscServiceType,$miscServiceDesc,$miscServiceCost)
{
	try {
		$params = array(
					':vcode'=>"V2799",
					':cost'=>$miscServiceCost,
					':description'=>$miscServiceDesc,
					':bill_type'=>$miscServiceType
				);
		//print "0 dbInsertV2799: $jobid,$miscServiceType,$miscServiceDesc,$miscServiceCost";
		//If vcode exists, get vcode id and insert it into the order
		$sql = "SELECT id FROM vcode WHERE (vcode='V2799' AND cost='$miscServiceCost' AND description = '$miscServiceDesc' AND bill_type='$miscServiceType')";
		$stmt = Database :: prepare ( $sql ) ;
		$stmt->execute($params);
		$count = $stmt->rowCount();
		//print "COUNT: $count<br />";
		if ($count > 0) { //Found existing vcode; update database
			$arrV2799 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$vcode_id = $arrV2799[0]['id'];
			print "dbInsertOrderInfoVCode($jobid,$vcode_id, 1)<br />";
			dbInsertOrderInfoVCodeID($jobid, $arrV2799[0]['id'], 1);
		}
		else {	//Insert new vcode 
			$sql = "INSERT INTO vcode(`vcode`,`cost`,`description`,`bill_type`) VALUES (:vcode,:cost,:description,:bill_type)";
			$stmt = Database :: prepare ( $sql ) ;
			/*** INSERT data ***/
			$stmt->execute($params);
			$vcodeid = Database :: lastInsertId();
			dbInsertOrderInfoVCodeID($jobid, $vcodeid, 1);
		}
		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
        if( $e->getCode() == 23000)
        {
            print "<div class=\"status\">function dbInsertV2799(): This V2799 already exists in the V-code database.</div><!--.status-->";
        }
		else {
			print "<div class=\"status\">function dbInsertV2799(): ".$e->getMessage()."</div><!--.status-->";
		}
	}
}

/**
 * Insert statement for orderinfo_has_vcode
 */
function dbInsertOrderInfoVCode($jobid, $vcode, $count) {
	$id = dbSelectIDByVCode($vcode);
	if ($id != "")
	{
		$sql = "INSERT INTO orderinfo_has_vcode(orderinfo_job_id, vcode_id, count) VALUES (:orderinfo_job_id, :vcode_id, :count)";
		$sqlArray = array(':orderinfo_job_id'=>$jobid,
					  ':vcode_id'=>$id,
					  ':count'=>$count);
		//print "jobid: $jobid - vcode_id: $id - count: $count";
		try {
			$stmt = Database :: prepare ( $sql ) ;
			/*** INSERT data ***/
			$count = $stmt->execute($sqlArray);

			/*** echo the number of affected rows ***/
			//print "<div class=\"status\">orderinfo_has_vcode: $count row(s) updated</div><!--.status-->";

			/*** close the database connection ***/
			$stmt->closeCursor ( ) ;
		}
		catch(PDOException $e)
		{
			if( $e->getCode() == 23000)
			{
				echo "<div class=\"status\">function dbInsertOrderInfoVCode($vcode): This V-code is already associated with this order.</div><!--.status-->";
			}
			else {
				echo "<div class=\"status\">function dbInsertOrderInfoVCode($vcode): ".$e->getMessage()."</div><!--.status-->";
			}
		}
	}
}

/**
 * Insert statement for orderinfo_has_vcode using vcode id
 */
function dbInsertOrderInfoVCodeID($jobid, $id, $count) {
	if ($id != "")
	{
		$sql = "INSERT INTO orderinfo_has_vcode(orderinfo_job_id, vcode_id, count) VALUES (:orderinfo_job_id, :vcode_id, :count)";
		$sqlArray = array(':orderinfo_job_id'=>$jobid,
					  ':vcode_id'=>$id,
					  ':count'=>$count);
		//print "jobid: $jobid - vcode_id: $id - count: $count";
		try {
			$stmt = Database :: prepare ( $sql ) ;
			/*** INSERT data ***/
			$count = $stmt->execute($sqlArray);

			/*** echo the number of affected rows ***/
			print "<div class=\"confirm\">orderinfo_has_vcode: $count row(s) updated</div><!--.status-->";

			/*** close the database connection ***/
			$stmt->closeCursor ( ) ;
		}
		catch(PDOException $e)
		{
			if( $e->getCode() == 23000)
			{
				print "<div class=\"status\">function dbInsertOrderInfoVCodeID($id): This V-code is already associated with this order.</div><!--.status-->";
			}
			else {
				print $e->getMessage();
			}
		}
	}
}

/**
 * Insert vcodes into database, associated with jobid
 */ 
function dbInsertVCodes($jobid,$arrVcodes) 
{
	//Delete all VCodes associated with this order and re-calculate?
	$arrSV1 = array_count_values($arrVcodes);
	$i = 1; //line count
	foreach ($arrSV1 as $vcode=>$count)
	{
		if ($vcode != 'V2799')
		{
			dbInsertOrderInfoVCode($jobid, $vcode, $count);
		}
		$i++;
	}
}


/** 
 * Select Vcodes from table "orderinfo_has_vcode"
 * Return array of vcodes, count and total cost (count * vcode cost)
 */
function dbGetVCodeArray($jobid)
{
	//$sql = "SELECT orderinfo_has_vcode.id, vcode.vcode, count FROM orderinfo_has_vcode WHERE orderinfo_job_id = :job_id ";
	$sql = "SELECT vcode_id, vcode, count, (vcode.cost * count) AS total
			FROM orderinfo_has_vcode, vcode
			WHERE vcode_id = vcode.id AND orderinfo_job_id = :job_id
			GROUP BY vcode_id";

	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->bindParam(':job_id', $jobid, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	/*** DEBUG
	echo "<pre>dbGetVCodeArray";
	var_dump($result);
	echo "</pre>";
	**/

	return $result;
}

/**
 * Delete vcodes associated with an order (by job id)
 */
function dbDeleteOrderVCodes($jobid) {
	$sql = "DELETE FROM orderinfo_has_vcode WHERE orderinfo_job_id=$jobid";

	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor ( ) ;
		echo "<div class=\"confirm\">function dbDeleteOrderVCodes($jobid): $result VCodes(s) deleted.</div>";
	}
	catch(PDOException $e)
	{
		echo "<div class=\"status\">function dbDeleteOrderVCodes($jobid):".$e->getMessage()."</div>";
		$result = 0;
	}
	/*** DEBUG
	echo "<pre>dbGetVCodeArray";
	var_dump($result);
	echo "</pre>";
	**/

	return $result;	
}

/**
 * Delete order (by job id); prerequisite: dbDeleteOrderVCodes($jobid) 
 */
function dbDeleteOrder($jobid) {
	$sql = "DELETE FROM orderinfo WHERE job_id=$jobid";

	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo "<div class=\"status\">function dbDeleteOrder($jobid):".$e->getMessage()."</div>";
		$result = 0;
	}
	/*** DEBUG
	echo "<pre>dbGetVCodeArray";
	var_dump($result);
	echo "</pre>";
	**/

	return $result;	
}

/**
 * Reset vcodes associated with an order (by job id); 
 * Call 1) dbDeleteOrderVCodes and 2) setVCodes
 */
function dbUpdateOrder($jobid,$customer_num,$dateOrder,$amount=0.00,$recipientID,$fname,$lname,$patientDOB,$sex,$priorAuthNum,$drchange,$diagnosisCode,$odSph,$odCyl,$odPsm,$odMulti,$osSph,$osCyl,$osPsm,$osMulti,$bal=0,$frameSupplied,$frameName,$tint,$slaboff,$miscService,$miscServiceType,$miscServiceCost,$miscServiceDesc) 
{
$priorAuthNum = ($priorAuthNum != '') ? $priorAuthNum : NULL; //Force NULL value
$sql = "UPDATE orderinfo 
		SET customer_num=:customer_num, 
			service_date=:service_date,
			amount=:amount,
			order_status=:order_status,
			recipient_id=:recipient_id, 
			patient_fname=:patient_fname, 
			patient_lname=:patient_lname,
			patient_dob=:patient_dob,
			patient_gender=:patient_gender,
			prior_auth_num=:prior_auth_num,
			dr_change=:dr_change,
			diagnosis_code=:diagnosis_code,
			od_sph=:od_sph,
			od_cyl=:od_cyl,
			od_psm=:od_psm,
			od_multi=:od_multi,
			os_sph=:os_sph,
			os_cyl=:os_cyl,
			os_psm=:os_psm,
			os_multi=:os_multi,
			bal=:bal,
			frame=:frame,
			frame_id=:frame_id,
			tint=:tint,
			slab_off=:slab_off,
			misc_service=:misc_service,
			misc_service_type=:misc_service_type,
			misc_service_cost=:misc_service_cost,
			misc_service_desc=:misc_service_desc
		WHERE job_id=:job_id";
$sqlArray = array(':job_id'=>$jobid,
				  ':customer_num'=>$customer_num,
				  ':service_date'=>$dateOrder,
				  ':amount'=>$amount,
				  ':order_status'=>0,
				  ':recipient_id'=>$recipientID,
				  ':patient_fname'=>$fname,
				  ':patient_lname'=>$lname,
				  ':patient_dob'=>$patientDOB,
				  ':patient_gender'=>$sex,
				  ':prior_auth_num'=>$priorAuthNum,
				  ':dr_change'=>$drchange,
				  ':diagnosis_code'=>$diagnosisCode,
				  ':od_sph'=>$odSph,
				  ':od_cyl'=>$odCyl,
				  ':od_psm'=>$odPsm,
				  ':od_multi'=>$odMulti,
				  ':os_sph'=>$osSph,
				  ':os_cyl'=>$osCyl,
				  ':os_psm'=>$osPsm,
				  ':os_multi'=>$osMulti,
				  ':bal'=>$bal,
				  ':frame'=>$frameSupplied,
				  ':frame_id'=>$frameName,
				  ':tint'=>$tint,
				  ':slab_off'=>$slaboff,
				  ':misc_service'=>$miscService,
				  ':misc_service_type'=>$miscServiceType,
				  ':misc_service_cost'=>$miscServiceCost,
				  ':misc_service_desc'=>$miscServiceDesc
				  );
	try {
		$stmt = Database :: prepare ( $sql ) ;

		/*** INSERT data ***/
		$count = $stmt->execute($sqlArray);

		/*** echo the number of affected rows ***/
		echo "<div class=\"confirm\">orderinfo: $count row(s) updated</div><!--.status-->";

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo "<div class=\"status\">".$e->getMessage()."</div><!--.status-->";
	}
}

/**
 * Used for saving draft of incomplete order
 */
function dbUpdateOrderStatus($jobid, $orderStatus)
{
	$sql = "UPDATE orderinfo 
		SET order_status=:order_status
		WHERE job_id=:job_id";
	$sqlArray = array('job_id'=>$jobid,':order_status'=>$orderStatus);
	try {
		$stmt = Database :: prepare ( $sql ) ;

		/*** INSERT data ***/
		$count = $stmt->execute($sqlArray);

		/*** echo the number of affected rows ***/
		echo "<div class=\"confirm\">dbUpdateOrderStatus: $count row(s) updated</div><!--.status-->";

		/*** close the database connection ***/
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo "<div class=\"status\">".$e->getMessage()."</div><!--.status-->";
	}
}

/**
 * Create .837 file and use timestamp as the name
 */
function write837toFile($filename, $timestamp, $billReportId)
{
	$myFile = "_pfiles/$filename";
	$fh = fopen($myFile, 'w') or die("Error: Cannot write to file. Please check write permissions on the file/directory.");
	$stringData = print837_header($timestamp);
	fwrite($fh, $stringData);
	
	$stringData = print837_allUnsubmitted($billReportId);
	fwrite($fh, $stringData);
	
	//Calculate how many transactions/lines have been written to the file
	$numTransactions = count(file($myFile)) - 1; 
	error_log("Lines in $filename: $numTransactions");
	$stringData = print837_footer($numTransactions);
	fwrite($fh, $stringData);
	
	fclose($fh);
}

/**
 * Checks to see if pfile already exists. If it does, increment the version number until an unused filename is found
 */
function pFileExists($filename,$dir) {
    $i=1; $newlabel=$filename; $ver = "";
	while (file_exists($dir.$newlabel))
	{
		$punt=strrpos($filename,".");
		$ver = (int) substr($newlabel,($punt-3));
		//print "int $ver";
		$ver++;
		$newlabel = substr($newlabel,0,16).str_pad($ver, 3, "0", STR_PAD_LEFT).".837";
	}
    return $newlabel;
} 
?>