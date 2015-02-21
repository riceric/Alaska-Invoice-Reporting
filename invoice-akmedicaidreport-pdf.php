<?php
include 'functions.php'; //Calls functions-db.php
include 'accesscontrol.php'; //Calls functions-db.php
require_once('C:\www\rochesteroptical\public_html\tcpdf\config\lang\eng.php');
require_once('C:\www\rochesteroptical\public_html\tcpdf\tcpdf.php');

// extend TCPF with custom functions
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
		global $batchInfo;
        // Logo
        $image_file = 'sig-RochesterOptical.jpg';
        $this->Image($image_file, 12, 5, 50, '', 'JPG', '', 'T', true, 300, 'L', false, false, 0, false, false, false);
		$this->Ln(); //Line break
		// Set font
        $this->SetFont('helvetica', 'B', 16);
        // Title
		$this->setCellMargins(0,7,0,0);
        $this->Cell(0, 0, 'Alaska Medicaid Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(); //Line break
        $this->SetFont('helvetica', 'B', 9);
		$this->Cell(0, 0, 'VISION ASSOCIATES OF ROCHESTER', 0, false, 'C', 0, '', 0, false, 'M', 'M');

		//Batch info: 2) Print out batch and date info
		$dateStamp = new DateTime($batchInfo['time']);
		$dateStamp = $dateStamp->format('m/d/Y');
		$this->Ln(); //Line break
		$html = "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date: ". $dateStamp ."<br /></p>";
		$this->SetFont('helvetica','',8);
		$this->writeHTML($html, false);		
    }

    // Page footer
    public function Footer() {
		//Timestamp
		$timestamp = new DateTime('Now', new DateTimeZone('America/New_York'));
        // Position at 15 mm from bottom
        $this->SetY(-10);
		$lineStyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->Line(0,201,500,201,$lineStyle);
        $this->SetFont('helvetica', '', 8);
		$this->Cell(131, 10, 'Medicaid Audit Report', 0, false, 'L', 0, '', 0, false, 'T', 'M');
        // Page number
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(20, 10, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', '', 8);
		$this->Cell(0, 10, 'Generated: '.$timestamp->format('m/d/y h:i A'), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
	
    // Colored table
    public function ColoredTable($header,$data,$totals) {
        // Colors, line width and bold font
        $this->SetFillColor(160, 160, 160);
        $this->SetTextColor(255);
        $this->SetDrawColor(180, 180, 180);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(45, 105, 30, 30, 30, 30);
        for($i = 0; $i < count($header); ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln(); //Line break
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        foreach($data as $col) {
            $this->Cell($w[0], 6, $col[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $col[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $col[2], 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, $col[3], 'LR', 0, 'R', $fill);
            $this->Cell($w[4], 6, $col[4], 'LR', 0, 'R', $fill);
            $this->Cell($w[5], 6, $col[5], 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill=!$fill;
        }

		$this->SetFillColor(210, 255, 230);
		for($i = 0; $i < count($totals); ++$i) {
            $this->Cell($w[$i], 6, $totals[$i], 'LR', 0, 'R', $fill);
        }
        $this->Ln(); //Line break
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(); //Line break

    }

	/** 
	 *Print monthly medicaid report (vcodes) based on date given
	 */
	function printPDFVCodesMonth($date)
	{
		//Define date ranges
		$dateRange[0] = $date->format('Y')."-01-01";//Start of year
		$dateRange[1] = $date->format('Y-m')."-01";//MTD
		$dateRange[2] = $date->format('Y-m-t');//t = num days of month
		
		$sqlMTDFrame = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='FRAME' GROUP BY vcode"; 
		$sqlMTDLens = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL) AND bill_type='LENS' GROUP BY vcode"; 
		$sqlMTDMisc = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL) AND bill_type='MISC' GROUP BY description ORDER BY vcode"; 

		$sqlYTDFrame = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL) AND bill_type='FRAME' GROUP BY vcode"; 
		$sqlYTDLens = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL) AND bill_type='LENS' GROUP BY vcode";
		$sqlYTDMisc = "SELECT vcode, description, SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) OR (count IS NULL) AND bill_type='MISC' GROUP BY description ORDER BY vcode";
		
		try {
			//Print HTML table
			//print "<h3>Reporting Period: ".$dateRange[1]." to ".$dateRange[2]."</h3>";
			$this->setY(23,false,false);
			$this->SetFont('helvetica', 'B', 9);
			$this->Cell(270, 0, 'Reporting Period: '.$dateRange[1].' to '.$dateRange[2].' (Month)', 1, $ln=0, 'L', 0, '', 0, false, 'T', 'T');

			$this->setY(30,false,false);
			$html = "<p>Bill Type: FRAME<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Bill Code', 'Description', 'MTD Qty', 'MTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();		
			$stmt = Database :: prepare ( $sqlMTDFrame );
			$stmt->execute();
			$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrame );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$sumMTDQty = 0;
			$sumMTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			
			for ($i=0;$i < count($mtdArray);$i++)
			{
				$data[] = array($mtdArray[$i]['Bill Code'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".number_format($mtdArray[$i]['MTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".number_format($sumMTDTotal,2),$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);
			
			//Print Lens table
			$html = "<p>Bill Type: LENS<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);	
		
			$header = array('Bill Code', 'Description', 'MTD Qty', 'MTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlMTDLens );
			$stmt->execute();
			$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDLens );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumMTDQty = 0;
			$sumMTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			
			for ($i=0;$i < count($mtdArray);$i++)
			{
				$data[] = array($mtdArray[$i]['Bill Code'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".number_format($mtdArray[$i]['MTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".number_format($sumMTDTotal,2),$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);

			//Print Misc table
			$html = "<p>Bill Type: MISC<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);	
		
			$header = array('Bill Code', 'Description', 'MTD Qty', 'MTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlMTDMisc );
			$stmt->execute();
			$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDMisc );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumMTDQty = 0;
			$sumMTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			
			for ($i=0;$i < count($mtdArray);$i++)
			{
				$data[] = array($mtdArray[$i]['Bill Code'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".number_format($mtdArray[$i]['MTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".number_format($sumMTDTotal,2),$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);	
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
	function printPDFFramesMonth($date)
	{
		//Define date ranges
		$dateRange[0] = $date->format('Y')."-01-01";//Start of year
		$dateRange[1] = $date->format('Y-m')."-01";//MTD
		$dateRange[2] = $date->format('Y-m-t');//t = num days of month
		//Define SQL statements

		$sqlMTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'MTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'MTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') GROUP BY patient_gender"; 
		$sqlMTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'MTD Qty', COUNT(frame_id)*frame.price AS 'MTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name"; 
		$sqlYTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'YTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'YTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') GROUP BY patient_gender";
		$sqlYTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'YTD Qty', COUNT(frame_id)*frame.price AS 'YTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name";
		try {
			//Print HTML table

			//Print Frame Type table
			$html = "<p>Total Number of Frames with Lenses<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Frame Type', 'Description', 'MTD Qty', 'MTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlMTDFrames );
			$stmt->execute();
			$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrames );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumMTDQty = 0;
			$sumMTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($mtdArray);$i++)
			{
				$data[] = array($mtdArray[$i]['Frame Type'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".number_format($mtdArray[$i]['MTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".number_format($sumMTDTotal,2),$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);
			

			//Print Frame Name table
			$html = "<p>Total Number of Frames by Style<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Frame Style', 'Description', 'MTD Qty', 'MTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlMTDFrameNames );
			$stmt->execute();
			$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrameNames );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$sumMTDQty = 0;
			$sumMTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($mtdArray);$i++)
			{
				$data[] = array($mtdArray[$i]['Frame Style'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".number_format($mtdArray[$i]['MTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".number_format($sumMTDTotal,2),$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);
		
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
	function printPDFVCodesQtr($date)
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
			//print "<h3>Reporting Period: ".$dateRange[1]." to ".$dateRange[2]."</h3>";

			$this->setY(23,false,false);
			$this->SetFont('helvetica', 'B', 9);
			$this->Cell(270, 0, 'Reporting Period: '.$dateRange[1].' to '.$dateRange[2].' (Quarter)', 1, $ln=0, 'L', 0, '', 0, false, 'T', 'T');

			$this->setY(30,false,false);
			$html = "<p>Bill Type: FRAME<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Bill Code', 'Description', 'QTD Qty', 'QTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();		
			$stmt = Database :: prepare ( $sqlQTDFrame );
			$stmt->execute();
			$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrame );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$sumMTDQty = 0;
			$sumMTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;

			for ($i=0;$i < count($qtdArray);$i++)
			{
				$data[] = array($qtdArray[$i]['Bill Code'],$qtdArray[$i]['description'],$qtdArray[$i]['QTD Qty'],"$".number_format($qtdArray[$i]['QTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
				$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumQTDQty,"$".number_format($sumQTDTotal,2),$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);
			
			//Print Lens table
			$html = "<p>Bill Type: LENS<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Bill Code', 'Description', 'QTD Qty', 'QTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlQTDLens );
			$stmt->execute();
			$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDLens );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumQTDQty = 0;
			$sumQTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($qtdArray);$i++)
			{
				$data[] = array($qtdArray[$i]['Bill Code'],$qtdArray[$i]['description'],$qtdArray[$i]['QTD Qty'],"$".number_format($qtdArray[$i]['QTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
				$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumQTDQty,"$".number_format($sumQTDTotal,2),$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);

			//Print Misc table
			$html = "<p>Bill Type: MISC<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Bill Code', 'Description', 'QTD Qty', 'QTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlQTDMisc );
			$stmt->execute();
			$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDMisc );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumQTDQty = 0;
			$sumQTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($qtdArray);$i++)
			{
				$data[] = array($qtdArray[$i]['Bill Code'],$qtdArray[$i]['description'],$qtdArray[$i]['QTD Qty'],"$".number_format($qtdArray[$i]['QTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
				$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumQTDQty,"$".number_format($sumQTDTotal,2),$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);	
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
	function printPDFFramesQtr($date)
	{
		//Define date ranges
		$dateRange[0] = $date->format('Y')."-01-01";//Start of year
		$dateRange = array_merge($dateRange, calcQuarterDates($date));//QTD
		//Define SQL statements
		$sqlQTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'QTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'QTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') GROUP BY patient_gender"; 
		$sqlQTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'QTD Qty', COUNT(frame_id)*frame.price AS 'QTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id WHERE (service_date BETWEEN '".$dateRange[1]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name"; 
		$sqlYTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'YTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'YTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') GROUP BY patient_gender";
		$sqlYTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'YTD Qty', COUNT(frame_id)*frame.price AS 'YTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[2]."') OR (service_date IS NULL) GROUP BY frame.name";

		try {
			//Print HTML table

			//Print Frame Type table
			$html = "<p>Total Number of Frames with Lenses<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Frame Type', 'Description', 'QTD Qty', 'QTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlQTDFrames );
			$stmt->execute();
			$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrames );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumQTDQty = 0;
			$sumQTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($qtdArray);$i++)
			{
				$data[] = array($qtdArray[$i]['Frame Type'],$qtdArray[$i]['description'],$qtdArray[$i]['QTD Qty'],"$".number_format($qtdArray[$i]['QTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
				$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumQTDQty,"$".number_format($sumQTDTotal,2),$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);
			

			//Print Frame Name table
			$html = "<p>Total Number of Frames by Style<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Frame Style', 'Description', 'QTD Qty', 'QTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlQTDFrameNames );
			$stmt->execute();
			$qtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrameNames );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$sumQTDQty = 0;
			$sumQTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($qtdArray);$i++)
			{
				$data[] = array($qtdArray[$i]['Frame Style'],$qtdArray[$i]['description'],$qtdArray[$i]['QTD Qty'],"$".number_format($qtdArray[$i]['QTD Total'],2),$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumQTDQty = $sumQTDQty + $qtdArray[$i]['QTD Qty'];
				$sumQTDTotal = $sumQTDTotal + $qtdArray[$i]['QTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumQTDQty,"$".number_format($sumQTDTotal,2),$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);
		
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
	function printPDFVCodesYear($date)
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
			//print "<h3>Reporting Period: ".$dateRange[1]." to ".$dateRange[2]."</h3>";

			$this->setY(23,false,false);
			$this->SetFont('helvetica', 'B', 9);
			$this->Cell(270, 0, 'Reporting Period: '.$dateRange[1].' to '.$dateRange[2].' (Year)', 1, $ln=0, 'L', 0, '', 0, false, 'T', 'T');

			$this->setY(30,false,false);
			$html = "<p>Bill Type: FRAME<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Bill Code', 'Description', '', '', 'YTD Qty', 'YTD Total');		
			$data = array();		
			$stmt = Database :: prepare ( $sqlYTDFrame );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($ytdArray);$i++)
			{
				$data[] = array($ytdArray[$i]['Bill Code'],$ytdArray[$i]['description'],'','',$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL","","",$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);
			
			//Print Lens table
			$html = "<p>Bill Type: LENS<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Bill Code', 'Description', '', '', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlYTDLens );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($ytdArray);$i++)
			{
				$data[] = array($ytdArray[$i]['Bill Code'],$ytdArray[$i]['description'],"","",$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL","","",$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);

			//Print Misc table
			$html = "<p>Bill Type: MISC<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Bill Code', 'Description', '', '', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlYTDMisc );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumYTDQty = 0;
			$sumYTDTotal = 0;	
			for ($i=0;$i < count($ytdArray);$i++)
			{
				$data[] = array($ytdArray[$i]['Bill Code'],$ytdArray[$i]['description'],"","",$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL","","",$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);	
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
	function printPDFFramesYear($date)
	{
		//Define date ranges
		$dateRange[0] = $date->format('Y')."-01-01";//Start of year
		$dateRange[1] = $date->format('Y')."-12-31";//End of year
		//Define SQL statements
		$sqlYTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'YTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'YTD Total' FROM orderinfo WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[1]."') GROUP BY patient_gender";
		$sqlYTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'YTD Qty', COUNT(frame_id)*frame.price AS 'YTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id WHERE (service_date BETWEEN '".$dateRange[0]."' AND '".$dateRange[1]."') OR (service_date IS NULL) GROUP BY frame.name";
		try {
			//Print HTML table

			//Print Frame Type table
			$html = "<p>Total Number of Frames with Lenses<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Frame Type', 'Description', '', '', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlYTDFrames );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($ytdArray);$i++)
			{
				$data[] = array($ytdArray[$i]['Frame Type'],$ytdArray[$i]['description'],"","",$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL","","",$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);
			

			//Print Frame Name table
			$html = "<p>Total Number of Frames by Style<br /></p>";
			$this->SetFont('helvetica','',8);
			$this->writeHTML($html, false);		
			$header = array('Frame Style', 'Description', '', '', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlYTDFrameNames );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			for ($i=0;$i < count($ytdArray);$i++)
			{
				$data[] = array($ytdArray[$i]['Frame Style'],$ytdArray[$i]['description'],'','',$ytdArray[$i]['YTD Qty'],"$".number_format($ytdArray[$i]['YTD Total'],2));
				
				//Update sums
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL","","",$sumYTDQty,"$".number_format($sumYTDTotal,2));
			$this->ColoredTable($header,$data,$totals);
		
			$stmt->closeCursor ();	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
}

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Rochester Optical');
$pdf->SetTitle('Medicaid Audit Report');
$pdf->SetSubject('Jobs Submitted to Medicaid');
$pdf->SetKeywords('Medicaid, Alaska, Report');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 32, 5,true);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(1);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

//Determine report type
$reportDate = isset($_REQUEST['date']) ? new DateTime($_REQUEST['date']) : new DateTime("Now");
$reportType = isset($_REQUEST['reportType']) ? $_REQUEST['reportType'] : "month";

//Print Data
setlocale(LC_MONETARY, 'en_US'); //Set currency locale
switch($reportType)
{
	case "month":
		$pdf->printPDFVCodesMonth($reportDate);
		$pdf->printPDFFramesMonth($reportDate);
		break;
	case "quarter":
		$pdf->printPDFVCodesQtr($reportDate);
		$pdf->printPDFFramesQtr($reportDate);
		break;
	case "year":
		$pdf->printPDFVCodesYear($reportDate);
		$pdf->printPDFFramesYear($reportDate);
		break;
}


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_011.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+