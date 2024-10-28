<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AllTalentsYearlyReport extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('talent', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
          
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $yearArray = array(
                '' => '[SELECT YEAR]'
            );
            $earliest_year = 2015;
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = $year;
            }
            $data['selectedYear'] = $this->input->post('year');
            $data['years'] = $yearArray;
            $YearReport = array();
           
            if ($this->input->post()) {
                $year = $this->input->post('year');
               
                $talent_id=null;
                if($session_data['is_talent'])
                {
                 $talent_id=$session_data['talentId'];
                }
                
                $YearReport = $this->talentAttendenceSheet->getAllYearReport($year,$talent_id);
            }
            $data['YearReport'] = $YearReport;
            $this->load->view('attendenceManagement/allTalentYearlyReport', $data);
        } else {
//If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

}
