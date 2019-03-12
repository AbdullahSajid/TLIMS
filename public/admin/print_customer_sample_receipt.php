<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/FPDF/fpdf.php");?>
<?php
access_admin();
$reception = get_user_info('reception');
if(isset($_GET['customer_id'])){
        $get_customer_id = $_GET['customer_id'];
    
   $customer =get_customer_order_by_id($get_customer_id);
   if(!$customer){
       // now checking for record in academic_students table
       $customer =get_student_order_by_id($get_customer_id);
       if(!$customer) {
           $_SESSION["message"] = "Customer ID isn't valid.";
            redirect_to("view_customer_sample_record.php");
       }
   }
   // now gather the name of tests of this sample in string
   if(isset($customer)){
       $query_success = TRUE;
       $test_names = find_test_names_of_sample( $customer['sample_id'],$customer['lab']);
       $test_names_list = implode(', ',$test_names);
       $lab_sample = get_lab_sample($customer['sample_id'],$customer['lab']);
   }
}
else{
    $_SESSION["message"] = "Search by Customer ID to get customer sample receipt";
    redirect_to("view_customer_sample_record.php");
}
class PDF extends FPDF{
 function Header(){
     // without the word sample receipt
  /*   $this->SetFont('Arial','B',17);
     $this->Cell(43);
     $this->Image('images/ntrc.png',15,10,25,20);
     $this->Cell(100,20,'National Textile Research Center',1,0,'C');
     $this->Image('images/ntu.jpg',165,10,25,20);
     $this->Cell(0,20,'',0,1);
     $this->Ln(5);*/
     // with the word sample receipt written
     $this->SetFont('Arial','B',17);
     $this->Cell(43);
     $this->Image('../assets/img/ntrc.png',15,10,25,20);
     $this->Cell(100,20,'National Textile Research Center',0,0,'C');
     $this->Image('../assets/img/ntu.jpg',165,10,25,20);
     $this->Cell(0,15,'',0,1);
     // dummy cell at the bottom of pic
     $this->Cell(43,5,'',0,0);
     $this->SetFont('Arial','',10);
     $this->Cell(100,5,'Sample Receipt',0,1,'C');
     $this->Ln(6);
 }

}
// for commercial and academic commercial customers
if($customer['type']=="commercial" || $customer['type']=="academic commercial"){
    $pdf = new PDF('p','mm','A4');
    $pdf->AddPage();
    $pdf->SetTitle("Commercial Sample Receipt");
    $pdf->SetAuthor("National Textile Research Centre, NTU Faisalabad");
    $pdf->SetFont('Arial','',10);

    $pdf->Cell(28,8,' Receiving Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y h:i:s A',strtotime($customer['creation_time'])),0,0);
    $pdf->Cell(65,8,' Customer ID: '. $customer['customer_id'],0,1);
    $pdf->Cell(28,8,' Expected Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y',strtotime($customer['expected_date'])),0,0);
    if(strlen($reception['name'])>15){
        $reception_name_parts = explode(' ',$reception['name']);
        $reception_name = $reception_name_parts[0];
    }
    else{
         $reception_name = $reception['name'];
    }
    $pdf->Cell(65,8,' Concerned Person: ' . $reception_name,0,1);
    $pdf->Cell(28,8,' Customer Ref:',0,0);
    $pdf->Cell(95,8,$customer['customer_ref'],0,0);
    $pdf->Cell(65,8,' Order Type: '. ucwords($customer['type']),0,1);

    //dump empty cell as a vertical spacer
    $pdf->Cell(189,3,'',0,1);
    // table start
    $pdf->Cell(35,8,' Name',1,0);
    $pdf->Cell(59,8," ". $customer['name'],1,0);
    $pdf->Cell(35,8,' Designation',1,0);
    $pdf->Cell(59,8," ". $customer['designation'],1,1);

    $pdf->Cell(35,8,' Organization',1,0);
    $pdf->Cell(59,8," ". $customer['organization'],1,0);
    $pdf->Cell(35,8,' Sample Type',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_type'],1,1);

    $pdf->Cell(35,8,' Sample Category',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_category'],1,0);
    $pdf->Cell(35,8,' No. of tests',1,0);
    $pdf->Cell(59,8," ". $lab_sample['no_of_tests'],1,1);

    $pdf->Cell(35,8,' Payment (Rs.)',1,0);
    $pdf->Cell(59,8," ". $customer['payment'],1,0);
    $pdf->Cell(35,8,' Received Payment',1,0);
    $pdf->Cell(59,8," ". $customer['payment_received'],1,1);

    // for multi column
    $cellWidth = 59; // wrapped cell width
    $cellHeight=8;   // normal one line cell height

    // check whether the text is overflowing
    if($pdf->GetStringWidth($test_names_list)< $cellWidth){
        $line =1;
        // do nothing
    }
    else{
        // if content is large then calculate the number of lines to fit the content
        $cellHeight=6; // for multiple lines we can reduce the height of the cells to maintain the structure
        $textLength= strlen($test_names_list);
        $errMargin = 10;
        $startChar = 0;
        $maxChar = 0;
        $textArray = array();
        $tmpString = '';

        while($startChar < $textLength){
            while ($pdf->GetStringWidth($tmpString) < ($cellWidth-$errMargin) && ($startChar+$maxChar)<$textLength){
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

    $pdf->Cell(35,($line*$cellHeight),' Pending Payment',1,0);
    $pdf->Cell(59,($line*$cellHeight)," ". $customer['payment_pending'],1,0);
    $pdf->Cell(35,($line*$cellHeight),' Tests',1,0);
    $pdf->MultiCell(59,$cellHeight,$test_names_list,1);

    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(35,8,'Receptionist',0,1);
    // add dummy cell for a cut-line
    $pdf->Cell(0,5,'','B',1);


    // for second receipt
    // Header of second receipt
    $pdf->SetFont('Arial','B',17);
    $pdf->SetY(119);
    $pdf->Ln(18);
    $pdf->Cell(43);
    $pdf->Image('../assets/img/ntrc.png',15,138,25,20);
    $pdf->Cell(100,20,'National Textile Research Center',0,0,'C');
    $pdf->Image('../assets/img/ntu.jpg',165,138,25,20);
    $pdf->Cell(0,15,'',0,1);
    // dummy cell at the bottom of pic
    $pdf->Cell(43,5,'',0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,'Sample Receipt',0,1,'C');
    $pdf->Ln(8);


    // below header content
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(28,8,' Receiving Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y h:i:s A',strtotime($customer['creation_time'])),0,0);
    $pdf->Cell(65,8,' Customer ID: '. $customer['customer_id'],0,1);
    $pdf->Cell(28,8,' Expected Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y',strtotime($customer['expected_date'])),0,0);
    if(strlen($reception['name'])>15){
        $reception_name_parts = explode(' ',$reception['name']);
        $reception_name = $reception_name_parts[0];
    }
    else{
         $reception_name = $reception['name'];
    }

    $pdf->Cell(65,8,' Concerned Person: '. $reception_name,0,1);
    $pdf->Cell(28,8,' Customer Ref:',0,0);
    $pdf->Cell(95,8,$customer['customer_ref'],0,0);
    $pdf->Cell(65,8,' Order Type: '. ucwords($customer['type']),0,1);

    //dump empty cell as a vertical spacer
    $pdf->Cell(189,3,'',0,1);
    // table start
    $pdf->Cell(35,8,' Name',1,0);
    $pdf->Cell(59,8," ". $customer['name'],1,0);
    $pdf->Cell(35,8,' Designation',1,0);
    $pdf->Cell(59,8," ". $customer['designation'],1,1);

    $pdf->Cell(35,8,' Organization',1,0);
    $pdf->Cell(59,8," ". $customer['organization'],1,0);
    $pdf->Cell(35,8,' Sample Type',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_type'],1,1);

    $pdf->Cell(35,8,' Sample Category',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_category'],1,0);
    $pdf->Cell(35,8,' No. of tests',1,0);
    $pdf->Cell(59,8," ". $lab_sample['no_of_tests'],1,1);

    $pdf->Cell(35,8,' Payment (Rs.)',1,0);
    $pdf->Cell(59,8," ". $customer['payment'],1,0);
    $pdf->Cell(35,8,' Received Payment',1,0);
    $pdf->Cell(59,8," ". $customer['payment_received'],1,1);

    // for multi column
    $cellWidth = 59; // wrapped cell width
    $cellHeight=8;   // normal one line cell height

    // check whether the text is overflowing
    if($pdf->GetStringWidth($test_names_list)< $cellWidth){
        $line =1;
        // do nothing
    }
    else{
        // if content is large then calculate the number of lines to fit the content
        $cellHeight=6; // for multiple lines we can reduce the height of the cells to maintain the structure
        $textLength= strlen($test_names_list);
        $errMargin = 10;
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

    $pdf->Cell(35,($line*$cellHeight),' Pending Payment',1,0);
    $pdf->Cell(59,($line*$cellHeight)," ". $customer['payment_pending'],1,0);
    $pdf->Cell(35,($line*$cellHeight),' Tests',1,0);
    $pdf->MultiCell(59,$cellHeight,$test_names_list,1);
}
// for academic customers
else{
    $pdf = new PDF('p','mm','A4');
    $pdf->AddPage();
    $pdf->SetTitle("Academic Sample Receipt");
    $pdf->SetAuthor("National Textile Research Center, NTU Faisalabad");
    $pdf->SetFont('Arial','',10);

    $pdf->Cell(28,8,' Receiving Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y h:i:s A',strtotime($customer['creation_time'])),0,0);
    $pdf->Cell(65,8,' Customer ID: '. $customer['customer_id'],0,1);
    $pdf->Cell(28,8,' Expected Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y',strtotime($customer['expected_date'])),0,0);
    if(strlen($reception['name'])>15){
        $reception_name_parts = explode(' ',$reception['name']);
        $reception_name = $reception_name_parts[0];
    }
    else{
         $reception_name = $reception['name'];
    }
    $pdf->Cell(65,8,' Concerned Person: ' . $reception_name,0,1);
    $pdf->Cell(28,8,' Registration No:',0,0);
    $pdf->Cell(95,8,$customer['reg_no'],0,0);
    $pdf->Cell(65,8,' Order Type: '. ucwords($customer['type']),0,1);

    //dump empty cell as a vertical spacer
    $pdf->Cell(189,3,'',0,1);
    // table start
    $pdf->Cell(35,8,' Name',1,0);
    $pdf->Cell(59,8," ". $customer['name'],1,0);
    $pdf->Cell(35,8,' Designation',1,0);
    $pdf->Cell(59,8," ". $customer['designation'],1,1);

    $pdf->Cell(35,8,' Institute',1,0);
    $pdf->Cell(59,8," ". $customer['institute'],1,0);
    $pdf->Cell(35,8,' Department',1,0);
    $pdf->Cell(59,8," ". $customer['department'],1,1);

    $pdf->Cell(35,8,' Sample Type',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_type'],1,0);
    $pdf->Cell(35,8,' Sample Category',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_category'],1,1);


    // for multi column
    $cellWidth = 59; // wrapped cell width
    $cellHeight=8;   // normal one line cell height

    // check whether the text is overflowing
    if($pdf->GetStringWidth($test_names_list)< $cellWidth){
        $line =1;
        // do nothing
    }
    else{
        // if content is large then calculate the number of lines to fit the content
        $cellHeight=6; // for multiple lines we can reduce the height of the cells to maintain the structure
        $textLength= strlen($test_names_list);
        $errMargin = 10;
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

    $pdf->Cell(35,($line*$cellHeight),' No. of tests',1,0);
    $pdf->Cell(59,($line*$cellHeight)," ". $lab_sample['no_of_tests'],1,0);
    $pdf->Cell(35,($line*$cellHeight),' Tests',1,0);
    $pdf->MultiCell(59,$cellHeight,$test_names_list,1);

    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(35,8,'Receptionist',0,1);
    // add dummy cell for a cut-line
    $pdf->Cell(0,5,'','B',1);


    // for second receipt
    // Header of second receipt
    $pdf->SetFont('Arial','B',17);
    $pdf->SetY(114);
    $pdf->Ln(18);
    $pdf->Cell(43);
    $pdf->Image('../assets/img/ntrc.png',15,133,25,20);
    $pdf->Cell(100,20,'National Textile Research Center',0,0,'C');
    $pdf->Image('../assets/img/ntu.jpg',165,133,25,20);
    $pdf->Cell(0,15,'',0,1);
    // dummy cell at the bottom of pic
    $pdf->Cell(43,5,'',0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,'Sample Receipt',0,1,'C');
    $pdf->Ln(8);


    // below header content
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(28,8,' Receiving Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y h:i:s A',strtotime($customer['creation_time'])),0,0);
    $pdf->Cell(65,8,' Customer ID: '. $customer['customer_id'],0,1);
    $pdf->Cell(28,8,' Expected Date:',0,0);
    $pdf->Cell(95,8,date('d-m-Y',strtotime($customer['expected_date'])),0,0);
    if(strlen($reception['name'])>15){
        $reception_name_parts = explode(' ',$reception['name']);
        $reception_name = $reception_name_parts[0];
    }
    else{
         $reception_name = $reception['name'];
    }

    $pdf->Cell(65,8,' Concerned Person: '. $reception_name,0,1);
    $pdf->Cell(28,8,' Registration No:',0,0);
    $pdf->Cell(95,8,$customer['reg_no'],0,0);
    $pdf->Cell(65,8,' Order Type: '. ucwords($customer['type']),0,1);

    //dump empty cell as a vertical spacer
    $pdf->Cell(189,3,'',0,1);
    // table start
    $pdf->Cell(35,8,' Name',1,0);
    $pdf->Cell(59,8," ". $customer['name'],1,0);
    $pdf->Cell(35,8,' Designation',1,0);
    $pdf->Cell(59,8," ". $customer['designation'],1,1);

    $pdf->Cell(35,8,' Institute',1,0);
    $pdf->Cell(59,8," ". $customer['institute'],1,0);
    $pdf->Cell(35,8,' Department',1,0);
    $pdf->Cell(59,8," ". $customer['department'],1,1);

    $pdf->Cell(35,8,' Sample Type',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_type'],1,0);
    $pdf->Cell(35,8,' Sample Category',1,0);
    $pdf->Cell(59,8," ". $lab_sample['sample_category'],1,1);


    // for multi column
    $cellWidth = 59; // wrapped cell width
    $cellHeight=8;   // normal one line cell height

    // check whether the text is overflowing
    if($pdf->GetStringWidth($test_names_list)< $cellWidth){
        $line =1;
        // do nothing
    }
    else{
        // if content is large then calculate the number of lines to fit the content
        $cellHeight=6; // for multiple lines we can reduce the height of the cells to maintain the structure
        $textLength= strlen($test_names_list);
        $errMargin = 10;
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

    $pdf->Cell(35,($line*$cellHeight),' No. of tests',1,0);
    $pdf->Cell(59,($line*$cellHeight)," ". $lab_sample['no_of_tests'],1,0);
    $pdf->Cell(35,($line*$cellHeight),' Tests',1,0);
    $pdf->MultiCell(59,$cellHeight,$test_names_list,1);
}

$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(35,8,'Receptionist',0,1);
// add dummy cell for a cut-line
$pdf->Cell(0,5,'','B',1);
$pdf->Output();
?>