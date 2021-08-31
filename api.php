<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$data = $_REQUEST;

include("config.php");

$invoice_number = random_int(100000, 999999);

$tabledata_press_only = '';
$tabledata_press_dry= '';

if(!empty($_REQUEST['press_only'])){
    $tabledata_press_only .= '<tr><td  align="center" valign="top" colspan="5">Press Only</td></tr>';
    $tabledata_press_only .= '<tr>';
    foreach($_REQUEST['press_only'] as $po){
        $tabledata_press_only .= '<td align="center" valign="top">'.$po['book_title'].'</td>';
        $tabledata_press_only .= '<td align="center" valign="top">#'.$po['issue'].'</td>';
        $tabledata_press_only .= '<td align="center" valign="top">'.$po['issue_date'].'</td>';
        $tabledata_press_only .= '<td align="center" valign="top">'.$po['declared_value'].'</td>';
        $tabledata_press_only .= '<td align="center" valign="top">$'.$po['price'].'</td>';
    }
    $tabledata_press_only .= '</tr>';

}
if(!empty($_REQUEST['press_dry'])){
    
    $tabledata_press_dry .= '<tr ><td  align="center" valign="top" colspan="5">Press and Dry</td></tr>';
    $tabledata_press_dry .= '<tr>';
    foreach($_REQUEST['press_dry'] as $pd){
        $tabledata_press_dry .= '<td align="center" valign="top">'.$pd['book_title'].'</td>';
        $tabledata_press_dry .= '<td align="center" valign="top">#'.$pd['issue'].'</td>';
        $tabledata_press_dry .= '<td align="center" valign="top">'.$pd['issue_date'].'</td>';
        $tabledata_press_dry .= '<td align="center" valign="top">'.$pd['declared_value'].'</td>';
        $tabledata_press_dry .= '<td align="center" valign="top">$'.$pd['price'].'</td>';
    }
    $tabledata_press_dry .= '</tr>';
}
//send email
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'ankitchugh006@gmail.com';                     //SMTP username
    $mail->Password   = 'yspSwVNmUcEF6fIz';                               //SMTP password
    $mail->Port = 587; // TLS only
    $mail->SMTPSecure = 'tls'; // ssl is depracated `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('ankitchugh006@gmail.com', 'Hero Trader');
    $mail->addAddress('info@herotrader.com', 'Joe User');     //Add a recipient 
  

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Form has been submitted';
    $mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title></title>
        <style></style>
    </head>
    <body>
        
<h1>Order Details</h1><br/><table cellpadding="0" cellspacing="0" height="100%" width="100%" border="1" id="bodyTable">
    
    '.$tabledata_press_only.$tabledata_press_dry.'<br/>
    
    
    
    </table><h1>User Details</h1><br/><table cellpadding="0" cellspacing="0" height="100%" width="100%" border="1" id="bodyTable">
        <tr>
            <td align="center" valign="top">Name:</td>
            <td align="center" valign="top">'.urldecode($_REQUEST['form_data']['name']).'</td>
        </tr>
        <tr>
            <td align="center" valign="top">Email:</td>
            <td align="center" valign="top">'.urldecode($_REQUEST['form_data']['email']).'</td>
        </tr>
        <tr>
            <td align="center" valign="top">Phone:</td>
            <td align="center" valign="top">'.urldecode($_REQUEST['form_data']['phone']).'</td>
        </tr>
        <tr>
            <td align="center" valign="top">Address:</td>
            <td align="center" valign="top">'.urldecode($_REQUEST['form_data']['address']).'</td>
        </tr>
        <tr>
            <td align="center" valign="top">City:</td>
            <td align="center" valign="top">'.urldecode($_REQUEST['form_data']['city']).'</td>
        </tr>
        <tr>
            <td align="center" valign="top">State:</td>
            <td align="center" valign="top">'.urldecode($_REQUEST['form_data']['state']).'</td>
        </tr>
         <tr>
            <td align="center" valign="top">Zip:</td>
            <td align="center" valign="top">'.urldecode($_REQUEST['form_data']['zip']).'</td>
        </tr>
    </table>';
   // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    
    $statement = $pdo->prepare("INSERT INTO mokomeme_comic (invoice_no, data) VALUES (?,?)");
    $statement->execute(array($invoice_number, json_encode($data)));
    //echo 'Message has been sent';
} catch (Exception $e) {
    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


//$last_id = $pdo->lastInsertId();
echo  $invoice_number;
exit();