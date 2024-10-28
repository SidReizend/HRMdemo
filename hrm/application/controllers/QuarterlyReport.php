<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class QuarterlyReport extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        //$this->output->enable_profiler(TRUE);
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $this->load->helper(array('form'));
            $subordinates = $this->talent->getSubordinateswithShowInReport($talentId, $is_management);
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($subordinates as $subord) {
                $id = $subord['id'];
                $talentsDropdown[$id] = $subord['name'];
            }
            $selectedTalent = null !== $this->input->post('talent') ? $this->input->post('talent') : null;
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $quarterArray = array();
            $quarterArray['1'] = 'Q1';
            $quarterArray['2'] = 'Q2';
            $quarterArray['3'] = 'Q3';
            $quarterArray['4'] = 'Q4';

            $data['selectedQuarter'] = $this->input->post('quarter');
            $data['selectedYear'] = $this->input->post('year');
            $data['years'] = $yearArray;
            $data['quarters'] = $quarterArray;
            $quarterReport = array();
            if ($this->input->post()) {
                $talentId = $this->input->post('talent');
                $quarter = $this->input->post('quarter');
                $year = $this->input->post('year');
                $quarterReport = $this->talentAttendenceSheet->reportOfTheQuarter($quarter, $year, $talentId);
            }
            $data['selectedTalent'] = $selectedTalent;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['quarterReport'] = $quarterReport;
            $this->load->view('attendenceManagement/quarterlyReport', $data);
        } else {
//If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

}
