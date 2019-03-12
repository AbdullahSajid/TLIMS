<?php 
// https://stackoverflow.com/questions/5116421/require-once-failed-to-open-stream-no-such-file-or-directory
if(!isset($expected_date)){
    header("Location: ../index.php");
    exit;
}
require_once("../../includes/config.php");

date_default_timezone_set('Asia/Karachi');
$current_datetime = date('d-m-Y h:i:s A', time());
/* we can send exact expected date like this
$expected_completion_date = date('d-m-Y', strtotime($expected_date));
but we are sending an expected date by adding 1 day furthur. so that a report may be completed before time in case of late generation */
$expected_completion_date = date('d-m-Y', strtotime($expected_date . ' +1 day'));
$name_of_tests = implode(', ',$all_tests);

$text = <<<EOT
National Textile Research Center

 Dear valued customer, Your sample with id {$customer_id} has been submitted to NTRC at {$current_datetime} 
 by {$c_name}. The following tests will be conducted.
 {$name_of_tests}.
 
 The expected date of completion of the report is {$expected_completion_date}. You can contact NTRC in case of any query about the report completion time
 For any queries, visit (http://www.ntu.edu.pk/) NTU website.
                       
EOT;

try{
//prepare email message
    /*  $message = (new Swift_Message())
     ->setSubject('Test of Swift Mailer')
     ->setFrom(['mehar.abdullah13@gmail.com' => 'NTRC'])
     //->setTo(['testing@foundationphp.com' => 'David Powers'])
     ->addTo('mehar.abdullah13@zoho.com', 'Abdullah Sajid')
     ->setBody('This is a test of Swift Mailer');
     echo $message->toString();*/
    // static methods mentioned in the swift mailer course are now depricated so we have to use the constructors
    $message = (new Swift_Message())
        ->setSubject('NTRC Sample Submission Mail')
        ->setFrom($from)
        ->addTo($email);

    //embed image
    $ntrc_logo = $message->embed( Swift_Image::fromPath(__DIR__ . '/../../assets/img/ntrc.png'));
    $ntu_logo = $message->embed( Swift_Image::fromPath(__DIR__ . '/../../assets/img/ntu.jpg'));

$html = <<<EOT
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>NTRC Mail</title>
</head>
<body bgcolor="#EBEBEB" link="#B64926" vlink="#FFB03B">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EBEBEB">
<tr>
<td>
<table width="600" align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr>
<td style="padding-top: 0.5em">
<h1 style="font-family: 'Lucida Grande', 'Lucida Sans Unicode', Verdana, sans-serif; color: #0E618C; text-align: center; border-bottom: solid 4px;">National Textile Research Center</h1>
</td>
</tr>
<tr>
<td style="font-family: 'Lucida Grande', 'Lucida Sans Unicode', Verdana, sans-serif; color: #1B1B1B; font-size: 14px; padding: 0 1em 1em 1em">
<p>
    Dear customer, Your sample with id <span style="color:#0E618C;">{$customer_id}</span> has been submitted to NTRC at {$current_datetime} by {$c_name}. The following test(s) will be conducted.
</p>
{$name_of_tests}.
<p>
The expected date of completion of the report is {$expected_completion_date}. You can contact NTRC in case of any query about the report completion time. 
For any queries, visit <a href="http://www.ntu.edu.pk/" target="_blank" style="text-decoration: none; font-weight: bold">NTU website</a>.
</p>
<p>
Best Regards, <span style="display: block;">NTRC Reception, NTU</span>
<span style="display: block;">Phone# +92 337 7401455</span>
</p>
<p style="border-top: solid grey 1px;padding-top:0.8em;">
<img style="float:left;width:10%;height:52px;" src="$ntrc_logo" alt="National Textile Research Center logo">
 
<img style="float:right;width:10%;height:49px;" src="$ntu_logo" alt="National Textile University logo">
 
<div style="clear:both;display:inline-block;font-size:12px;color:grey;text-align: center;width:50%;margin-left:13%;margin-right:13%;margin-top:-9px;">
&copy; NTRC, National Textile University, Manawala, Sheikupura Road, Faisalabad, Pakistan
</div>  
 
</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
EOT;
    
       $message->setBody($html, 'text/html');
//    $message->setBody($html, 'text/html')
//        ->addPart($text, 'text/plain');
    
//        ->setReadReceiptTo(['mehar.abdullah13@gmail.com'])
//        ->setBody('This message was sent using the Swift Mailer SMTP transport');

    // attach local file
    /*   $attachment = Swift_Attachment::fromPath('./images/a.png',
           'image/png');
       $attachment->setFilename('mascot.png');
       $message->attach($attachment);*/

    // validate email address and setting reply-to-header
    /* $validator = new EmailValidator();
     if($validator->isValid("mehar.abdullah13@gmail.com", new RFCValidation())){
         $message->setReplyTo("mehar.abdullah13@gmail.com");
     }*/

    // create the transport
    $transport = (new Swift_SmtpTransport($smtp_server,587,'tls'))
        ->setUsername($username)
        ->setPassword($password);
    $mailer = new Swift_Mailer($transport);
    $is_email_sent = $mailer->send($message);

}
catch (Exception $e){
    $error[] = $e;
}
?>
