<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require('AttendenceSheetUpload.php');

class Home extends AttendenceSheetUpload {

    function __construct() {
        parent::__construct();
        $this->load->model('holiday', '', TRUE);
        $this->load->model('myOd', '', TRUE);
        $this->load->model('TimePredictionModel', '', TRUE);
    }

    public function index() {

        $this->load->helper(array('form'));
        $this->load->model('talent');
        $this->load->model('notifications');
        $this->load->model('leaves');
        $this->load->model('talentAttendenceSheet');
        $this->load->model('loginLogs');
        $this->load->model("dashBoardMessages");
        $this->load->model("KnowledgeRepository");
        $this->load->model("ProjectMappingModel");
        $this->load->model("policy");
        $this->load->model("defaultValues");
        $this->load->model("leaveApprovals");
        if ($this->session->userdata('logged_in')) {
            
            $this->dummyAttendanceUpload();
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];
            $talentId = $session_data['talentId'];
            $data['isManagement'] = $is_management = $session_data['is_management'];
            $data['username'] = $session_data['username'];
            $roleStream = $session_data['role_stream_id'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
            $talent_id_Array = array();
            $talentArray[''] = "Select Talent";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;
            /* GET UPCOMING HOLIDAYS */
            $data['holidayDetails'] = $this->holiday->getHolidaysForDashboard();
            /* Count of KSHOP */
            $data['kshopCount'] = $this->KnowledgeRepository->getKnowledgeCount($roleStream);
            /* Last KSHOP */
            $data['lastKshopHeading'] = '';
            $data['lasKshopdescription'] = '';
            $lastKshop = $this->KnowledgeRepository->lastKshop($roleStream);
            if ($lastKshop != '') {
                $data['lastKshopHeading'] = $lastKshop->heading;
                $data['lastKshopDescription'] = $lastKshop->description;
            }

            //my knowledge portal count
            $data['myKnowledgeCount'] = $this->KnowledgeRepository->getMyKnowledgeCount($talentId);
            //my approved knowledge portal count
            $data['myApprovedKnowledgeCount'] = $this->KnowledgeRepository->getMyApprovedKnowledgeCount($talentId);


            /* GET UPCOMING BIRTHDAYS OF TALENTS */
            $data['birthDayDetails'] = $this->talent->getTalentNearBirthday();
            
            /*  GET ANNIVERSARY OF TALENTS */
            $data['anniversaryDetails'] = $this->talent->getTalentAnniversary();
          
            /*  GET NEW JOINERS */
            $data['newJoiners'] = $this->talent->getNewJoiners();
            
            /* GET NOTIFICATIONS TO USER */
            $notifications = $this->notifications->getNotificationByUser($user_id);
            $data['notifications'] = $notifications;

            /* GET LEAVE COUNT */
            $leaveKit = $this->leaves->getTalentLeaveKit($talentId);
            $data['leaveKit'] = $leaveKit;


            //Od waiting count

            $odWaiting = $this->myOd->getCountOdWaiting($talentId);
            $data['odWaiting'] = $odWaiting;

            //Leave waiting count

            $leaveWaiting = $this->leaves->getCountLeaveWaiting($talentId);
            $halfDayMechanism = $this->defaultValues->getValue(34);
            $data['halfDayMechanism'] = $halfDayMechanism;
            if ($halfDayMechanism == 1) {
                $leaveCountDueToAbsent = $this->leaveApprovals->getCountLeaveDueToAbsentReportingOfficer($talentId);
            } else {
                $leaveCountDueToAbsent = 0;
            }
            $data['leaveWaiting'] = $leaveWaiting + $leaveCountDueToAbsent;

            //compOff count

            $compWaiting = $this->talentAttendenceSheet->getCountCompWaiting($talentId);
            $data['compWaiting'] = $compWaiting;

            //encashment count

            $encashmentWaiting = $this->talentAttendenceSheet->getCountencashmentWaiting($talentId);
            $data['encashmentWaiting'] = $encashmentWaiting;

            //Dashboard Messages from Management
            $message = $this->dashBoardMessages->getDBMessage();
            if ($message) {
                if (1 == $message->show) {
                    $data['management_message'] = $message->message;
                }
            }

            //knowledge portal count
            //Recent Logins
            $recentLogDetails = $this->loginLogs->getRecentLogins($user_id);
            $data['recentLogDetails'] = $recentLogDetails;
            if (true === $session_data['is_talent']) {
                $data['attendance_statistics'] = $this->attendance_statistics($talentId);
            }

            $data['policies'] = $this->policy->getPolicies();
            $data['talent'] = $talentId;
            $pendingData = $this->getPendingTimesheetOfLastOneMonth($talentId);
            $countPending = count($pendingData);
            $data['countTimesheetPending'] = $countPending;
            $data['pendingSheetData'] = $pendingData;

            $talents = $this->ProjectMappingModel->getTalents($talentId, false, false);
            foreach ($talents as $talent) {
                $talent_id_Array[] = $talent['id'];
            }
            if (count($talent_id_Array)) {
                $data['HomeGetTotalData'] = count($this->TimePredictionModel->HomeGetApprovalDataTalents($talent_id_Array));
            } else {
                $data['HomeGetTotalData'] = 0;
            }

            $this->load->view('home/home', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
            //$this->load->view('home');
        }
    }

    public function getPendingTimesheetOfLastOneMonth($talent) {
        $fromdate = date("y-m-d", strtotime("-30 day"));
        $todate = date('Y-m-d');
        if (strtotime($todate) > strtotime(date('Y-m-d'))) {
            $todate = date('Y-m-d');
        }
        $period = new DatePeriod(
                new DateTime($fromdate), new DateInterval('P1D'), new DateTime($todate)
        );
        $datesBetweenDates = [];
        foreach ($period as $key => $value) {
            $datesBetweenDates[] = $value->format('Y-m-d');
        }

        $talentArray[$talent] = $this->TimePredictionModel->getTalentNameById($talent);
        $data = $this->TimePredictionModel->pendingTimesheetToSubmit($talentArray, $datesBetweenDates, 1);
        return $data;
    }

    public function getTimesheetPendingList() {
        $talent = $this->input->post('talent');
        $fromdate = date("y-m-d", strtotime("-30 day"));
        $todate = date('Y-m-d');
        if (strtotime($todate) > strtotime(date('Y-m-d'))) {
            $todate = date('Y-m-d');
        }
        $period = new DatePeriod(
                new DateTime($fromdate), new DateInterval('P1D'), new DateTime($todate)
        );
        $datesBetweenDates = [];
        foreach ($period as $key => $value) {
            $datesBetweenDates[] = $value->format('Y-m-d');
        }


        $talentArray[$talent] = $this->TimePredictionModel->getTalentNameById($talent);
        $data['PendingSubmitReportTalent'] = $this->TimePredictionModel->pendingTimesheetToSubmit($talentArray, $datesBetweenDates, 1);
        $data['dashboard'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function policy_show() {
        $this->load->view('policy/policy');
    }

    public function policy_show_detail($id) {
        switch ($id) {
            case 1:
                $this->load->view('policy/anti_sexual');
                break;
            case 2:
                $this->load->view('policy/attendence');
                break;
            case 3:
                $this->load->view('policy/claim');
                break;
            case 4:
                $this->load->view('policy/dress');
                break;
            case 5:
                $this->load->view('policy/grievance');
                break;
            case 6:
                $this->load->view('policy/induction');
                break;
            case 7:
                $this->load->view('policy/leave');
                break;
            case 8:
                $this->load->view('policy/talent');
                break;
                break;
            case 9:
                $this->load->view('policy/structure');
                break;
            default:
                $this->load->view('policy/policy');
        }
    }

    function logout() {
        $this->session->unset_userdata('logged_in');
        session_destroy();
        redirect('home', 'refresh');
    }

    private function attendance_statistics($talentId) {


        $this->load->model('defaultValues');
        $this->load->model('talentAttendance');
        $dateFromTo = $this->defaultValues->getCurrentMonthStartEnd();
        $dateFrom = $dateFromTo[3];
        $dateTo = $dateFromTo[4];

        $result = $this->talentAttendenceSheet->getLatestUploadedDate(1);
        if (!empty($result)) {
            foreach ($result as $row) {
                $lastDateOfUploaded = $row->attendance_date;
            }
        } else {
            $lastDateOfUploaded = $this->defaultValues->getValue(2);
        }

        $monthYearReport = $this->talentAttendenceSheet->getMonthYearReport($dateFrom, $dateTo, $talentId);

        $statistics = $this->talentAttendance->statistics($monthYearReport, $talentId);


        $data['lastDateOfUploaded'] = $lastDateOfUploaded;
        $data['presentDay'] = $statistics['presentDay'];
        $data['averageProductiveHours'] = $statistics['averageProductiveHours'];
        $data['monthYearReport'] = $statistics['monthYearReport'];
        $data['totalAbsent'] = $statistics['totalAbsent'];
        $data['averageHourRequired'] = $statistics['averageHourRequired'];
        $data['hourShortage'] = $statistics['hourShortage'];
        $data['extraHours'] = $statistics['extraHours'];
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;

        return $data;
    }

}
