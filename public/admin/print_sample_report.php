<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/config.php");?>
<?php
access_admin();
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Output\QRImage;
use chillerlan\QRCode\Output\QRImageOptions;
/*using chillerlan/php-qrcode version 1.1.0 to generate qrcode
which is called using autoload.php. See composer.json for library detail
For html tables see http://www.fpdf.org/en/script/script70.php */
if(isset($_GET['customer_id'])&&isset($_GET['lab'])){
    $customer_order_id = $_GET['customer_id'];
    $lab = $_GET['lab'];
    $customer_order =find_order_by_customer_id($customer_order_id);
    if(!$customer_order){
       
        $_SESSION["message"] = "Customer ID isn't valid.";
        redirect_to("pending_reports.php");
    }
    if($customer_order['status']=='finalized' || $customer_order['status']=='finished'){
        if($lab!=$customer_order['lab']){
                $_SESSION["message"] = "Sample does not belong to lab";
                redirect_to("pending_reports.php"); 
        }
        $customer = get_customer_by_type($customer_order['customer_id'],$customer_order['type']);
        $lab_table = $lab . '_' . 'samples';
        $sample_id = $customer_order['sample_id'];
        $sample = get_lab_sample($sample_id,$lab);
        $test_names=find_test_names_of_sample_by_lab_sample($sample,$lab);

    }
    elseif($customer_order['status']=='submiited'){
        $_SESSION["message"] = "Sample is not submiited to lab yet";
        redirect_to("view_customer_sample_record.php?customer_id={$customer_order_id}");
    }
    elseif($customer_order['status']=='pending'){
        $_SESSION["message"] = "Sample Test Report is not completed";
        redirect_to("pending_reports.php");
    }
    else{
        redirect_to("pending_reports.php");
    }
    
}
else{
     $_SESSION["message"] = "Access denied due to incorrect url";
     redirect_to("pending_reports.php");
}
    // start report content
    // at this point customer id should exist so lets generate a report
    $pdf = new \Mpdf\Mpdf();
    $pdf->SetAuthor("National Textile Research Centre, NTU FSD");
    $pdf->SetTitle("Customer Sample Report");
    $pdf->SetAutoPageBreak(true,15);
    $pdf->SetLeftMargin(15);
    $pdf->SetFont('Arial','',7);
    /*/ Define the Header/Footer before writing anything so they appear on the first page*/
    $pdf->SetHTMLHeader('<table width="100%">
        <tr>
            <td width="17%"><img style="width:55px;height:50px;" src="../assets/img/ntrc.png" alt="ntrc logo"/></td>
            <td width="66%" align="center"><span style="font-size:18px;font-weight:bold;">National Textile Research Centre</span><br>Sample Test Report</td>
            <td width="17%" style="text-align:right;"><img style="width:55px;height:50px;" src="../assets/img/ntu.jpg" alt="ntu logo"/></td>
        </tr>
    </table><br/><br/>');
    $pdf->SetHTMLFooter('
    <table width="100%" style="border-bottom:5px solid;border-color:#3D3D3D;">
        <tr>
            <td width="100%"><span style="font-size:9px;font-weight:bold;">This is a computer generated report. This document cannot be reproduced except in full, without prior approval of the Company.</span></td>
        </tr>
        <tr> 
            <td style="padding-top:-3px;">
                <table width="100%">
                <tr>
                    <td width="37%" style="text-align:right;padding-top:-10px;border-right:1px solid;"><span style="font-size:8px;font-weight:bold;">National Textile Research Center</span></td>
                    <td width="72%"><span style="font-size:8px;">National Textile University, Sheikhupura Road, Manawala, Faisalabad, Pakistan.<br/>Tel: +92 41 9230081-85 (Ext: 191-195)  Fax: +92 41 9230098  E-mail: ahsan@ntu.edu.pk  Website: www.ntu.edu.pk</span></td>
                </tr>
                </table>
            </td>
        </tr>
    </table><div style="text-align:center;font-size:10px;margin-top:4px;">Page {PAGENO} of {nb}</div>');
    
    $pdf->AddPage();
    // start page content
    $pdf->SetFont('Arial',"B",8);
    $pdf->Cell(189,20,'',0,1); // same as pdf->Ln(20) in FPDF
    $pdf->Cell(108);
    $pdf->Cell(27,5,"REPORT NUMBER:",0,0,'L');
    $pdf->SetFont('Arial',"",8);
    $pdf->Cell(22,5,$customer_order['customer_id'],0,1,'L');
    $pdf->Cell(108);
    $pdf->SetFont('Arial',"B",8);
    $pdf->Cell(27,5,"ISSUE DATE:",0,0,'L');
    $pdf->SetFont('Arial',"",8);
    $pdf->Cell(22,5,date("M d, Y"),0,1,'L');
    $pdf->Cell(108);
    $pdf->SetFont('Arial',"B",8);
    $pdf->Cell(27,5,"PAGE NO:",0,0,'L');
    $pdf->SetFont('Arial',"",8);
    $pdf->Cell(22,5,$pdf->PageNo(),0,1,'L');
    // using qrcode generator library to generate qrcode png
    // help https://github.com/chillerlan/php-qrcode/tree/1.1.0
    $data = $customer_order['sample_id'];
    $outputOptions = new QRImageOptions;
    $outputOptions->type = QRCode::OUTPUT_IMAGE_PNG;
    $outputInterface = new QRImage($outputOptions);

    $qrcode = new QRCode($data, $outputInterface);
    $pdf->Image($qrcode->output(),174,35,20,18,'PNG');
    // using google api to generate qrcode png
    /*$pdf->Image('https://chart.googleapis.com/chart?chs=160x160&cht=qr&chl='. $customer_order['sample_id'] . '.png',165,39,25,23);*/
    // for commercial customers
    if($customer_order['type']==="commercial" || $customer_order['type']==="academic commercial"){
        
        // set other content
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(85,6,"SAMPLE SUBMITTED AND IDENTIFIED BY SUPPLIER AS:",0,1,'L');
        $pdf->WriteHTML('<br/>');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"SUBMITTED BY:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer['name']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"CUSTOMER REFERENCE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer['customer_ref']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ORDER TYPE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer_order['type']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ORGANIZATION",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer['organization']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ADDRESS:",0,0,'L');
        if(empty($customer['address'])){
            $address = "N/A";
        }
        else{
            $address = ucfirst($customer['address']);
        }
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,$address,0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"CITY:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucfirst($customer['city']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ORIGIN COUNTRY:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,"Pakistan",0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"COUNTRY DESTINATION:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,"N/A",0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"CONTACT DETAILS:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,"Phone: ".$customer['phone'],0,1,'L');
        $pdf->Cell(40);
        $pdf->Cell(60,6,"Email: ".$customer['email'],0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ARRIVAL TIME:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $arrival_time = date('d-m-Y h:i:s A',strtotime($customer['creation_time']));
        $pdf->Cell(60,6,$arrival_time,0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"SAMPLE TYPE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,$sample['sample_type'],0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"COLOR:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucfirst($sample['sample_color']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"STYLE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        if(!empty($sample['sample_style'])) {
            $pdf->Cell(60, 6,$sample['sample_style'], 0, 1, 'L');
        }
        else{
            $pdf->Cell(60, 6,"N/A", 0, 1, 'L');
        }
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"WEIGHT:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        if(!empty($sample['sample_weight'])) {
            $pdf->Cell(60, 6,$sample['sample_weight'], 0, 1, 'L');
        }
        else{
            $pdf->Cell(60, 6,"N/A", 0, 1, 'L');
        }
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"NO OF TESTS:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,$sample['no_of_tests'],0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"TESTS:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        foreach($test_names as $testname){
             $pdf->Cell(60,5,$testname, 0, 1, 'L');
             $pdf->Cell(40);
        }
        $pdf->Cell(0,0,'',0,1); // same as Ln(1)
        $pdf->SetY(186);
        if(!empty($sample['sample_image'])){
            $pdf->WriteHTML('<table width="100%">
            <tr>
                <td width="32%"></td>
                <td width="36%" style="border:3px solid;border-color:grey;"><img style="width:36%;height:180px;" src="../../includes/samples-pics/' . $sample['sample_image'] . '" alt="sample image"/></td>
                <td width="32%" style="padding-top:160px;padding-left:51px;">
                    <span style="font-size:10px;">
                    Signed on the behalf of:<br/>
                    National Textile Research Centre
                    </span>
                </td>
            </tr>
            </table>
            <table width="100%">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;height:60px;border-bottom:1px solid;">
                    </td>
                </tr>
            </table>
            <table width="100%" style="font-size:10px;">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;text-align:center;">
                      <span><b>Dr. Ahsan Nazir</b></span>
                      <br/>
                      <span>Director ORIC</span>
                    </td>
                </tr>
            </table>');
        }
        else{
            $pdf->WriteHTML('<table width="100%">
            <tr>
                <td width="32%"></td>
                <td width="36%" style="height:180px;"></td>
                <td width="32%" style="padding-top:160px;padding-left:51px;">
                    <span style="font-size:10px;">
                    Signed on the behalf of:<br/>
                    National Textile Research Centre
                    </span>
                </td>
            </tr>
            </table>
            <table width="100%">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;height:60px;border-bottom:1px solid;">
                    </td>
                </tr>
            </table>
            <table width="100%" style="font-size:10px;">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;text-align:center;">
                      <span><b>Dr. Ahsan Nazir</b></span>
                      <br/>
                      <span>Director ORIC</span>
                    </td>
                </tr>
            </table>');
        }
    }
    // for academic students
    else{
        // set other content
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(85,6,"SAMPLE SUBMITTED AND IDENTIFIED BY SUPPLIER AS:",0,1,'L');
        $pdf->WriteHTML('<br/>');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"SUBMITTED BY:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer['name']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ORDER TYPE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer_order['type']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"REGISTRATION NO:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,strtoupper($customer['reg_no']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"INSTITUTE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer['institute']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"DEPARTMENT:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucwords($customer['department']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"TOPIC OF STUDY:",0,0,'L');
        if(empty($customer['topic_of_study'])){
            $topic_of_study = "N/A";
        }
        else{
            $topic_of_study = ucfirst($customer['topic_of_study']);
        }
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,$topic_of_study,0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ADDRESS:",0,0,'L');
        if(empty($customer['address'])){
            $address = "N/A";
        }
        else{
            $address = ucfirst($customer['address']);
        }
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,$address,0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"CITY:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucfirst($customer['city']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ORIGIN COUNTRY:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,"Pakistan",0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"CONTACT DETAILS:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,"Phone: ".$customer['phone'],0,1,'L');
        $pdf->Cell(40);
        $pdf->Cell(60,6,"Email: ".$customer['email'],0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"ARRIVAL TIME:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $arrival_time = date('d-m-Y h:i:s A',strtotime($customer['creation_time']));
        $pdf->Cell(60,6,$arrival_time,0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"SAMPLE TYPE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,$sample['sample_type'],0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"COLOR:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,ucfirst($sample['sample_color']),0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"STYLE:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        if(!empty($sample['sample_style'])) {
            $pdf->Cell(60, 6,$sample['sample_style'], 0, 1, 'L');
        }
        else{
            $pdf->Cell(60, 6,"N/A", 0, 1, 'L');
        }
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"WEIGHT:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        if(!empty($sample['sample_weight'])) {
            $pdf->Cell(60, 6,$sample['sample_weight'], 0, 1, 'L');
        }
        else{
            $pdf->Cell(60, 6,"N/A", 0, 1, 'L');
        }
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"NO OF TESTS:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        $pdf->Cell(60,6,$sample['no_of_tests'],0,1,'L');
        $pdf->SetFont('Arial',"B",8);
        $pdf->Cell(40,6,"TESTS:",0,0,'L');
        $pdf->SetFont('Arial',"",8);
        foreach($test_names as $testname){
             $pdf->Cell(60,5,$testname, 0, 1, 'L');
             $pdf->Cell(40);
        }
        $pdf->Cell(0,0,'',0,1); // same as Ln(1)
        $pdf->SetY(186);
        if(!empty($sample['sample_image'])){
            $pdf->WriteHTML('<table width="100%">
            <tr>
                <td width="32%"></td>
                <td width="36%" style="border:3px solid;border-color:grey;"><img style="width:36%;height:180px;" src="../../includes/samples-pics/' . $sample['sample_image'] . '" alt="sample image"/></td>
                <td width="32%" style="padding-top:160px;padding-left:51px;">
                    <span style="font-size:10px;">
                    Signed on the behalf of:<br/>
                    National Textile Research Centre
                    </span>
                </td>
            </tr>
            </table>
            <table width="100%">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;height:60px;border-bottom:1px solid;">
                    </td>
                </tr>
            </table>
            <table width="100%" style="font-size:10px;">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;text-align:center;">
                      <span><b>Dr. Ahsan Nazir</b></span>
                      <br/>
                      <span>Director ORIC</span>
                    </td>
                </tr>
            </table>');
        }
        else{
            $pdf->WriteHTML('<table width="100%">
            <tr>
                <td width="32%"></td>
                <td width="36%" style="height:180px;"></td>
                <td width="32%" style="padding-top:160px;padding-left:51px;">
                    <span style="font-size:10px;">
                    Signed on the behalf of:<br/>
                    National Textile Research Centre
                    </span>
                </td>
            </tr>
            </table>
            <table width="100%">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;height:60px;border-bottom:1px solid;">
                    </td>
                </tr>
            </table>
            <table width="100%" style="font-size:10px;">
                <tr>
                    <td style="width:76%;"></td>
                    <td style="width:24%;text-align:center;">
                      <span><b>Dr. Ahsan Nazir</b></span>
                      <br/>
                      <span>Director ORIC</span>
                    </td>
                </tr>
            </table>');
        }
    }

    // counting test number to use for each test
    $test_count = 1;
    // iterate over sample test results
    foreach($test_names as $testname){
        
        $test_table = uglify_fieldname($testname) . '_' . 'test'; 
        $sample_test = get_test($sample_id,$test_table);
        $pdf->AddPage();

        $pdf->SetFont('Arial', "B", 8);
        $pdf->Cell(0,20,'',0,1);
        $pdf->Cell(130);
        $pdf->Cell(30, 5, "REPORT NUMBER:", 0, 0, 'L');
        $pdf->SetFont('Arial', "", 8);
        $pdf->Cell(22, 5, $customer_order['customer_id'], 0, 1, 'L');
        $pdf->Cell(130);
        $pdf->SetFont('Arial', "B", 8);
        $pdf->Cell(30, 5, "PAGE NO:", 0, 0, 'L');
        $pdf->SetFont('Arial', "", 8);
        $pdf->Cell(22, 5, $pdf->PageNo(), 0, 1, 'L');
        // setting test results content
        $pdf->Cell(0,10,"", 0,1);
        $pdf->WriteHTML('<table>
            <tr>
                <td><h4>'. $test_count++ . '.</h4></td>
                <td><h4 style="padding-bottom:2px;border-bottom:1px solid;">'. $testname . '</h4></td>
            </tr>
        </table>');
        $pdf->Cell(0,4,"", 0,1);

        // test standard
        $pdf->SetFont('Arial', "B", 9);
        $pdf->Cell(70,5,"Test Standard",0,1);
        $pdf->Cell(0,1,"", 0,1);
        $pdf->WriteHTML('<span style="font-family:Arial;font-size:12px;">' . $sample_test['test_standard'] .  '</span><br/>');
        $pdf->Cell(0,1,'',0,1);
        
        // test conditions
        $pdf->SetFont('Arial', "B", 9);
        $pdf->Cell(70,5,"Test Conditions",0,1);
        $pdf->Cell(0,1,"", 0,1);
// Note Only if we are using FPDF
//  Don't use UTF-8 encoding. Standard FPDF fonts use ISO-8859-1 or Windows-1252.
// It is possible to perform a conversion to ISO-8859-1 with utf8_decode(): $str = utf8_decode($str);
// But some characters such as Euro won't be translated correctly.
// If the iconv extension is available, the right way to do it is the following: $str = iconv('UTF-8', 'windows-1252', $str);
//  we can also use entity names like &deg;
//  $degree = html_entity_decode("&deg;",ENT_XHTML,"ISO-8859-1");
//  Or we can also use hexadecimal notation of character entities

        $pdf->WriteHTML('<span style="font-family:Arial;font-size:12px;">Temperature:&nbsp;&nbsp;' . $sample_test['temperature'] .  ' &deg;C</span><br/>');
        $pdf->Cell(0,1,'',0,1);
        $pdf->WriteHTML('<span style="font-family:Arial;font-size:12px;">Humidity:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $sample_test['humidity'] . ' %</span><br/>');
        $pdf->Cell(0,1,'',0,1);
        // Test Results
        if($sample_test['test_result']){
            $pdf->SetFont('Arial', "B", 9);
            $pdf->Cell(70,5,"Test Outcomes",0,1);
            $pdf->Cell(0,1,"", 0,1);
            $pdf->SetFont('Arial', "", 9);
            $pdf->WriteHTML($sample_test['test_result']);
            $pdf->Cell(0,1,'',0,1);
        }
        else{
            $pdf->SetFont('Arial', "B", 9);
            $pdf->Cell(70,5,"Test Outcomes",0,1);
            $pdf->Cell(0,1,"", 0,1);
            $pdf->SetFont('Arial', "", 9);
            $pdf->Cell(20,5,"Test results are available on the attached file",0,1);
        }
        // Test Conditions
        if($sample_test['test_conditions']){
            $pdf->SetFont('Arial', "B", 9);
            $pdf->Cell(70,5,"Notes",0,1);
            $pdf->Cell(0,1,"", 0,1);
            $pdf->SetFont('Arial', "", 9);
            $pdf->WriteHTML($sample_test['test_conditions']);
        }
     }

     // end report message
      $pdf->SetFont("Courier","B",12);
      $pdf->Cell(0,20,"", 0,1);
      $pdf->Cell(184,6,"-------End of Report-------",0,1,'C');
    // output pdf
    $pdf->Output('sample_report','I');

?>