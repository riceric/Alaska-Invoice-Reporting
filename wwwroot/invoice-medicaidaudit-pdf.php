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
        $this->Cell(0, 0, 'Medicare Audit Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(); //Line break
        $this->SetFont('helvetica', 'B', 9);
		$this->Cell(0, 0, 'VISION ASSOCIATES OF ROCHESTER', 0, false, 'C', 0, '', 0, false, 'M', 'M');

		//Batch info: 2) Print out batch and date info
		$dateStamp = new DateTime($batchInfo['time']);
		$dateStamp = $dateStamp->format('m/d/Y');
		$this->Ln(); //Line break
		$html = "<p>Jobs submitted to Medicaid - First time jobs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batch Number: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date: ". $dateStamp ."<br /></p>";
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
        $this->SetFont('', 'B',8);
        // Header
        $w = array(33, 55, 15, 25, 35, 35, 25, 30, 24);
        for($i = 0; $i < count($header); ++$i) {
            $this->Cell($w[$i], 5, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln(); //Line break
        // Color and font restoration
        $this->SetTextColor(0);
        $this->SetFont('helvetica','',7);
        // Data
        $fill = 0;
		foreach($data as $col) {
			if ($fill) { $this->SetFillColor(224, 235, 255); } else { $this->SetFillColor(255, 255, 255); }		
			$this->Cell($w[0], 4, $col[0], 'LR', 0, 'L', 1);
			$this->Cell($w[1], 4, $col[1], 'LR', 0, 'L', 1);
			$this->Cell($w[2], 4, $col[2], 'LR', 0, 'C', 1);
			$this->Cell($w[3], 4, $col[3], 'LR', 0, 'C', 1);
			$this->Cell($w[4], 4, $col[4], 'LR', 0, 'L', 1);
			$this->Cell($w[5], 4, $col[5], 'LR', 0, 'L', 1);
			$this->Cell($w[6], 4, $col[6], 'LR', 0, 'L', 1);
			$this->Cell($w[7], 4, $col[7], 'LR', 0, 'C', 1);
			$this->Cell($w[8], 4, $col[8], 'LR', 0, 'R', 1);
			$this->Ln();
			$fill=!$fill;
		}

		$this->SetFillColor(210, 255, 230);
		for($i = 0; $i < count($totals); ++$i) {
            $this->Cell($w[$i], 6, $totals[$i], 'LR', 0, 'R', 1);
		}
        $this->Ln(); //Line break
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(); //Line break

    }
	/**
	 * Print Medicaid Audit Report - First time jobs
	 */
	function dbPDFPrintMedicaidAudit($billingReportID)
	{
		$qAuditOrders = "SELECT customer.customer_num, customer.comp_name, job_id, invoice_num, patient_lname, patient_fname, recipient_id, prior_auth_num, frame, amount FROM customer, orderinfo WHERE customer.customer_num = orderinfo.customer_num AND billingreport_id=$billingReportID ORDER BY service_date DESC";
		try {
			$stmt = Database :: prepare ( $qAuditOrders );
			$stmt->execute();
			$numJobs = $stmt->rowCount();
			$tableArray = $stmt->fetchAll(PDO::FETCH_ASSOC);		
		
			//Print HTML table
			$header = array('Customer Number', 'Customer Name', 'Job ID', 'Invoice Number', 'Patient Last Name', 'Patient First Name', 'Recipient ID', 'Authorization #', 'Amount');		
			$data = array();
			$subtotals = array();
			
			$subTotal = 0;
			$totalFrames = 0;
			foreach ($tableArray as $row)
			{
				$data[] = array($row['customer_num'],strtoupper($row['comp_name']),$row['job_id'],$row['invoice_num'],strtoupper($row['patient_lname']),strtoupper($row['patient_fname']),$row['recipient_id'],$row['prior_auth_num'],"$".$row['amount']);		
				$subTotal += $row['amount']; //Increment sub total
				$totalFrames += $row['frame']; //Increment sub total
			}
			$subtotals = array("Sub Total","Number of jobs: ".$numJobs," "," "," "," "," "," ","$".$subTotal);
			$this->ColoredTable($header,$data,$subtotals);
		}
		catch(PDOException $e)
		{
			echo "<div class=\"status\">dbPDFPrintMedicaidAudit(): ".$e->getMessage()."</div><!--.status-->";
		}
	
		$result = "";
		//Get number of frames with lenses
		try {
			//Get number of lenses
			$qLenses = "SELECT SUM(orderinfo_has_vcode.count) AS numLenses FROM orderinfo, orderinfo_has_vcode, vcode WHERE orderinfo.job_id=orderinfo_job_id AND vcode_id=vcode.id AND bill_type='LENS' AND billingreport_id=$billingReportID GROUP BY bill_type";
			$stmt = Database :: prepare ( $qLenses );
			$stmt->execute();
			$numLenses = $stmt->fetchAll();
			$numLenses = $numLenses[0]['numLenses'];

			$qFramesWLenses = "SELECT job_id, vcode.bill_type 
								FROM orderinfo, orderinfo_has_vcode, vcode 
								WHERE billingreport_id=$billingReportID AND orderinfo.job_id=orderinfo_job_id AND vcode_id=vcode.id AND bill_type= 'LENS' 
								GROUP BY job_id";
			$stmt = Database :: prepare ( $qFramesWLenses );
			$stmt->execute();
			$numFramesWLenses = $stmt->rowCount();
				
			$result .= "<p><strong>Total</strong></p>";
			$result .= "<p>Number of Jobs: ".$numJobs."</p>";
			$result .= "<p>Number of Lenses: ".$numLenses."</p>";
			$result .= "<p>Number of Frames with Lenses: ".$numFramesWLenses."</p>";
			$result .= "<p>Number of Frames without Lenses: ".(int)($totalFrames - $numFramesWLenses)."</p>";
			$stmt->closeCursor ();
		}
		catch(PDOException $e)
		{
			$result .= "<div class=\"status\">int FramesWLenses: ".$e->getMessage()."</div><!--.status-->";
		}
		$this->writeHTML($result, true, 0, true, 0);
	}
}
// Get parameters
global $batchInfo;
$billingReportID = isset($_REQUEST['billreport']) ? $_REQUEST['billreport'] : 0;
$batchInfo = dbGetBillingReportBatchNum($billingReportID);

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

//Print table data
$data = $pdf->dbPDFPrintMedicaidAudit($billingReportID);

// ---------------------------------------------------------
$fileDate = new DateTime($dateStamp);
//Close and output PDF document
$pdf->Output($fileDate->format('Ymd').'-medicaidaudit.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+