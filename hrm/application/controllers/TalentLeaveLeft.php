<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TalentLeaveLeft extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('leaves', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        error_reporting(1);
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $talents = $this->talent->getTalents();
            $talentLeaveLeft = array();
            foreach ($talents as $talent) {
                $talentLeaveLeft[$talent['id']]['talent_code'] = $talent['talent_code'];
                $talentLeaveLeft[$talent['id']]['name'] = $talent['name'];
                $leaveLeft = $this->leaves->getMyLeavesLeft($talent['id']);
                foreach ($leaveLeft as $leaves) {
                    $startDayOfMonth = $this->defaultValues->getValue(3);
                    $leaveUsedPlanned = $this->leaves->getMyPlannedLeaves($talent['id'], $startDayOfMonth,$leaves['id']);
                    if (1 == $leaves['id']) {
                        $talentLeaveLeft[$talent['id']]['comp'] = $leaves['leave_left'];
                        $talentLeaveLeft[$talent['id']]['comp_planned'] = $leaveUsedPlanned->number_of_leaves;
                    } else if (2 == $leaves['id']) {
                        $talentLeaveLeft[$talent['id']]['el'] = $leaves['leave_left'];
                        $talentLeaveLeft[$talent['id']]['el_planned'] = $leaveUsedPlanned->number_of_leaves;
                    } else if (3 == $leaves['id']) {
                        $talentLeaveLeft[$talent['id']]['emergency'] = $leaves['leave_left'];
                    } else if ($talent['gender'] == 'M') {
                        $talentLeaveLeft[$talent['id']]['paternity'] = $leaves['leave_left'];
                        $talentLeaveLeft[$talent['id']]['maternity'] = 0;
                    } else {
                        $talentLeaveLeft[$talent['id']]['maternity'] = $leaves['leave_left'];
                        $talentLeaveLeft[$talent['id']]['paternity'] = 0;
                    }
                }
            }
            $data['talentLeaveLeft'] = $talentLeaveLeft;
            $this->load->view('leaveManagement/talentLeaveLeft', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function compOffDate() {
        $session_data = $this->session->userdata('logged_in');
        $this->load->helper(array('form'));
        $currentMonth = date('m');
        if ($currentMonth <= 3) {
            $SelectBoxyear = date('Y') - 1;
        } else {
            $SelectBoxyear = date('Y');
        }
        $selectedYear = $this->input->post('year') == '' ? $SelectBoxyear : $this->input->post('year');

        $subordinates = $this->talent->getSubordinatesByStatus($session_data['talentId'], $session_data['is_management'], 0);
        $talentsDropdown = array("" => "[SELECT-Talent]");
        foreach ($subordinates as $subord) {
            $id = $subord['id'];
            $talentsDropdown[$id] = $subord['name'];
        }
        $selectedTalent = null !== $this->input->post('talent') ? $this->input->post('talent') : null;
        $compOffId = $this->input->post('compOffId');
        if ($compOffId) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->post('newDate'));
            $compOffDate = $date->format('Y-m-d');
            $compDetails = $this->leaves->getCompOffDetailsById($compOffId);
            $selectedTalent = $compDetails->talent;
            $updateData = array(
                'date_of_compoff' => $compOffDate,
            );
            $this->db->where('id', $compOffId);
            $queryResultUpdate = $this->db->update('talent_compoff', $updateData);
            if ($queryResultUpdate) {
                $this->session->set_flashdata('flashSuccess', 'Comp Off date changed successfully');
            } else {
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        }
        $data['talentsDropdown'] = $talentsDropdown;
        $data['selectedTalent'] = $selectedTalent;
        $data['creditDetailsComp'] = $this->leaves->getLeaveCreditDetailsComp($selectedTalent);
        $this->load->view('leaveManagement/compOffDate', $data);
    }

}
