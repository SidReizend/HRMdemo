<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class knowledgePortalReport extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('KnowledgeRepository', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));

            $subordinates = $this->talent->getSubordinates($session_data['talentId'], $session_data['is_management']);
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($subordinates as $subord) {
                $id = $subord['id'];
                $talentsDropdown[$id] = $subord['name'];
            }
            $statusDropdown = array(
                "" => "[SELECT Status]",
                "1" => "Pending",
                "2" => "Approved"
            );
            $selectedTalent = $this->input->post('talent');
            $selectedStatus = $this->input->post('status');
            //finds the first and last date of current Month
            $startDateOfCurrentMonth = date('Y-m-d', strtotime("first day of this month"));
            $endDateOfCurrentMonth = date('Y-m-d', strtotime("last day of this month"));

            if($this->input->post('startDate')) {
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('startDate'));
                $dateStart = $fromDate = $date->format('Y-m-d');
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('endDate'));
                $dateEnd = $fromDate = $date->format('Y-m-d');
            }

            $dateFrom = $this->input->post('startDate') ? $dateStart : $startDateOfCurrentMonth;
            $dateTo = $this->input->post('endDate') ? $dateEnd : $endDateOfCurrentMonth;

            $data['report'] = $this->KnowledgeRepository->getKnowledgePortals($dateFrom, $dateTo, $selectedTalent, $selectedStatus);
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['statusDropdown'] = $statusDropdown;
            $data['selectedStatus'] = $selectedStatus;
            $data['selectedStartDate'] = $dateFrom;
            $data['selectedEndDate'] = $dateTo;

            $this->load->view('talentKnowledgeProcess/knowledgePortalReport', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

}
