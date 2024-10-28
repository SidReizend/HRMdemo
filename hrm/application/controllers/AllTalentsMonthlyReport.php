<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AllTalentsMonthlyReport extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('talentAttendance', '', TRUE);
        $this->load->model('TimePredictionModel', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('leaves', '', TRUE);
    }

    public function index() {

        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $yearArray = array(
                '' => '[SELECT YEAR]'
            );
            $earliest_year = 2015;
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = $year;
            }
            $monthArray = array(
                '' => '[SELECT MONTH]'
            );
            $months = $this->talentAttendenceSheet->getMonths();
            foreach ($months as $month) {
                $monthArray[$month['id']] = $month['month'];
            }

            $data['selectedMonth'] = $this->input->post('month');
            $data['selectedYear'] = $this->input->post('year');
            $data['years'] = $yearArray;
            $data['months'] = $monthArray;
            $monthYearReport = array();
            if ($this->input->post()) {
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $monthYearReport = $this->talentAttendenceSheet->getAllMonthReport($month, $year);
            }
            $data['monthYearReport'] = $monthYearReport;
            $this->load->view('attendenceManagement/allTalentMonthlyReport', $data);
        } else {
//If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function generateSalary() {

        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $yearArray = array(
                '' => '[SELECT YEAR]'
            );
            $earliest_year = 2015;
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = $year;
            }
            $monthArray = array(
                '' => '[SELECT MONTH]'
            );
            $months = $this->talentAttendenceSheet->getMonths();
            foreach ($months as $month) {
                $monthArray[$month['id']] = $month['month'];
            }

            $data['selectedMonth'] = $this->input->post('month');
            $data['selectedYear'] = $this->input->post('year');
            $data['years'] = $yearArray;
            $data['months'] = $monthArray;
            $monthYearReport = array();
            if ($this->input->post()) {
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $monthYearReport = $this->talentAttendenceSheet->getAllMonthReport($month, $year);
            }
            $data['monthYearReport'] = $monthYearReport;
            $this->load->view('attendenceManagement/allTalentSalaryProcessing', $data);
        } else {
//If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function generateReport() {
        $startDayOfMonth = $this->defaultValues->getValue(3);
        $endDayOfMonth = $this->defaultValues->getValue(4);
        $month = date('M', strtotime($endDayOfMonth));
        $year = date('Y', strtotime($endDayOfMonth));
        $data['startDayOfMonth'] = $startDayOfMonth;
        $data['endDayOfMonth'] = $endDayOfMonth;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['disabled'] = '';
        if (strtotime(date('Y-m-d')) <= strtotime($endDayOfMonth)) {
            $data['disabled'] = 'disabled';
        }
        $this->load->view('attendenceManagement/generateReport', $data);
    }

    public function triggerProcess() {
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $fromDate = $this->defaultValues->getValue(3);
        $endDate = $this->defaultValues->getValue(4);
        $monthBackend = date('M', strtotime($endDate));
        $yearBackend = date('Y', strtotime($endDate));
        if ($monthBackend != $month || $yearBackend != $year) {
            $status = "error";
            $msg = "Already Processed for the month";
            print_r(json_encode(['status' => $status, 'msg' => $msg]));
            die;
        }
        $monthDigit = date('m', strtotime($endDate));

        //get talents list whose monthly process already done
        $talentsProcessDoneList = $this->talent->getTalentsMonthlyProcessAlreadyDone($monthDigit, $year);

        if (strtotime(date('Y-m-d')) <= strtotime($endDate)) {
            $status = "error";
            $msg = "Date not reached to run";
            print_r(json_encode(['status' => $status, 'msg' => $msg]));
            die;
        }

        //check attandace uploaded for all the dates
//        $isAttendanceUploaded = $this->talent->getAttendandanceUploadedCheck($endDate);
//
//        if (!$isAttendanceUploaded) {
//            $status = "error";
//            $msg = "Attendance uploading pending for the month";
//            print_r(json_encode(['status' => $status, 'msg' => $msg]));
//            die;
//        }


        $createLeaveNotificationStatus = $this->leaves->getStatusLeaveWaitingForApprovalBetweenDates($fromDate, $endDate);
        if ($createLeaveNotificationStatus) {
            $status = "error";
            $msg = "Leave approvals pending";
            print_r(json_encode(['status' => $status, 'msg' => $msg]));
            die;
        }

        $createCompNotificationStatus = $this->talentAttendance->getStatusCompWaitingForApprovalBetweenDates($fromDate, $endDate);
        if ($createCompNotificationStatus) {
            $status = "error";
            $msg = "Comp-off approvals pending";
            print_r(json_encode(['status' => $status, 'msg' => $msg]));
            die;
        }

        $createEncashNotificationStatus = $this->talentAttendance->getStatusEncashmentWaitingForApprovalBetweenDates($fromDate, $endDate);
        if ($createEncashNotificationStatus) {
            $status = "error";
            $msg = "Encashment approvals pending";
            print_r(json_encode(['status' => $status, 'msg' => $msg]));
            die;
        }

        $timesheetApprovalPending = $this->talentAttendance->timesheetApprovalPending($fromDate, $endDate);
        if ($timesheetApprovalPending) {
            $status = "error";
            $msg = "Timesheet Approval Pending";
            print_r(json_encode(['status' => $status, 'msg' => $msg]));
            die;
        }

        //get talents joined before joining date
        $talents = $this->talent->getTalentsByDateOfJoining($endDate, $talentsProcessDoneList);
        if (empty($talents)) {
            $this->addResignedTalentsToMonthlyProcess($endDate);
            $this->changeMonthlyProcessDate();
            $status = 'done';
            $msg = 'Completed attendance process';
            print_r(json_encode(['status' => $status, 'msg' => $msg]));
            die;
        }
        $data['talents'] = $talents;
        $data['month'] = $month;
        $data['year'] = $year;
        $view = $this->load->view('attendenceManagement/talentsPendingToProcess', $data, TRUE);
        $status = 'success';
        print_r(json_encode(['status' => $status, 'view' => $view]));
    }

    public function addResignedTalentsToMonthlyProcess($dateTo) {
        $month = date('m', strtotime($dateTo));
        $year = date('Y', strtotime($dateTo));
        $reportResigned = $this->talentAttendance->getReportOfResignedTalents($month, $year);
        foreach ($reportResigned as $resigned) {
            $insertMonthlyProcess = array(
                'talent_id' => $resigned['talent_id'],
                'month' => $month,
                'year' => $year,
                'lop_by_process' => $resigned['lop_by_process'],
                'lop_by_shortage' => $resigned['lop_by_shortage'],
                'total_present_days' => $resigned['total_present_days'],
                'hrs_required' => $resigned['hrs_required'],
                'hrs_earned' => $resigned['hrs_earned'],
                'hrs_shortage' => $resigned['hrs_shortage'],
                'encashment' => $resigned['encashment'],
                'encashment_for_extra' => $resigned['encashment_for_extra'],
                'extra_od_hrs' => $resigned['extra_od_hrs'],
                'total_travel_hours' => $resigned['total_travel_hours'],
            );
            $this->db->insert('talent_monthly_report', $insertMonthlyProcess);
        }
    }

    public function changeMonthlyProcessDate() {
        $endDate = $this->defaultValues->getValue(4);
        $updateValue = array(
            'value' => $endDate,
        );
        $this->db->where('id', 2);
        $this->db->update('default_values', $updateValue);
        $nextStartDate = date('Y-m-d', strtotime(date($endDate) . " +1 day"));
        $nextEndDate = date('Y-m-t', strtotime(date($nextStartDate)));
        $updateData = array(
            'value' => $nextStartDate,
        );
        $this->db->where('id', 3);
        $this->db->update('default_values', $updateData);
        $updateData1 = array(
            'value' => $nextEndDate,
        );
        $this->db->where('id', 4);
        $this->db->update('default_values', $updateData1);
    }

    public function saveSalaryAmount() {
        $id = $this->input->post('id');
        $totalSalary = $this->input->post('totalSalary');
        $totalDeduction = $this->input->post('totalDeduction');
        $totalAddition = $this->input->post('totalAddition');
        $totalClaims = $this->input->post('totalClaims');
        $monthlyReport = $this->talentAttendance->getMonthlyReportById($id);
        if ($monthlyReport) {
            $totalLop = $monthlyReport->lop_by_shortage + $monthlyReport->lop_by_process + $monthlyReport->lop_due_to_lop;
            $totalEncashment = $monthlyReport->encashment_for_extra + $monthlyReport->encashment;
            $totalDays = $monthlyReport->total_days;
            $dayPay = floatval($totalSalary / $totalDays);
            $actualSalary = round($totalSalary + ($dayPay * $totalEncashment) - ($dayPay * $totalLop) - $totalDeduction);
            $updateValue = array(
                'total_salary' => $totalSalary,
                'total_deductions' => $totalDeduction,
                'actual_salary' => $actualSalary,
                'total_additions' => $totalAddition,
                'total_claims' => $totalClaims,
            );
            $this->db->where('id', $id);
            $this->db->update('talent_monthly_report', $updateValue);
            print_r(json_encode(['status' => 'success']));
        }
    }

    public function runProcess() {
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $talent = $this->input->post('talent');

        $endDate = $this->defaultValues->getValue(4);
        $monthBackend = date('M', strtotime($endDate));
        $yearBackend = date('Y', strtotime($endDate));
        $status = "error";
        $msg = "Some error found";
        if ($monthBackend != $month || $yearBackend != $year) {
            $status = 'error';
            $msg = "Already process done for the month";
        }
        $monthDigit = date('m', strtotime($endDate));
        //check data already exist in monthly report before insert it
        $isMonthlyProcessed = $this->talent->checkMonthlyProcessDoneForTheTalent($talent, $monthDigit, $yearBackend);
        if (!$isMonthlyProcessed) {
            $this->db->trans_begin();
            $this->runProcessTalent($talent, $endDate);
            $this->db->trans_commit();
            $status = "success";
            $msg = "Successfully processed";
        }
        $response = ['status' => $status, 'msg' => $msg];
        print_r(json_encode($response));
    }

    public function runProcessTalent($talent, $dateTo, $isResigned = 0, $tempResigned = 0) {
        $encashment = 0;
        $noOfDaysPay = 0;
        $presentDays = 0;
        $totalProductiveSeconds = 0;
        $shortage = 0;
        $lopCount = 0;
        $lopByProcess = 0;
        $elUsed = 0;
        $compUsed = 0;
        $lopByShortage = 0;
        $secondsShortage = 0;
        $secondsTravelTime = 0;
        $encashmentForExtra = 0;
        $leave_before_after_holiday = 0;
        $extraSeconds = 0;
        $startDayOfMonth = $this->defaultValues->getValue(3);
        $avgHourShortageCalculation = $this->defaultValues->getValue(41);
        $month = date('m', strtotime($dateTo));
        $year = date('Y', strtotime($dateTo));
        $talentDetails = $this->talent->getTalentDetailsForMonthlyProcess($talent);
        $averageHourRequired = $talentDetails->average_working_hour_per_day;
        $averageHourSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($averageHourRequired);
        $avgHourShortageCalculationSec = $this->talentAttendenceSheet->hourToSecondsConvertion($avgHourShortageCalculation);
        if (strtotime($startDayOfMonth) <= strtotime($talentDetails->date_of_joining)) {
            $dateCount = $talentDetails->date_of_joining;
        } else {
            $dateCount = $startDayOfMonth;
        }
        while (strtotime($dateTo) >= strtotime($dateCount)) {
            $noOfDaysPay++;
            //get attendance details
            $rowTalentAttance = $this->talent->attendanceOfTalentForDate($talent, $dateCount);
            if (!$rowTalentAttance) {
                $dateNext = strtotime("+1 day", strtotime($dateCount));
                $dateCount = date("Y-m-d", $dateNext);
                continue;
            }
            if ($rowTalentAttance->encashment == 1) {
                $encashment++;
            }
            if ($rowTalentAttance->is_public_holiday_worked == 1) {
                $encashment++;
            }
            if ($rowTalentAttance->avg_consider == 0 && $rowTalentAttance->is_holiday == 0 && $rowTalentAttance->is_leave == 0) {
                //apply leave for the day
                $request = "Leave applied due to abset during monthly process";
                $ltype = $this->applyLeave($talent, $dateCount, $dateCount, $request, 0, 0, $tempResigned);
                if ($ltype == 0) {
                    $lopByProcess++;
                    $lopCount++;
                }
            }
            if ($rowTalentAttance->is_leave == 1 && $this->talent->checkCurrentDayIsLop($talent, $dateCount)) {
                $lopByProcess++;
                $lopCount++;
            }
            if ($rowTalentAttance->avg_consider == 1) {
                $presentDays++;
            }
            if ($rowTalentAttance->is_holiday == 1 && $rowTalentAttance->avg_consider == 0 && $this->checkNextWorkingDayIsLeave($talent, $dateCount) && $this->checkPreviousWorkingDayIsLeave($talent, $dateCount)) {
                $leave_before_after_holiday = 1;
            }
            if ($rowTalentAttance->is_holiday == 1 && $rowTalentAttance->is_leave == 0 && $rowTalentAttance->avg_consider == 0 && $this->checkNextWorkingDayIsLop($talent, $dateCount) && $this->checkPreviousWorkingDayIsLop($talent, $dateCount)) {
                //if holiday check previous working day and next working day if not worked then consider that holiday as leave
                //apply leave for the day
                $request = "Leave applied due to leave on date before and after a holiday";
                $ltype = $this->applyLeave($talent, $dateCount, $dateCount, $request, 0, 0, $tempResigned);
                if ($ltype == 0) {
                    $lopByProcess++;
                    $lopCount++;
                }
                if ($ltype == 2) {
                    $elUsed++;
                }
                if ($ltype == 1) {
                    $compUsed++;
                }
            }

            $totalProductiveSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($rowTalentAttance->od_hours);
            $secondsTravelTime += $this->talentAttendenceSheet->hourToSecondsConvertion($rowTalentAttance->travel_time);
            $dateNext = strtotime("+1 day", strtotime($dateCount));
            $dateCount = date("Y-m-d", $dateNext);
        }
        $totalSecondRequired = ($averageHourSeconds) * $presentDays;
        if ($totalSecondRequired > $totalProductiveSeconds) {
            $secondsShortage = ($totalSecondRequired) - $totalProductiveSeconds;
            $avgHourShortageCalculation = $this->defaultValues->getValue(41);
            $shortageEligbleHr = $this->defaultValues->getValue(42);
            $shortageEligble = $this->talentAttendenceSheet->hourToSecondsConvertion($shortageEligbleHr);
            if ($secondsShortage > $shortageEligble) {
                $shortage = ceil($secondsShortage / $avgHourShortageCalculationSec);
            }
        }
        //create leave for shortage
        for ($i = 1; $i <= $shortage; $i++) {
            $request = "Leave applied due to shortage during monthly process";
            $ltype = $this->applyLeave($talent, $dateTo, $dateTo, $request, 0, 1);
            if ($ltype == 0) {
                $lopByShortage++;
                $lopCount++;
            }
            if ($ltype == 2) {
                $elUsed++;
            }
            if ($ltype == 1) {
                $compUsed++;
            }
        }

        $totalHoursRequired = $this->talentAttendenceSheet->secondsToMinitues($totalSecondRequired);
        $extraHours = $this->talentAttendenceSheet->secondsToMinitues($extraSeconds);
        $travelHours = $this->talentAttendenceSheet->secondsToMinitues($secondsTravelTime);
        $totalHoursEarned = $this->talentAttendenceSheet->secondsToMinitues($totalProductiveSeconds);
        $totalHoursShortage = $this->talentAttendenceSheet->secondsToMinitues($secondsShortage);
        $elLeft = $this->leaves->getMyLeavesLeft_byone($talent, 2);
        $compLeft = $this->leaves->getMyLeavesLeft_byone($talent, 1);
        if ($isResigned || $tempResigned) {
            $elResponse = $this->leaves->getExpectedEl($talent, $dateTo, $talentDetails->date_of_joining);
        }
        $insertMonthlyProcess = array(
            'talent_id' => $talent,
            'month' => $month,
            'year' => $year,
            'lop_by_process' => $lopByProcess,
            'lop_by_shortage' => $lopByShortage,
            'total_present_days' => $presentDays,
            'hrs_required' => $totalHoursRequired,
            'hrs_earned' => $totalHoursEarned,
            'hrs_shortage' => $totalHoursShortage,
            'encashment' => $encashment,
            'encashment_for_extra' => $encashmentForExtra,
            'extra_od_hrs' => $extraHours,
            'total_travel_hours' => $travelHours,
            'leave_before_after_holiday' => $leave_before_after_holiday,
            'total_days' => $noOfDaysPay,
        );
        if ($isResigned) {
            $insertMonthlyProcess['el_left'] = $elLeft;
            $insertMonthlyProcess['comp_off_left'] = $compLeft;
            $insertMonthlyProcess['el_actual'] = $elResponse['el_actual'];
            $insertMonthlyProcess['el_expected'] = $elResponse['el_expected'];
            $this->db->insert('talent_monthly_report_resigned', $insertMonthlyProcess);
        } else if ($tempResigned) {
            $insertMonthlyProcess['el_left'] = $elLeft - $elUsed;
            $insertMonthlyProcess['comp_off_left'] = $compLeft - $compUsed;
            $insertMonthlyProcess['el_actual'] = $elResponse['el_actual'];
            $insertMonthlyProcess['el_expected'] = $elResponse['el_expected'];
            $this->db->insert('talent_monthly_report_resigned_temp', $insertMonthlyProcess);
        } else {
            $this->db->insert('talent_monthly_report', $insertMonthlyProcess);
        }
    }

    public function checkPreviousWorkingDayIsLop($talent, $date) {
        $datePrevious = strtotime("-1 day", strtotime($date));
        $dateCount = date("Y-m-d", $datePrevious);
        $rowTalentAttance = $this->talent->attendanceOfTalentForDate($talent, $dateCount);
        if (!$rowTalentAttance) {
            return FALSE;
        } else if ($rowTalentAttance->is_leave == 1 && $rowTalentAttance->is_holiday == 0) {
            if ($this->talent->checkCurrentDayIsLop($talent, $dateCount) == true) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else if ($rowTalentAttance->is_holiday == 1) {
            return $this->checkPreviousWorkingDayIsLop($talent, $dateCount);
        } else if ($rowTalentAttance->avg_consider == 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkPreviousWorkingDayIsLeave($talent, $date) {
        $datePrevious = strtotime("-1 day", strtotime($date));
        $dateCount = date("Y-m-d", $datePrevious);
        $rowTalentAttance = $this->talent->attendanceOfTalentForDate($talent, $dateCount);
        if (!$rowTalentAttance) {
            return FALSE;
        } else if ($rowTalentAttance->is_leave == 1 && $rowTalentAttance->is_holiday == 0) {
            return TRUE;
        } else if ($rowTalentAttance->is_holiday == 1) {
            return $this->checkPreviousWorkingDayIsLeave($talent, $dateCount);
        } else if ($rowTalentAttance->avg_consider == 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkNextWorkingDayIsLop($talent, $date) {
        $dateNext = strtotime("+1 day", strtotime($date));
        $dateCount = date("Y-m-d", $dateNext);
        $rowTalentAttance = $this->talent->attendanceOfTalentForDate($talent, $dateCount);
        if (!$rowTalentAttance) {
            return FALSE;
        } else if ($rowTalentAttance->is_leave == 1 && $rowTalentAttance->is_holiday == 0) {
            if ($this->talent->checkCurrentDayIsLop($talent, $dateCount) == true) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else if ($rowTalentAttance->is_holiday == 1) {
            return $this->checkNextWorkingDayIsLop($talent, $dateCount);
        } else if ($rowTalentAttance->avg_consider == 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkNextWorkingDayIsLeave($talent, $date) {
        $dateNext = strtotime("+1 day", strtotime($date));
        $dateCount = date("Y-m-d", $dateNext);
        $rowTalentAttance = $this->talent->attendanceOfTalentForDate($talent, $dateCount);
        if (!$rowTalentAttance) {
            return FALSE;
        } else if ($rowTalentAttance->is_leave == 1 && $rowTalentAttance->is_holiday == 0) {
            return TRUE;
        } else if ($rowTalentAttance->is_holiday == 1) {
            return $this->checkNextWorkingDayIsLeave($talent, $dateCount);
        } else if ($rowTalentAttance->avg_consider == 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function applyLeave($talent, $fromDate, $toDate, $request, $isLop = 0, $shortage = 0, $tempResigned = 0) {
        if ($shortage == 1) {
            $fromDateUpdate = '0000-00-00';
            $toDateUpdate = '0000-00-00';
        } else {
            $fromDateUpdate = $fromDate;
            $toDateUpdate = $toDate;
        }
        $insertTalentLeaveData = array(
            'talent' => $talent,
            'request' => $request,
            'from_date' => $fromDateUpdate,
            'to_date' => $toDateUpdate,
            'considered' => 1,
            'is_approved' => 1
        );
        if (!$tempResigned) {
            //inserting
            $this->db->insert('talent_leave', $insertTalentLeaveData);
            $insert_id = $this->db->insert_id();
        }


        $count = 0; //variable to find total working days
        $lop = 0; //variable to find total lop
        $leave_left = 0; //variable to find leave left

        $compoff_count = 0;
        $el_count = 0;
        $compoff = 0;
        $el = 0;
        //leave left
        if (!$isLop) {
            $leave = [];
            $taletLeaveDate = $this->leaves->getMyLeavesLeft($talent);
            foreach ($taletLeaveDate as $taletLeave) {
                $type = $taletLeave['id'];
                $leave[$type] = $taletLeave['leave_left'];
            }
            if (isset($leave['1'])) {
                $leave_left = $leave_left + $leave['1'];
                $date = $toDateUpdate == '0000-00-00' ? date('Y-m-d') : $toDateUpdate;
                $compOffId = $this->leaves->findDateOfCompOffBeforeTheDate($talent, $date);
                if ($compOffId) {
                    $compoff = $leave['1'];
                }
            }
            if (isset($leave['2'])) {
                $leave_left = $leave_left + $leave['2'];
                $el = $leave['2'];
            }
        }
        //processing each date
        $ltype = 0; //variable to find leave type

        $count++;
        //if leave not left and no leave choosed for that date then lop
        if ($leave_left < $count) {
            $lop++;
            $ltype = 0;
        } else if ($compoff > 0) {
            $ltype = 1;
            $compoff--;
            $compoff_count++;
        } else if ($el > 0) {
            $ltype = 2;
            $el--;
            $el_count++;
        }
        //leave applied and approved by reporting officer

        if (!$tempResigned) {
            $insertSpecificData = array(
                'talent_leave' => $insert_id,
                'date_of_leave' => date('Y-m-d', strtotime($toDateUpdate)),
                'leave_type' => $ltype,
                'is_approved' => '1'
            );
            $this->db->insert('talent_leave_date', $insertSpecificData);
            $updateData = array(
                'is_leave' => '1'
            );
            if ($toDateUpdate != '0000-00-00') {
                $this->db->where('attendance_date', date('Y-m-d', strtotime($toDateUpdate)));
                $this->db->where('talent_id', $talent);
                $this->db->update('talent_attendance', $updateData);
            }
            if ($compoff_count > 0) {
                $compoffLeft = $leave['1'] - $compoff_count;
                $this->updateLeaveLeftAndUsedLog($talent, 1, $compoffLeft);
                //set comp off used
                $this->setCompUsed($talent, $compoff_count, $toDateUpdate);
            }
            if ($el_count > 0) {
                $insert_el = $leave['2'] - $el_count;
                $this->updateLeaveLeftAndUsedLog($talent, 2, $insert_el);
            }
        }

        return $ltype;
    }

    public function setCompUsed($talent, $compCount, $date) {
        for ($i = $compCount; $i > 0; $i--) {
            $date = $date == '0000-00-00' ? date('Y-m-d') : $date;
            $compOffId = $this->leaves->findDateOfCompOffBeforeTheDate($talent, $date);
            $this->db->set('used', '1', FALSE);
            $this->db->where('id', $compOffId);
            $this->db->update('talent_compoff');
        }
    }

    public function updateLeaveLeftAndUsedLog($talent, $leaveType, $leaveLeft) {
        $updateData1 = array(
            'leave_left' => $leaveLeft,
        );
        $this->db->where('leave_type', $leaveType);
        $this->db->where('talent', $talent);
        $queryResult1 = $this->db->update('talent_leave_left', $updateData1);

        return $queryResult1;
    }

    public function talentLog() {
        $this->load->helper('url');
        $logId = $this->uri->segment(4);
        $viewLogData['logId'] = $logId;
        $logDatas = $this->talentAttendenceSheet->getTalentLog($logId);
        foreach ($logDatas as $logData) {
            $log = $logData['in_out_log'];
            $logDate = $logData['attendance_date'];
        }
        $viewLogData['log'] = $log;
        $viewLogData['logDate'] = $logDate;
        $this->load->view('attendenceManagement/talentLogData', $viewLogData);
    }

    public function excelDownload() {
        $this->load->helper('url');
        $month = $this->uri->segment(3);
        $year = $this->uri->segment(4);
        $monthYearReport = $this->talentAttendenceSheet->getAllMonthReport($month, $year);
        $heading = array(
            0 => 'SL. No.',
            1 => 'Talent',
            2 => 'Total Present Days',
            3 => 'Hours Required',
            4 => 'Hours Earned',
            5 => 'Hours Shortage',
            6 => 'Lop By Monthly Shortage',
            7 => 'Lop By Absent',
            8 => 'Extra Hours',
            9 => 'Encashment for Extra',
            10 => 'Total Lop',
            11 => 'Total Encashment',
        );
        $slno = 0;
        $content = array();
        foreach ($monthYearReport as $monthYearReportSpecific) {
            $content[$slno] = array(
                0 => $slno + 1,
                1 => $monthYearReportSpecific['name'],
                2 => $monthYearReportSpecific['total_present_days'],
                3 => $monthYearReportSpecific['hrs_required'],
                4 => $monthYearReportSpecific['hrs_earned'],
                5 => $monthYearReportSpecific['hrs_shortage'],
                6 => $monthYearReportSpecific['is_resigned'] ? '' : $monthYearReportSpecific['lop_by_shortage'],
                7 => $monthYearReportSpecific['is_resigned'] ? '' : $monthYearReportSpecific['lop_by_process'],
                8 => $monthYearReportSpecific['is_resigned'] ? '' : $monthYearReportSpecific['total_extra_hours'],
                9 => $monthYearReportSpecific['is_resigned'] ? '' : $monthYearReportSpecific['encashment_for_extra'],
                10 => $monthYearReportSpecific['is_resigned'] ? '' : $monthYearReportSpecific['lop_by_shortage'] + $monthYearReportSpecific['lop_by_process'] + $monthYearReportSpecific['lop_due_to_lop'],
                11 => $monthYearReportSpecific['is_resigned'] ? 'Resigned' : $monthYearReportSpecific['encashment_for_extra'] + $monthYearReportSpecific['encashment'],
            );
            $slno++;
        }
        $data['heading'] = $heading;
        $data['tableName'] = 'All Talent Monthly Report';
        $data['content'] = $content;
        $data['slno'] = $slno;
        $this->load->view('excel/excel', $data);
    }

    public function excelSalaryDownload() {
        $this->load->helper('url');
        $month = $this->uri->segment(3);
        $year = $this->uri->segment(4);
        $monthYearReport = $this->talentAttendenceSheet->getAllMonthReport($month, $year);
        $heading = array(
            0 => 'SL. No.',
            1 => 'Talent',
            2 => 'Total Days',
            3 => 'Lop Due To Shortage',
            4 => 'Total Lop',
            5 => 'Total Encashment',
            6 => 'Total Salary',
            7 => 'Total Additions',
            8 => 'Total Claims',
            9 => 'Total Deducions',
            10 => 'Actual Salary',
        );
        $slno = 0;
        $content = array();
        foreach ($monthYearReport as $monthYearReportSpecific) {
            $totalLop = $monthYearReportSpecific['lop_by_shortage'] + $monthYearReportSpecific['lop_by_process'] + $monthYearReportSpecific['lop_due_to_lop'];
            $totalEncashment = $monthYearReportSpecific['encashment_for_extra'] + $monthYearReportSpecific['encashment'];
            $content[$slno] = array(
                0 => $slno + 1,
                1 => $monthYearReportSpecific['name'],
                2 => $monthYearReportSpecific['total_days'],
                3 => $monthYearReportSpecific['lop_by_shortage'],
                4 => $totalLop,
                5 => $totalEncashment,
                6 => $monthYearReportSpecific['total_salary'],
                7 => $monthYearReportSpecific['total_additions'],
                8 => $monthYearReportSpecific['total_claims'],
                9 => $monthYearReportSpecific['total_deductions'],
                10 => $monthYearReportSpecific['actual_salary'],
            );
            $slno++;
        }
        $data['heading'] = $heading;
        $data['tableName'] = 'All Talent Monthly Salary Report';
        $data['content'] = $content;
        $data['slno'] = $slno;
        $this->load->view('excel/excel', $data);
    }

    public function monthlyReportExcel() {
        $this->load->helper('url');
        $month = $this->uri->segment(3);
        $year = $this->uri->segment(4);

        $startDayOfMonth = $this->defaultValues->getValue(12);
        $endDayOfMonth = $this->defaultValues->getValue(13);
        $dateFrom = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . $startDayOfMonth) . " -1 month"));
        $dateTo = date("Y-m-d", strtotime($year . '-' . $month . '-' . $endDayOfMonth));
        $monthYearReport = $this->talentAttendenceSheet->getAllMonthReport($month, $year);
        $heading = array(
            0 => 'SL. No.',
            1 => 'Talent',
            2 => 'Total Present Days',
            3 => 'Hours Required',
            4 => 'Hours Earned',
            5 => 'Hours Shortage',
            6 => 'Grand Total Hours',
            7 => 'Hours Shortage Actual',
        );
        $slno = 0;
        $content = array();
        foreach ($monthYearReport as $monthYearReportSpecific) {
            $grandTotalHoursArray = $this->talentAttendenceSheet->getGrandTotalHours($dateFrom, $dateTo, $monthYearReportSpecific['talent_id']);
//            $grandTotalHoursArray[] = $monthYearReportSpecific['hrs_earned'];
            $grandTotalHours = $this->TimePredictionModel->sum_time($grandTotalHoursArray);
            $hoursRequiredSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($monthYearReportSpecific['hrs_required']);
            $grandTotalSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($grandTotalHours);
            if ($grandTotalSeconds < $hoursRequiredSeconds) {
                $secondsShortage = abs($grandTotalSeconds - ($hoursRequiredSeconds));
                $hrsShortageActual = $this->talentAttendenceSheet->secondsToMinitues($secondsShortage);
            } else {
                $hrsShortageActual = 0;
            }
            if ($hrsShortageActual != $monthYearReportSpecific['hrs_shortage']) {
                $content[$slno] = array(
                    0 => $slno + 1,
                    1 => $monthYearReportSpecific['name'],
                    2 => $monthYearReportSpecific['total_present_days'],
                    3 => $monthYearReportSpecific['hrs_required'],
                    4 => $monthYearReportSpecific['hrs_earned'],
                    5 => $monthYearReportSpecific['hrs_shortage'],
                    6 => $grandTotalHours,
                    7 => $hrsShortageActual,
                );
            }
            $slno++;
        }
        $data['heading'] = $heading;
        $data['tableName'] = 'Talent Monthly Report';
        $data['content'] = $content;
        $data['slno'] = $slno;
        $this->load->view('excel/excel', $data);
    }

}
