<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/FPDF/fpdf.php");?>
<?php
access_receptionist();
$reception = get_user_info('reception');
class PDF extends FPDF{
    function Header(){

        // with the word sample receipt written
        $this->SetFont('Arial','B',15);
        $this->Cell(43);
        $this->Image('../assets/img/ntrc.png',15,10,25,20);
        $this->Cell(100,20,'National Textile Research Centre',0,0,'C');
        $this->Image('../assets/img/ntu.jpg',165,10,25,20);
        $this->Cell(0,15,'',0,1);
        // dummy cell at the bottom of pic
        $this->Cell(43,5,'',0,0);
        $this->SetFont('Arial','',10);
        $this->Cell(100,5,'Customers Sales Report',0,1,'C');
        $this->Ln(6);
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' / {pages}',0,0,'C');
    }

}
if(isset($_POST['generate_report'])) {
    // Initializing variables to use in reporting form at the bottom of the page
    $customer_type = $_POST['customer_type'];
    $lab = $_POST['lab'];
    $status = $_POST['order_status'];
    $time_period = $_POST['dates'];
    $starting_date = "";
    $ending_date = "";
    
    if(!empty($time_period)){
        $dates = explode(' - ',$time_period);
        $starting_date = date("Y-m-d", strtotime($dates[0]));
        $ending_date = date("Y-m-d", strtotime($dates[1]));
      
        $time_interval = "AND timestamp BETWEEN '$starting_date' AND '$ending_date' + INTERVAL 1 DAY ";
        
        // changing dates format to pakistani format for pdf display
         $starting_date = date("d-m-Y", strtotime($dates[0]));
         $ending_date = date("d-m-Y", strtotime($dates[1]));
    }
    
    $query  = "SELECT * FROM orders ";
    $stats_query = "SELECT sum(payment), sum(payment_received), sum(payment_pending) FROM orders ";
    
    if($customer_type=='all'){
        $query .= "WHERE type IN('commercial','academic commercial') ";
        $stats_query .= "WHERE type IN('commercial','academic commercial') ";
    }
    else{
        $query .= "WHERE type IN('{$customer_type}') ";
        $stats_query .= "WHERE type IN('{$customer_type}') ";
    }
    if($status!='all'){ 
        $query .= "AND status='{$status}' ";
        $stats_query .= "AND status='{$status}' ";
    }
    if($lab!='all'){
        $query .= "AND lab='{$lab}' ";
        $stats_query .= "AND lab='{$lab}' ";
    }
    if(!empty($time_period)){
        $query .= $time_interval;
        $stats_query .= $time_interval;
    }
    $total_samples = mysqli_query($connection, $query);
    $samples_count = mysqli_num_rows($total_samples);
    confirm_query($total_samples);
    
    $total_statistics = mysqli_query($connection, $stats_query);
    confirm_query($total_statistics);
    $total_statistics = mysqli_fetch_row($total_statistics);
    
    if($samples_count!=0){
        $pdf = new PDF('p','mm','A4');
        $pdf->AliasNbPages('{pages}');
        $pdf->SetAutoPageBreak(true,15);
        $pdf->AddPage();
        $pdf->SetAuthor("National Textile Research Center, NTU FSD");
        $pdf->SetTitle("Customers Sales Report");
        // below header content
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(24,6,' Customers Type: ',0,0);
        $pdf->Cell(109,6, beautify_fieldname($customer_type),0,0);
        $pdf->Cell(55,6,' No. of Customers: '. $samples_count,0,1);
        $pdf->Cell(20,6,' Selected Lab: ',0,0);
        $pdf->Cell(113,6, beautify_fieldname($lab),0,0);
        switch($status){
            case 'submitted':
                $status = 'Submitted Tests';
                break;
            case 'pending':
                $status = 'Pending Tests';
                break;
            case 'completed':
                $status = 'Pending Reports';
                break;
            case 'finished':
                $status = 'Completed Reports';
                break;
            default:
                $status = 'All';   
        }
        $pdf->Cell(60,6,' Sample Status: '. $status,0,1);
        if (!empty($starting_date)) {
            $pdf->Cell(20, 6, ' Starting Date: ', 0, 0);
            $pdf->Cell(113, 6, $starting_date, 0, 0);
        }
        else{
            $pdf->Cell(20, 6, ' Starting Date: ', 0, 0);
            $pdf->Cell(113, 6, 'N/A', 0, 0);
        }
        if (!empty($ending_date)) {
            $pdf->Cell(55, 6, ' Ending Date: ' . $ending_date, 0, 1);
        }
        else {
            $pdf->Cell(55, 6, ' Ending Date: N/A', 0, 1);
        }

        //dump empty cell as a vertical spacer
        $pdf->Cell(189,3,'',0,1);
        //Content
        $pdf->SetFont('Arial','B','6');
        $pdf->Cell(9,5,'Sr#',1,0,'C');
        $pdf->Cell(15,5,'Customer ID',1,0,'C');
        $pdf->Cell(24,5,'Organization',1,0,'C');
        $pdf->Cell(19,5,'Type',1,0,'C');
        $pdf->Cell(21,5,'Arrival Time',1,0,'C');
        $pdf->Cell(21,5,'Lab',1,0,'C');
        $pdf->Cell(30,5,'Tests',1,0,'C');
        $pdf->SetFont('Arial','B','5');
        $pdf->Cell(19,5,'Total Payment (Rs.)',1,0,'C');
        $pdf->Cell(21,5,'Pending Payment (Rs.)',1,1,'C');

        $counter=1;
        $pdf->SetFont('Arial','','5');
        $fontSize=5;
        $tempFontSize = $fontSize;
        while($sample = mysqli_fetch_assoc($total_samples)) {
            $test_names = find_test_names_of_sample( $sample['sample_id'],$sample['lab']);
            $test_names_list = implode(', ',$test_names);
            // for multi column
            $cellWidth = 30; // wrapped cell width
            $cellHeight= 4;  // normal one line cell height

            // check whether the text is overflowing
            if($pdf->GetStringWidth($test_names_list)< $cellWidth){
                $line =1;
                // do nothing
            }
            else{
                // if content is large then calculate the number of lines to fit the content
                $cellHeight=4; // for multiple lines we can reduce the height of the cells to maintain the structure
                $textLength= strlen($test_names_list);
                $errMargin = 6;
                $startChar = 0;
                $maxChar = 0;
                $textArray = array();
                $tmpString = '';

                while($startChar < $textLength){
                    while ($pdf->GetStringWidth($tmpString) < ($cellWidth-$errMargin) &&
                        ($startChar+$maxChar)<$textLength){
                        $maxChar++;
                        $tmpString = substr($test_names_list,$startChar,$maxChar);
                    }
                    $startChar =$startChar +$maxChar;
                    array_push($textArray,$tmpString);
                    $maxChar = 0;
                    $tmpString = '';
                }
                $line = count($textArray);
            }
            $pdf->Cell(9,($line*$cellHeight),$counter++, 1, 0, 'C');
            $pdf->Cell(15,($line*$cellHeight),$sample["customer_id"], 1, 0, 'C');
            $customer = get_customer_by_type($sample['customer_id'],$sample['type']);
            // setting smaller font size for larger organizations names
            while ($pdf->GetStringWidth($customer['organization'])>23){
            $pdf->SetFontSize($tempFontSize -=0.1);
            }
            $pdf->Cell(24,($line*$cellHeight),$customer['organization'], 1, 0, 'C');
            // resetting the standard fontsize
            $tempFontSize = $fontSize;
            $pdf->SetFontSize($fontSize);
            // setting smaller font size for larger customer-types like academic-commercial
            while ($pdf->GetStringWidth(beautify_fieldname($sample["type"]))>18){
                $pdf->SetFontSize($tempFontSize -=0.1);
            }
            $pdf->Cell(19,($line*$cellHeight),beautify_fieldname($sample["type"]), 1, 0, 'C');
            // resetting the standard fontsize
            $tempFontSize = $fontSize;
            $pdf->SetFontSize($fontSize);
         
            $arrival_time=date('d-m-Y h:i:s A',strtotime($sample['timestamp']));
            $pdf->Cell(21,($line*$cellHeight),$arrival_time,1,0,'C');
            
            // setting smaller font size for larger lab names like composite characterization lab
            while ($pdf->GetStringWidth(beautify_fieldname($sample["lab"]))>20){
                $pdf->SetFontSize($tempFontSize -=0.1);
            }
            $pdf->Cell(21,($line*$cellHeight),beautify_fieldname($sample["lab"]), 1, 0, 'C');
            // resetting the standard fontsize
            $tempFontSize = $fontSize;
            $pdf->SetFontSize($fontSize);
            
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY();
            $pdf->MultiCell($cellWidth,$cellHeight,$test_names_list,1,'C');
            $pdf->SetXY($xPos+$cellWidth,$yPos);
            $pdf->Cell(19,($line*$cellHeight),$sample['payment'],1,0,'C');
            $pdf->Cell(21,($line*$cellHeight),$sample['payment_pending'],1,1,'C');
        }
        $pdf->Cell(139);
        $pdf->SetFont('Arial','B','5');
        $pdf->Cell(19,4,'Total Payment',1,0,'C');
        $pdf->SetFont('Arial','','5');
        $pdf->Cell(21,4,'Rs. ' . $total_statistics[0],1,1,'C');
        $pdf->Cell(139);
        $pdf->SetFont('Arial','B','5');
        $pdf->Cell(19,4,'Received Payment',1,0,'C');
        $pdf->SetFont('Arial','','5');
        $pdf->Cell(21,4,'Rs. ' . $total_statistics[1],1,1,'C');
        $pdf->Cell(139);
        $pdf->SetFont('Arial','B','5');
        $pdf->Cell(19,4,'Pending Payment',1,0,'C');
        $pdf->SetFont('Arial','','5');
        $pdf->Cell(21,4,'Rs. ' . $total_statistics[2],1,1,'C');
        $pdf->Output();

    }
    else {
        redirect_to(rawurlencode("index.php") . "?err=" .
            urlencode("Sales Record does not exist"));
    }
}
else{
    redirect_to(rawurlencode("index.php") . "?err=" .
        urlencode("Select parameters to find sales statistics"));
}