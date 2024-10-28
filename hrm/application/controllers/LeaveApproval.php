<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LeaveApproval extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('leaveApprovals', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('holiday', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('talent', '', TRUE);
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

            $data['myLeavesDetails'] = $this->leaveApprovals->getLeaveRequestFinYearBased($session_data['talentId'], $selectedYear, $isResigned, $selectedTalent);
            $halfDayMechanism = $this->defaultValues->getValue(34);
            if ($halfDayMechanism == 1) {
                $data['leaveDueToAbsent'] = $this->leaveApprovals->getLeaveDueToAbsentReportingOfficer($session_data['talentId'], $selectedYear, $isResigned, $selectedTalent);
            }
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['isResigned'] = $isResigned;
            $data['selectedYear'] = $selectedYear;
            $data['yearArray'] = $yearArray;
            $data['redirect'] = '';
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $this->load->view('leaveManagement/allLeaveView', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function allLeave() {
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
            $data['myLeavesDetails'] = $this->leaveApprovals->getLeaveRequestFinYearBasedAll($session_data['talentId'], $selectedYear, $isResigned, $session_data['is_management'], $selectedTalent);
            $halfDayMechanism = $this->defaultValues->getValue(34);
            if ($halfDayMechanism == 1) {
                $data['leaveDueToAbsent'] = $this->leaveApprovals->getLeaveDueToAbsent($session_data['talentId'], $selectedYear, $isResigned, $session_data['is_management'], $selectedTalent);
            }
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['isResigned'] = $isResigned;
            $data['selectedYear'] = $selectedYear;
            $data['yearArray'] = $yearArray;
            $data['redirect'] = '/allLeave';
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $this->load->view('leaveManagement/allLeaveView', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function cancelLeave() {
        $error_flag = 0;
        $this->db->trans_begin();
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $myLeaveId = $this->uri->segment(4);
        $redirectLink = $this->uri->segment(5);

        $editData['myLeaveId'] = $myLeaveId;

        $leavePendingStatus = $this->leaves->checkLeaveCancelled($myLeaveId);
        if (!$leavePendingStatus) {
            $this->session->set_flashdata('flashError', 'Leave already cancelled');
            if ($redirectLink) {
                redirect('leaveApproval/allLeave', 'refresh');
            } else {
                redirect('leaveApproval', 'refresh');
            }
        }
        $considered = $this->leaves->CheckConsidered($myLeaveId);
        $lop = $this->leaves->LopApplied($myLeaveId);

        if ($considered == 1) {
            $cancel_data = $this->leaves->getcountLeavetype($myLeaveId);

            foreach ($cancel_data as $cancel) {
                if ($cancel['leave_type'] != 0) {
                    $gettalent = $this->leaveApprovals->gettalent($myLeaveId);
                    $this->db->where('leave_type', $cancel['leave_type']);
                    $this->db->where('talent', $gettalent);
                    $this->db->set('leave_left', 'leave_left+' . $cancel['leftt'], FALSE);
                    $queryResult = $this->db->update('talent_leave_left');
                    if (!$queryResult) {
                        $error_flag = 1;
                        break;
                    }
                }
            }
            if ($error_flag == 0) {
                $updateData2 = array(
                    'is_approved' => '2'
                );
                //var_dump($updateData);
                $this->db->where('talent_leave', $myLeaveId);
                $queryResult = $this->db->update('talent_leave_date', $updateData2);
                if (!$queryResult) {
                    $error_flag = 1;
                }
            }
            if ($error_flag == 0) {
                $findCompoffId = $this->leaves->findCompoffId($myLeaveId);
                foreach ($findCompoffId as $CompoffId) {
                    $updateData2 = array(
                        'used' => '0'
                    );
                    //var_dump($updateData);
                    $this->db->where('id', $CompoffId['compoff_id']);
                    $queryResult = $this->db->update('talent_compoff', $updateData2);
                    if (!$queryResult) {
                        $error_flag = 1;
                        break;
                    }
                }
            }
            if ($error_flag == 0) {
                $talent = $this->leaves->Checktalent($myLeaveId);
                $this->db->set('lop', 'lop-' . $lop, FALSE);
                $this->db->where('talent', $talent);
                $queryResult2 = $this->db->update('talent_lop');
                if (!$queryResult2) {
                    $error_flag = 1;
                }
            }
        }
        if ($error_flag == 0) {
            $updateData = array(
                'is_approved' => '2'
            );
            $this->db->where('id', $myLeaveId);
            $queryResult = $this->db->update('talent_leave', $updateData);
            if (!$queryResult) {
                $error_flag = 1;
            }
        }

        if ($error_flag == 0) {
            $leaveDetails = $this->leaves->getMyLeaveById($myLeaveId);
            foreach ($leaveDetails as $leave) {
                $talent = $leave['talent'];
            }
            $talentDetails = $this->talent->getTalentById($talent);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }
            //creating notification for talent
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Red',
                'message' => $session_data['firstName'] . ' Declined Your leave request',
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);

            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Declined successfully');
        } else if ($error_flag == 1) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirectLink) {
            redirect('leaveApproval/allLeave', 'refresh');
        } else {
            redirect('leaveApproval', 'refresh');
        }
    }

    public function acceptleave() {
        $error_flag = 0;
        $this->db->trans_begin();
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $myLeaveId = $this->uri->segment(4);
        $redirectLink = $this->uri->segment(5);

        $editData['myLeaveId'] = $myLeaveId;

        $considered = $this->leaves->CheckConsidered($myLeaveId);

        if ($considered == 1) {
            $getdatetalent = $this->leaveApprovals->getdatetalent($myLeaveId);
            foreach ($getdatetalent as $getdate) {

                $talent = $getdate['talent'];
                $date = $getdate['date_of_leave'];
                $updateData = array(
                    'is_leave' => '1'
                );
                $this->db->where('attendance_date', $date);
                $this->db->where('talent_id', $talent);
                $queryResult = $this->db->update('talent_attendance', $updateData);
                if (!$queryResult) {
                    $error_flag = 1;
                    break;
                }
            }
            if ($error_flag == 0) {
                $updateData2 = array(
                    'is_approved' => '1'
                );
                //var_dump($updateData);
                $this->db->where('talent_leave', $myLeaveId);
                $queryResult = $this->db->update('talent_leave_date', $updateData2);
                if (!$queryResult) {
                    $error_flag = 1;
                }
            }
        }
        if ($error_flag == 0) {
            $updateData = array(
                'is_approved' => '1'
            );
            $this->db->where('id', $myLeaveId);
            $queryResult = $this->db->update('talent_leave', $updateData);
            if (!$queryResult) {
                $error_flag = 1;
            }
        }

        if ($error_flag == 0) {
            $leaveDetails = $this->leaves->getMyLeaveById($myLeaveId);
            foreach ($leaveDetails as $leave) {
                $talent = $leave['talent'];
            }
            $talentDetails = $this->talent->getTalentById($talent);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }

            //creating notification for talent
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Green',
                'message' => $session_data['firstName'] . ' approved Your leave request',
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);
            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Approved successfully');
        } else if ($error_flag == 1) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirectLink) {
            redirect('leaveApproval/allLeave', 'refresh');
        } else {
            redirect('leaveApproval', 'refresh');
        }
    }

    public function savenote() {
        $alertify_text = $this->input->get('alertify_text');
        $id = $this->input->get('id');
        $updateData = array(
            'note' => $alertify_text
        );
        $this->db->where('id', $id);
        $queryResult = $this->db->update('talent_leave', $updateData);
        if ($queryResult) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function view_details() {
        $id = $this->input->get('tid');
        $view_details['LeaveId'] = $id;

        $getalldataleave = $this->leaveApprovals->getalldataleave($id);
        foreach ($getalldataleave as $getalldata) {
            $fromDate = $getalldata['from_date'];
            $toDate = $getalldata['to_date'];
            $talent = $getalldata['talent'];
        }

        //get leave status
        $talentOdsBetweenDate = $this->leaveApprovals->getLeaveDayStatus($id);
        $talentLeaves = array();
        $LeaveTypes = array();
        $count = 0;
        foreach ($talentOdsBetweenDate as $tod) {
            $talentLeaves[] = $tod['date_of_leave'];
            if ($tod['leave_type'] == "0") {
                $LeaveTypes[] = "LOP";
            } else {
                $LeaveTypes[] = $tod['type'];
            }
            $count++;
        }

        $talentWorkingDay = $this->leaves->getWorkingDayStatus($talent, $fromDate, $toDate);
        $talentWorking = array();
        foreach ($talentWorkingDay as $tod) {
            $talentWorking[] = $tod['attendance_date'];
        }
        //finding holidays and leaves between the date
        $holidayArray = array();
        $holidays = $this->holiday->getHolidayStatus($fromDate, $toDate);
        foreach ($holidays as $holiday) {
            $holidayArray[] = $holiday['date_of_holiday'];
        }
        //weekends
        $weekendArray = array();
        $weekends = $this->holidaysWeek->getHolidayTalent($talent);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }
        $view_details['weekends'] = $weekendArray;
        $view_details['holidays'] = $holidayArray;
        $view_details['talentLeaves'] = $talentLeaves;
        $view_details['talentWorking'] = $talentWorking;
        $view_details['LeaveTypes'] = $LeaveTypes;
        $view_details['fromDate'] = $fromDate;
        $view_details['toDate'] = $toDate;
        $view_details['dateCount'] = $fromDate;

        $this->load->view('leaveManagement/viewLeaveDetails', $view_details);
    }

    public function manageAttendance() {
        $this->load->helper(array('form'));
        $date = $this->input->get('date');
        $redirect = $this->input->get('redirect');
        $dateFormated = date('Y-m-d', strtotime($date));
        $talent = $this->input->get('talent');
        $attendance = $this->input->get('attendance');
        $data['checkElPending'] = $this->leaves->getMyLeavesLeft_byone($talent, 2);
        $data['checkCompPendingBefore'] = $this->leaves->checkCompoffPendingBefore($talent, $dateFormated);
        $attendanceDetails = $this->leaves->getAttendanceDetailsById($attendance);
        $data['actualHoursSpend'] = $attendanceDetails->actual_hours_spend;
        $data['odHours'] = $attendanceDetails->od_hours;
        $selectOption = array(
            '1' => "Present",
        );
        if ($data['checkElPending'] >= 1) {
            $selectOption['2'] = "Full day leave";
        }
        if ($data['checkElPending'] >= .5) {
            $selectOption['3'] = "Half day leave";
        }
        if ($data['checkCompPendingBefore'] > 0) {
            $selectOption['4'] = "Comp Off";
        }
        $selectOption['5'] = "LOP";
        $data['options'] = $selectOption;
        $data['talent'] = $talent;
        $data['dateFormated'] = $dateFormated;
        $data['attendance'] = $attendance;
        $data['redirect'] = $redirect;
        $this->load->view('leaveManagement/manageAttendance', $data);
    }

    public function manageAbsentAttendance() {
        $manageAbsent = $this->input->post("manageAbsent");
        $talent = $this->input->post("talent");
        $date = $this->input->post("date");
        $attendance = $this->input->post("attendance");
        $dateFormated = date('Y-m-d', strtotime($date));
        $this->load->helper('url');
        $redirect = $this->uri->segment(3);

        $error = TRUE;
        if ($manageAbsent == 1) {
            $error = $this->manageAsPresent($attendance);
        }
        if ($manageAbsent == 2) {
            $error = $this->manageAsElLeave($talent, $date, $attendance, 0);
        }
        if ($manageAbsent == 3) {
            $error = $this->manageAsElLeave($talent, $date, $attendance, 1);
        }
        if ($manageAbsent == 4) {
            $error = $this->manageAsCompOff($talent, $date, $attendance);
        }
        if ($manageAbsent == 5) {
            $error = $this->manageAsLop($talent, $date, $attendance);
        }
        if ($redirect) {
            $url = 'leaveApproval/allLeave';
        } else {
            $url = 'leaveApproval';
        }
        if ($error) {
            $this->session->set_flashdata('flashError', 'Some error found');
        } else {
            $this->session->set_flashdata('flashSuccess', 'Managed successfully');
        }
        redirect($url, 'refresh');
    }

    public function manageAsLop($talent, $date, $attendance) {
        $error = false;
        $this->db->trans_begin();
        $insertData1 = array(
            'talent' => $talent,
            'from_date' => $date,
            'to_date' => $date,
            'is_approved' => 1,
            'request' => 'System raised due to daily shortage ',
            'considered' => 1,
            'lop' => 1,
        );
        $queryResult1 = $this->db->insert('talent_leave', $insertData1);
        if (!$queryResult1) {
            $error = TRUE;
        }
        $insert_id = $this->db->insert_id();
        $insertData2 = array(
            'talent_leave' => $insert_id,
            'date_of_leave' => $date,
            'leave_type' => 0,
            'is_approved' => 1,
        );
        $queryResult2 = $this->db->insert('talent_leave_date', $insertData2);
        if (!$queryResult2) {
            $error = TRUE;
        }
        //check already have lop if yes then update else insert
        $CheckMyLopsLeft = $this->leaves->CheckMyLopsLeft($talent);
        if ($CheckMyLopsLeft > 0) {
            $this->db->set('lop', 'lop+' . 1, FALSE);
            $this->db->where('talent', $talent);
            $queryResult3 = $this->db->update('talent_lop');
            if (!$queryResult3) {
                $error = TRUE;
            }
        } else {
            $insertTalentLeaveData = array(
                'lop' => 1,
                'talent' => $talent
            );
            $queryResult4 = $this->db->insert('talent_lop', $insertTalentLeaveData);
            if (!$queryResult4) {
                $error = TRUE;
            }
        }
        $updateData1 = array(
            'is_leave' => 1,
            'absent_raise_status' => 2,
        );
        $this->db->where('id', $attendance);
        $queryResult3 = $this->db->update('talent_attendance', $updateData1);
        if (!$queryResult3) {
            $error = TRUE;
        }
        if (TRUE === $error) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        return $error;
    }

    public function manageAsPresent($attendance) {
        $error = false;
        $this->db->set('avg_consider', 1, FALSE);
        $this->db->set('absent_raise_status', 1, FALSE);
        $this->db->where('id', $attendance);
        $queryResult = $this->db->update('talent_attendance');
        if (!$queryResult) {
            $error = TRUE;
        }
        return $error;
    }

    public function manageAsElLeave($talent, $date, $attendance, $nature) {
        $error = false;
        $this->db->trans_begin();
        $insertData1 = array(
            'talent' => $talent,
            'from_date' => $date,
            'to_date' => $date,
            'is_approved' => 1,
            'request' => 'System raised due to daily shortage ',
            'considered' => 1,
        );
        $queryResult1 = $this->db->insert('talent_leave', $insertData1);
        if (!$queryResult1) {
            $error = TRUE;
        }
        $insert_id = $this->db->insert_id();
        $insertData2 = array(
            'talent_leave' => $insert_id,
            'date_of_leave' => $date,
            'leave_type' => 2,
            'is_approved' => 1,
            'leave_nature' => $nature,
        );
        $queryResult2 = $this->db->insert('talent_leave_date', $insertData2);
        if (!$queryResult2) {
            $error = TRUE;
        }
        $updateData1 = array(
            'is_leave' => 1,
            'absent_raise_status' => 2,
            'leave_nature_absent' => $nature,
        );
        $this->db->where('id', $attendance);
        $queryResult3 = $this->db->update('talent_attendance', $updateData1);
        if (!$queryResult3) {
            $error = TRUE;
        }
        //update talent leave left
        if ($nature) {
            $leave = .5;
        } else {
            $leave = 1;
        }
        $this->db->set('leave_left', 'leave_left-' . $leave, FALSE);
        $this->db->where('talent', $talent);
        $this->db->where('leave_type', 2);
        $queryResult4 = $this->db->update('talent_leave_left');
        if (!$queryResult4) {
            $error = TRUE;
        }
        if (TRUE === $error) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        return $error;
    }

    public function manageAsCompOff($talent, $date, $attendance) {
        $this->db->trans_begin();
        $error = FALSE;
        //find min compoff id not used
        $compOffId = $this->leaves->findDateOfCompOffBeforeTheDate($talent, $date);
        if ($compOffId) {
            $insertData1 = array(
                'talent' => $talent,
                'from_date' => $date,
                'to_date' => $date,
                'is_approved' => 1,
                'request' => 'System raised due to daily shortage ',
                'considered' => 1,
            );
            $queryResult1 = $this->db->insert('talent_leave', $insertData1);
            if (!$queryResult1) {
                $error = TRUE;
            }
            $insert_id = $this->db->insert_id();
            $insertData2 = array(
                'talent_leave' => $insert_id,
                'date_of_leave' => $date,
                'leave_type' => 1,
                'is_approved' => 1,
                'compoff_id' => $compOffId,
            );
            $queryResult2 = $this->db->insert('talent_leave_date', $insertData2);
            if (!$queryResult2) {
                $error = TRUE;
            }
            $updateData1 = array(
                'is_leave' => 1,
                'absent_raise_status' => 2,
            );
            $this->db->where('id', $attendance);
            $queryResult3 = $this->db->update('talent_attendance', $updateData1);
            if (!$queryResult3) {
                $error = TRUE;
            }
            //update talent leave left
            $leave = 1;
            $this->db->set('leave_left', 'leave_left-' . $leave, FALSE);
            $this->db->where('talent', $talent);
            $this->db->where('leave_type', 1);
            $queryResult4 = $this->db->update('talent_leave_left');
            if (!$queryResult4) {
                $error = TRUE;
            }
            //update leave as used
            $this->db->set('used', '1', FALSE);
            $this->db->where('id', $compOffId);
            $queryResult5 = $this->db->update('talent_compoff');
            if (!$queryResult5) {
                $error = TRUE;
            }
        } else {
            $error = TRUE;
        }
        if (TRUE === $error) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        return $error;
    }

}
