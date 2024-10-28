<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TimePredictionCntrl extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('TimePredictionModel', '', TRUE);
        $this->load->model('TaskManagementModel', '', TRUE);
        $this->load->model('ProjectMappingModel', '', TRUE);
        $this->load->model('Projects', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('claim', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('Talent', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('holiday', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $talent = $session_data['talentId'];
            $this->load->helper(array('form'));
            if ($this->input->post('fromdate')) {
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromdate'));
                $fromdate = $date->format('Y-m-d');
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('todate'));
                $todate = $date->format('Y-m-d');
                $data['post_fromdate'] = $this->input->post('fromdate');
                $data['post_todate'] = $this->input->post('todate');
            } else {
                $todate = date('Y-m-t');
                $fromdate = date('Y-m-01');
            }


            $data['HomeGetTotalData'] = $this->TimePredictionModel->HomeGetTotalData($talent, $fromdate, $todate);

            $this->load->view('TimePrediction/TimePredictionView', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function savetimeSplittingMaster() {
        $this->load->helper(array('form'));
        $session_data = $this->session->userdata('logged_in');
        $username = $session_data['username'];
        $createdBy = $session_data['talentId'];
        $to_projects = $this->input->post('to_project');
        $parent_project = $this->input->post('parent_project');
        $task_code = $this->input->post('task_code');
        $projects = '';
        if (count($to_projects) > 0) {
            foreach ($to_projects as $to_project) {
                $projects .= $to_project . ",";
            }
        }
        $createdAt = date('Y-m-d H:i:s');
        // Do insert operation here
        $insertUserData = array(
            'to_project' => $projects,
            'parent_project' => $parent_project,
            'task' => $task_code,
            'added_by' => $createdBy,
            'created_at' => $createdAt,
        );
        $this->db->insert('timesheet_split', $insertUserData);
        //success
        $this->session->set_flashdata('flashSuccess', 'Added successfully');
    }

    public function timeSplittingMaster() {
        $this->load->helper(array('form'));
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $projectDropdown = array();
        $projects = $this->Projects->getProjects();
        foreach ($projects as $project) {
            $id = $project['id'];
            $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
        }
        //dropdown for task
        $taskDropdown = array("" => "[SELECT Task]");
        $tabledata = $this->Projects->timeSplittingMaster();
        $data['projectDropdown'] = $projectDropdown;
        $data['taskDropdown'] = $taskDropdown;
        $data['tabledata'] = $tabledata;
        $this->load->view('TimePrediction/timeSplitMaster', $data);
    }

    public function timeSplittingReport() {
        $this->load->helper(array('form'));
        $yearArray = array(
            '' => '[SELECT YEAR]'
        );
        $earliest_year = 2015;
        foreach (range(date('Y') + 1, $earliest_year) as $year) {
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
        $this->load->view('TimePrediction/timeSplittingReport', $data);
    }

    public function calculateSplitReport() {
        $this->load->helper(array('form'));
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $data['tableData'] = $this->Projects->generateSplitReport($year, $month);
        $this->load->view('TimePrediction/calculateSplitReport', $data);
    }

    //Addding of time
    public function AddNewPrediction() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $this->load->helper(array('form'));
            $this->load->helper('url');
            $date = $this->uri->segment(4);
            if ($date == "") {
                $date = date('d/m/Y');
            } else {
                $date = date('d/m/Y', strtotime($date));
            }
            $data['date'] = $date;

            $this->load->view('TimePrediction/TimePrediction', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    //cancel Approval send
    public function CancelApproval() {
        $this->load->helper('url');
        $id = $this->uri->segment(4);
        $alldata = $this->TimePredictionModel->getAllDataById($id);
        $date = $alldata[0]['date'];
        $talent = $alldata[0]['talent'];

        $updateData = array(
            'send_approval' => 0
        );
        //inserting
        $this->db->where('date', $date);
        $this->db->where('send_approval', 1);
        $this->db->where('talent', $talent);
        $queryResult = $this->db->update('time_prediction', $updateData);
        if (!$queryResult) {
            $this->session->set_flashdata('flashError', 'Some error found');
            redirect('TimePredictionCntrl', 'refresh');
        } else {
            $this->declineTalentAllowance($date, $talent);
            $this->session->set_flashdata('flashSuccess', 'Cancelled successfully');
            redirect('TimePredictionCntrl', 'refresh');
        }
    }

    public function showInputElements() {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('Dat'));
        $date = $date->format('Y-m-d');
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $this->load->helper(array('form'));
        $talent = $session_data['talentId'];
        $taskArray = array();
        $taskArray[''] = "--Task--";
        $data['tasks'] = $taskArray;
        $projects = $this->TimePredictionModel->getAllProjectbyDateAndToDate($talent, $date);
        $projectArray = array();
        $projectArray[''] = "--Project--";
        foreach ($projects as $project) {
            $projectArray[$project['id']] = $project['project_code'];
        }
        $data['projects'] = $projectArray;

        $data['inputelements'] = 1;

        $number = $this->TimePredictionModel->CheckApprovedOrNot($date, $talent);
        //echo $this->db->last_query().$number;
        if ($number == 0) {
            $data['submitShow'] = 1;
        }

        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function showTableData() {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('Dat'));
        $date = $date->format('Y-m-d');
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $talent = $session_data['talentId'];

        $data['CheckActualHourAndOD'] = $this->TimePredictionModel->CheckActualHourAndOD($talent, $date);

        $data['findActualHourAndOD'] = $this->TimePredictionModel->findActualHourAndOD($talent, $date);

        $data['getAllTimeByDate'] = $this->TimePredictionModel->getAllTimeByDate($date, $talent);
        $data['date'] = $date;
        $data['talent'] = $talent;
        $data['viewAllowanceByDate'] = 1;
        $data['showrow'] = 1;
        $data['showTableData'] = 1;
        $data['isHoliday'] = $this->isWeekend($date, $talent);
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }

    public function checkDateIsAllowedHoliday($date, $talent) {
        $id = $this->holiday->getHolidayId($date);
        if (!$id) {
            return TRUE;
        } else {
            $id = $this->holiday->checkPermissionGiven($id, $talent);
            return $id ? TRUE : FALSE;
        }
    }

    public function checkDateIsBeforeDeployDate($date, $task_id) {
        $deploydate = $this->TimePredictionModel->getDeployDate($task_id);
        if (!$deploydate) {
            return TRUE;
        } else {
            if ($date <= $deploydate) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    public function SaveInputElements() {
        $session_data = $this->session->userdata('logged_in');
        $talent = $session_data['talentId'];
        $talentDetails = $this->talent->getTalentDetailsByTalentId($talent);

        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('Dat'));
        $now = new DateTime(Date('Y-m-d'));
        $blockDays = $this->defaultValues->getValue(38);
        $blockDaysMonth = $this->defaultValues->getValue(39);
        $difference = $date->diff($now)->days;
        if (!$talentDetails->timesheet_restriction) {
            if (strtotime($date->format('Y-m-d')) < strtotime($now->format('Y-m-d')) && $blockDays <= $difference) {
                echo "Timesheet not allowed to add for a day before " . $blockDays . " days to current date";
                return;
            }
            if (strtotime($date->format('Y-m-d')) < strtotime($now->format('Y-m-d')) && $now->format('m') != $date->format('m') && ($now->format('d') > $blockDaysMonth)) {
                echo "Timesheet not allowed to add for a previous month after the current month " . $blockDaysMonth . " Day";
                return;
            }
        }
        $lastSalaryProcessMonthDate = $this->defaultValues->getValue(3);
        
        if(strtotime($date->format('Y-m-d')) < strtotime($lastSalaryProcessMonthDate)){
            echo "Salary process already done";
            return;
        }

        $date = $date->format('Y-m-d');
        $checkDateIsAllowedHoliday = $this->checkDateIsAllowedHoliday($date, $talent);
        if (!$checkDateIsAllowedHoliday) {
            echo "Don't have permission for enter timesheet on a public holiday without Aproval";
            return;
        }

        $data['username'] = $session_data['username'];
        $talent = $session_data['talentId'];

        $task_id = $this->input->get('task_id');
        $checkDateIsBeforeDeployDate = $this->checkDateIsBeforeDeployDate($date, $task_id);
        if (!$checkDateIsBeforeDeployDate) {
            echo "Timesheet not allowed to add for a date after deployment date";
            return;
        }
        $checkTaskIsInvoiced =$this->ProjectMappingModel->getTaskInvoiceStatus($task_id);
         if ((3 == $checkTaskIsInvoiced['invoice_status']) || (2 == $checkTaskIsInvoiced['invoice_status'])) {
            echo "Selected task invoice completed";
            return;
        }   
        $project_id = $this->input->get('project_id');
        $timepicker = $this->input->get('timepicker');
        $description = $this->input->get('description');
        $is_outside_time = $this->input->get('is_outside_time');
        $checkProjectToDate = $this->ProjectMappingModel->checkProjectToDate($project_id, $date);
        if (FALSE === $checkProjectToDate) {
            echo "Project's to date is less than the date you are chosen";
            return;
        }
        $insertData = array(
            'talent' => $talent,
            'date' => $date,
            'Project' => $project_id,
            'task' => $task_id,
            'time' => $timepicker,
            'description' => $description,
            'is_outside_time' => $is_outside_time,
        );
        //inserting
        $queryResult = $this->db->insert('time_prediction', $insertData);
        if (!$queryResult) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function UpdateInputElements() {
        $error_flag = 0;
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('Dat'));
        $date = $date->format('Y-m-d');
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $talent = $session_data['talentId'];
        $task_id = $this->input->get('task_id');
        $project_id = $this->input->get('project_id');
        $timepicker = $this->input->get('timepicker');
        $description = $this->input->get('description');
        $is_outside_time = $this->input->get('is_outside_time');
        $id = $this->input->get('id');

        $checkDateIsBeforeDeployDate = $this->checkDateIsBeforeDeployDate($date, $task_id);

        if (!$checkDateIsBeforeDeployDate) {
            echo 2;
            return;
        }

        $updateData = array(
            'talent' => $talent,
            'date' => $date,
            'Project' => $project_id,
            'task' => $task_id,
            'time' => $timepicker,
            'description' => $description,
            'is_outside_time' => $is_outside_time
        );
        //inserting
        $this->db->where('id', $id);
        $queryResult = $this->db->update('time_prediction', $updateData);
        if (!$queryResult) {
            $error_flag = 1;
        }
        echo $error_flag;
    }

    public function DeleteData() {
        $id = $this->input->get('id');
        $all_data = $this->TimePredictionModel->getAllDataById($id);
        $talent = $all_data[0]['talent'];
        $talentDetails = $this->talent->getTalentDetailsByTalentId($talent);
        $date = $all_data[0]['date'];
        $dateObj = new DateTime(Date('Y-m-d', strtotime($date)));
        $dateObj = DateTime::createFromFormat('d/m/Y', $dateObj->format('d/m/Y'));

        $now = new DateTime(Date('Y-m-d'));
        $blockDays = $this->defaultValues->getValue(38);
        $blockDaysMonth = $this->defaultValues->getValue(39);
        $difference = $dateObj->diff($now)->days;
        $response = [];
        if (!$talentDetails->timesheet_restriction) {
            if (strtotime($dateObj->format('Y-m-d')) < strtotime($now->format('Y-m-d')) && $blockDays <= $difference) {
                $response = "error-Timesheet not allowed to delete for a day before " . $blockDays . " days to current date";
                echo $response;
                return false;
            }
            if (strtotime($dateObj->format('Y-m-d')) < strtotime($now->format('Y-m-d')) && $now->format('m') != $dateObj->format('m') && ($now->format('d') > $blockDaysMonth)) {
                $response = "error-Timesheet not allowed to delete for a previous month after the current month " . $blockDaysMonth . " Day";
                echo $response;
                return;
            }
        }
        $this->db->where('id', $id);
        $queryResult = $this->db->delete('time_prediction');
        if (!$queryResult) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function EditData() {
        $id = $this->input->get('id');
        $all_data = $this->TimePredictionModel->getAllDataById($id);
        $this->load->helper(array('form'));
        $talent = $all_data[0]['talent'];
        $talentDetails = $this->talent->getTalentDetailsByTalentId($talent);
        $date = $all_data[0]['date'];
        $dateObj = new DateTime(Date('Y-m-d', strtotime($date)));
        $dateObj = DateTime::createFromFormat('d/m/Y', $dateObj->format('d/m/Y'));
        $now = new DateTime(Date('Y-m-d'));
        $blockDays = $this->defaultValues->getValue(38);
        $blockDaysMonth = $this->defaultValues->getValue(39);
        $difference = $dateObj->diff($now)->days;
        $response = [];
        if (!$talentDetails->timesheet_restriction) {
            if (strtotime($dateObj->format('Y-m-d')) < strtotime($now->format('Y-m-d')) && $blockDays <= $difference) {
                $response = "error-Timesheet not allowed to add for a day before " . $blockDays . " days to current date";
                echo $response;
                return false;
            }
            if (strtotime($dateObj->format('Y-m-d')) < strtotime($now->format('Y-m-d')) && $now->format('m') != $dateObj->format('m') && ($now->format('d') > $blockDaysMonth)) {
                $response = "error-Timesheet not allowed to add for a previous month after the current month " . $blockDaysMonth . " Day";
                echo $response;
                return;
            }
        }

        $projects = $this->TimePredictionModel->getAllProjectbyDateAndToDate($talent, $date);
        $projectArray = array();
        foreach ($projects as $project) {
            $projectArray[$project['id']] = $project['project_code'];
        }
        $data['projects'] = $projectArray;

        $tasks = $this->ProjectMappingModel->getTasksbyProject($all_data[0]['Project']);
        $taskArray = array();
        $taskArray[''] = "--Task--";
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'] . "-" . $task['task_desc'];
        }


        //$taskArray[''] = "--Task--";
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'];
        }
        $data['tasks'] = $taskArray;

        $data['project'] = $all_data[0]['Project'];
        $data['task_selected'] = $all_data[0]['task'];
        $data['date'] = $all_data[0]['date'];
        $data['time'] = $all_data[0]['time'];
        $data['description'] = $all_data[0]['description'];
        $data['is_outside_time'] = $all_data[0]['is_outside_time'];
        $data['id'] = $all_data[0]['id'];
        $data['EditData'] = 1;

        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function SendApproval() {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('Dat'));
        $food_allowance = $this->input->get('food_allowance');
        $food_andtravel_allowance = $this->input->get('food_andtravel_allowance');
        $date = $date->format('Y-m-d');
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $talent = $session_data['talentId'];

        $lastMonthlyProcessedDate = $this->defaultValues->getValue(3);
        if (strtotime($date) < strtotime($lastMonthlyProcessedDate)) {
            echo 'Already attendance processed for the date';
            die;
        }
        $talentDetails = $this->talent->getTalentDetailsForMonthlyProcess($talent);
        $averageHourRequired = $talentDetails->average_working_hour_per_day;
        $averageHourRequiredSec = $this->talentAttendenceSheet->hourToSecondsConvertion($averageHourRequired);
        $travel_time = $this->TimePredictionModel->getSumOfTimeTravel($date, $talent);
        if ($travel_time && ($travel_time > $averageHourRequiredSec)) {
            echo 'Traveling time should less than ' . $averageHourRequired;
            die;
        }
        $allowance_projects = $this->TimePredictionModel->getAllTimeByDate($date, $talent);
        $allowances = array();
        foreach ($allowance_projects as $allowance) {
            if ($allowance['allowance']) {
                $allowances = $allowance['project_code'];
            }
        }
        if ($food_allowance != "0" || $food_andtravel_allowance != "0") {
            if (count($allowances) == 0) {
                echo 'Food and Travel Allowance are not provided for Selected Projects';
                die;
            }
        }
        $talent_approved = $this->TimePredictionModel->TalentCheckauto($talent);

        if ($talent_approved > 0) {
            $updateData = array(
                'is_approved' => 1,
            );
            //inserting
            $this->db->where('date', $date);
            $this->db->where('talent', $talent);
            $this->db->where('replaced !=', 1);
            $queryResult = $this->db->update('time_prediction', $updateData);
            if (!$queryResult) {
                echo 0;
            } else {
                echo 1;
                $this->addAttendance($date, $talent);
                $this->addAutoTalentAllowance($food_allowance, $food_andtravel_allowance, $date, $talent); //approved talanet allowance
            }
        } else {
            $updateData = array(
                'is_approved' => 0,
                'send_approval' => 1,
            );
            //inserting
            $this->db->where('date', $date);
            $this->db->where('talent', $talent);
            $this->db->where('replaced !=', 1);
            $queryResult = $this->db->update('time_prediction', $updateData);
            if (!$queryResult) {
                echo 0;
            } else {
                echo 1;
                $this->addAllowance($food_allowance, $food_andtravel_allowance, $date, $talent);   //allowance is send for approval
            }
        }
    }

    //Approval Control Method goes here

    public function HomeDataApproval() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $dashbord = $this->input->get('dashbord');
            if ($dashbord) {
                $talents = $this->ProjectMappingModel->getTalents($talentId, false, false);
            } else {
                $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            }
            $talentArray = array();
            $talent_id_Array = array();
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talent_id_Array[] = $talent['id'];
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;
            $data['dashbord'] = $dashbord;
            $talent = "";
            $data['talent'] = $talent;
            if (!empty($talent_id_Array)) {
                $data['HomeGetTotalData'] = $this->TimePredictionModel->HomeGetApprovalDataTalents($talent_id_Array);
            } else {
                $data['HomeGetTotalData'] = array();
            }
            $this->load->helper(array('form'));
            $this->load->view('TimePrediction/TimeApproval', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function homeDataApproved() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
            $talent_id_Array = array();
            $talentArray[''] = "[SELECT TALENT]";
            foreach ($talents as $talent) {
                $talent_id_Array[] = $talent['id'];
                $talentArray[$talent['id']] = $talent['name'];
            }
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

            $data['years'] = $yearArray;
            $data['months'] = $monthArray;
            $data['talents'] = $talentArray;
            $talent = "";
            $data['talent'] = $talent;
            $data['HomeGetTotalData'] = $this->TimePredictionModel->HomeGetApprovedDataTalents($talent_id_Array, date('Y'), date('m'));
            $this->load->helper(array('form'));
            $this->load->view('TimePrediction/timeApproved', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function HomeDataApprovalAjax() {
        $talent = $this->input->get('talent');
        if ($talent) {
            $data['HomeGetTotalData'] = $this->TimePredictionModel->HomeGetApprovalDataTalents([$talent]);
        } else {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $dashbord = $this->input->get('dashbord');
            if ($dashbord) {
                $talents = $this->ProjectMappingModel->getTalents($talentId, false, false);
            } else {
                $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            }
            $talent_id_Array = [];
            foreach ($talents as $talent) {
                $talent_id_Array[] = $talent['id'];
            }

            $data['HomeGetTotalData'] = $this->TimePredictionModel->HomeGetApprovalDataTalents($talent_id_Array);
        }
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function HomeDataApprovedAjax() {
        $talent = $this->input->get('talent');
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $data['HomeGetTotalDataApproved'] = $this->TimePredictionModel->HomeGetApprovedData($talent, $year, $month);
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function PendingSubmitReport() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getAllTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
            $talent_id_Array = array();
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talent_id_Array[$talent['id']] = $talent['name'];
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;
            $talent = "";
            $data['talent'] = $talent;

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

            $data['years'] = $yearArray;
            $data['months'] = $monthArray;
            $monthYear = date('Y-m');
            $fromdate = $monthYear . '-01';
            $todate = date('Y-m-t');
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
            $data['PendingSubmitReport'] = $this->TimePredictionModel->pendingTimesheetToSubmit($talent_id_Array, $datesBetweenDates);

            $this->load->helper(array('form'));
            $this->load->view('TimePrediction/PendingSubmitReport', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function PendingSubmitReportAjax() {
        $session_data = $this->session->userdata('logged_in');
        $is_management = $session_data['is_management'];
        $is_pmo = $session_data['is_pmo'];
        $talent = $this->input->get('talent');
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $talentId = $session_data['talentId'];
        $fromdate = $year . "-" . $month . '-01';
        $todate = date('Y-m-t', strtotime($fromdate));
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
        if (strtotime($todate) < strtotime(date('Y-m-d'))) {
            $datesBetweenDates[] = $todate;
        }


        if ($talent) {
            $talentArray[$talent] = $this->TimePredictionModel->getTalentNameById($talent);
            $data['PendingSubmitReport'] = $this->TimePredictionModel->pendingTimesheetToSubmit($talentArray, $datesBetweenDates);
        } else {
            $talents = $this->ProjectMappingModel->getAllTalents($talentId, $is_management, $is_pmo);
            $talent_id_Array = array();
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talent_id_Array[$talent['id']] = $talent['name'];
            }
            $data['PendingSubmitReport'] = $this->TimePredictionModel->pendingTimesheetToSubmit($talent_id_Array, $datesBetweenDates);
        }

        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function PendingSubmitTalentReport() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talent = $session_data['talentId'];

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

            $data['years'] = $yearArray;
            $data['months'] = $monthArray;
            $monthYear = date('Y-m');
            $fromdate = $monthYear . '-01';
            $todate = date('Y-m-t');
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
            $data['PendingSubmitReport'] = $this->TimePredictionModel->pendingTimesheetToSubmit($talentArray, $datesBetweenDates, 1);

            $this->load->helper(array('form'));
            $this->load->view('TimePrediction/PendingSubmitReportTalent', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function PendingSubmitReportTalentAjax() {
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];

        $is_management = $session_data['is_management'];
        $talent = $session_data['talentId'];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $fromdate = $year . "-" . $month . '-01';
        $todate = date('Y-m-t', strtotime($fromdate));
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

        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ApproveTime() {
        $session_data = $this->session->userdata('logged_in');
        $talentApproved = $session_data['talentId'];
        $date = $this->input->get('date');
        $talent = $this->input->get('talent');
        $updateData = array(
            'is_approved' => 1,
            'send_approval' => 1,
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $talentApproved
        );
        //inserting
        $this->db->where('date', $date);
        $this->db->where('talent', $talent);
        $this->db->where('send_approval', 1);
        $queryResult = $this->db->update('time_prediction', $updateData);
        if (!$queryResult) {
            echo 0;
        } else {
            $this->addAttendance($date, $talent);
            $this->approveTalentAllowance($date, $talent);    //approved talent allowance
        }
    }

    public function addAttendance($dateOfAttendence, $talentId) {
        $getSumOfTimeByDate = $this->TimePredictionModel->getSumOfTimeByDate($dateOfAttendence, $talentId);
        $getSumOfTimeTravel = $this->TimePredictionModel->getSumOfTimeTravel($dateOfAttendence, $talentId);
        $timeWorked = gmdate("H:i:s", $getSumOfTimeByDate);
        $timeTravelled = gmdate("H:i:s", $getSumOfTimeTravel);
        $minimumHourRequered = $this->defaultValues->getValue(14);
        $managementRoleStream = $this->defaultValues->getValue(28);
        $leaveStatus = $this->leaves->getDayLeaveStatus($dateOfAttendence, $talentId);
        $publicHolidayStatus = $this->leaves->getDayHolidayStatus($dateOfAttendence);
        $regularHoliday = false;
        $weekends = $this->holidaysWeek->getHolidayTalent($talentId);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }
        $day = strtolower(date("l", strtotime($dateOfAttendence)));
        $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateOfAttendence);
        if (in_array($day, $weekendArray) && ($checkDateInStrictlyWorking == 0)) {
            $regularHoliday = TRUE;
        }
        $isHoliday = ($regularHoliday || $publicHolidayStatus) ? TRUE : FALSE;
        $talent = $this->talent->getTalentDetailsByTalentId($talentId);
        $roleStream = isset($talent->role_stream) ? $talent->role_stream : null;
        $checkAttendenceInserted = $this->talentAttendenceSheet->getTalentAttendanceDate($talentId, $dateOfAttendence);

        $id = null;
        if ($checkAttendenceInserted) {
            $id = $checkAttendenceInserted->id;
        }
        $encashment = 0;
        if (strtotime($timeWorked) < strtotime($minimumHourRequered)) {
            $avgConsider = 0;
            if ($roleStream == $managementRoleStream) {
                $creditCompOff = $isHoliday ? 2 : 0;
            } else {
                $creditCompOff = 0;
            }
        } else {
            $avgConsider = 1;
            if ($roleStream == $managementRoleStream) {
                $creditCompOff = $isHoliday ? 1 : 0;
            } else {
                $creditCompOff = 0;
            }
            $creditCompOff = $publicHolidayStatus ? 1 : 0;
            $encashment = $publicHolidayStatus ? 1 : 0;
        }
//        if ($regularHoliday && $avgConsider == 1) {
//            $isHolidayWorkedForAvg = $this->talentAttendenceSheet->checkHolidayWorkedForAvg($talentId, $isHoliday, $dateOfAttendence);
//            $avgConsider = $isHolidayWorkedForAvg ? 0 : 1;
//        }
        $this->db->trans_begin();
        if ($id) {
            $this->updatAttendanceIfAlreadyExist($timeWorked, $id, $isHoliday, $leaveStatus, $avgConsider, $creditCompOff, $encashment, $timeTravelled);
        } else {
            $this->insertAttendance($talentId, $timeWorked, $isHoliday, $leaveStatus, $dateOfAttendence, $avgConsider, $creditCompOff, $encashment, $timeTravelled);
        }
        if ($creditCompOff == 1) {
            $this->updateCompOff($talentId, $dateOfAttendence);
        }
        $this->db->trans_commit();
    }

    public function insertAttendance($talentId, $timeWorked, $isHoliday, $isLeave, $dateOfAttendence, $avgConsider, $creditCompOff, $encashment, $timeTravelled) {
        $insertData = array(
            'talent_id' => $talentId,
            'attendance_date' => $dateOfAttendence,
            'od_hours' => $timeWorked,
            'productive_hrs' => $timeWorked,
            'avg_consider' => $avgConsider,
            'is_holiday' => $isHoliday,
            'is_leave' => $isLeave,
            'holiday_hr_status' => $creditCompOff,
            'encashment' => $encashment,
            'is_public_holiday_worked' => $encashment,
            'travel_time' => $timeTravelled,
        );
        $this->db->insert('talent_attendance', $insertData);
    }

    public function updateCompOff($talentId, $date) {
        //check data already exist
        $leaveCreditStatus = $this->leaves->checkCompExist($talentId, $date, 1);
        if ($leaveCreditStatus) {
            return;
        }
        $user = $this->talent->getUserByTalentId($talentId);
        //creating notification for reporting manager
        $insertNotificationData = array(
            'user' => $user,
            'type' => 'Green',
            'message' => 'Comp off credicted for the date ' . date('d/m/Y', strtotime($date)),
            'message_time' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('notifications', $insertNotificationData);

        //inserting compoff to table for managing talent's compoff fo leave purpose
        $insertTalentCompoff = array(
            'talent' => $talentId,
            'date_of_compoff' => $date
        );
        //inserting
        $queryResult = $this->db->insert('talent_compoff', $insertTalentCompoff);
        $this->db->set('leave_left', 'leave_left+1', False);
        $this->db->where('leave_type', 1);
        $this->db->where('talent', $talentId);
        $queryResult = $this->db->update('talent_leave_left');
    }

    public function updatAttendanceIfAlreadyExist($timeWorked, $id, $isHoliday, $isLeave, $avgConsider, $creditCompOff, $encashment, $timeTravelled) {
        $updateData = array(
            'od_hours' => $timeWorked,
            'productive_hrs' => $timeWorked,
            'avg_consider' => $avgConsider,
            'is_holiday' => $isHoliday,
            'is_leave' => $isLeave,
            'holiday_hr_status' => $creditCompOff,
            'encashment' => $encashment,
            'is_public_holiday_worked' => $encashment,
            'travel_time' => $timeTravelled,
        );
        $this->db->where('id', $id);
        $this->db->update('talent_attendance', $updateData);
    }

    public function DeclineTime() {
        $date = $this->input->get('date');
        $talent = $this->input->get('talent');
        $reason = $this->input->get('reason');
        $updateData = array(
            'is_approved' => 3,
            'send_approval' => 0,
            'reason' => $reason
        );
        //inserting
        $this->db->where('date', $date);
        $this->db->where('send_approval', 1);
        $this->db->where('talent', $talent);
        $queryResult = $this->db->update('time_prediction', $updateData);
        if (!$queryResult) {
            echo 0;
        } else {
            $this->declineTalentAllowance($date, $talent);   //decline talent allowance 
        }
    }

    public function DeclineApprovedTime() {
        $date = $this->input->get('date');
        $talent = $this->input->get('talent');
        $reason = $this->input->get('reason');
        $updateData = array(
            'is_approved' => 3,
            'send_approval' => 0,
            'reason' => $reason
        );
        //inserting
        $this->db->where('date', $date);
        $this->db->where('is_approved', 1);
        $this->db->where('talent', $talent);
        $queryResult = $this->db->update('time_prediction', $updateData);
        if (!$queryResult) {
            echo 0;
        } else {
            $this->declineApprovedTalentAllowance($date, $talent);   //decline approved allowance 
        }
    }

    public function ReportTime() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

            $projects = $this->ProjectMappingModel->getAllProjects();
            $projectArray = array();
            $projectArray[''] = "Select";
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['name'] . "-" . $project['project_code'];
            }
            $data['projects'] = $projectArray;

            $tasks = $this->TaskManagementModel->getAllTask();
            $taskArray = array();
            $taskArray[''] = "Select";
            foreach ($tasks as $task) {
                $taskArray[$task['id']] = $task['task_code'];
            }
            $data['tasks'] = $taskArray;

            $this->load->helper(array('form'));

            $this->load->view('TimePrediction/ReportTime', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function ReportAjax() {
        $talent = "";
        if ($this->input->get('talent')) {
            $talent = $this->input->get('talent');
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = $this->input->get('project');
        }
        $task = "";
        if ($this->input->get('task')) {
            $task = $this->input->get('task');
        }
        $track = "";
        if ($this->input->get('track')) {
            $track = $this->input->get('track');
        }
        $fromdate = "";
        if ($this->input->get('fromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromdate'));
            $fromdate = $date->format('Y-m-d');
        }
        $todate = "";
        if ($this->input->get('todate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('todate'));
            $todate = $date->format('Y-m-d');
        }
        $data['ReportData'] = $this->TimePredictionModel->ReportData($talent, $project, $task, $fromdate, $todate, $track);
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ReportTimeExcel() {
        $talent = "";
        if ($this->input->get('talent')) {
            $talent = $this->input->get('talent');
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = $this->input->get('project');
        }
        $task = "";
        if ($this->input->get('task')) {
            $task = $this->input->get('task');
        }
        $track = "";
        if ($this->input->get('track')) {
            $track = $this->input->get('track');
        }
        $fromdate = "";
        if ($this->input->get('fromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromdate'));
            $fromdate = $date->format('Y-m-d');
        }
        $todate = "";
        if ($this->input->get('todate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('todate'));
            $todate = $date->format('Y-m-d');
        }
        $data['ReportData'] = $this->TimePredictionModel->ReportData($talent, $project, $task, $fromdate, $todate, $track);
        $this->load->view('TimePrediction/ExcelTime', $data);
    }

    public function ProjectReportTime() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
//            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

            $projects = $this->ProjectMappingModel->getAllProjects();
            $projectArray = array();
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['name'] . "-" . $project['project_code'];
            }
            $data['projects'] = $projectArray;

            $tasks = $this->TaskManagementModel->getAllTask();
            $taskArray = array();
            foreach ($tasks as $task) {
                $taskArray[$task['id']] = $task['task_code'];
            }
            $data['tasks'] = $taskArray;
            $invoiceStatus = $this->Projects->getInvoiceStatuses();
            $invoiceStatusArray = array(
                '' => 'All',
            );
            foreach ($invoiceStatus as $status) {
                $invoiceStatusArray[$status['status_id']] = $status['status'];
            }
            $data['invoiceStatus'] = $invoiceStatusArray;
            $this->load->helper(array('form'));

            $this->load->view('TimePrediction/ProjectConsolidatedReport', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function ProjectReportAjax() {
        $talent = "";
        if ($this->input->get('talent')) {
            $talent = $this->input->get('talent');
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = $this->input->get('project');
        }
        $task = "";
        if ($this->input->get('task')) {
            $task = $this->input->get('task');
        }
        $track = "";
        if ($this->input->get('track')) {
            $track = $this->input->get('track');
        }
        $fromdate = "";
        if ($this->input->get('fromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromdate'));
            $fromdate = $date->format('Y-m-d');
        }
        $todate = "";
        if ($this->input->get('todate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('todate'));
            $todate = $date->format('Y-m-d');
        }
        $advanceinvoicefromdate = "";
        if ($this->input->get('advanceinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicefromdate'));
            $advanceinvoicefromdate = $date->format('Y-m-d');
        }
        $advanceinvoicetodate = "";
        if ($this->input->get('advanceinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicetodate'));
            $advanceinvoicetodate = $date->format('Y-m-d');
        }
        $completedinvoicefromdate = "";
        if ($this->input->get('completedinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicefromdate'));
            $completedinvoicefromdate = $date->format('Y-m-d');
        }
        $completedinvoicetodate = "";
        if ($this->input->get('completedinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicetodate'));
            $completedinvoicetodate = $date->format('Y-m-d');
        }
        $invoicestatus = "";
        if ($this->input->get('invoicestatus') !== '') {
            $invoicestatus = $this->input->get('invoicestatus');
        }
        //$data['ProjectReportData'] = $this->TimePredictionModel->ProjectReportDataCollapse($talent, $project, $task, $fromdate, $todate);
        $data['ProjectReportData'] = $this->TimePredictionModel->ProjectsReportDataCollapse($talent, $project, $task, $fromdate, $todate, $track, $advanceinvoicefromdate, $advanceinvoicetodate, $completedinvoicefromdate, $completedinvoicetodate, $invoicestatus);
        $data['talent'] = $talent;
        $data['task'] = $task;
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;
        $data['track'] = $track;
        $data['advanceinvoicefromdate'] = $advanceinvoicefromdate;
        $data['advanceinvoicetodate'] = $advanceinvoicetodate;
        $data['completedinvoicefromdate'] = $completedinvoicefromdate;
        $data['completedinvoicetodate'] = $completedinvoicetodate;
        $data['invoicestatus'] = $invoicestatus;
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ProjectReportpiechartCanvas() {
        $data['canvas'] = 1;
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ProjectReportpiechart() {
        $talent = "";
        if ($this->input->get('talent')) {
            $talent = $this->input->get('talent');
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = $this->input->get('project');
        }
        $task = "";
        if ($this->input->get('task')) {
            $task = $this->input->get('task');
        }
        $track = "";
        if ($this->input->get('track')) {
            $track = $this->input->get('track');
        }
        $fromdate = "";
        if ($this->input->get('fromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromdate'));
            $fromdate = $date->format('Y-m-d');
        }
        $todate = "";
        if ($this->input->get('todate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('todate'));
            $todate = $date->format('Y-m-d');
        }
        $advanceinvoicefromdate = "";
        if ($this->input->get('advanceinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicefromdate'));
            $advanceinvoicefromdate = $date->format('Y-m-d');
        }
        $advanceinvoicetodate = "";
        if ($this->input->get('advanceinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicetodate'));
            $advanceinvoicetodate = $date->format('Y-m-d');
        }
        $completedinvoicefromdate = "";
        if ($this->input->get('completedinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicefromdate'));
            $completedinvoicefromdate = $date->format('Y-m-d');
        }
        $completedinvoicetodate = "";
        if ($this->input->get('completedinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicetodate'));
            $completedinvoicetodate = $date->format('Y-m-d');
        }
        $invoicestatus = "";
        if ($this->input->get('invoicestatus')) {
            $invoicestatus = $this->input->get('invoicestatus');
        }
        //$data['ProjectReportPieData'] = $this->TimePredictionModel->ProjectReportDataCollapse($talent, $project, $task, $fromdate, $todate);
        $data['ProjectReportPieData'] = $this->TimePredictionModel->ProjectsReportDataCollapse($talent, $project, $task, $fromdate, $todate, $track, $advanceinvoicefromdate, $advanceinvoicetodate, $completedinvoicefromdate, $completedinvoicetodate, $invoicestatus);
        $data['talent'] = $talent;
        $data['task'] = $task;
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;

        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function TotalTime() {
        $talent = "";
        if ($this->input->get('talent')) {
            $talent = $this->input->get('talent');
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = $this->input->get('project');
        }
        $task = "";
        if ($this->input->get('task')) {
            $task = $this->input->get('task');
        }
        $fromdate = "";
        if ($this->input->get('fromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromdate'));
            $fromdate = $date->format('Y-m-d');
        }
        $todate = "";
        if ($this->input->get('todate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('todate'));
            $todate = $date->format('Y-m-d');
        }
        $ProjectReportPieData = $this->TimePredictionModel->ProjectReportDataCollapse($talent, $project, $task, $fromdate, $todate);
        $alltime = array();
        foreach ($ProjectReportPieData as $data) {
            $alltime[] = $this->talentAttendenceSheet->secondsToMinitues($data['time']);
        }
        $data['totalTimeSpentForInterval'] = '';
        if (count($alltime) > 0) {
            $data['totalTimeSpentForInterval'] = $this->TimePredictionModel->sum_time($alltime);
        }

        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ChangeTask() {
        $project_id = $this->input->get('project');
        $tasks = array();
        foreach ($project_id as $project) {
            $tasks = array_merge($tasks, $this->ProjectMappingModel->getTasksbyProject($project));
        }
        $taskArray = array();
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'] . "-" . $task['task_desc'];
        }
        $data['tasks'] = $taskArray;
        $data['ChangeTask'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ChangeTaskSingle() {
        $project_id = $this->input->get('project_id');
        $tasks = $this->ProjectMappingModel->getTasksbyProject($project_id);
        $taskArray = array();
        $taskArray[''] = "--Task--";
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'] . "-" . $task['task_desc'];
        }
        $data['tasks'] = $taskArray;
        $data['ChangeTaskSingle'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function getTaskByProject() {
        $project_id = $this->input->get('project_id');
        $tasks = $this->ProjectMappingModel->getTasksbyProject($project_id);
        $taskArray = array();
        $taskArray[''] = "--Task--";
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'] . "-" . $task['task_desc'];
        }
        $data['tasks'] = $taskArray;
        $data['getTaskByProject'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function getTaskByProjectById() {
        $project_id = $this->input->get('project_id');
        $task_id = $this->input->get('task_id');
        $tasks = $this->ProjectMappingModel->getTasksbyProject($project_id);
        $taskArray = array();
        $taskArray[''] = "--Task--";
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'] . "-" . $task['task_desc'];
        }
        $data['tasks'] = $taskArray;
        $data['task_id'] = $task_id;
        $data['getTaskByProjectById'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function getTaskByProjectActual() {
        $project_id = $this->input->get('project_id');
        $tasks = $this->ProjectMappingModel->getTasksbyProject($project_id);
        $taskArray = array();
        $taskArray[''] = "--Task--";
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'] . "-" . $task['task_desc'];
        }
        $data['tasks'] = $taskArray;
        $data['getTaskByProjectActual'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ViewDetailsApprove() {
        $talent = $this->input->get('talent');
        $talentDetails = $this->talent->getTalentDetailsByTalentId($talent);
        $talentName = $talentDetails->first_name . " " . $talentDetails->middle_name . " " . $talentDetails->last_name;
        $date = $this->input->get('date');
        $approv = $this->input->get('approv');
        $viewAll = $this->input->get('view_all');

        $data['CheckActualHourAndOD'] = $this->TimePredictionModel->CheckActualHourAndOD($talent, $date);

        $data['findActualHourAndOD'] = $this->TimePredictionModel->findActualHourAndOD($talent, $date);
        if ($viewAll == 1) {
            $data['getAllTimeByDate'] = $this->TimePredictionModel->getAllTimeBeforeApproval($date, $talent);
        } else {
            $data['getAllTimeByDate'] = $this->TimePredictionModel->getAllTimeByDate($date, $talent);
        }
        $data['viewAllowanceByDate'] = 1;
        ;
        $data['showTableData'] = 1;
        $data['viewDetails'] = 1;
        $data['viewAllTimeSheet'] = $viewAll;
        $data['talent'] = $talent;
        $data['date'] = $date;
        $data['talentName'] = $talentName;
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function ProjectAmountCal() {
        if ($this->session->userdata('logged_in')) {

            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $projects = $this->ProjectMappingModel->getAllProjects();
            $talentArray = array();
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;
            $projectArray = array();
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['name'] . "-" . $project['project_code'];
            }
            $data['projects'] = $projectArray;

            $taskArray = array();
            $data['tasks'] = $taskArray;

            $this->load->helper(array('form'));

            $this->load->view('TimePrediction/ProjectAmountCal', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function ProjectAmountCalAjax() {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromdate'));
        $fromdate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('todate'));
        $todate = $date->format('Y-m-d');
        $project = $this->input->get('project');
        $task = $this->input->get('task');
        $talent = $this->input->get('talent');
        $track = $this->input->get('track');
        $data['project'] = $project;
        $data['talent'] = $talent;
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;
        $data['task'] = $task;
        $data['track'] = $track;
        //$data['ProjectAmountCal'] = $this->TimePredictionModel->ProjectAmountTalents($project, $fromdate, $todate, $task);
        $data['ProjectAmountCal'] = $this->TimePredictionModel->ProjectsAmountTalents($project, $fromdate, $todate, $task, $talent, $track);
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function showProductivity() {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('date'));
        $data['date'] = $date->format('Y-m-d');
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $is_management = $session_data['is_management'];
        $talentId = $session_data['talentId'];
        $data['talentsProductivity'] = $this->ProjectMappingModel->getTalentsByJoiningAndResignDate($talentId, $is_management, $data['date']);
        $this->load->helper(array('form'));
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function getProductivityLog() {
        $talent = $this->input->get('talent');
        $data['userProductivity'] = $this->TimePredictionModel->getProductivityLogByTalent($talent);
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function Productivity() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $data['talents'] = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $this->load->helper(array('form'));

            $this->load->view('TimePrediction/Productivity', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function ProductivityUpdate() {
        $this->load->helper('date');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('date'));
        $productivityDate = $date->format('Y-m-d');
        $talent = $this->input->get('talent');
        $productivity = $this->input->get('productivity');
        $insertData = array(
            'productivity' => $productivity,
            'talent' => $talent,
            'date' => $productivityDate
        );
        //inserting
        $queryResult = $this->db->insert('productivity', $insertData);
        if (!$queryResult) {
            echo "0";
        } else {
            echo "1";
        }
    }

    public function TalentSheetFilling() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

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

            $data['years'] = $yearArray;
            $data['months'] = $monthArray;

            $this->load->helper(array('form'));
            $this->load->helper('url');
            $this->load->view('TimePrediction/TalentSheetFilling', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function showInputElementsTalents() {
        $talent = $this->input->get('talent');
        $year = $this->input->get('year');
        $month = $this->input->get('month');

        $monthYear = $year . "-" . $month;
        $fromdate = $monthYear . '-01';
        $todate = date('Y-m-t', strtotime($fromdate));

        $taskArray = array();
        $taskArray[''] = "--Task--";
        $data['tasks'] = $taskArray;
        $projects = $this->TimePredictionModel->getAllProjectbyDateTalent($talent, $fromdate, $todate);
        $projectArray = array();
        $projectArray[''] = "--Project--";
        foreach ($projects as $project) {
            $projectArray[$project['id']] = $project['project_code'];
        }
        $data['projects'] = $projectArray;

        $data['inputelements'] = 1;

        $number = $this->TimePredictionModel->CheckApprovedOrNotTalent($talent, $year, $month);
        //echo $time = $this->TimePredictionModel->HoursDecimal("10.35");
        //echo $this->db->last_query().$number;
        if ($number == 0) {

            $data['submitShow'] = 1;
        } else {
            $attendanceDate = $this->TimePredictionModel->PendingTalentsheetnumber($talent, $fromdate, $todate);
            if ($attendanceDate > 0) {
                $data['submitShow'] = 1;
            }
        }
        $data['Percentage'] = 1;
        $this->load->helper(array('form'));

        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function showTableDataTalent() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $talent = $this->input->get('talent');
        $data['getAllTimeByDate'] = $this->TimePredictionModel->getAllTimeByDateTalent($year, $month, $talent);
        $data['showTableDataTalent'] = 1;

        $monthYear = $year . "-" . $month;
        $fromdate = $monthYear . '-01';
        $todate = date('Y-m-t', strtotime($fromdate));

        $attendanceDate = $this->TimePredictionModel->PendingTalentsheetnumber($talent, $fromdate, $todate);
        $data['pendingSheet'] = 0;
        if ($attendanceDate > 0) {
            $data['pendingSheet'] = 1;
        }

        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function SaveInputElementsTalent() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $talent = $this->input->get('talent');
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $added_by = $session_data['talentId'];
        $task_id = $this->input->get('task_id');
        $project_id = $this->input->get('project_id');
        $percentage = $this->input->get('percentage');
        $description = $this->input->get('description');
        $insertData = array(
            'talent' => $talent,
            'year' => $year,
            'month' => $month,
            'project' => $project_id,
            'task' => $task_id,
            'percentage' => $percentage,
            'description' => $description,
            'added_by' => $added_by
        );
        //inserting
        $queryResult = $this->db->insert('talent_time_prediction', $insertData);
        if (!$queryResult) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function EditDataTalent() {
        $id = $this->input->get('id');
        $all_data = $this->TimePredictionModel->getAllDataByIdTalent($id);
        $this->load->helper(array('form'));
        $talent = $all_data[0]['talent'];
        $year = $all_data[0]['year'];
        $month = $all_data[0]['month'];

        $monthYear = $year . "-" . $month;
        $fromdate = $monthYear . '-01';
        $todate = date('Y-m-t', strtotime($fromdate));

        $projects = $this->TimePredictionModel->getAllProjectbyDateTalent($talent, $fromdate, $todate);
        $projectArray = array();
        foreach ($projects as $project) {
            $projectArray[$project['id']] = $project['project_code'];
        }
        $data['projects'] = $projectArray;

        $project_details = $this->Projects->getProjectById($all_data[0]['project']);
        $assigned_tasks = explode(",", $project_details->assigned_tasks);
        $tasks = $this->TimePredictionModel->getProjectTask($assigned_tasks);
        $taskArray = array();
        //$taskArray[''] = "--Task--";
        foreach ($tasks as $task) {
            $taskArray[$task['id']] = $task['task_code'];
        }
        $data['tasks'] = $taskArray;

        $data['project'] = $all_data[0]['project'];
        $data['task_selected'] = $all_data[0]['task'];
        $data['percentage'] = $all_data[0]['percentage'];
        $data['description'] = $all_data[0]['description'];
        $data['id'] = $all_data[0]['id'];
        $data['EditData'] = 1;

        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    public function UpdateInputElementsTalent() {
        $error_flag = 0;
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $talent = $this->input->get('talent');
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $added_by = $session_data['talentId'];
        $task_id = $this->input->get('task_id');
        $project_id = $this->input->get('project_id');
        $percentage = $this->input->get('percentage');
        $description = $this->input->get('description');
        $id = $this->input->get('id');
        $updateData = array(
            'talent' => $talent,
            'year' => $year,
            'month' => $month,
            'project' => $project_id,
            'task' => $task_id,
            'percentage' => $percentage,
            'description' => $description,
            'added_by' => $added_by
        );
        //inserting
        $this->db->where('id', $id);
        $queryResult = $this->db->update('talent_time_prediction', $updateData);
        if (!$queryResult) {
            $error_flag = 1;
        }
        echo $error_flag;
    }

    public function DeleteDataTalent() {
        $id = $this->input->get('id');
        $this->db->where('id', $id);
        $queryResult = $this->db->delete('talent_time_prediction');
        if (!$queryResult) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function SendApprovalTalent() {
        $this->db->trans_begin();
        $error_flag = 1;
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $talent = $this->input->get('talent');

        $monthYear = $year . "-" . $month;
        $fromdate = $monthYear . '-01';
        $todate = date('Y-m-t', strtotime($fromdate));

        $attendanceDate = $this->TimePredictionModel->PendingSubmitReport($talent, $fromdate, $todate);
        foreach ($attendanceDate as $atDate) {
            if ($error_flag == 1) {
                $date = $atDate['attendance_date'];
                $number = $this->TimePredictionModel->CheckApprovedOrNot($date, $talent);
                if ($number == 0) {
                    $this->db->where('talent', $talent);
                    $this->db->where('date', $date);
                    $queryResult = $this->db->delete('time_prediction');
                    if (!$queryResult) {
                        $error_flag = 0;
                        break;
                    }
                    $alltime = array();
                    $alltime[] = $atDate['actual_hours_spend'];
                    $alltime[] = $atDate['od_hours'];
                    $time = $this->TimePredictionModel->sum_time($alltime);
                    $timedecimal = $this->TimePredictionModel->decimalHours($time);
                    $getAllData = $this->TimePredictionModel->getAllTimeByDateTalent($year, $month, $talent);
                    foreach ($getAllData as $gdat) {
                        $timecal = $timedecimal * ($gdat['percentage'] / 100);
                        $time = $this->TimePredictionModel->HoursDecimal($timecal);
                        $insertData = array(
                            'talent' => $talent,
                            'date' => $date,
                            'Project' => $gdat['project'],
                            'task' => $gdat['task'],
                            'time' => $time,
                            'description' => $gdat['description'],
                            'is_approved' => 1
                        );
                        //inserting
                        $queryResult = $this->db->insert('time_prediction', $insertData);
                        if (!$queryResult) {
                            $error_flag = 0;
                            break;
                        }
                    }
                }
            }
        }
        if ($error_flag == 1) {
            $updateData = array(
                'is_approved' => 1
            );
            //inserting
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $this->db->where('talent', $talent);
            $queryResult = $this->db->update('talent_time_prediction', $updateData);
            if (!$queryResult) {
                $error_flag = 0;
            }
        }
        if ($error_flag == 1) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        echo $error_flag;
    }

    public function excelDownload() {
//        error_reporting(1);
        $talent = "";
        if ($this->input->get('talent')) {
            $talent = explode(',', $this->input->get('talent'));
        }
        $fromDate = '';
        if ($this->input->get('fromDate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromDate'));
            $fromDate = $date->format('Y-m-d');
        }
        $toDate = "";
        if ($this->input->get('toDate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('toDate'));
            $toDate = $date->format('Y-m-d');
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = explode(',', $this->input->get('project'));
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = explode(',', $this->input->get('project'));
        }
        $task = "";
        if ($this->input->get('task')) {
            $task = explode(',', $this->input->get('task'));
        }
        $track = "";
        if ($this->input->get('track')) {
            $track = $this->input->get('track');
        }
        $advanceinvoicefromdate = "";
        if ($this->input->get('advanceinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicefromdate'));
            $advanceinvoicefromdate = $date->format('Y-m-d');
        }
        $advanceinvoicetodate = "";
        if ($this->input->get('advanceinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicetodate'));
            $advanceinvoicetodate = $date->format('Y-m-d');
        }
        $completedinvoicefromdate = "";
        if ($this->input->get('completedinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicefromdate'));
            $completedinvoicefromdate = $date->format('Y-m-d');
        }
        $completedinvoicetodate = "";
        if ($this->input->get('completedinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicetodate'));
            $completedinvoicetodate = $date->format('Y-m-d');
        }
        $invoicestatus = "";
        if ($this->input->get('invoicestatus')) {
            $invoicestatus = $this->input->get('invoicestatus');
        }
        $heading = array(
            0 => 'SL. No.',
            1 => 'Project',
            2 => 'Estimated Hours',
            3 => 'Time',
            4 => 'Difference',
        );
        $slno = 0;
        $content = array();
        $ProjectReportData = $this->TimePredictionModel->ProjectsReportDataCollapse($talent, $project, $task, $fromDate, $toDate, $track, $advanceinvoicefromdate, $advanceinvoicetodate, $completedinvoicefromdate, $completedinvoicetodate, $invoicestatus);
        foreach ($ProjectReportData as $pro) {
            $estimatedInSeconds = $pro['estimated_hours'] * 3600;
            $diffrence = abs($pro['time'] - $estimatedInSeconds);
            $content[$slno] = array(
                0 => $slno + 1,
                1 => $pro['project'],
                2 => $this->talentAttendenceSheet->secondsToMinitues($estimatedInSeconds),
                3 => $this->talentAttendenceSheet->secondsToMinitues($pro['time']),
                4 => $this->talentAttendenceSheet->secondsToMinitues($diffrence),
            );
            $slno++;
        }
        $data['heading'] = $heading;
        $data['tableName'] = 'Project Consolidated Report';
        $data['content'] = $content;
        $data['slno'] = $slno;
        $this->load->view('excel/excel', $data);
    }

    /*
     * Talent's datewise billable and non billable works report
     */

    public function dateWiseBillability() {
        $this->load->helper(array('form'));
        $talent = $this->input->post('talent');
        $fromDateNotStandered = $this->input->post('fromDate') ? $this->input->post('fromDate') : date('d/m/Y');
        $toDateNotStandered = $this->input->post('toDate') ? $this->input->post('toDate') : date('d/m/Y');
        $isResigned = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
        $date = DateTime::createFromFormat('d/m/Y', $fromDateNotStandered);
        $fromDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $toDateNotStandered);
        $toDate = $date->format('Y-m-d');
        //finds the billability between dates for a talent
        if ($talent) {
            $talentBillabilityBetweenDates = $this->TimePredictionModel->talentBillabilityBetweenDates($talent, $fromDate, $toDate);
        } else {
            $talentBillabilityBetweenDates = array();
        }
        //finds the talents based on the resigned status
        $talents = $this->talent->getTalentsAllByResignStatus($isResigned);
        $talentsDropdown = array("" => "[SELECT Talent]");
        foreach ($talents as $talentSpecific) {
            $id = $talentSpecific['id'];
            $talentsDropdown[$id] = $talentSpecific['name'];
        }
        $data['fromDate'] = $fromDateNotStandered;
        $data['toDate'] = $toDateNotStandered;
        $data['isResigned'] = $isResigned;
        $data['talentsDropdown'] = $talentsDropdown;
        $data['selectedTalent'] = $talent;
        $data['talentBillabilityBetweenDates'] = $talentBillabilityBetweenDates;
        $this->load->view('TimePrediction/dateWiseBillability', $data);
    }

    /*
     * Talent's teamwise billable and non billable works report
     */

    public function teamWiseBillability() {
        $this->load->helper(array('form'));
        $fromDateNotStandered = $this->input->post('fromDate') ? $this->input->post('fromDate') : date('d/m/Y');
        $toDateNotStandered = $this->input->post('toDate') ? $this->input->post('toDate') : date('d/m/Y');
        $isResigned = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
        $date = DateTime::createFromFormat('d/m/Y', $fromDateNotStandered);
        $fromDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $toDateNotStandered);
        $toDate = $date->format('Y-m-d');
        //finds the talents based on the resigned status
        $talents = $this->talent->getTalentsAllByResignStatus($isResigned);
        //finds the billability between dates for each talent
        $teamBillabilityBetweenDates = array();
        if ($this->input->post()) {
            foreach ($talents as $talentSpecific) {
                $talentBillabilityBetweenDates = $this->TimePredictionModel->talentBillabilityBetweenDates($talentSpecific['id'], $fromDate, $toDate);
                $billableTimeTalent = array();
                $billableProductivityTalent = 0;
                $nonBillableProductivityTalent = 0;
                $totalProductivityTalent = 0;
                $nonBillableTimeTalent = array();
                $talentTime = array();
                foreach ($talentBillabilityBetweenDates as $talentBillabilityBetweenDatesSpecific) {
                    $billableTimeTalent[] = $talentBillabilityBetweenDatesSpecific['billable_time'];
                    $billableProductivityTalent += $talentBillabilityBetweenDatesSpecific['billable_productivity'];
                    $nonBillableTimeTalent[] = $talentBillabilityBetweenDatesSpecific['non_billable_time'];
                    $nonBillableProductivityTalent += $talentBillabilityBetweenDatesSpecific['non_billable_productivity'];
                    $talentTime[] = $talentBillabilityBetweenDatesSpecific['time'];
                    $totalProductivityTalent += $talentBillabilityBetweenDatesSpecific['productivity_date'];
                }
                $totalBillableTimeTalent = $this->TimePredictionModel->sum_time($billableTimeTalent);
                $timeBillableDecimal = $this->TimePredictionModel->decimalHours($totalBillableTimeTalent);

                $totalNonBillableTimeTalent = $this->TimePredictionModel->sum_time($nonBillableTimeTalent);

                $totalTime = $this->TimePredictionModel->sum_time($talentTime);
                $totalTimeDecimal = $this->TimePredictionModel->decimalHours($totalTime);
                $talentAverageHour = $this->TimePredictionModel->decimalHours($talentSpecific['average_working_hour_per_day']);
                $talentNumberOfWorkingDays = $this->talentAttendenceSheet->getNumberOfWorkingDaysTalent($talentSpecific['id'], $fromDate, $toDate);
                $totalWorkingHoursDecimal = $talentAverageHour * $talentNumberOfWorkingDays;
                if ($totalProductivityTalent) {
                    $percentageOfBillableProductivity = $totalWorkingHoursDecimal ? round(($timeBillableDecimal * 100 / $totalWorkingHoursDecimal), 2) . '%' : '0%';
                    $teamBillabilityBetweenDates[] = array(
                        'name' => $talentSpecific['name'],
                        'talent_billable_time' => $totalBillableTimeTalent,
                        'talent_billable_productivity' => $this->claim->commaSeperated($billableProductivityTalent),
                        'talent_non_billable_time' => $totalNonBillableTimeTalent,
                        'talent_non_billable_productivity' => $this->claim->commaSeperated($nonBillableProductivityTalent),
                        'talent_total_time' => $totalTime,
                        'talent_total_productivity' => $this->claim->commaSeperated($totalProductivityTalent),
                        'percentage_billable_productivity' => $percentageOfBillableProductivity,
                        'number_of_working_days' => $talentNumberOfWorkingDays,
                    );
                }
            }
        }
        $data['fromDate'] = $fromDateNotStandered;
        $data['toDate'] = $toDateNotStandered;
        $data['isResigned'] = $isResigned;
        $data['teamBillabilityBetweenDates'] = $teamBillabilityBetweenDates;
        $this->load->view('TimePrediction/teamWiseBillability', $data);
    }

    public function teamWiseBillabilityExcel() {
        $fromDateNotStandered = $this->input->get('fromDate') ? $this->input->get('fromDate') : date('d/m/Y');
        $toDateNotStandered = $this->input->get('toDate') ? $this->input->get('toDate') : date('d/m/Y');
        $isResigned = $this->input->get('resigned') == '' ? 0 : $this->input->get('resigned');
        $date = DateTime::createFromFormat('d/m/Y', $fromDateNotStandered);
        $fromDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $toDateNotStandered);
        $toDate = $date->format('Y-m-d');
        //finds the talents based on the resigned status
        $talents = $this->talent->getTalentsAllByResignStatus($isResigned);
        //finds the billability between dates for each talent
        $teamBillabilityBetweenDates = array();
        $slno = 0;
        foreach ($talents as $talentSpecific) {
            $talentBillabilityBetweenDates = $this->TimePredictionModel->talentBillabilityBetweenDates($talentSpecific['id'], $fromDate, $toDate);
            $billableTimeTalent = array();
            $billableProductivityTalent = 0;
            $nonBillableProductivityTalent = 0;
            $totalProductivityTalent = 0;
            $nonBillableTimeTalent = array();
            $talentTime = array();
            foreach ($talentBillabilityBetweenDates as $talentBillabilityBetweenDatesSpecific) {
                $billableTimeTalent[] = $talentBillabilityBetweenDatesSpecific['billable_time'];
                $billableProductivityTalent += $talentBillabilityBetweenDatesSpecific['billable_productivity'];
                $nonBillableTimeTalent[] = $talentBillabilityBetweenDatesSpecific['non_billable_time'];
                $nonBillableProductivityTalent += $talentBillabilityBetweenDatesSpecific['non_billable_productivity'];
                $talentTime[] = $talentBillabilityBetweenDatesSpecific['time'];
                $totalProductivityTalent += $talentBillabilityBetweenDatesSpecific['productivity_date'];
            }
            $totalBillableTimeTalent = $this->TimePredictionModel->sum_time($billableTimeTalent);
            $timeBillableDecimal = $this->TimePredictionModel->decimalHours($totalBillableTimeTalent);

            $totalNonBillableTimeTalent = $this->TimePredictionModel->sum_time($nonBillableTimeTalent);

            $totalTime = $this->TimePredictionModel->sum_time($talentTime);
            $totalTimeDecimal = $this->TimePredictionModel->decimalHours($totalTime);
            $talentAverageHour = $this->TimePredictionModel->decimalHours($talentSpecific['average_working_hour_per_day']);
            $talentNumberOfWorkingDays = $this->talentAttendenceSheet->getNumberOfWorkingDaysTalent($talentSpecific['id'], $fromDate, $toDate);
            $totalWorkingHoursDecimal = $talentAverageHour * $talentNumberOfWorkingDays;
            if ($totalProductivityTalent) {
                $percentageOfBillableProductivity = $totalWorkingHoursDecimal ? round(($timeBillableDecimal * 100 / $totalWorkingHoursDecimal), 2) . '%' : '0%';
                $teamBillabilityBetweenDates[$slno] = array(
                    0 => $slno + 1,
                    1 => $talentSpecific['name'],
                    2 => $totalBillableTimeTalent,
                    3 => $this->claim->commaSeperated($billableProductivityTalent),
                    4 => $totalNonBillableTimeTalent,
                    5 => $this->claim->commaSeperated($nonBillableProductivityTalent),
                    6 => $totalTime,
                    7 => $this->claim->commaSeperated($totalProductivityTalent),
                    9 => $talentNumberOfWorkingDays,
                    8 => $percentageOfBillableProductivity,
                );
                $slno++;
            }
        }
        $data['tableName'] = 'Team Wise Billability';
        $heading = array(
            0 => 'SL. No.',
            1 => 'Talent',
            2 => 'Billable Time',
            3 => 'Billable Productivity',
            4 => 'Non Billable Time',
            5 => 'Non Billable Productivity',
            6 => 'Total Time',
            8 => 'Total Productivity',
            7 => 'Total Working Days',
            9 => 'Percentage of Billability',
        );
//        echo '<pre>';
//        var_dump($teamBillabilityBetweenDates);
//        echo '</pre>';
//        die();
        $data['heading'] = $heading;
        $data['content'] = $teamBillabilityBetweenDates;
        $this->load->view('excel/excel', $data);
    }

    public function billableNonBillableDetails() {
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $project = $this->input->post('project');
        $task = $this->input->post('task');
        $talent = $this->input->post('talent');
        $data['project'] = $project;
        $data['talent'] = $talent;
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;
        $data['task'] = $task;
        $this->load->view('TimePrediction/BillableNonBillableDetails', $data);
    }

    public function billableNonBillableDetailsComplete() {
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $project = $this->input->post('project');
        $task = $this->input->post('task');
        $talent = $this->input->post('talent');
        $data['project'] = $project;
        $data['talent'] = $talent;
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;
        $data['task'] = $task;
        $this->load->view('TimePrediction/BillableNonBillableDetailsComplete', $data);
    }

    public function ProjectTaskAmountCal() {
        if ($this->session->userdata('logged_in')) {

            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $projects = $this->ProjectMappingModel->getAllProjects();
            $talentArray = array();
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;
            $projectArray = array();
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['name'] . "-" . $project['project_code'];
            }
            $data['projects'] = $projectArray;
            $taskArray = array();
            $data['tasks'] = $taskArray;

            $this->load->helper(array('form'));

            $this->load->view('TimePrediction/ProjectTaskAmountCal', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function ProjectTaskAmountCalAjax() {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromdate'));
        $fromdate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('todate'));
        $todate = $date->format('Y-m-d');
        $project = $this->input->get('project');
        $task = $this->input->get('task');
        $talent = $this->input->get('talent');
        $data['project'] = $project;
        $data['searchTalent'] = $talent;
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate;
        $data['task'] = $task;
        $data['ProjectAmountCal'] = $this->TimePredictionModel->projectsAmountTasks($project, $fromdate, $todate, $task, $talent);
        $this->load->view('TimePrediction/PredictionTaskAjax', $data);
    }

    public function excelDetailedDownload() {
//        error_reporting(1);
        $talent = "";
        if ($this->input->get('talent')) {
            $talent = explode(',', $this->input->get('talent'));
        }
        $fromDate = '';
        if ($this->input->get('fromDate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('fromDate'));
            $fromDate = $date->format('Y-m-d');
        }
        $toDate = "";
        if ($this->input->get('toDate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('toDate'));
            $toDate = $date->format('Y-m-d');
        }
        $project = "";
        if ($this->input->get('project')) {
            $project = explode(',', $this->input->get('project'));
        }
        $task = "";
        if ($this->input->get('task')) {
            $task = explode(',', $this->input->get('task'));
        }
        $track = "";
        if ($this->input->get('track')) {
            $track = $this->input->get('track');
        }
        $advanceinvoicefromdate = "";
        if ($this->input->get('advanceinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicefromdate'));
            $advanceinvoicefromdate = $date->format('Y-m-d');
        }
        $advanceinvoicetodate = "";
        if ($this->input->get('advanceinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('advanceinvoicetodate'));
            $advanceinvoicetodate = $date->format('Y-m-d');
        }
        $completedinvoicefromdate = "";
        if ($this->input->get('completedinvoicefromdate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicefromdate'));
            $completedinvoicefromdate = $date->format('Y-m-d');
        }
        $completedinvoicetodate = "";
        if ($this->input->get('completedinvoicetodate')) {
            $date = DateTime::createFromFormat('d/m/Y', $this->input->get('completedinvoicetodate'));
            $completedinvoicetodate = $date->format('Y-m-d');
        }
        $invoicestatus = "";
        if ($this->input->get('invoicestatus')) {
            $invoicestatus = $this->input->get('invoicestatus');
        }
        //$data['ProjectReportData'] = $this->TimePredictionModel->ProjectReportDataCollapse($talent, $project, $task, $fromdate, $todate);
        $projectReportData = $this->TimePredictionModel->ProjectsReportDataCollapse($talent, $project, $task, $fromDate, $toDate, $track, $advanceinvoicefromdate, $advanceinvoicetodate, $completedinvoicefromdate, $completedinvoicetodate, $invoicestatus);
        $heading = array(
            0 => 'SL. No.',
            1 => 'Project',
            2 => 'Talent',
            3 => 'Track',
            4 => 'Task Code',
            5 => 'Task',
            6 => 'Date',
            7 => 'Estimated Hours',
            8 => 'Invoice Status',
            9 => 'Advance Invoice Number',
            10 => 'Advance Invoice Date',
            11 => 'Invoice Number',
            12 => 'Invoice Date',
            13 => 'Time',
            14 => 'Difference',
            15 => 'Description',
        );
        $excelSerialNumber = 0;
        $projectSerialNumber = 0;
        foreach ($projectReportData as $pro) {
            $alltime[] = $this->talentAttendenceSheet->secondsToMinitues($pro['time']);
            $talentSerialNumber = 0;
            $estimatedInSeconds = $pro['estimated_hours'] * 3600;
            $diffrence = abs($pro['time'] - $estimatedInSeconds);
            $content[++$excelSerialNumber] = array(
                0 => '',
                1 => '',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            );
            $content[++$excelSerialNumber] = array(
                0 => ++$projectSerialNumber,
                1 => $pro['project'],
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => $this->talentAttendenceSheet->secondsToMinitues($estimatedInSeconds),
                8 => '',
                9 => '',
                10 => '',
                11 => '',
                12 => '',
                13 => $this->talentAttendenceSheet->secondsToMinitues($pro['time']),
                14 => $this->talentAttendenceSheet->secondsToMinitues($diffrence),
                15 => ''
            );
            $dataProject = $this->TimePredictionModel->ProjectsReportDataExplode($talent, $pro['project_id'], $task, $fromDate, $toDate, $track, $advanceinvoicefromdate, $advanceinvoicetodate, $completedinvoicefromdate, $completedinvoicetodate, $invoicestatus);
            if ($dataProject) {

                foreach ($dataProject as $dataPrjt) {
                    $invice_status = $this->TimePredictionModel->getSelectedInvoiceStatuses($dataPrjt['invoice_status']);
                    $advance_invoice_number = $dataPrjt['advance_invoice_number'];
                    if ($dataPrjt['advance_invoice_number'] == 0) {
                        $advance_invoice_number = '';
                    }
                    $advance_invoice_date = date('d/m/Y', strtotime($dataPrjt['advance_invoice_date']));
                    if ($dataPrjt['advance_invoice_date'] == null) {
                        $advance_invoice_date = '';
                    }
                    $invoice_number = $dataPrjt['invoice_number'];
                    if ($dataPrjt['invoice_number'] == 0) {
                        $invoice_number = '';
                    }
                    $invoice_date = date('d/m/Y', strtotime($dataPrjt['invoice_date']));
                    if ($dataPrjt['invoice_date'] == null) {
                        $invoice_date = '';
                    }
                    $content[++$excelSerialNumber] = array(
                        0 => $projectSerialNumber . '.' . ++$talentSerialNumber,
                        1 => $dataPrjt['project'],
                        2 => $dataPrjt['talent'],
                        3 => $dataPrjt['track'],
                        4 => $dataPrjt['task'],
                        5 => $dataPrjt['task_desc'],
                        6 => date('d/m/Y', strtotime($dataPrjt['date'])),
                        7 => '',
                        8 => $invice_status['status'],
                        9 => $advance_invoice_number,
                        10 => $advance_invoice_date,
                        11 => $invoice_number,
                        12 => $invoice_date,
                        13 => $dataPrjt['time'],
                        14 => $invoice_date,
                        15 => $dataPrjt['description'],
                    );
                }
            }
        }
        if (count($alltime) > 0) {
            $totalTime = $this->TimePredictionModel->sum_time($alltime);
        }
        $content[++$excelSerialNumber] = array(
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => 'Total Time -',
            13 => $totalTime,
            14 => ''
        );
        $data['heading'] = $heading;
        $data['tableName'] = 'Project Consolidated Detailed Report';
        $data['content'] = $content;
        $this->load->view('excel/excel', $data);
    }

    public function taskConsolidatedReport() {
        if ($this->session->userdata('logged_in')) {
            $this->load->view('TaskConsolidatedReport/taskConsolidatedReport');
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function taskConsolidatedAjax() {
        $taskCode = "";
        $resultArray = [];
        if ($this->input->get('taskCode')) {
            $taskCode = $this->input->get('taskCode');
            $resultArray = $this->TaskManagementModel->getTaskConsolidatedData($taskCode);
        }
        $data['dateWiseData'] = !empty($resultArray['dateWiseData']) ? $resultArray['dateWiseData'][0] : "";
        $data['talentWiseData'] = !empty($resultArray['talentWiseData']) ? $resultArray['talentWiseData'][0] : "";

        $this->load->view('TaskConsolidatedReport/taskConsolidatedAjax', $data);
    }

    /*
     * add allowance details
     */

    public function addAllowance($food_allowance, $food_andtravel_allowance, $date, $talent) {

        $insertdata = array(
            'food_allowance' => $food_allowance,
            'food_andtravel_allowance' => $food_andtravel_allowance,
            'date' => $date,
            'talent' => $talent,
            'send_approval' => 1
        );

        $this->db->insert('talent_allowance', $insertdata);
    }

    /*
     * add allowance on auto update talents
     */

    public function addAutoTalentAllowance($food_allowance, $food_andtravel_allowance, $date, $talent) {

        $insertdata = array(
            'food_allowance' => $food_allowance,
            'food_andtravel_allowance' => $food_andtravel_allowance,
            'date' => $date,
            'talent' => $talent,
            'approved' => 1,
            'send_approval' => 0
        );

        $this->db->insert('talent_allowance', $insertdata);
    }

    /*
     * show  load page talent allowance deails in selected year and month
     */

    public function showTalentAllowance() {

        $talents = $this->talent->getTalents();
        $talentsArray = array(
            '' => '[SELECT TALENT]'
        );
        foreach ($talents as $talent) {
            $talentsArray[$talent['id']] = $talent['name'];
        }
        $data['talent'] = $talentsArray;
        $monthArray = array(
            '' => '[SELECT MONTH]'
        );
        $months = $this->talentAttendenceSheet->getMonths();
        foreach ($months as $month) {
            $monthArray[$month['id']] = $month['month'];
        }

        $data['months'] = $monthArray;
        $this->load->helper(array('form'));

        $this->load->view('TimePrediction/showTalentAllowance', $data);
    }

    /*
     * get talent allowanc eon seleced year and month
     */

    public function getTalentAllowance() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $talentAllowance = $this->TimePredictionModel->gettalentAllowance($year, $month);
        $data['talentAllowance'] = $talentAllowance;
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    /*
     * get talent allowane details on view details click
     */

    public function getAllowanceDetails() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $talent = $this->input->get('talent');
        $talentname = $this->input->get('talentname');
        $talentDetailedAllowance = $this->TimePredictionModel->gettalentDetailedAllowance($year, $month, $talent);
        $data['talentDetailedAllowance'] = $talentDetailedAllowance;
        $data['talentname'] = $talentname;
        $this->load->view('TimePrediction/PredictionAjax', $data);
    }

    /*
     * decline talent allowance 
     */

    public function declineTalentAllowance($date, $talent) {
        $updateAllowance = array(
            'send_approval' => 0,
            'approved' => 0
        );
        $this->db->where('date', $date);
        $this->db->where('send_approval', 1);
        $this->db->where('talent', $talent);
        $queryResults = $this->db->update('talent_allowance', $updateAllowance);
        if (!$queryResults) {
            echo 0;
        } else {
            echo 1;
        }
    }

    /*
     * decline approved allowance 
     */

    public function declineApprovedTalentAllowance($date, $talent) {
        $updateAllowance = array(
            'send_approval' => 0,
            'approved' => 0
        );
        $this->db->where('date', $date);
        $this->db->where('approved', 1);
        $this->db->where('talent', $talent);
        $queryResults = $this->db->update('talent_allowance', $updateAllowance);
        if (!$queryResults) {
            echo 0;
        } else {
            echo 1;
        }
    }

    /*
     * approve talent allowance details
     */

    public function approveTalentAllowance($date, $talent) {
        $updateAllowance = array(
            'send_approval' => 0,
            'approved' => 1
        );
        $this->db->where('date', $date);
        $this->db->where('send_approval', 1);
        $this->db->where('talent', $talent);
        $queryResults = $this->db->update('talent_allowance', $updateAllowance);
        if (!$queryResults) {
            echo 0;
        } else {
            echo 1;
        }
    }

    /*
     * project dropdown options loading
     */

    public function projectDropDown() {
        $project = $this->input->post('search');
        $projectDetails = $this->TimePredictionModel->getAllProjects($project);
        foreach ($projectDetails as $details) {
            $json[] = ['id' => $details['id'], 'text' => $details['project_code']];
        }
        echo json_encode($json);
    }

    /*
     * task dropdown options loading
     */

    public function taskDropDown() {
        $task = $this->input->post('search');
        $project = $this->input->post('project');
        $projectId = json_decode($project);
        $taskDetails = $this->TaskManagementModel->getAllTaskDropDown($task, $projectId);
        foreach ($taskDetails as $details) {
            $json[] = ['id' => $details['id'], 'text' => $details['task_code']];
        }
        echo json_encode($json);
    }

    /*
     * talent dropdown options loading
     */

    public function talentDropDown() {
        $search = $this->input->post('search');
        $session_data = $this->session->userdata('logged_in');
        $is_management = $session_data['is_management'];
        $talentId = $session_data['talentId'];
        $is_pmo = $session_data['is_pmo'];
        $talentsDetails = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo, FALSE, $search);
        foreach ($talentsDetails as $details) {
            $json[] = ['id' => $details['id'], 'text' => $details['name']];
        }
        echo json_encode($json);
    }
    /*
     * pending Sheet excel download action
     */
    public function pendingSheetExcelDownload() {
        $session_data = $this->session->userdata('logged_in');
        $is_management = $session_data['is_management'];
        $is_pmo = $session_data['is_pmo'];
        $talent = $this->input->get('talent');
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $talentId = $session_data['talentId'];
        $fromdate = $year . "-" . $month . '-01';
        $todate = date('Y-m-t', strtotime($fromdate));
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
        if (strtotime($todate) < strtotime(date('Y-m-d'))) {
            $datesBetweenDates[] = $todate;
        }


        if ($talent) {
            $talentArray[$talent] = $this->TimePredictionModel->getTalentNameById($talent);
            $PendingSubmitReport = $this->TimePredictionModel->pendingTimesheetToSubmit($talentArray, $datesBetweenDates);
        } else {
            $talents = $this->ProjectMappingModel->getAllTalents($talentId, $is_management, $is_pmo);
            $talent_id_Array = array();
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talent_id_Array[$talent['id']] = $talent['name'];
            }
            $PendingSubmitReport = $this->TimePredictionModel->pendingTimesheetToSubmit($talent_id_Array, $datesBetweenDates);
        }
        $heading = array(
            0 => 'SL. No.',
            1 => 'Talent',
            2 => 'Date',
            3 => 'Timesheet Manager',
            4 => 'Apply Status',
        );
        $slno = 0;
        foreach ($PendingSubmitReport as $data) {
            $approval_status = $this->TimePredictionModel->ApprovalStatus($data['talent_id'], $data['attendance_date']);

            if (count($approval_status) == 0) {
                $content[$slno] = array(
                    0 => $slno + 1,
                    1 => $data['name'],
                    2 => date('d/m/Y', strtotime($data['date'])),
                    3 => $data['reporting_manager_name'],
                    4 => $data['apply_status'],
                );
                $slno++;
            }
        }
        $data['heading'] = $heading;
        $data['tableName'] = 'Pending Sheets- Team Report';
        $data['content'] = $content;
        $data['slno'] = $slno;
        $this->load->view('excel/excel', $data);
    }

}
