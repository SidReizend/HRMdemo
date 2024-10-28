<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ClaimSalarySettlement extends CI_Controller {

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
            $settlementDropdown = array(
                '' => '[SEL. Settlement]',
                'Account' => 'Account',
                'Salary' => 'Salary'
            );
            $categorys = $this->claim->getClaimCategory();
            $categoryArray =  array();
            foreach ($categorys as $category) {
                $id = $category['id'];
                $categoryArray[$id]['value'] = $category['value'];
                $categoryArray[$id]['planned'] = '0';
                $categoryArray[$id]['settled'] = '0';
            }
            $data['isResigned'] = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
            $talents = $this->talent->getTalentsAllByResignStatus($data['isResigned']);
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $currentMonth = date('m');
            if ($currentMonth <= 3) {
                $SelectBoxyear = date('Y') - 1;
            } else {
                $SelectBoxyear = date('Y');
            }
            $data['selectedYear'] = $this->input->post('year') == '' ? $SelectBoxyear : $this->input->post('year');
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['selectedYear'] = $this->input->post('year');
            $data['years'] = $yearArray;
            $selectedSettlement = $this->input->post('settlement');
            $data['categoryArray'] = $categoryArray;
            $data['selectedSettlement'] = $selectedSettlement;
            $data['statusDropdown'] = $statusDropdown;
            $data['settlementDropdown'] = $settlementDropdown;
            $data['selectedTalent'] = $this->input->post('talent');
            $data['talentsDropdown'] = $talentsDropdown;
            $data['claims'] = $this->claim->getClaimsByResignedStatus('Salary', $data['selectedYear'],$data['isResigned'],'',$data['selectedTalent']);

            $this->load->view('claimManagement/claimSalarySettlement', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function statusUpdate() {
        //using url for get id
        $this->load->helper('url');
        $id = $this->input->get('id');
        $status = $this->input->get('status');
        $updateData = array(
            'status' => $status
        );
        $this->db->where('id', $id);
        $this->db->update('claims', $updateData);
        echo 1;
    }

}
