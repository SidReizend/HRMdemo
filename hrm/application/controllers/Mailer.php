<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mailer {

    function __construct() {
        $this->load->library('PHPMailer');
        $this->load->model('defaultValues', '', TRUE);
    }

    function sendMail($subject, $email, $body, $cc) {

        $hrmName = $this->defaultValues->getValue(19);
        $from = $this->defaultValues->getValue(6);
        $this->phpmailer->AddAddress($email);
        $this->phpmailer->AddCc($cc);
        $this->phpmailer->IsMail();
        $this->phpmailer->From = $from;
        $this->phpmailer->FromName = $hrmName;
        $this->phpmailer->IsHTML(true);
        $this->phpmailer->Subject = $subject;
        $this->phpmailer->Body = $body;
        $result = $this->phpmailer->Send();
        return $result;
    }

}
