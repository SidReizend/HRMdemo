<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class RoleStream extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('roles', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['roelStreamDetails'] = $this->roles->getRoleStreams();

            $this->load->view('talentManagement/roleStream', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function saveShowReports() {
        /*
         * to set show_in_reports privilage
         */
        $menu = $this->input->get('menu');
        $check = $this->input->get('check');
        $updateData = array(
            'show_in_reports' => $check
        );
        $this->db->where('id', $menu);
        $queryResult = $this->db->update('role_stream', $updateData);
        return 1;
    }
    public function saveMonthlyProcess() {
        /*
         * to set show_in_reports privilage
         */
        $menu = $this->input->get('menu');
        $check = $this->input->get('check');
        $updateData = array(
            'monthly_process' => $check
        );
        $this->db->where('id', $menu);
        $queryResult = $this->db->update('role_stream', $updateData);
        return 1;
    }

}
