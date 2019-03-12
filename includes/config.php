<?php

/*
 * $loader needs to be a relative path to an autoloader script.
 * Swift Mailer's autoloader is swift_required.php in the lib directory.
 * If you used Composer to install Swift Mailer, use vendor/autoload.php.
 */
$loader = __DIR__ . '/vendor/autoload.php';

require_once $loader;

/*
 * Login details for mail server
 To send email from gmail besides other settings you also need to set username as your full gmail address(like mehar.abdullah13@gmail.com) and you have to turn on less secure apps in your settings and for zoho set the username as (mehar.abdullah13)
 */

/*$smtp_server = 'smtp.zoho.com';
$username = 'mehar.abdullah13@zoho.com';
$password = '';*/
$smtp_server = 'smtp.office365.com';
$username = 'ntrc@ntu.edu.pk';
$password = '';
/*
 * Email addresses for testing
 * The first two are associative arrays in the format
 * ['email_address' => 'name']. The rest contain just
 * an email address as a string.
 */
$from = ['ntrc@ntu.edu.pk' => 'NTRC, NTU Faisalabad'];
$test1 = ['mehar.abdullah13@gmail.com' => 'Mehar Abdullah'];
$testing = '';
$test2 = '';
$test3 = '';
$secret = '';
$private = '';

?>
