<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AbscondList extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->output->enable_profiler(TRUE);

        $this->load->model('notifications');

        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];

            if (false === $session_data['is_management']) {
                $this->session->set_flashdata('flashError', 'Invalid Attempt');
                redirect('home', 'refresh');
            }

            $this->load->model('talentAttendenceSheet');

            $absconding_list = $this->talentAttendenceSheet->getAbscondingList(7);

            
            $data['absconding_list'] = $absconding_list;

            $this->load->view('home/abscond_list', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
            //$this->load->view('home');
        }
    }

}
