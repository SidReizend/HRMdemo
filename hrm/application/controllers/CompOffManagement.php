<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CompOffManagement extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('myOd', '', TRUE);
        $this->load->model('odType', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('holiday', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('TalentAttendenceSheet', '', TRUE);
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
            $data['compOffDetails'] = $this->TalentAttendenceSheet->getCompOffForApprovalFinYearBased($session_data['talentId'], $selectedYear, $isResigned, $selectedTalent);
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['selectedYear'] = $selectedYear;
            $data['isResigned'] = $isResigned;
            $data['yearArray'] = $yearArray;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['redirect'] = "";
            $this->load->view('compOffManagement/compOffManagement', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function allCompOff() {
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
            $data['compOffDetails'] = $this->TalentAttendenceSheet->getCompOffForApprovalFinYearBasedAll($session_data['talentId'], $selectedYear, $isResigned, $session_data['is_management'], $selectedTalent);
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['selectedYear'] = $selectedYear;
            $data['isResigned'] = $isResigned;
            $data['yearArray'] = $yearArray;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['redirect'] = "/allCompOff";
            $this->load->view('compOffManagement/compOffManagement', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function compOffApprove() {
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $attendanceId = $this->uri->segment(4);
        $redirect = $this->uri->segment(5);

        $talentArray = $this->TalentAttendenceSheet->getTalentIdUsingAttendanceId($attendanceId);

        foreach ($talentArray as $talents) {
            $talent = $talents['talent_id'];
            $date = $talents['attendance_date'];
        }

        $this->db->trans_begin();
        $updateData = array(
            'holiday_hr_status' => '1',
            'processed_by_status' => $session_data['talentId']
        );
        $this->db->where('id', $attendanceId);
        $queryResult = $this->db->update('talent_attendance', $updateData);


        $leaveCreditStatus = $this->leaves->checkCompExist($talent, $date);
        if ($leaveCreditStatus) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Already approved');
            if ($redirect) {
                redirect('compOffManagement/allCompOff', 'refresh');
            } else {
                redirect('compOffManagement', 'refresh');
            }
            return;
        }

        if ($queryResult) {
            $talentDetails = $this->talent->getTalentById($talent);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }
            //creating notification for reporting manager
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Green',
                'message' => 'Comp off credicted for the date ' . date('d/m/Y', strtotime($date)),
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);
            //inserting compoff to table for managing talent's compoff fo leave purpose
            $insertTalentCompoff = array(
                'talent' => $talent,
                'date_of_compoff' => $date
            );
            //inserting
            $queryResult = $this->db->insert('talent_compoff', $insertTalentCompoff);
            $this->db->set('leave_left', 'leave_left+1', False);
            $this->db->where('leave_type', 1);
            $this->db->where('talent', $talent);
            $queryResult = $this->db->update('talent_leave_left');

            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Comp-Off given successfully');
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirect) {
            redirect('compOffManagement/allCompOff', 'refresh');
        } else {
            redirect('compOffManagement', 'refresh');
        }
    }

    public function compOffDecline() {
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $attendanceId = $this->uri->segment(4);
        $redirect = $this->uri->segment(5);
        $this->db->trans_begin();
        $updateData = array(
            'holiday_hr_status' => '3',
            'processed_by_status' => $session_data['talentId']
        );
        $this->db->where('id', $attendanceId);
        $queryResult = $this->db->update('talent_attendance', $updateData);
        if ($queryResult) {
            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Comp-Off/Extra hours is declined successfully');
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirect) {
            redirect('compOffManagement/allCompOff', 'refresh');
        } else {
            redirect('compOffManagement', 'refresh');
        }
    }

    public function extraApprove() {
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
        $this->db->trans_begin();
        $updateData = array(
            'holiday_hr_status' => '2',
            'processed_by_status' => $session_data['talentId']
        );
        $this->db->where('id', $attendanceId);
        $queryResult = $this->db->update('talent_attendance', $updateData);
        $this->db->set('extra_od_hrs', 'od_hours', False);
        $this->db->set('extra_attendance_hrs', 'actual_hours_spend', False);
        $this->db->where('id', $attendanceId);
        $queryResult = $this->db->update('talent_attendance');
        if ($queryResult) {
            $talentDetails = $this->talent->getTalentById($talent);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }
            //creating notification for reporting manager
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Green',
                'message' => 'Approved your extra working hours for the date ' . date('d/m/Y', strtotime($attendanceDate)),
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);
            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Extra hours approved successfully');
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirect) {
            redirect('compOffManagement/allCompOff', 'refresh');
        } else {
            redirect('compOffManagement', 'refresh');
        }
    }

    public function savenote() {
        $alertify_text = $this->input->get('alertify_text');
        $id = $this->input->get('id');
        $updateData = array(
            'note' => $alertify_text
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
