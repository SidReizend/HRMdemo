<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LeaveCreditDetails extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('leaves', '', TRUE);
    }

    public function comp() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $talentId = $session_data['talentId'];

            $data['creditDetailsComp'] = $this->leaves->getLeaveCreditDetailsComp($talentId);

            $this->load->view('leaveManagement/leaveCreditDetailsComp', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }
    public function EL() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $talentId = $session_data['talentId'];

            $data['creditDetailsEL'] = $this->leaves->getLeaveCreditDetailsEL($talentId);

            $this->load->view('leaveManagement/leaveCreditDetailsEL', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

}
