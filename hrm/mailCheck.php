 <?php
// the message
$msg = "Mail Checking";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
$result = mail("sunil@reizend.in","Checking simple mail",$msg);
var_dump($result);
?> 