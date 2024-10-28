<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EncashmentCntrl extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('myOd', '', TRUE);
        $this->load->model('odType', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('holiday', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('TalentAttendenceSheet', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $currentMonth = date('m');
            if ($currentMonth <= 3) {
                $SelectBoxyear = date('Y') - 1;
            } else {
                $SelectBoxyear = date('Y');
            }
            $selectedYear = $this->input->post('year') == '' ? $SelectBoxyear : $this->input->post('year');
            $isResigned = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
            //talents under the officer
            $subordinates = $this->talent->getSubordinatesByStatus($session_data['talentId'], $session_data['is_management'], $isResigned);
            $talentsDropdown = array("" => "[SELECT-Talent]");
            foreach ($subordinates as $subord) {
                $id = $subord['id'];
                $talentsDropdown[$id] = $subord['name'];
            }
            $selectedTalent = null !== $this->input->post('talent') ? $this->input->post('talent') : null;
            $data['compOffDetails'] = $this->TalentAttendenceSheet->getEncashmentForApprovalFinYearBased($session_data['talentId'], $selectedYear, $isResigned, $selectedTalent);
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['selectedYear'] = $selectedYear;
            $data['yearArray'] = $yearArray;
            $data['isResigned'] = $isResigned;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['redirect'] = "";
            $this->load->view('encashment/encashment_view', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function allEncashment() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $currentMonth = date('m');
            if ($currentMonth <= 3) {
                $SelectBoxyear = date('Y') - 1;
            } else {
                $SelectBoxyear = date('Y');
            }
            $selectedYear = $this->input->post('year') == '' ? $SelectBoxyear : $this->input->post('year');
            $isResigned = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
            //talents under the officer
            $subordinates = $this->talent->getSubordinatesByStatus($session_data['talentId'], $session_data['is_management'], $isResigned);
            $talentsDropdown = array("" => "[SELECT-Talent]");
            foreach ($subordinates as $subord) {
                $id = $subord['id'];
                $talentsDropdown[$id] = $subord['name'];
            }
            $selectedTalent = null !== $this->input->post('talent') ? $this->input->post('talent') : null;
            $data['compOffDetails'] = $this->TalentAttendenceSheet->getEncashmentForApprovalFinYearBasedAll($session_data['talentId'], $selectedYear, $isResigned, $session_data['is_management'], $selectedTalent);
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['selectedYear'] = $selectedYear;
            $data['yearArray'] = $yearArray;
            $data['isResigned'] = $isResigned;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['redirect'] = "/allEncashment";
            $this->load->view('encashment/encashment_view', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function encashmentApprove() {
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $attendanceId = $this->uri->segment(4);
        $redirect = $this->uri->segment(5);
        $talentArray = $this->TalentAttendenceSheet->getTalentIdUsingAttendanceId($attendanceId);

        foreach ($talentArray as $talents) {
            $talent = $talents['talent_id'];
            $attendanceDate = $talents['attendance_date'];
        }
        $updateData = array(
            'productive_hrs' => '8:00:00',
            'encashment' => 1
        );
        $this->db->where('id', $attendanceId);
        $queryResult = $this->db->update('talent_attendance', $updateData);
        if ($queryResult) {
            $talentDetails = $this->talent->getTalentById($talent);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }
            //creating notification for reporting manager
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Green',
                'message' => 'Approved Your Encashment for the date ' . date('d/m/Y', strtotime($attendanceDate)),
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);
            $this->session->set_flashdata('flashSuccess', 'Encashment Approved successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirect) {
            redirect('encashmentCntrl/allEncashment', 'refresh');
        } else {
            redirect('encashmentCntrl', 'refresh');
        }
    }

    public function encashmentDecline() {
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $attendanceId = $this->uri->segment(4);
        $redirect = $this->uri->segment(5);

        $updateData = array(
            'encashment' => 2
        );
        $this->db->where('id', $attendanceId);
        $queryResult = $this->db->update('talent_attendance', $updateData);
        if ($queryResult) {
            $this->session->set_flashdata('flashSuccess', 'Encashment declined successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirect) {
            redirect('encashmentCntrl/allEncashment', 'refresh');
        } else {
            redirect('encashmentCntrl', 'refresh');
        }
    }

    public function savenote() {
        $alertify_text = $this->input->get('alertify_text');
        $id = $this->input->get('id');
        $updateData = array(
            'encashment_note' => $alertify_text
        );
        $this->db->where('id', $id);
        $queryResult = $this->db->update('talent_attendance', $updateData);
        if ($queryResult) {
            echo 1;
        } else {
            echo 0;
        }
    }

}
