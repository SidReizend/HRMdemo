<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LateReport extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('talentAttendance', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('myOd', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));

            $is_management = $session_data['is_management'];
            $talentId_user = $session_data['talentId'];
            $subordinates = $this->talent->getSubordinateswithShowInReport($talentId_user, $is_management);
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($subordinates as $subord) {
                $id = $subord['id'];
                $talentsDropdown[$id] = $subord['name'];
            }
            $selectedTalent = null !== $this->input->post('talent') ? $this->input->post('talent') : null;
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

            $startDayOfMonth = $this->defaultValues->getValue(12);
            $endDayOfMonth = $this->defaultValues->getValue(13);

            if ($this->input->post()) {
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $dateFrom = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . '01')));
                $dateTo = date('Y-m-t',strtotime($dateFrom));
            } else {

                $this->load->model('defaultValues');
                $dateFromTo = $this->defaultValues->getCurrentMonthStartEnd();
                $dateFrom = $dateFromTo[3];
                $dateTo = $dateFromTo[4];
            }

            //$this->output->enable_profiler(TRUE);
            $monthYearReport = $this->talentAttendenceSheet->getMonthYearReport($dateFrom, $dateTo, $selectedTalent);
            $lastDate = $this->talentAttendenceSheet->getLastDate();
            $lastCronJobDate = $this->defaultValues->getValue(2);
            $talentLeaveBetweenDates = $this->leaves->getLeaveDayStatus($selectedTalent, $dateFrom, $dateTo);
            $leaveAppliedDates = array();
            $leaveTypes = array();
            foreach ($talentLeaveBetweenDates as $leaves) {
                if ($leaves['is_approved'] == 0) {
                    $leaveAppliedDates[] = $leaves['date_of_leave'];
                    $leaveTypes[$leaves['date_of_leave']] = $leaves['leave_type'];
                }
            }
            $talentOdBetweenDates = $this->myOd->getOdDayStatus($selectedTalent, $dateFrom, $dateTo);
            $odDates = array();
            foreach ($talentOdBetweenDates as $ods) {
                if ($ods['is_approved'] == 0) {
                    $odDates[] = $ods['date_of_od'];
                }
            }
            $data['isManagement'] = $is_management;
            $data['selectedTalent'] = $selectedTalent;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedMonth'] = $this->input->post('month') == '' ? date('m') : $this->input->post('month');
            $data['selectedYear'] = $this->input->post('year') == '' ? date('Y') : $this->input->post('year');
            $talentId = $selectedTalent;
            $data['years'] = $yearArray;
            $data['months'] = $monthArray;
            $data['leaveAppliedDates'] = $leaveAppliedDates;
            $data['leaveTypes'] = $leaveTypes;
            $data['odDates'] = $odDates;
            $data['lastCronJobDate'] = $lastCronJobDate;
            //var_dump($talentLeaveBetweenDates);
            $statistics = $this->talentAttendance->statistics($monthYearReport, $talentId);


            $data['presentDay'] = $statistics['presentDay'];
            $data['averageProductiveHours'] = $statistics['averageProductiveHours'];
            $data['monthYearReport'] = $statistics['monthYearReport'];
            $data['totalAbsent'] = $statistics['totalAbsent'];
            $data['averageHourRequired'] = $statistics['averageHourRequired'];
            $data['hourShortage'] = $statistics['hourShortage'];
            $data['extraHours'] = $statistics['extraHours'];
            $data['totalPrdouctiveHour'] = $statistics['totalPrdouctiveHour'];
            $data['totalHourRequired'] = $statistics['totalHourRequired'];
            $this->load->view('attendenceManagement/lateReport', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
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

    public function talentDropDown() {
        $this->load->helper(array('form'));
        $session_data = $this->session->userdata('logged_in');
        $is_management = $session_data['is_management'];
        $talentId_user = $session_data['talentId'];
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $talentJoindBefore = $this->talent->getTalentJoinedBefore($year, $month, $talentId_user, $is_management);
        $talentsDropdown = array("" => "[SELECT Talent]");
        foreach ($talentJoindBefore as $subord) {
            $id = $subord['id'];
            $talentsDropdown[$id] = $subord['name'];
        }
        $dataTalents['talents'] = $talentsDropdown;
        $this->load->view('attendenceManagement/talents', $dataTalents);
    }

}
