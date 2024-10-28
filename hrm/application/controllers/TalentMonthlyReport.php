<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TalentMonthlyReport extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('talentAttendance', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('myOd', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('TimePredictionModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $talent_id = $session_data['talentId'];
            $this->load->helper(array('form'));
            $yearArray = array(
                '' => '[SELECT YEAR]'
            );
            $startDayOfMonth = $this->defaultValues->getValue(12);
            $endDayOfMonth = $this->defaultValues->getValue(13);
            $earliest_year = 2015;
            foreach (range(date('Y')+1, $earliest_year) as $year) {
                $yearArray[$year] = $year;
            }
            $monthArray = array(
                '' => '[SELECT MONTH]'
            );
            $months = $this->talentAttendenceSheet->getMonths();
            foreach ($months as $month) {
                $monthArray[$month['id']] = $month['month'];
            }


            $data['years'] = $yearArray;
            $data['months'] = $monthArray;
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
            $data['selectedMonth'] = $this->input->post('month');
            $data['selectedYear'] = $this->input->post('year');

            //$this->output->enable_profiler(TRUE);
            $monthYearReport = $this->talentAttendenceSheet->getMonthYearReport($dateFrom, $dateTo, $session_data['talentId']);
            $talentLeaveBetweenDates = $this->leaves->getLeaveDayStatus($session_data['talentId'], $dateFrom, $dateTo);
            $leaveAppliedDates = array();
            $leaveTypes = array();
            foreach ($talentLeaveBetweenDates as $leaves) {
                if ($leaves['is_approved'] == 0) {
                    $leaveAppliedDates[] = $leaves['date_of_leave'];
                }
                $leaveTypes[$leaves['date_of_leave']] = $leaves['leave_type'];
            }
            $talentOdBetweenDates = $this->myOd->getOdDayStatus($session_data['talentId'], $dateFrom, $dateTo);
            $odDates = array();
            foreach ($talentOdBetweenDates as $ods) {
                if ($ods['is_approved'] == 0) {
                    $odDates[] = $ods['date_of_od'];
                }
            }
            $data['leaveAppliedDates'] = $leaveAppliedDates;
            $data['leaveTypes'] = $leaveTypes;
            $data['odDates'] = $odDates;
            //var_dump($talentLeaveBetweenDates);
            $statistics = $this->talentAttendance->statistics($monthYearReport, $talent_id);


            $data['presentDay'] = $statistics['presentDay'];
            $data['averageProductiveHours'] = $statistics['averageProductiveHours'];
            $data['monthYearReport'] = $statistics['monthYearReport'];
            $data['totalAbsent'] = $statistics['totalAbsent'];
            $data['averageHourRequired'] = $statistics['averageHourRequired'];
            $data['hourShortage'] = $statistics['hourShortage'];
            $data['extraHours'] = $statistics['extraHours'];
            $data['totalPrdouctiveHour'] = $statistics['totalPrdouctiveHour'];
            $data['totalHourRequired'] = $statistics['totalHourRequired'];
            $data['travelHours'] = $statistics['travelHours'];
            $this->load->view('attendenceManagement/talentMonthlyReport', $data);
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

}
