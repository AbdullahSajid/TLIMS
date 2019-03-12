<?php 
// https://stackoverflow.com/questions/5116421/require-once-failed-to-open-stream-no-such-file-or-directory
// the path will not work unless relative to the file 'add_user.php' calling this file 'send_email_to_user.php'
if(!isset($privileges)){
    header("Location: ../index.php");
    exit;
}
require_once("../../includes/config.php");

$lab_name = beautify_fieldname($privileges);
$text = <<<EOT
National Textile Research Center

Assalam o Alaikum, Hi {$full_name}. Congratulations to you for becoming a new user of T-LIMS web based software. Dr. Ahsan has granted you the privileges of {$lab_name}. Your can visit following url (http://localhost/LIMS_V2/public/admin/add_user.php) to sign in.

Your credentials are:
Username: {$user_name}
Password: {$pass_word}
 
For any queries, visit NTRC for detail.
                       
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
        ->setSubject('NTRC User Registration')
        ->setFrom($from)
        ->addTo($email);

    //embed image
    $image = $message->embed( Swift_Image::fromPath(__DIR__ . '/../../assets/img/ntrc.png'));

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
<td align="center">
<img src="$image" width="240" height="193"
 alt="National Textile Research Center logo">
</td>
</tr>
<tr>
<td style="font-family: 'Lucida Grande', 'Lucida Sans Unicode', Verdana, sans-serif; color: #1B1B1B; font-size: 14px; padding: 1em">
<p>
    Assalam o Alaikum, Hi {$full_name}. Congratulations to you for becoming a new user of T-LIMS web based software. Dr. Ahsan has granted you the privileges of {$lab_name}. Your can visit following <a href="http://10.0.10.111:8000/tlims/" target="_blank" style="text-decoration: none; font-weight: bold">url</a> to sign in.</p>
<p>
Your credentials are:<br/>
Username: {$user_name} <br/>
Password: {$pass_word} 
</p>
<p>
For any queries, visit NTRC.
</p>
<p>
Best Regards, <span style="display: block;">NTRC Admin, NTU</span>
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

    $message->setBody($html, 'text/html')
        ->addPart($text, 'text/plain');
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
