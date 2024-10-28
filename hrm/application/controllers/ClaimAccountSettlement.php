<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ClaimAccountSettlement extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('claim', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('projects', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $statusDropdown = array(
                '' => 'All',
                '0' => 'Planned',
                '1' => 'Settled'
            );
            $currentMonth = date('m');
            if ($currentMonth <= 3) {
                $SelectBoxyear = date('Y') - 1;
            } else {
                $SelectBoxyear = date('Y');
            }
            $data['selectedYear'] = $this->input->post('year') == '' ? $SelectBoxyear : $this->input->post('year');
            $yearArray = array(
                date('Y') => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $categorys = $this->claim->getClaimCategory();
            $categoryArray =  array();
            foreach ($categorys as $category) {
                $id = $category['id'];
                $categoryArray[$id]['value'] = $category['value'];
                $categoryArray[$id]['planned'] = '0';
                $categoryArray[$id]['settled'] = '0';
            }
            $data['categoryArray'] = $categoryArray;
            $data['isResigned'] = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
            $talents = $this->talent->getTalentsAllByResignStatus($data['isResigned']);
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $yearArray[($year)] = ($year) . '-' . ($year + 1);
            $yearArray[($year+1)] = ($year+1) . '-' . ($year + 2);
            $data['years'] = $yearArray;
            $selectedSettlement = $this->input->post('settlement');
            $data['selectedSettlement'] = $selectedSettlement;
            $data['statusDropdown'] = $statusDropdown;
            $data['selectedTalent'] = $this->input->post('talent');
            $data['talentsDropdown'] = $talentsDropdown;
            $data['claims'] = $this->claim->getClaimsByResignedStatus('Account', $data['selectedYear'], $data['isResigned'], '',$data['selectedTalent']);

            $this->load->view('claimManagement/claimAccountSettlement', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function excelDownload() {

        
        $this->load->helper('url');
        $settlement = $this->input->get('settlement');
        $resigned = $this->input->get('resigned');
        $year = $this->input->get('year');
        $talent = $this->input->get('talent');
        $claims = $this->claim->getClaimsByResignedStatus($settlement, $year, $resigned, '',$talent);
        $heading = array(
            0 => 'SL. No.',
            1 => 'Claim Id',
            2 => 'Talent',
            3 => 'Settlement',
            4 => 'Project',
            5 => 'Amount',
            6 => 'Date of approval',
            7 => 'Approved by',
            8 => 'Creating User',
            9 => 'Status',
            10 => 'Category',
        );
        $slno = 0;
        $content = array();
        foreach ($claims as $claim) {
            $content[$slno] = array(
                0 => $slno + 1,
                1 => $claim['claim_id'],
                2 => $claim['name'],
                3 => $claim['settlement'],
                4 => $claim['project'],
                5 => $claim['amount'],
                6 => date('d/m/Y', strtotime($claim['date_of_approval'])),
                7 => $claim['approved_by'],
                8 => $claim['created_by'],
                9 => $claim['status'] == '0' ? 'Planned' : 'Settled',
                10 => $claim['category'],
            );
            $slno++;
        }
        $data['heading'] = $heading;
        $data['tableName'] = $settlement . ' Settlement Claims';
        $data['content'] = $content;
        $data['slno'] = $slno;
        $this->load->view('excel/excel', $data);
    }

}
