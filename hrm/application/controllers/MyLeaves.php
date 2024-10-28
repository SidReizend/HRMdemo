<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MyLeaves extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('leaves', '', TRUE);
        $this->load->model('holiday', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['myLeavesDetails'] = $this->leaves->getMyLeaves($session_data['talentId']);
            //echo $this->db->last_query();
            $this->load->view('leaveManagement/myLeaves', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewLeaves() {

        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['getMyLeavesLeft'] = $this->leaves->getMyLeavesLeft($session_data['talentId']);
            $data['getMylopLeft'] = $this->leaves->getMyLopsLeft($session_data['talentId']);

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->helper('date');

            $this->load->library('form_validation');
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'request',
                    'label' => 'Request',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'fromDate',
                    'label' => 'From Date',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'toDate',
                    'label' => 'To Date',
                    'rules' => 'required'
                )
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() == TRUE) {

                //Validation Success.

                $request = $this->input->post('request');

                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
                $fromDate = $date->format('Y-m-d');

                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
                $toDate = $date->format('Y-m-d');

                $error_flag = 0;
                $self = 1; //variable to check application by self or reporting manager
                $leave = array();
                $talent = $session_data['talentId'];


                $typeArray = $this->input->post('type');
                $leftArray = $this->input->post('left');
                if (isset($typeArray)) {
                    $i = 0;
                    foreach ($typeArray as $type) {
                        $leave[$type] = $leftArray[$i];
                        $i++;
                    }
                }

                $error_flag = $this->allProcesses($talent, $fromDate, $toDate, $leave, $request, $self);

                if ($error_flag == 0) {
                    $talentDetails = $this->talent->getTalentById($session_data['talentId']);
                    foreach ($talentDetails as $talet) {
                        $reportingManager = $talet['reporting_manager'];
                    }
                    $talentDetails = $this->talent->getTalentById($reportingManager);
                    foreach ($talentDetails as $talet) {
                        $talentUserId = $talet['user'];
                    }
                    //creating notification for reporting manager
                    $insertNotificationData = array(
                        'user' => $talentUserId,
                        'type' => 'Yellow',
                        'message' => $session_data['firstName'] . ' Created a leave request',
                        'message_time' => date('Y-m-d H:i:s'),
                    );
                    //inserting
                    $queryResult = $this->db->insert('notifications', $insertNotificationData);

                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Leave Applied successfully');
                    redirect('myLeaves', 'refresh');
                } else if ($error_flag == 1) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    redirect('myLeaves/addNewLeaves', 'refresh');
                }
            }
            $this->load->view('leaveManagement/addNewMyLeave', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function showDatesBetweenDates() {
        $this->load->helper(array('form'));

        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
        $fromDate = $date->format('Y-m-d');

        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
        $toDate = $date->format('Y-m-d');
        $session_data = $this->session->userdata('logged_in');

        $this->allProcessesView($session_data['talentId'], $fromDate, $toDate);
    }

    public function showLeavedates() {
        $this->load->helper(array('form'));

        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
        $fromDate = $date->format('Y-m-d');

        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
        $toDate = $date->format('Y-m-d');

        $current_quarter = $this->leaves->splitDateBetweenQuarter(date("Y-m-d"));
        $fromdate_quarter = $this->leaves->splitDateBetweenQuarter($fromDate);
        $todate_quarter = $this->leaves->splitDateBetweenQuarter($toDate);
        if ($todate_quarter > $current_quarter) {
            $toDate = $this->leaves->findEndDateQuarter($fromdate_quarter);
        }

        $leave_info_array = $this->input->post('leave_info_array');
        $session_data = $this->session->userdata('logged_in');

        $talentWorkingDay = $this->leaves->getWorkingDayStatus($session_data['talentId'], $fromDate, $toDate);
        $talentWorking = array();
        foreach ($talentWorkingDay as $tod) {
            $talentWorking[] = $tod['attendance_date'];
        }
        //get od status
        $talentOdsBetweenDate = $this->leaves->getLeaveDayStatus($session_data['talentId'], $fromDate, $toDate);
        $talentLeaves = array();
        foreach ($talentOdsBetweenDate as $tod) {
            $talentLeaves[] = $tod['date_of_leave'];
        }
        //finding holidays and leaves between the date
        $holidayArray = array();
        $holidays = $this->holiday->getHolidayStatus($fromDate, $toDate);
        foreach ($holidays as $holiday) {
            $holidayArray[] = $holiday['date_of_holiday'];
        }
        $weekendArray = array();
        $weekends = $this->holidaysWeek->getHolidayTalent($session_data['talentId']);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }
        $leftArray = array();
        $typeArray = array();
        if (is_array($leave_info_array) || is_object($leave_info_array)) {
            foreach ($leave_info_array as $lia) {
                $leftArray[] = $lia['left'];
                $typeArray[] = $lia['type'];
            }
        }
        $dataDaysLoad['weekends'] = $weekendArray;
        $dataDaysLoad['holidays'] = $holidayArray;
        $dataDaysLoad['talentLeaves'] = $talentLeaves;
        $dataDaysLoad['talentWorking'] = $talentWorking;
        $dataDaysLoad['fromDate'] = $fromDate;
        $dataDaysLoad['toDate'] = $toDate;
        $dataDaysLoad['dateCount'] = $fromDate;
        $dataDaysLoad['leave_info_array'] = $leave_info_array;

        $dataDaysLoad['getMyLeavesLeft'] = $this->leaves->getMyLeavesLeft($session_data['talentId']);

        $dataDaysLoad['leftArray'] = $leftArray;
        $dataDaysLoad['typeArray'] = $typeArray;


        $this->load->view('leaveManagement/daysLoad_leave', $dataDaysLoad);
    }

    public function cancelMyLeave() {
        $error_flag = 0;
        $this->db->trans_begin();
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $myLeaveId = $this->uri->segment(4);

        $editData['myLeaveId'] = $myLeaveId;
        $leavePendingStatus = $this->leaves->checkLeaveCancelled($myLeaveId);
        if (!$leavePendingStatus) {
            $this->session->set_flashdata('flashError', 'Leave already cancelled');
            redirect('myLeaves', 'refresh');
        }
        $considered = $this->leaves->CheckConsidered($myLeaveId);
        $lop = $this->leaves->LopApplied($myLeaveId);

        if ($considered == 1) {
            $cancel_data = $this->leaves->getcountLeavetype($myLeaveId);

            foreach ($cancel_data as $cancel) {
                if ($cancel['leave_type'] != 0) {
                    $this->db->where('leave_type', $cancel['leave_type']);
                    $this->db->where('talent', $session_data['talentId']);
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
                    'is_approved' => '3'
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
            $talentDetails = $this->talent->getTalentById($session_data['talentId']);
            foreach ($talentDetails as $talet) {
                $reportingManager = $talet['reporting_manager'];
            }
            $talentDetails = $this->talent->getTalentById($reportingManager);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }
            //creating notification for reporting manager
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Yellow',
                'message' => $session_data['firstName'] . ' Canceled a leave request',
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);
            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Cancelled successfully');
        } else if ($error_flag == 1) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        redirect('myLeaves', 'refresh');
    }

    public function addTalentLeave() {
        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $talents = $this->leaves->talentsUnderReportManager($session_data['talentId']);
            $talentsArray = array(
                '' => 'SELECT'
            );
            foreach ($talents as $talent) {
                $talentsArray[$talent['id']] = $talent['first_name'];
            }
            $data['talentsUnderReportManager'] = $talentsArray;
            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->helper('date');

            $this->load->library('form_validation');
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'request',
                    'label' => 'Request',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'fromDate',
                    'label' => 'From Date',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'toDate',
                    'label' => 'To Date',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'talent',
                    'label' => 'Talent',
                    'rules' => 'required'
                )
            );
            $this->form_validation->set_rules($config);
            $data['selected_talent'] = $this->input->post('talent');
            if ($this->form_validation->run() == TRUE) {

                $error_flag = 0;
                $self = 0;
                $leave = array();
                //Validation Success.
                $request = $this->input->post('request');

                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
                $fromDate = $date->format('Y-m-d');

                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
                $toDate = $date->format('Y-m-d');

                $talent = $this->input->post('talent');


                $error_flag = $this->allProcesses($talent, $fromDate, $toDate, $leave, $request, $self);

                if ($error_flag == 0) {
                    $talentDetails = $this->talent->getTalentById($talent);
                    foreach ($talentDetails as $talet) {
                        $talentUserId = $talet['user'];
                    }
                    //creating notification for reporting manager
                    $insertNotificationData = array(
                        'user' => $talentUserId,
                        'type' => 'green',
                        'message' => $session_data['firstName'] . ' created a leave request and Approved',
                        'message_time' => date('Y-m-d H:i:s'),
                    );
                    //inserting
                    $queryResult = $this->db->insert('notifications', $insertNotificationData);

                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Leave Applied And Approved successfully');
                } else if ($error_flag == 1) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
                redirect('myLeaves/addTalentLeave', 'refresh');
            }
            $this->load->view('leaveManagement/addTalentLeave', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function showTalentData() {
        $this->load->helper(array('form'));
        $talent = $this->input->post('talent');
        $data['getMyLeavesLeft'] = $this->leaves->getMyLeavesLeft($talent);
        $data['getMylopLeft'] = $this->leaves->getMyLopsLeft($talent);
        $this->load->view('leaveManagement/showTalentData', $data);
    }

    public function showTalentDatesBetweenDates() {
        $this->load->helper(array('form'));
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
        $fromDate = $date->format('Y-m-d');

        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
        $toDate = $date->format('Y-m-d');

        $talent = $this->input->post('talent');
        $this->allProcessesView($talent, $fromDate, $toDate);
    }

    public function showTalentLeavedates() {
        $this->load->helper(array('form'));

        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
        $fromDate = $date->format('Y-m-d');

        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
        $toDate = $date->format('Y-m-d');

        $talent = $this->input->post('talent');
        $current_quarter = $this->leaves->splitDateBetweenQuarter(date("Y-m-d"));
        $fromdate_quarter = $this->leaves->splitDateBetweenQuarter($fromDate);
        $todate_quarter = $this->leaves->splitDateBetweenQuarter($toDate);
        if ($todate_quarter > $current_quarter) {
            $toDate = $this->leaves->findEndDateQuarter($fromdate_quarter);
        }

        $leave_info_array = $this->input->post('leave_info_array');
        $session_data = $this->session->userdata('logged_in');

        $talentWorkingDay = $this->leaves->getWorkingDayStatus($talent, $fromDate, $toDate);
        $talentWorking = array();
        foreach ($talentWorkingDay as $tod) {
            $talentWorking[] = $tod['attendance_date'];
        }
        //get od status
        $talentOdsBetweenDate = $this->leaves->getLeaveDayStatus($talent, $fromDate, $toDate);
        $talentLeaves = array();
        foreach ($talentOdsBetweenDate as $tod) {
            $talentLeaves[] = $tod['date_of_leave'];
        }
        //finding holidays and leaves between the date
        $holidayArray = array();
        $holidays = $this->holiday->getHolidayStatus($fromDate, $toDate);
        foreach ($holidays as $holiday) {
            $holidayArray[] = $holiday['date_of_holiday'];
        }
        $weekendArray = array();
        $weekends = $this->holidaysWeek->getHolidayTalent($talent);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }
        $leftArray = array();
        $typeArray = array();
        if (is_array($leave_info_array) || is_object($leave_info_array)) {
            foreach ($leave_info_array as $lia) {
                $leftArray[] = $lia['left'];
                $typeArray[] = $lia['type'];
            }
        }

        $dataDaysLoad['weekends'] = $weekendArray;
        $dataDaysLoad['holidays'] = $holidayArray;
        $dataDaysLoad['talentLeaves'] = $talentLeaves;
        $dataDaysLoad['talentWorking'] = $talentWorking;
        $dataDaysLoad['fromDate'] = $fromDate;
        $dataDaysLoad['toDate'] = $toDate;
        $dataDaysLoad['dateCount'] = $fromDate;
        $dataDaysLoad['leave_info_array'] = $leave_info_array;

        $dataDaysLoad['getMyLeavesLeft'] = $this->leaves->getMyLeavesLeft($talent);

        $dataDaysLoad['leftArray'] = $leftArray;
        $dataDaysLoad['typeArray'] = $typeArray;


        $this->load->view('leaveManagement/daysLoad_leave', $dataDaysLoad);
    }

    public function processCurrentQuarter($talent, $fromDate, $toDate, $leave, $request, $self) {
        $error_flag = 0;
        $dateCount = $fromDate;

        if ($self == 1) {//checks the application by him or his reporting manager
            //preparing data to insert
            $insertTalentLeaveData = array(
                'talent' => $talent,
                'request' => $request,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'considered' => 1
            );
        } else {
            $insertTalentLeaveData = array(
                'talent' => $talent,
                'request' => $request,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'considered' => 1,
                'is_approved' => 1
            );
        }
        //inserting
        $queryResult = $this->db->insert('talent_leave', $insertTalentLeaveData);
        if (!$queryResult) {
            $error_flag = 1;
        }
        $insert_id = $this->db->insert_id();

        $talentWorkingDay = $this->leaves->getWorkingDayStatus($talent, $fromDate, $toDate);
        $talentWorking = array();
        foreach ($talentWorkingDay as $tod) {
            $talentWorking[] = $tod['attendance_date'];
        }
        //get leave status
        $talentOdsBetweenDate = $this->leaves->getLeaveDayStatus($talent, $fromDate, $toDate);
        $talentLeaves = array();
        foreach ($talentOdsBetweenDate as $tod) {
            $talentLeaves[] = $tod['date_of_leave'];
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

        $count = 0; //variable to find total working days
        $lop = 0; //variable to find total lop
        $leave_left = 0; //variable to find leave left

        $compoff_count = 0;
        $el_count = 0;
        $emergency_count = 0;
        $maternity_count = 0;
        $paternity_count = 0;


        $compoff = 0;
        if (isset($leave['1'])) {
            $leave_left = $leave_left + $leave['1'];
            $compoff = $leave['1'];
        }
        $el = 0;
        if (isset($leave['2'])) {
            $leave_left = $leave_left + $leave['2'];
            $el = $leave['2'];
        }
        $emergency = 0;
        if (isset($leave['3'])) {
            $leave_left = $leave_left + $leave['3'];
            $emergency = $leave['3'];
        }
        $maternity = 0;
        if (isset($leave['4'])) {
            $leave_left = $leave_left + $leave['4'];
            $maternity = $leave['4'];
        }
        $paternity = 0;
        if (isset($leave['5'])) {
            $leave_left = $leave_left + $leave['5'];
            $paternity = $leave['5'];
        }
        while (strtotime($toDate) >= strtotime($dateCount)) {
            $id_to_update = 0;
            $ltype = 0; //variable to find leave type
            $insert = 0;
            $dateCountStandard = date('Y-m-d', strtotime($dateCount));
            $strToTimeDate = strtotime($dateCountStandard);
            $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateCount);
            $day = strtolower(date("l", $strToTimeDate));
            if (in_array($dateCountStandard, $talentWorking)) {
                $insert = 0;
            } else if (in_array($dateCountStandard, $talentLeaves)) {
                $insert = 0;
            } else if (in_array($dateCountStandard, $holidayArray)) {
                if ($leave_left < $count) {
                    $lop++;
                    $insert = 1;
                }
            } else if (in_array($day, $weekendArray) && ($checkDateInStrictlyWorking == 0)) {
                if ($leave_left < $count) {
                    $lop++;
                    $insert = 1;
                }
            } else {
                $count++;
                if ($leave_left < $count) {
                    $lop++;
                } else if ($compoff > 0) {
                    $ltype = 1;
                    $compoff--;
                    $compoff_count++;
                    $id_to_update = $this->leaves->minIdInCompoff($talent);
                    $updateData = array(
                        'used' => '1'
                    );
                    $this->db->where('id', $id_to_update);
                    $queryResult = $this->db->update('talent_compoff', $updateData);
                    if (!$queryResult) {
                        $error_flag = 1;
                        break;
                    }
                } else if ($el > 0) {
                    $ltype = 2;
                    $el--;
                    $el_count++;
                } else if ($emergency > 0) {
                    $ltype = 3;
                    $emergency--;
                    $emergency_count++;
                } else if ($maternity > 0) {
                    $ltype = 4;
                    $maternity--;
                    $maternity_count++;
                } else if ($paternity > 0) {
                    $ltype = 5;
                    $paternity--;
                    $paternity_count++;
                }
                $insert = 1;
            }
            if ($insert == 1) {
                if ($self == 1) {
                    //leave application waiting for approval
                    $insertSpecificData = array(
                        'talent_leave' => $insert_id,
                        'date_of_leave' => date('Y-m-d', strtotime($dateCountStandard)),
                        'leave_type' => $ltype,
                        'compoff_id' => $id_to_update,
                    );
                    $queryResult = $this->db->insert('talent_leave_date', $insertSpecificData);
                    if (!$queryResult) {
                        $error_flag = 1;
                        break;
                    }
                } else {
                    //leave applied and approved by reporting officer
                    $insertSpecificData = array(
                        'talent_leave' => $insert_id,
                        'date_of_leave' => date('Y-m-d', strtotime($dateCountStandard)),
                        'leave_type' => $ltype,
                        'is_approved' => '1'
                    );
                    $queryResult = $this->db->insert('talent_leave_date', $insertSpecificData);
                    if (!$queryResult) {
                        $error_flag = 1;
                        break;
                    }
                    $updateData = array(
                        'is_leave' => '1'
                    );
                    $this->db->where('attendance_date', date('Y-m-d', strtotime($dateCountStandard)));
                    $this->db->where('talent_id', $talent);
                    $queryResult = $this->db->update('talent_attendance', $updateData);
                    if (!$queryResult) {
                        $error_flag = 1;
                        break;
                    }
                }
            }

            $dateCount = strtotime("+1 day", strtotime($dateCount));
            $dateCount = date("m/d/Y", $dateCount);
        }
        if ($error_flag == 0) {
            if ($compoff_count > 0) {
                $compoff_pending = $this->leaves->getMyLeavesLeft_byone($talent, 1);
                $insert_compoff = $compoff_pending - $compoff_count;
                $updateData1 = array(
                    'leave_left' => $insert_compoff,
                );
                $this->db->where('leave_type', 1);
                $this->db->where('talent', $talent);
                $queryResult1 = $this->db->update('talent_leave_left', $updateData1);
                if (!$queryResult1) {
                    $error_flag = 1;
                }
            }
        }
        if ($error_flag == 0) {
            if ($el_count > 0) {
                $insert_el = $leave['2'] - $el_count;
                $updateData1 = array(
                    'leave_left' => $insert_el,
                );
                $this->db->where('leave_type', 2);
                $this->db->where('talent', $talent);
                $queryResult1 = $this->db->update('talent_leave_left', $updateData1);
                if (!$queryResult1) {
                    $error_flag = 1;
                }
            }
        }
        if ($error_flag == 0) {
            if ($emergency_count > 0) {
                $insert_emerg = $leave['3'] - $emergency_count;
                $updateData1 = array(
                    'leave_left' => $insert_emerg,
                );
                $this->db->where('leave_type', 3);
                $this->db->where('talent', $talent);
                $queryResult1 = $this->db->update('talent_leave_left', $updateData1);
                if (!$queryResult1) {
                    $error_flag = 1;
                }
            }
        }
        if ($error_flag == 0) {
            if ($maternity_count > 0) {
                $insert_mat = $leave['4'] - $maternity_count;
                $updateData1 = array(
                    'leave_left' => $insert_mat,
                );
                $this->db->where('leave_type', 4);
                $this->db->where('talent', $talent);
                $queryResult1 = $this->db->update('talent_leave_left', $updateData1);
                if (!$queryResult1) {
                    $error_flag = 1;
                }
            }
        }
        if ($error_flag == 0) {
            if ($paternity_count > 0) {
                $insert_pate = $leave['5'] - $paternity_count;
                $updateData1 = array(
                    'leave_left' => $insert_pate,
                );
                $this->db->where('leave_type', 5);
                $this->db->where('talent', $talent);
                $queryResult1 = $this->db->update('talent_leave_left', $updateData1);
                if (!$queryResult1) {
                    $error_flag = 1;
                }
            }
        }
        if ($error_flag == 0) {
            $updateData2 = array(
                'lop' => $lop,
            );
            $this->db->where('id', $insert_id);
            $queryResult2 = $this->db->update('talent_leave', $updateData2);
            if (!$queryResult2) {
                $error_flag = 1;
            }
        }
        if ($error_flag == 0) {
            $CheckMyLopsLeft = $this->leaves->CheckMyLopsLeft($talent);
            if ($CheckMyLopsLeft > 0) {
                $this->db->set('lop', 'lop+' . $lop, FALSE);
                $this->db->where('talent', $talent);
                $queryResult2 = $this->db->update('talent_lop');
                if (!$queryResult2) {
                    $error_flag = 1;
                }
            } else {
                $insertTalentLeaveData = array(
                    'lop' => $lop,
                    'talent' => $talent
                );
                //inserting
                $queryResult = $this->db->insert('talent_lop', $insertTalentLeaveData);
                if (!$queryResult) {
                    $error_flag = 1;
                }
            }
        }

        return $error_flag;
    }

    public function processNextQuarter($talent, $fromDate, $toDate, $request, $self) {

        $error_flag = 0;
        $plannedLeaveArray = array();
        $planned = $this->leaves->checkPlannedDate($talent);
        foreach ($planned as $plan) {
            $fromDate_leave = $plan['from_date'];
            $toDate_leave = $plan['to_date'];
            $plannedLeaveArray = $this->leaves->createDateRangeArray($fromDate_leave, $toDate_leave);
        }
        $booked_leave_array = $this->leaves->createDateRangeArray($fromDate, $toDate);
        $result = array_intersect($plannedLeaveArray, $booked_leave_array);
        if (count($result) > 0) {
            $error_flag = 1;
        } else {
            if ($self == 1) {
                $insertTalentLeaveData = array(
                    'talent' => $talent,
                    'request' => $request,
                    'from_date' => $fromDate,
                    'to_date' => $toDate
                );
            } else {
                $insertTalentLeaveData = array(
                    'talent' => $talent,
                    'request' => $request,
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'is_approved' => '1'
                );
            }

            //inserting
            $queryResult = $this->db->insert('talent_leave', $insertTalentLeaveData);
            if (!$queryResult) {
                $error_flag = 1;
            }
        }

        return $error_flag;
    }

    public function allProcessesView($talent, $fromDate, $toDate) {
        $checkCompoffBetweenDate = $this->leaves->checkCompoffBetweenDate($fromDate, $toDate, $talent);
        //echo $this->db->last_query();
        //die();
        if ($checkCompoffBetweenDate > 0) {
            echo "<center><h2><b>Your Selection has Compoff in it Please Choose different Dates</b></h2></center>";
        } else if ($fromDate > $toDate) {
            echo "<center><h2><b>Please Choose From Date less Than To date</b></h2></center>";
        } else {
            $current_quarter = $this->leaves->splitDateBetweenQuarter(date("Y-m-d"));
            $fromdate_quarter = $this->leaves->splitDateBetweenQuarter($fromDate);
            $todate_quarter = $this->leaves->splitDateBetweenQuarter($toDate);
            $last_processed_date_value = $this->talentAttendenceSheet->getLastDate();
            $last_processed_date = $fromDate;
            if (isset($last_processed_date_value->last_date)) {
                $last_processed_date = $last_processed_date_value->last_date;
            }
            if ($fromdate_quarter > 4) {
                echo "<center><h2><b>Leave Application Controlled To This Financial Year Only</b></h2></center>";
            } else if ($fromdate_quarter < $current_quarter) {
                echo "<center><h2><b>Leave Application Should Be in Current Quarter OR Next Quarter</b></h2></center>";
            } else if ($fromDate < $last_processed_date) {
                echo "<center><h2><b>Processing Finished For This Date</b></h2></center>";
            } else if (($fromdate_quarter == $current_quarter) && ($todate_quarter == $current_quarter)) {
                $this->processLeaveData($talent, $fromDate, $toDate, 1);
            } else if (($fromdate_quarter == $current_quarter) && ($todate_quarter > $current_quarter)) {
                $enddateFrom = $this->leaves->findEndDateQuarter($fromdate_quarter);
                $startdateFrom = $this->leaves->findStartDateQuarter($todate_quarter);
                $this->processLeaveData($talent, $fromDate, $enddateFrom, 1);
                $this->processLeaveData($talent, $startdateFrom, $toDate, 0);
            } else if ($fromdate_quarter > $current_quarter) {
                $this->processLeaveData($talent, $fromDate, $toDate, 0);
            }
        }
    }

    public function allProcesses($talent, $fromDate, $toDate, $leave, $request, $self) {
        $current_quarter = $this->leaves->splitDateBetweenQuarter(date("Y-m-d"));
        $fromdate_quarter = $this->leaves->splitDateBetweenQuarter($fromDate);
        $todate_quarter = $this->leaves->splitDateBetweenQuarter($toDate);
        $last_processed_date_value = $this->talentAttendenceSheet->getLastDate();
        $last_processed_date = $fromDate;
        if (isset($last_processed_date_value->last_date)) {
            $last_processed_date = $last_processed_date_value->last_date;
        }

        if ($fromdate_quarter < $current_quarter) {
            /* checks the leave application in current quarter or not */
            $error_flag = 3;
            $this->session->set_flashdata('flashError', 'Leave Application Should Be in Current Quarter OR Next Quarter');
        } else if ($fromDate < $last_processed_date) {
            /* checks the leave application in already proceessed date or not */
            $error_flag = 3;
            $this->session->set_flashdata('flashError', 'Processing Finished For This Date');
        } else if (($fromdate_quarter == $current_quarter) && ($todate_quarter == $current_quarter)) {
            /* Leave application in current quarter */
            $error_flag = $this->processCurrentQuarter($talent, $fromDate, $toDate, $leave, $request, $self);
        } else if (($fromdate_quarter == $current_quarter) && ($todate_quarter > $current_quarter)) {

            /* Leave application between current quarter and next quarter */

            $enddateFrom = $this->leaves->findEndDateQuarter($fromdate_quarter); //Finding quarter end date
            $startdateFrom = $this->leaves->findStartDateQuarter($todate_quarter); //Finding Next quarter start date

            $error_flag = $this->processCurrentQuarter($talent, $fromDate, $enddateFrom, $leave, $request, $self);
            if ($error_flag == 0) {
                $error_flag = $this->processNextQuarter($talent, $startdateFrom, $toDate, $request, $self);
            }
        } else if ($fromdate_quarter > $current_quarter) {
            /* planning for next quarter */
            $error_flag = $this->processNextQuarter($talent, $fromDate, $toDate, $request, $self);
        }

        return $error_flag;
    }

    public function processLeaveData($talent, $fromDate, $toDate, $current_quarter) {

        //get leave status
        $talentWorkingDay = $this->leaves->getWorkingDayStatus($talent, $fromDate, $toDate);
        $talentWorking = array();
        foreach ($talentWorkingDay as $tod) {
            $talentWorking[] = $tod['attendance_date'];
        }
        //get leave status
        $talentOdsBetweenDate = $this->leaves->getLeaveDayStatus($talent, $fromDate, $toDate);
        $talentLeaves = array();
        foreach ($talentOdsBetweenDate as $tod) {
            $talentLeaves[] = $tod['date_of_leave'];
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
        $plannedLeaveArray = array();
        if ($current_quarter != 1) {
            $planned = $this->leaves->checkPlannedDate($talent);
            foreach ($planned as $plan) {
                $fromDate_leave = $plan['from_date'];
                $toDate_leave = $plan['to_date'];
                $plannedLeaveArray = $this->leaves->createDateRangeArray($fromDate_leave, $toDate_leave);
            }
        }

        $dataDaysLoad['plannedLeaveArray'] = $plannedLeaveArray;
        $dataDaysLoad['weekends'] = $weekendArray;
        $dataDaysLoad['holidays'] = $holidayArray;
        $dataDaysLoad['talentLeaves'] = $talentLeaves;
        $dataDaysLoad['talentWorking'] = $talentWorking;
        $dataDaysLoad['fromDate'] = $fromDate;
        $dataDaysLoad['toDate'] = $toDate;
        $dataDaysLoad['dateCount'] = $fromDate;
        $dataDaysLoad['current_quarter'] = $current_quarter;

        $dataDaysLoad['getMyLeavesLeft'] = $this->leaves->getMyLeavesLeft($talent);
        $dataDaysLoad['compoffTillDate'] = $this->leaves->checkCompoffAgainstDate($talent, $fromDate);
        //echo ($this->leaves->checkCompoffAgainstDate($fromDate));
        // echo $this->db->last_query();
        //die();

        $this->load->view('leaveManagement/daysLoad', $dataDaysLoad);
    }

}
