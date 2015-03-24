<?php
include 'functions.php'; //Calls functions-db.php
include 'accesscontrol.php'; //Calls functions-db.php
require_once('C:\www\rochesteroptical\public_html\tcpdf\config\lang\eng.php');
require_once('C:\www\rochesteroptical\public_html\tcpdf\tcpdf.php');

// extend TCPF with custom functions
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        // Logo
        $image_file = 'sig-RochesterOptical.jpg';
        $this->Image($image_file, 15, 5, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 16);
        // Title
		$this->setCellMargins(0,7,0,0);
        $this->Cell(160, 50, 'Medicare Audit Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
	 * Get service numbers and costs: Vcode, MTD Qty, MTD Total, YTD Qty, YTD Total
	 */
	function dbPDFMedicaidReportVCodes($startDate="",$endDate="")
	{
		if ($startDate == "") {
			$curDate = new DateTime("Now");
			$sqlMTDFrame = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '2011-04-01' AND '".$curDate->format('Y-m-d')."') OR (service_date IS NULL) OR (count IS NULL)) AND bill_type='FRAME' GROUP BY vcode "; 
			$sqlYTDFrame = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '2011-01-01' AND '".$curDate->format('Y-m-d')."') OR (service_date IS NULL)  OR (count IS NULL)) AND bill_type='FRAME' GROUP BY vcode ";
			$sqlMTDLens = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '2011-04-01' AND '".$curDate->format('Y-m-d')."') OR (service_date OR count IS NULL)) AND bill_type='LENS' GROUP BY vcode "; 
			$sqlYTDLens = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '2011-01-01' AND '".$curDate->format('Y-m-d')."') OR (service_date IS NULL)  OR (count IS NULL)) AND bill_type='LENS' GROUP BY vcode ";
			$sqlMTDMisc = "SELECT vcode AS 'Bill Code', description, SUM(COALESCE(count,0)) AS 'MTD Qty', SUM(COALESCE(count,0))*cost AS 'MTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '2011-04-01' AND '".$curDate->format('Y-m-d')."') OR (service_date OR count IS NULL)) AND bill_type='MISC' GROUP BY vcode "; 
			$sqlYTDMisc = "SELECT SUM(COALESCE(count,0)) AS 'YTD Qty', SUM(COALESCE(count,0))*cost AS 'YTD Total' FROM vcode LEFT OUTER JOIN orderinfo_has_vcode ON id=vcode_id LEFT OUTER JOIN orderinfo ON job_id=orderinfo_job_id WHERE ((service_date BETWEEN '2011-01-01' AND '".$curDate->format('Y-m-d')."') OR (service_date OR count IS NULL)) AND bill_type='MISC' GROUP BY vcode ";
		}
		try {
			//print "<pre>".var_dump($mtdArray)."</pre>";
			//print "<pre>".var_dump($ytdArray)."</pre>";
			
			//Print HTML table
			//print "<h3>Reporting Period: ".$curDate->format('M Y (m/d/Y)')."</h3>";

			//Print Frames table
			$stmt = Database :: prepare ( $sqlMTDFrame );
			$stmt->execute();
			$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrame );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			//print "<h4>Bill Type: FRAME</h4>";
			$header = array('Bill Code', 'Description', 'MTD Qty', 'MTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			
			$sumMTDQty = 0;
			$sumMTDTotal = 0;
			$sumYTDQty = 0;
			$sumYTDTotal = 0;
			
			for ($i=0;$i < count($mtdArray);$i++)
			{
				$data[] = array($mtdArray[$i]['Bill Code'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".$mtdArray[$i]['MTD Total'],$ytdArray[$i]['YTD Qty'],"$".$ytdArray[$i]['YTD Total']);
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".$sumMTDTotal,$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);
			
			//Print Lens table
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
				$data[] = array($mtdArray[$i]['Bill Code'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".$mtdArray[$i]['MTD Total'],$ytdArray[$i]['YTD Qty'],"$".$ytdArray[$i]['YTD Total']);
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".$sumMTDTotal,$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);

			//Print Misc table
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
				$data[] = array($mtdArray[$i]['Bill Code'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".$mtdArray[$i]['MTD Total'],$ytdArray[$i]['YTD Qty'],"$".$ytdArray[$i]['YTD Total']);
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".$sumMTDTotal,$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);
						
			$stmt->closeCursor ();	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}

	}

	/**
	 *
	 */
	function dbPDFMedicaidReportFrames($startDate="",$endDate="")
	{
		if ($startDate == "") {
			$curDate = new DateTime("Now");
			$sqlMTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'MTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'MTD Total' FROM orderinfo WHERE (service_date BETWEEN '2011-04-01' AND '".$curDate->format('Y-m-d')."') GROUP BY patient_gender"; 
			$sqlYTDFrames = "SELECT patient_gender AS 'Frame Type', COUNT(patient_gender) AS 'YTD Qty', COUNT(COALESCE(patient_gender,0))*amount AS 'YTD Total' FROM orderinfo WHERE (service_date BETWEEN '2011-01-01' AND '".$curDate->format('Y-m-d')."') GROUP BY patient_gender";
			$sqlMTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'MTD Qty', COUNT(frame_id)*frame.price AS 'MTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id WHERE (service_date BETWEEN '2011-04-01' AND '2011-04-29') OR (service_date IS NULL) GROUP BY frame.name"; 
			$sqlYTDFrameNames = "SELECT frame.name AS 'Frame Style', COUNT(frame_id) AS 'YTD Qty', COUNT(frame_id)*frame.price AS 'YTD Total' FROM frame LEFT OUTER JOIN orderinfo ON frame.id = orderinfo.frame_id WHERE (service_date BETWEEN '2011-01-01' AND '2011-04-29') OR (service_date IS NULL) GROUP BY frame.name";
		}
		try {
			//Print HTML table

			//Print Frame Type table
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
				$data[] = array($mtdArray[$i]['Frame Type'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".$mtdArray[$i]['MTD Total'],$ytdArray[$i]['YTD Qty'],"$".$ytdArray[$i]['YTD Total']);
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".$sumMTDTotal,$sumYTDQty,"$".$sumYTDTotal);
			$this->ColoredTable($header,$data,$totals);
			

			//Print Frame Name table
			$header = array('Frame Style', 'Description', 'MTD Qty', 'MTD Total', 'YTD Qty', 'YTD Total');		
			$data = array();
			$stmt = Database :: prepare ( $sqlMTDFrameNames );
			$stmt->execute();
			$mtdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = Database :: prepare ( $sqlYTDFrameNames );
			$stmt->execute();
			$ytdArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
			for ($i=0;$i < count($mtdArray);$i++)
			{
				$data[] = array($mtdArray[$i]['Frame Style'],$mtdArray[$i]['description'],$mtdArray[$i]['MTD Qty'],"$".$mtdArray[$i]['MTD Total'],$ytdArray[$i]['YTD Qty'],"$".$ytdArray[$i]['YTD Total']);
				
				//Update sums
				$sumMTDQty = $sumMTDQty + $mtdArray[$i]['MTD Qty'];
				$sumMTDTotal = $sumMTDTotal + $mtdArray[$i]['MTD Total'];
				$sumYTDQty = $sumYTDQty + $ytdArray[$i]['YTD Qty'];
				$sumYTDTotal = $sumYTDTotal + $ytdArray[$i]['YTD Total'];
			}
			$totals = array("","SUBTOTAL",$sumMTDQty,"$".$sumMTDTotal,$sumYTDQty,"$".$sumYTDTotal);
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
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

//Print Data
$data = $pdf->dbPDFMedicaidReportVCodes();
$data = $pdf->dbPDFMedicaidReportFrames();


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_011.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+