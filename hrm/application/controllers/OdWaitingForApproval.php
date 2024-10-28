<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OdWaitingForApproval extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('myOd', '', TRUE);
        $this->load->model('odType', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('holiday', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('TalentAttendenceSheet', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        die;
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
            $data['odDetails'] = $this->myOd->getOdwaitingForApprovalFinYearBased($session_data['talentId'], $selectedYear, $isResigned, $selectedTalent);
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['lastCronJobDate'] = $lastCronJobDate;
            $data['selectedYear'] = $selectedYear;
            $data['isResigned'] = $isResigned;
            $data['yearArray'] = $yearArray;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['redirect'] = "";
            $this->load->view('odManagement/odWaitingForApproval', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function allOd() {
        die;
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
            $data['odDetails'] = $this->myOd->getOdwaitingForApprovalFinYearBasedAll($session_data['talentId'], $selectedYear, $isResigned, $session_data['is_management'], $selectedTalent);
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $lastCronJobDate = $this->defaultValues->getValue(2);
            $data['lastCronJobDate'] = $lastCronJobDate;
            $data['selectedYear'] = $selectedYear;
            $data['isResigned'] = $isResigned;
            $data['yearArray'] = $yearArray;
            $data['redirect'] = "/allOd";
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $this->load->view('odManagement/odWaitingForApproval', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function approveOd() {
        $minimumHourRequered = $this->defaultValues->getValue(14);
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $OdId = $this->uri->segment(4);
        $variable = $this->uri->segment(3);
        $redirect = $this->uri->segment(5);
        $checkOdApproved = $this->myOd->checkOdIsApproved($OdId);
        if ($checkOdApproved) {
            $this->session->set_flashdata('flashError', 'Already approved');
        } else {
            $this->db->trans_begin();
            $updateData = array(
                'is_approved' => '1',
                'processed_by' => $session_data['talentId']
            );
            $this->db->where('id', $OdId);
            $queryResult = $this->db->update('talent_od', $updateData);
            $eachOdDates = $this->myOd->getEeachOdDates($OdId);
            foreach ($eachOdDates as $eachDate) {
                $odDate = $eachDate['date_of_od'];
                $hoursOfOd = $eachDate['hours_of_od'];
                if ($hoursOfOd != '00:00:00') {
                    $talent = $eachDate['talent'];
                    //holiday leave checking
                    $holidayStatus = $this->leaves->getDayHolidayStatus($odDate);
                    $weekendArray = array();
                    $weekends = $this->holidaysWeek->getHolidayTalent($talent);
                    foreach ($weekends as $weekend) {
                        $weekendArray[] = strtolower($weekend['week_day']);
                    }
                    $strToTimeDate = strtotime($odDate);
                    $day = strtolower(date("l", $strToTimeDate));

                    $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($odDate);
                    if (in_array($day, $weekendArray) && ($checkDateInStrictlyWorking == 0)) {
                        $holidayStatus = TRUE;
                    }
                    $leaveStatus = $this->leaves->getDayLeaveStatus($odDate, $talent);
                    $extraOdHours = "0";
                    $isHoliday = 0;
                    if ($holidayStatus === TRUE) {
                        $isHoliday = 1;
                    }
                    $isLeave = 0;
                    if ($leaveStatus === TRUE) {
                        $isLeave = 1;
                    }
                    $avgConsider = 1;

                    //get the comp-off status
                    $compOffValue = '';
                    $compOffStatus = $this->TalentAttendenceSheet->getDayCompOffStatus($talent, $odDate);
                    foreach ($compOffStatus as $compOff) {
                        $compOffValue = $compOff['holiday_hr_status'];
                    }
                    $checkAttendenceInserted = $this->TalentAttendenceSheet->getCheckTalentsDateDataExist($talent, $odDate);
                    $compOffValueUpdate = 0;
                    //var_dump($checkAttendenceInserted);
                    if ($checkAttendenceInserted) {
                        foreach ($checkAttendenceInserted as $row) {
                            $id = $row->id;
                            $odHourOld = $row->od_hours;
                            $actualSpentHours = $row->actual_hours_spend;
                        }
                        //update od, productive hours, holiday check, leave check,extra_od_hours,avg_consider
                        //productive hours = $odHourOld+$actualSpentHours+$hoursOfOd
                        $secsNewOd = $this->TalentAttendenceSheet->hourToSecondsConvertion($hoursOfOd);
                        $secsOldOd = $this->TalentAttendenceSheet->hourToSecondsConvertion($odHourOld);
                        $secsSpent = $this->TalentAttendenceSheet->hourToSecondsConvertion($actualSpentHours);
                        $resultAdd = $this->TalentAttendenceSheet->secondsToMinitues($secsNewOd + $secsOldOd + $secsSpent);
                        //check od+current >$minimumHourRequered
                        $totalOdHours = $this->TalentAttendenceSheet->secondsToMinitues($secsNewOd + $secsOldOd);
                        $productiveHours = $resultAdd;
                        if ($holidayStatus === TRUE || $leaveStatus === TRUE) {
                            if ($compOffValue == '1') {
                                $compOffValueUpdate = 1;
                                if (($resultAdd) < $minimumHourRequered) {
                                    $avgConsider = 0;
                                    $compOffValueUpdate = 3;
                                }
                            } else if ($compOffValue == '2') {
                                if (($resultAdd) >= $minimumHourRequered) {
                                    $compOffValueUpdate = 0;
                                } else {
                                    $avgConsider = 0;
                                    $compOffValueUpdate = 3;
                                    $extraOdHours = $totalOdHours;
                                }
                            } else if ($compOffValue == '3') {
                                if (($totalOdHours) >= $minimumHourRequered) {
                                    $compOffValueUpdate = 0;
                                } else {
                                    $avgConsider = 0;
                                    $compOffValueUpdate = 3;
                                    $extraOdHours = $totalOdHours;
                                }
                            } else {
                                if (($resultAdd) >= $minimumHourRequered) {
                                    $compOffValueUpdate = 0;
                                } else {
                                    $compOffValueUpdate = 3;
                                    $avgConsider = 0;
                                }
                            }
                            if ($productiveHours > 0) {
                                //set extra attendance hours as zero because productive hour araised
                                $updateData = array(
                                    'extra_attendance_hrs' => 0
                                );
                                $this->db->where('id', $id);
                                $queryResultUpdate = $this->db->update('talent_attendance', $updateData);
                            }
                        } else {
                            if (($resultAdd) < $minimumHourRequered) {
                                $avgConsider = 0;
                            }
                        }
                        if ($productiveHours < $minimumHourRequered) {
                            $avgConsider = 0;
                            $compOffValueUpdate = 3;
                        }
                        $updateData = array(
                            'od_hours' => $totalOdHours,
                            'productive_hrs' => $productiveHours,
                            'is_holiday' => $isHoliday,
                            'is_leave' => $isLeave,
                            'extra_od_hrs' => $extraOdHours,
                            'avg_consider' => $avgConsider,
                            'holiday_hr_status' => $compOffValueUpdate,
                        );
                        $this->db->where('id', $id);
                        $queryResultUpdate = $this->db->update('talent_attendance', $updateData);
                        //Decline if any time sheet approved or send for approval
                        if ($hoursOfOd != "00:00:00") {
                            $updateData = array(
                                'is_approved' => 0,
                                'send_approval' => 0
                            );
                            $this->db->where('talent', $talent);
                            $this->db->where('date', $odDate);
                            $this->db->where("(is_approved='1' OR send_approval='1')", NULL, FALSE);
                            $queryResultUpdate = $this->db->update('time_prediction', $updateData);
                        }
                    } else {
                        //insert od,productive hours,holiday check,extra_od_hours,avg_consider,talentId,date
                        $productiveHours = $hoursOfOd;
                        if (($hoursOfOd) < $minimumHourRequered) {
                            $avgConsider = 0;
                            $compOffValueUpdate = 3;
                        }
                        $insertData = array(
                            'talent_id' => $talent,
                            'attendance_date' => $odDate,
                            'od_hours' => $hoursOfOd,
                            'productive_hrs' => $productiveHours,
                            'extra_od_hrs' => $extraOdHours,
                            'avg_consider' => $avgConsider,
                            'is_holiday' => $isHoliday,
                            'is_leave' => $isLeave,
                            'holiday_hr_status' => $compOffValueUpdate,
                        );
                        $queryResult = $this->db->insert('talent_attendance', $insertData);
                        //Decline if any time sheet approved or send for approval
                        if ($hoursOfOd != "00:00:00") {
                            $updateData = array(
                                'is_approved' => 0,
                                'send_approval' => 0
                            );
                            $this->db->where('talent', $talent);
                            $this->db->where('date', $odDate);
                            $this->db->where("(is_approved='1' OR send_approval='1')", NULL, FALSE);
                            $queryResultUpdate = $this->db->update('time_prediction', $updateData);
                        }
                    }
                }
            }
            if ($variable == 'id') {
                //apprval of management
                $this->session->set_flashdata('flashSuccess', 'Od added and approved successfully');
                $this->db->trans_commit();
                redirect('MyOds', 'refresh');
            }
            //find the talent id
            $odDetails = $this->myOd->getMyOdTypeById($OdId);
            foreach ($odDetails as $od) {
                $talent = $od['talent'];
            }
            $talentDetails = $this->talent->getTalentById($talent);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }
            //creating notification for reporting manager
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Green',
                'message' => $session_data['firstName'] . ' approved your OD request',
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);
            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Approved successfully');
        }
        if ($redirect) {
            redirect('odWaitingForApproval/allOd', 'refresh');
        } else {
            redirect('odWaitingForApproval', 'refresh');
        }
    }

    public function declainOd() {
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $OdId = $this->uri->segment(4);
        $redirect = $this->uri->segment(5);
        $updateData = array(
            'is_approved' => '2',
            'processed_by' => $session_data['talentId']
        );
        $this->db->where('id', $OdId);
        $queryResult = $this->db->update('talent_od', $updateData);
        if ($queryResult) {
            //find the talent id
            $odDetails = $this->myOd->getMyOdTypeById($OdId);
            foreach ($odDetails as $od) {
                $talent = $od['talent'];
            }
            $talentDetails = $this->talent->getTalentById($talent);
            foreach ($talentDetails as $talet) {
                $talentUserId = $talet['user'];
            }

            //creating notification for reporting manager
            $insertNotificationData = array(
                'user' => $talentUserId,
                'type' => 'Red',
                'message' => $session_data['firstName'] . ' declined your OD request',
                'message_time' => date('Y-m-d H:i:s'),
            );
            //inserting
            $queryResult = $this->db->insert('notifications', $insertNotificationData);

            $this->session->set_flashdata('flashSuccess', 'Declined successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        if ($redirect) {
            redirect('odWaitingForApproval/allOd', 'refresh');
        } else {
            redirect('odWaitingForApproval', 'refresh');
        }
    }

    public function editOdWaitingForApproval() {
        $session_data = $this->session->userdata('logged_in');
        $this->load->helper('date');
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $myOdId = $this->uri->segment(4);
        $editData['redirect'] = $this->uri->segment(5);
        $editData['myOdId'] = $myOdId;
        $editData['talent'] = $talent = $this->myOd->getTalentByOdId($myOdId);
        $editData['talent_name'] = $this->talent->getTalentNameById($talent);
        $lastCronJobDate = $this->defaultValues->getValue(2);
        $editData['lastCronJobDate'] = $lastCronJobDate;

        $odTypes = $this->odType->getOdTypes();
        $odTypesArray = array(
            '' => '[SELECT]'
        );
        foreach ($odTypes as $odType) {
            $odTypesArray[$odType['id']] = $odType['od_type_name'];
        }


        if ($this->input->post()) {
            $editData['redirect'] = $this->input->post('redirect');
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
                //var_dump($updateData);
                $this->db->where('id', $myOdId);
                $queryResult = $this->db->update('talent_od', $updateData);
                if ($queryResult) {
                    $error_flag = 0;
                    while (strtotime($dateCount) <= strtotime($toDate)) {
                        $dateCount = date("Y-m-d", strtotime($dateCount));
                        $hourOfOdText = 'hour[' . $dateCount . ']';
                        $minOfOdText = 'min[' . $dateCount . ']';
                        $hourOfOd = $this->input->post($hourOfOdText);
                        $minOfOd = $this->input->post($minOfOdText);
                        //die();
                        $odTime = date("H:i:s", strtotime($hourOfOd . ':' . $minOfOd . ':00'));
                        /* if od value is changed then revert all compoff and encashment for the date */
                        $this->changeAttendanceDueToOdChange($dateCount, $odTime, $myOdId, $talent);
                        $updateSpecificData = array(
                            'date_of_od' => date('Y-m-d', strtotime($dateCount)),
                            'hours_of_od' => $odTime
                        );
                        $this->db->where('talent_od', $myOdId);
                        $this->db->where('date_of_od', $dateCount);
                        $queryResult = $this->db->update('talent_od_specific', $updateSpecificData);
                        if ($queryResult) {
                            $dateCount = strtotime("+1 day", strtotime($dateCount));
                            $dateCount = date("m/d/Y", $dateCount);
                        } else {
                            $error_flag = 1;
                            break;
                        }
                    }
                    if ($error_flag == 0) {
                        //if success
                        $this->db->trans_commit();
                    } else {
                        //failure
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
                //die();
                $this->session->set_flashdata('flashSuccess', 'Updated successfully');
                redirect('odWaitingForApproval/' . $editData['redirect'], 'refresh');
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
                $odOwner = $myOdSpecific['talent'];
            }
            $fromDateStandard = date('Y-m-d', strtotime($fromDate));
            $toDateStandard = date('Y-m-d', strtotime($toDate));
            //for coloring
            //get od status
            $talentOdsBetweenDate = $this->myOd->getOdDayStatusWithoutCurrentOd($odOwner, $fromDateStandard, $toDateStandard, $myOdId);
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
            $leaves = $this->leaves->getDayStatus($odOwner, $fromDate, $toDate);
            foreach ($leaves as $leave) {
                $leaveArray[] = $leave['date_of_leave'];
            }
            $weekendArray = array();
            $weekends = $this->holidaysWeek->getHolidayTalent($odOwner);
            foreach ($weekends as $weekend) {
                $weekendArray[] = strtolower($weekend['week_day']);
            }
            $editData['weekends'] = $weekendArray;
            $editData['holidays'] = $holidayArray;
            $editData['leaves'] = $leaveArray;
            $editData['talentOds'] = $talentOds;
            $editData['odTypes'] = $odTypesArray;
            $editData['odOwner'] = $odOwner;
        }
        $this->load->view('odManagement/editOdWaitingForApproval', $editData);
    }

    public function changeAttendanceDueToOdChange($dateCount, $odTime, $myOdId, $talent) {
        /* check the od is approved or not, if not approved then no need to continue */
        $odDetails = $this->myOd->getOdDetailsBYOdId($myOdId);
        if ($odDetails->is_approved != 1) {
            return;
        }
        $previousOdTime = $this->myOd->findOdDetailsByOdIdAndDate($dateCount, $myOdId);
        /* if previous od time and current od time are not equal then */
        if (strtotime($previousOdTime) != strtotime($odTime)) {
            /* revert deails of attendance table */
            $this->calculateProductiveHours($dateCount, $myOdId, $odTime, $talent, $previousOdTime);
        }
        return;
    }

    public function calculateProductiveHours($dateCount, $myOdId, $odTime, $talent, $previousOdTime) {
        $productiveHours = 0;
        $avgConsider = 0;
        $attendacneDetails = $this->TalentAttendenceSheet->getDateDetails($talent, $dateCount);
        $previousAttendanceOdTime = $attendacneDetails->od_hours;
        $previousActualHours = $attendacneDetails->actual_hours_spend;

        $previousActualSec = $this->TalentAttendenceSheet->hourToSecondsConvertion($previousActualHours);
        $odTimeSec = $this->TalentAttendenceSheet->hourToSecondsConvertion($odTime);
        $previousOdTimeSec = $this->TalentAttendenceSheet->hourToSecondsConvertion($previousOdTime);
        $previousAttendanceOdTimeSec = $this->TalentAttendenceSheet->hourToSecondsConvertion($previousAttendanceOdTime);

        $currentAttendanceOdTimeSec = $previousAttendanceOdTimeSec - $previousOdTimeSec + $odTimeSec;
        $totalTime = $currentAttendanceOdTimeSec + $previousActualSec;

        $resultAdd = $this->TalentAttendenceSheet->secondsToMinitues($totalTime);
        $minimumHourRequered = $this->defaultValues->getValue(14);
        if ($resultAdd >= $minimumHourRequered) {
            $avgConsider = 1;
            $productiveHours = $resultAdd;
        }
        $updateData = array(
            'od_hours' => $odTime,
            'productive_hrs' => $productiveHours,
            'extra_attendance_hrs' => 0,
            'avg_consider' => $avgConsider,
            'holiday_hr_status' => 0,
            'encashment' => 0,
        );
        $this->db->where('attendance_date', $dateCount);
        $this->db->where('talent_id', $talent);
        $queryResult = $this->db->update('talent_attendance', $updateData);
        return;
    }

    public function savenote() {
        $alertify_text = $this->input->get('alertify_text');
        $id = $this->input->get('id');
        $updateData = array(
            'decline_note' => $alertify_text
        );
        $this->db->where('id', $id);
        $queryResult = $this->db->update('talent_od', $updateData);
        if ($queryResult) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function dateDetails() {
        $odOwner = $this->input->get('odOwner');
        $date = DateTime::createFromFormat('Y-m-d', $this->input->get('date'));
        $odDate = $fromDate = $date->format('Y-m-d');
        $dayDetails = $this->TalentAttendenceSheet->getDateDetails($odOwner, $odDate);
        $data['actualHoursSpend'] = isset($dayDetails->actual_hours_spend) ? $dayDetails->actual_hours_spend : '';
        $data['odHours'] = isset($dayDetails->od_hours) ? $dayDetails->od_hours : '';
        $data['productiveHours'] = isset($dayDetails->productive_hrs) ? $dayDetails->productive_hrs : '';
        $data['extraAttendanceHours'] = isset($dayDetails->extra_attendance_hrs) ? $dayDetails->extra_attendance_hrs : '';
        $data['extraOdHours'] = isset($dayDetails->extra_od_hrs) ? $dayDetails->extra_od_hrs : '';
        $this->load->view('odManagement/dateDetails', $data);
    }

}
