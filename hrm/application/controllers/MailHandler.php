<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailHandler
 *
 * @author sibin_francis
 */
class MailHandler {

    protected $CI;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct() {
        // Assign the CodeIgniter super-object
        $this->CI = & get_instance();
    }

    public function sendEmail($mail_type, $from, $to_group, $subject, $cc, $bcc, $content) {

        $ci = &get_instance();
        $ci->load->model('defaultValues');
        $hrmName = $ci->defaultValues->getValue(19);
        $config = Array(
            'mailtype' => $mail_type,
        );
        $this->CI->load->library('email', $config);
        $this->CI->email->from($from, $hrmName);
        $this->CI->email->to($to_group);
        if ($cc !== '') {
            $this->CI->email->cc($cc);
        }
        if ($bcc != '') {
            $this->CI->email->bcc($bcc);
        }

        $this->CI->email->subject($subject);
        $this->CI->email->message($content);
        $this->CI->email->send();
    }

}
