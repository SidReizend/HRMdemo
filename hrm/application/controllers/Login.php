<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->session->unset_userdata('logged_in');
        session_destroy();
        $this->load->helper(array('form'));
        $this->load->view('login/login');
    }

}
