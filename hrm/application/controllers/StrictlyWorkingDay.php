<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class StrictlyWorkingDay extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('StrictWorking', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $data['strictWorkingDays'] = $this->StrictWorking->getStrictWorkingDays();

            $this->load->view('strictlyWorkingDay/strictlyWorkings', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addStrictWorking() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['strictWorkingDays'] = $this->StrictWorking->getStrictWorkingDays();
            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->helper('date');

            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('dateToWork', 'Date to work', 'trim|required|xss_clean|callback_check_database_working');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateToWork'));
                $dateToWork = $date->format('Y-m-d');


                // Do insert operation here
                $insertUserData = array(
                    'date_to_work' => $dateToWork
                );
                $queryResult = $this->db->insert('strict_working_date', $insertUserData);
                if ($queryResult) {
                    //if success
                    $this->session->set_flashdata('flashSuccess', 'New working day is added successfully');
                    redirect('StrictlyWorkingDay', 'refresh');
                } else {
                    //failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $this->load->view('strictlyWorkingDay/strictlyWorkings', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    function check_database_working($date) {
        
        //Field validation succeeded.  Validate against database
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateToWork'));
        $dateToWork = $date->format('Y-m-d');

        //query the database
        $result = $this->StrictWorking->getDayToWork($dateToWork);
        if ($result) {
            $this->form_validation->set_message('check_database_working', 'Date already exists');
            return false;
        } else {
            return TRUE;
        }
    }
}
