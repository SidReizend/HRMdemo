<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('KnowledgeRepository', '', TRUE);
    }

    public function index() {
        $message = "Hi cron job worked";
        $mobile = 9020452331;
        
        $message = str_replace(" ", "%20", $message);
        $message = str_replace("&", "and", $message);
        $file_handle = fopen("http://sms.lyvee.com/sendsms?uname=orrisonkl&pwd=newpass&senderid=FLMCTY&to=$mobile&msg=$message&route=T", "r");
        fclose($file_handle);
    }

}
