<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MyOds extends CI_Controller {

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
        $this->load->model('timePredictionModel', '', TRUE);
    }

    public function index() {
        die;
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['myOdDetails'] = $this->myOd->getMyOds($session_data['talentId']);
            $this->load->view('odManagement/myOds', $data);
        } else {
//If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewMyOd() {
        die;
        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $is_management = $session_data['is_management'];
            $data['username'] = $session_data['username'];
            $autoApproval = $this->myOd->getOdAutoApprovalStatus($session_data['talentId']);

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->helper('date');
            $odTypes = $this->odType->getOdTypes();
            $odTypesArray = array(
                '' => '[SELECT]'
            );
            foreach ($odTypes as $odType) {
                $odTypesArray[$odType['id']] = $odType['od_type_name'];
            }
            $data['odTypes'] = $odTypesArray;
            $this->load->library('form_validation');
            //for form validayion, setting rules
            $this->form_validation->set_rules('reason', 'reason', 'trim|required|xss_clean');
            $data['selected_odType'] = $this->input->post('odType');
            //for max date restrictions
            $month = date('m');
            $year = date('Y');
            $day = date('d');
            $endDayOfMonth = $this->defaultValues->getValue(13);
            if ($day > $endDayOfMonth) {
                $datemax = date("Y-m-d", strtotime(date($year . '-' . $month . '-' . $endDayOfMonth) . " +1 month"));
            } else {
                $datemax = date("Y-m-d", strtotime($year . '-' . $month . '-' . $endDayOfMonth));
            }
            if ($this->form_validation->run() == TRUE) {
//Validation Success.  
                $reason = $this->input->post('reason');
                $odType = $this->input->post('odType');
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
                $dateCount = $fromDate = $date->format('Y-m-d');
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
                $toDate = $date->format('Y-m-d');
                if ($autoApproval === TRUE) {
                    $isApproved = 1;
                } else {
                    $isApproved = 0;
                }
                $insertTalentOdData = array(
                    'od_type' => $odType,
                    'reason' => $reason,
                    'talent' => $session_data['talentId'],
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'created_on' => date('Y-m-d H:i:s'),
                    'is_approved' => $isApproved
                );
                $queryResult = $this->db->insert('talent_od', $insertTalentOdData);
                if ($queryResult) {
                    $error_flag = 0;
                    $insert_id = $this->db->insert_id();
                    while (strtotime($dateCount) <= strtotime($toDate)) {
                        $dateCount = date("Y-m-d", strtotime($dateCount));
                        $hourOfOdText = 'hour[' . $dateCount . ']';
                        $minOfOdText = 'min[' . $dateCount . ']';

                        if (strtotime($datemax) >= strtotime($dateCount)) {
                            $hourOfOd = $this->input->post($hourOfOdText);
                            $minOfOd = $this->input->post($minOfOdText);
//die();
                            $odTime = date("H:i:s", strtotime($hourOfOd . ':' . $minOfOd . ':00'));
//die();
                            $insertSpecificData = array(
                                'talent_od' => $insert_id,
                                'date_of_od' => date('Y-m-d', strtotime($dateCount)),
                                'date_of_od' => date('Y-m-d', strtotime($dateCount)),
                                'hours_of_od' => $odTime
                            );
                            $queryResult = $this->db->insert('talent_od_specific', $insertSpecificData);
                            if ($queryResult) {
                                if ($autoApproval === TRUE) {
                                    //update talent Attendance table
                                    $this->updateTalentAttendanceTable($session_data['talentId'], $odTime, date('Y-m-d', strtotime($dateCount)));
                                    //if timesheet filled then cancel
                                    $updateData = array(
                                        'is_approved' => 0,
                                        'send_approval' => 0
                                    );
                                    $this->db->where('talent', $session_data['talentId']);
                                    $this->db->where('date', date('Y-m-d', strtotime($dateCount)));
                                    $this->db->where("(is_approved='1' OR send_approval='1')", NULL, FALSE);
                                    $queryResultUpdate = $this->db->update('time_prediction', $updateData);
                                }
                                $dateCount = strtotime("+1 day", strtotime($dateCount));
                                $dateCount = date("Y-m-d", $dateCount);
                            } else {
                                $error_flag = 1;
                                break;
                            }
                        } else {
                            $dateCount = strtotime("+1 day", strtotime($dateCount));
                            $dateCount = date("Y-m-d", $dateCount);
                        }
                    }
                    if ($error_flag == 0) {
//ger reporting manager
                        $talentDetails = $this->talent->getTalentById($session_data['talentId']);
                        foreach ($talentDetails as $talet) {
                            $reportingManager = $talet['reporting_manager'];
                        }
                        $talentDetails = $this->talent->getTalentById($reportingManager);
                        foreach ($talentDetails as $talet) {
                            $reportingManagerUserId = $talet['user'];
                        }
                        if ($is_management === TRUE) {
                            $reportingManagerUserId = $session_data['id'];
                        }
                        //creating notification for reporting manager
                        $insertNotificationData = array(
                            'user' => $reportingManagerUserId,
                            'type' => 'Yellow',
                            'message' => $session_data['firstName'] . ' created an OD request',
                            'message_time' => date('Y-m-d H:i:s'),
                        );
                        //inserting
                        $queryResult = $this->db->insert('notifications', $insertNotificationData);
                        //if success
                        $this->db->trans_commit();

                        if ($autoApproval === TRUE) {
                            $this->session->set_flashdata('flashSuccess', 'Od Added and approved successfully');
                        }
                        $this->session->set_flashdata('flashSuccess', 'Added successfully');
                        redirect('MyOds', 'refresh');
                    } else {
                        //failure
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                } else {
//failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $this->load->view('odManagement/addNewMyOd', $data);
        } else {
//If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editMyOd() {
        $session_data = $this->session->userdata('logged_in');
        $this->load->helper('date');
        $this->load->helper(array('form'));

//using url for get id
        $this->load->helper('url');
        $myOdId = $this->uri->segment(4);
        $editData['myOdId'] = $myOdId;

        $odTypes = $this->odType->getOdTypes();
        $odTypesArray = array(
            '' => '[SELECT]'
        );
        foreach ($odTypes as $odType) {
            $odTypesArray[$odType['id']] = $odType['od_type_name'];
        }
        $editData['odTypes'] = $odTypesArray;
        $editData['talent'] = $session_data['talentId'];
//for max date restrictions
        $month = date('m');
        $year = date('Y');
        $day = date('d');
        $endDayOfMonth = $this->defaultValues->getValue(13);
        if ($day > $endDayOfMonth) {
            $datemax = date("Y-m-d", strtotime(date($year . '-' . $month . '-' . $endDayOfMonth) . " +1 month"));
        } else {
            $datemax = date("Y-m-d", strtotime($year . '-' . $month . '-' . $endDayOfMonth));
        }
        if ($this->input->post()) {
            $this->db->trans_begin();
            $this->load->library('form_validation');

//for form validayion, setting rules
            $this->form_validation->set_rules('reason', 'Reason', 'trim|required|xss_clean');

            $reason = $this->input->post('reason');
            $odType = $this->input->post('odType');
            $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
            $dateCount = $fromDate = $date->format('Y-m-d');
            $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
            $toDate = $date->format('Y-m-d');


            if ($this->form_validation->run() == TRUE) {
//Validation Success.  
// Do update operation here
                $updateData = array(
                    'od_type' => $odType,
                    'reason' => $reason
                );
                $this->db->where('id', $myOdId);
                $queryResult = $this->db->update('talent_od', $updateData);
                if ($queryResult) {
                    $error_flag = 0;
                    while (strtotime($dateCount) <= strtotime($toDate)) {
                        $dateCount = date("Y-m-d", strtotime($dateCount));
                        $hourOfOdText = 'hour[' . $dateCount . ']';
                        $minOfOdText = 'min[' . $dateCount . ']';
                        if (strtotime($datemax) >= strtotime($dateCount)) {
                            $hourOfOd = $this->input->post($hourOfOdText) ? $this->input->post($hourOfOdText) : 0;
                            $minOfOd = $this->input->post($minOfOdText) ? $this->input->post($minOfOdText) : 0;
                            $odTime = date("H:i:s", strtotime($hourOfOd . ':' . $minOfOd . ':00'));
                            $updateSpecificData = array(
                                'date_of_od' => date('Y-m-d', strtotime($dateCount)),
                                'hours_of_od' => $odTime
                            );
                            $this->db->where('talent_od', $myOdId);
                            $this->db->where('date_of_od', $dateCount);
                            $queryResult = $this->db->update('talent_od_specific', $updateSpecificData);
                            if ($queryResult) {
                                $dateCount = strtotime("+1 day", strtotime($dateCount));
                                $dateCount = date("Y-m-d", $dateCount);
                            } else {
                                $error_flag = 1;
                                break;
                            }
                        } else {
                            $dateCount = strtotime("+1 day", strtotime($dateCount));
                            $dateCount = date("Y-m-d", $dateCount);
                        }
                        if ($error_flag == 0) {
//if success
                            $this->db->trans_commit();
                            $this->session->set_flashdata('flashSuccess', 'Updated successfully');
                        } else {
//failure
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('flashError', 'Some error found');
                        }
                    }
                }
                redirect('MyOds', 'refresh');
            }
            $editData["odTypeDetailsById"] = [0 => array(
                    'od_type_name' => $odTypeName
            )];
        } else {
//for get edit details
            $myOdDetailsById = $editData['myOdDetailsById'] = $this->myOd->getMyOdTypeById($myOdId);
            $editData['myOdSpecificByMyOdId'] = $this->myOd->getMyOdSpecificByMyOdId($myOdId);
            foreach ($myOdDetailsById as $myOdSpecific) {
                $fromDate = $myOdSpecific['from_date'];
                $toDate = $myOdSpecific['to_date'];
            }
            $fromDateStandard = date('Y-m-d', strtotime($fromDate));
            $toDateStandard = date('Y-m-d', strtotime($toDate));
//for coloring
//get od status
            $talentOdsBetweenDate = $this->myOd->getOdDayStatusWithoutCurrentOd($session_data['talentId'], $fromDateStandard, $toDateStandard, $myOdId);
            $talentOds = array();
            foreach ($talentOdsBetweenDate as $tod) {
                $talentOds[] = $tod['date_of_od'];
            }
//finding holidays and leaves between the date
            $holidayArray = array();
            $holidays = $this->holiday->getHolidayStatus($fromDate, $toDate);
            foreach ($holidays as $holiday) {
                $holidayArray[] = $holiday['date_of_holiday'];
            }
            $leaveArray = array();
            $leaves = $this->leaves->getDayStatus($session_data['talentId'], $fromDate, $toDate);
            foreach ($leaves as $leave) {
                $leaveArray[] = $leave['date_of_leave'];
            }
            $weekendArray = array();
            $weekends = $this->holidaysWeek->getHolidayTalent($session_data['talentId']);
            foreach ($weekends as $weekend) {
                $weekendArray[] = strtolower($weekend['week_day']);
            }
            $editData['weekends'] = $weekendArray;
            $editData['holidays'] = $holidayArray;
            $editData['leaves'] = $leaveArray;
            $editData['talentOds'] = $talentOds;
            $editData['odTypes'] = $odTypesArray;
        }
        $this->load->view('odManagement/editMyOd', $editData);
    }

    public function showDatesBetweenDates() {
        $this->load->helper(array('form'));
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
        $fromDateStandard = $fromDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
        $toDateStandard = $toDate = $date->format('Y-m-d');
//Field validation succeeded.  Validate date
        $month = date('m');
        $year = date('Y');
        $day = date('d');
        $last = $this->TalentAttendenceSheet->getLastDate();
        $lastDate = isset($last->last_date) ? $last->last_date : null;
        $endDayOfMonth = $this->defaultValues->getValue(13);
        if ($day > $endDayOfMonth) {
            $datemax = date("Y-m-d", strtotime(date($year . '-' . $month . '-' . $endDayOfMonth) . " +1 month"));
        } else {
            $datemax = date("Y-m-d", strtotime($year . '-' . $month . '-' . $endDayOfMonth));
        }
//if todate is greater than endDayOfMonth of the current month then not allows to apply
        if (strtotime($datemax) < strtotime($toDate) || strtotime($datemax) < strtotime($fromDate)) {
            echo 1;
        } else if (strtotime($toDate) < strtotime($fromDate)) {
//to date validation false
            echo 2;
        } else if (strtotime($fromDate) <= strtotime($lastDate)) {
//cronjob run for the date
            echo 3;
        } else {
            $session_data = $this->session->userdata('logged_in');
//get od status
            $talentOdsBetweenDate = $this->myOd->getOdDayStatus($session_data['talentId'], $fromDateStandard, $toDateStandard);
            $talentOds = array();
            foreach ($talentOdsBetweenDate as $tod) {
                $talentOds[] = $tod['date_of_od'];
            }
//finding holidays and leaves between the date
            $holidayArray = array();
            $holidays = $this->holiday->getHolidayStatus($fromDate, $toDate);
            foreach ($holidays as $holiday) {
                $holidayArray[] = $holiday['date_of_holiday'];
            }
            $leaveArray = array();
            $leaves = $this->leaves->getDayStatus($session_data['talentId'], $fromDate, $toDate);
            foreach ($leaves as $leave) {
                $leaveArray[] = $leave['date_of_leave'];
            }
            $weekendArray = array();
            $weekends = $this->holidaysWeek->getHolidayTalent($session_data['talentId']);
            foreach ($weekends as $weekend) {
                $weekendArray[] = strtolower($weekend['week_day']);
            }
            $dataDaysLoad['weekends'] = $weekendArray;
            $dataDaysLoad['holidays'] = $holidayArray;
            $dataDaysLoad['leaves'] = $leaveArray;
            $dataDaysLoad['talentOds'] = $talentOds;
            $dataDaysLoad['fromDate'] = $fromDate;
            $dataDaysLoad['toDate'] = $toDate;
            $dataDaysLoad['dateCount'] = $fromDate;
            $dataDaysLoad['talent'] = $session_data['talentId'];


            $this->load->view('odManagement/daysLoad', $dataDaysLoad);
        }
    }

    public function updateTalentAttendanceTable($talent, $time, $date) {
        $minimumHourRequered = $this->defaultValues->getValue(14);
//        $minimumHourRequeredTime = date("H:i:s",  strtotime($minimumHourRequered));
        //check already have entry in attendance table
        $checkAlreadyAttendance = $this->TalentAttendenceSheet->getCheckTalentsDateDataExist($talent, $date);
        $extraOd = 0;
        $productiveHour = 0;
        $compOffStatus = 0;
        $averageConsider = 0;
        $holidayHrStatus = 0;
        $odHour = 0;
        //check the date is holiday or not
        $holidayStatus = $this->getHolidayStatusByDate($talent, $date);
        $leaveStatus = $this->leaves->getDayLeaveStatus($date, $talent);
        if (false === $checkAlreadyAttendance) {
            //if not then insert the attendance
            if (TRUE === $holidayStatus || TRUE === $leaveStatus) {
                if ($time >= $minimumHourRequered) {
                    $productiveHour = $time;
                    $odHour = $time;
                    $averageConsider = 1;
                } else {
                    $odHour = $time;
                    $extraOd = $time;
                    $holidayHrStatus = 2;
                }
            } else {
                if ($time >= $minimumHourRequered) {
                    $productiveHour = $time;
                    $odHour = $time;
                    $averageConsider = 1;
                } else {
                    $odHour = $time;
                }
            }
            //insert attendance here
            $insertData = array(
                'talent_id' => $talent,
                'attendance_date' => $date,
                'od_hours' => $odHour,
                'productive_hrs' => $productiveHour,
                'extra_od_hrs' => $extraOd,
                'avg_consider' => $averageConsider,
                'is_holiday' => $holidayStatus,
                'is_leave' => $leaveStatus,
            );
            $queryResult = $this->db->insert('talent_attendance', $insertData);
        } else {
            //get attendance detail for the date
            $attendanceOfTheDay = $this->TalentAttendenceSheet->getDateDetails($talent, $date);
            $id = $attendanceOfTheDay->id;
            $odHourOld = $attendanceOfTheDay->od_hours;
            $extraAttendanceHourOld = $attendanceOfTheDay->extra_attendance_hrs;
            $extraOdHourOld = $attendanceOfTheDay->extra_od_hrs;
            $actualSpentHours = $attendanceOfTheDay->actual_hours_spend;
            //all the hr spend on the day 
            $arrayOfAllHours = array(
                0 => $odHourOld,
                1 => $actualSpentHours,
                3 => $extraAttendanceHourOld,
                4 => $extraOdHourOld,
                5 => $time,
            );
            //all od hr spend on the day
            $arrayOfOdHours = array(
                0 => $odHourOld,
                1 => $time,
                2 => $extraOdHourOld,
            );
            //finding sum of hours
            $resultAddAll = $this->timePredictionModel->sum_time($arrayOfAllHours);
            $resultAddOd = $this->timePredictionModel->sum_time($arrayOfOdHours);
            if (TRUE === $holidayStatus || TRUE === $leaveStatus) {
                if ($resultAddAll < $minimumHourRequered) {
                    $extraOd = $resultAddOd;
                    $odHour = $resultAddOd;
                    $holidayHrStatus = 2;
                } else {
                    $productiveHour = $resultAddAll;
                    $odHour = $resultAddOd;
                    $averageConsider = 1;
                }
            } else {
                if ($resultAddAll < $minimumHourRequered) {
                    $odHour = $resultAddOd;
                } else {
                    $productiveHour = $resultAddAll;
                    $odHour = $resultAddOd;
                    $averageConsider = 1;
                }
            }
            //update attendance here
            $updateData = array(
                'od_hours' => $odHour,
                'productive_hrs' => $productiveHour,
                'is_holiday' => $holidayStatus,
                'is_leave' => $leaveStatus,
                'extra_od_hrs' => $extraOd,
                'avg_consider' => $averageConsider,
                'holiday_hr_status' => $holidayHrStatus,
            );
            //var_dump($updateData);
            $this->db->where('id', $id);
            //die();
            $queryResultUpdate = $this->db->update('talent_attendance', $updateData);
        }
    }

    public function getHolidayStatusByDate($talent, $date) {
        $holidayStatus = $this->leaves->getDayHolidayStatus($date);
        $weekendArray = array();
        $weekends = $this->holidaysWeek->getHolidayTalent($talent);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }
        $strToTimeDate = strtotime($date);
        $day = strtolower(date("l", $strToTimeDate));
        $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($date);
        if (in_array($day, $weekendArray) && ($checkDateInStrictlyWorking == 0)) {
            $holidayStatus = TRUE;
        }
        return $holidayStatus;
    }

    public function declainOd() {
        $this->load->helper('url');
        $myOdId = $this->uri->segment(3);
        $this->db->set('is_approved', 2, FALSE);
        $this->db->where('id',$myOdId);
        $queryResult = $this->db->update('talent_od');
        if ($queryResult) {
            $this->session->set_flashdata('flashSuccess', 'cancelled successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        redirect("myOds", "refresh");
    }

}
