<?php
//error_reporting(0);
$servername = $_SERVER['HTTP_HOST'];
$pathimg=$servername."/";
define("ROOT_PATH",$_SERVER['DOCUMENT_ROOT']);
define("UPLOAD_PATH","http://app.gambay.me/gambay");
define("BASE_PATH","http://app.gambay.me/gambay/");

define("SERVER_OFFSET","0");
$DB_HOST = 'localhost';
$DB_DATABASE = 'codebrew_gambay';
$DB_USER = 'root';
$DB_PASSWORD = 'codebrew2015';

//GCM
define("AUTH_KEY","AIzaSyCAi-kxIDNTPxdUS6UJTEOrA1dZ6cAEx2c");

/*define('SMTP_USER','pargat@code-brew.com');
define('SMTP_EMAIL','pargat@code-brew.com');
define('SMTP_PASSWORD','core2duo');
define('SMTP_NAME','Gambay');
define('SMTP_HOST','mail.code-brew.com');
define('SMTP_PORT','25');
*/
define('SMTP_USER','receipt-no-reply@gambay.me');
define('SMTP_EMAIL','receipt-no-reply@gambay.me');
define('SMTP_PASSWORD','PartyNstops1');
define('SMTP_NAME','Gambay');
define('SMTP_HOST','smtpout.secureserver.net');
define('SMTP_PORT','25');


