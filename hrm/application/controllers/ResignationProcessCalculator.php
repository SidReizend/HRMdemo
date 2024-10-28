<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require('AllTalentsMonthlyReport.php');

class ResignationProcessCalculator extends AllTalentsMonthlyReport {

    function __construct() {
        parent::__construct();
        $this->load->model('resignProcess', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('talentAttendance', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $this->load->helper(array('form'));
            $data['username'] = $session_data['username'];
            $talentId = $session_data['talentId'];
            $is_management = $session_data['is_management'];

            $resignedTalents = $this->talent->getTalentsAll();
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($resignedTalents as $subord) {
                $id = $subord['id'];
                $talentsDropdown[$id] = $subord['name'];
            }
            $selectedTalent = $this->input->post('talent');
            $selectedDate = $this->input->post('dateOfResignation');
            $elLeft = 0;
            $elActual = 0;
            $settlementEl = 0;
            $compOffLeft = 0;
            $elExcessCredicted = 0;
            $totalLop = 0;
            $lopByShortage = 0;
            $lopByAbsent = 0;
            $noOfDaysPay = 0;
            $claim = 0;
            $dateOfResignation = '';
            $isResigned = '';
            $permenantlyResigned = 0;
            if ($selectedTalent) {
                $talentDetails = $this->talent->getTalentDetailsByTalentId($selectedTalent);
                if ($talentDetails->is_resigned == 1) {
                    $permenantlyResigned = 1;
                    $dateOfResignation = $talentDetails->date_of_resignation;
                    $selectedDate = date('d/m/Y', strtotime($dateOfResignation));
                    $isResigned = 1;
                } else if ($selectedDate) {
                    $isResigned = 0;
                    $date = DateTime::createFromFormat('d/m/Y', $selectedDate);
                    $dateOfResignation = $date->format('Y-m-d');
                }
            }

            $selectedPrevMonthSalary = $this->input->post('previousMonthSalary');
            if ($selectedTalent != '' && $selectedDate != '') {
                $date = DateTime::createFromFormat('d/m/Y', $selectedDate);
                $dateOfResignation = $date->format('Y-m-d');
                if (!$isResigned) {
                    $this->runProcessTalent($selectedTalent, $dateOfResignation, 0, 1);
                }
                $startDate = $this->talent->getStartAndEndDateToFindReport($selectedTalent, $dateOfResignation);
                //generate temp resignation process
                $talentResignDetails = $this->resignProcess->resignDetails($selectedTalent);
                if ($talentResignDetails) {
                    $elLeft = $talentResignDetails->el_left;
                    $elActual = $talentResignDetails->el_actual;
                    $elExpectedByTalent = $talentResignDetails->el_expected;
                    $elExcessCredicted = $elExpectedByTalent - $elActual;
                    $settlementEl = $elLeft + $elActual - $elExpectedByTalent;
                    $compOffLeft = $talentResignDetails->comp_off_left;
                    $lopByShortage = $talentResignDetails->lop_by_shortage;
                    $totalLop = ($talentResignDetails->lop_by_shortage) + ($talentResignDetails->lop_by_process) + ($talentResignDetails->lop_due_to_lop);
                    if ($settlementEl < 0) {
                        $totalLop = $totalLop - $settlementEl;
                    }
                    $lopByAbsent = ($talentResignDetails->lop_by_process) + ($talentResignDetails->lop_due_to_lop);
//                    $noOfDaysPay = ($talentResignDetails->no_of_days_pay);
                    if ($selectedPrevMonthSalary == 2) {
                        $datestring = $dateOfResignation . ' first day of last month';
                        $dt = date_create($datestring);
                        $fromDate = $dt->format('Y-m-d');
                    } else {
                        // First day of the month.
                        $fromDate = date('Y-m-01', strtotime($dateOfResignation));
                    }
                    $noOfDaysPay = $this->resignProcess->getNoOfDaysPay($fromDate, $talentResignDetails->date_of_joining, $dateOfResignation);
                    $claimPendingDetails = $this->resignProcess->sumClaimPending($selectedTalent);
                    $claim = $claimPendingDetails->total_claim;
                }
            }
            if ($selectedPrevMonthSalary == 2) {
                $datestring = $dateOfResignation . ' first day of last month';
                $dt = date_create($datestring);
                $previousYear = $dt->format('Y');
                $previousMonth = $dt->format('m');
                $monthlyReportOfPreviousMonth = $this->resignProcess->getMonthlyReport($previousMonth, $previousYear, $selectedTalent);
                if ($monthlyReportOfPreviousMonth) {
                    $lopByAbsent = $lopByAbsent + $monthlyReportOfPreviousMonth->lop_by_process + $monthlyReportOfPreviousMonth->lop_due_to_lop;
                    $lopByShortage = $lopByShortage + $monthlyReportOfPreviousMonth->lop_by_shortage;
                    $totalLop = $totalLop + $monthlyReportOfPreviousMonth->lop_by_process + $monthlyReportOfPreviousMonth->lop_due_to_lop + $monthlyReportOfPreviousMonth->lop_by_shortage;
//                    $noOfDaysPayInPreviousMonth = $this->resignProcess->getNoOfDaysPayInMonth($previousMonth, $previousYear, $talentResignDetails->date_of_joining);
//                    $noOfDaysPay = $noOfDaysPay + $noOfDaysPayInPreviousMonth;
                }
            }
            $preMonthSalaryDropdown = array(
                '1' => 'Paid',
                '2' => 'Not Paid',
            );
            $data['preMonthSalaryDropdown'] = $preMonthSalaryDropdown;
            $data['selectedPrevMonthSalary'] = $selectedPrevMonthSalary;
            $data['dateOfResignation'] = $dateOfResignation;
            $data['permenantlyResigned'] = $permenantlyResigned;
            $data['isResigned'] = $isResigned;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['lopByAbsent'] = $lopByAbsent;
            $data['elLeft'] = $elLeft;
            $data['elActual'] = $elActual;
            $data['elExcessCredicted'] = $elExcessCredicted;
            $data['settlementEl'] = $settlementEl;
            $data['compOffLeft'] = $compOffLeft;
            $data['lopByShortage'] = $lopByShortage;
            $data['totalLop'] = $totalLop;
            $data['noOfDaysPay'] = $noOfDaysPay;
            $data['claim'] = $claim;
            $data['selectedTalent'] = $selectedTalent;
            $this->load->view('resignManagement/resignProcessCalculator', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    function addData() {
        $talent = $this->input->post('talent');
        $salary = $this->input->post('salary');
        $elLeft = $this->input->post('elLeft');
        $compOff = $this->input->post('compOff');
        $elActual = $this->input->post('elActual');
        $settlementEl = $this->input->post('settlementEl');
        $talentLop = $this->input->post('talentLop');
        $daysPay = $this->input->post('daysPay');
        $dailyPay = $this->input->post('dailyPay');
        $elPay = $this->input->post('elPay');
        $claimPending = $this->input->post('claimPending');
        $incentive = $this->input->post('incentive');
        $totalSettlement = $this->input->post('totalSettlement');
        $tillDate = $this->input->post('tillDate');
        $note = $this->input->post('note');
        $lopByShortage = $this->input->post('lopByShortage');
        $lopByAbsent = $this->input->post('lopByAbsent');
        // Do insert operation here
        $insertUserData = array(
            'talent' => $talent,
            'el_left' => $elLeft,
            'comp_off' => $compOff,
            'el_actual' => $elActual,
            'settlement_el' => $settlementEl,
            'total_lop' => $talentLop,
            'days_pay' => $daysPay,
            'daily_pay' => $dailyPay,
            'el_settlement_pay' => $elPay,
            'claim_pending' => $claimPending,
            'insentive' => $incentive,
            'total' => $totalSettlement,
            'salary' => $salary,
            'till_date' => $tillDate,
            'note' => $note,
            'lop_by_shortage' => $lopByShortage,
            'lop_by_absent' => $lopByAbsent,
        );
        $queryResult = $this->db->insert('resignationProcessCalculator', $insertUserData);
        echo 1;
    }

    function printData() {
        $this->load->helper(array('form'));
        $resignCalculator = $this->resignProcess->lastResignCalculator();
        $data['resignCalculator'] = $resignCalculator;
        $this->load->view('resignManagement/lastResigneCalculator', $data);
    }

}
