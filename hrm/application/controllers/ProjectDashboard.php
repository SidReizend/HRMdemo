<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectDashboard extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('PipelineModel', '', TRUE);
        $this->load->model('ProjectTypeModel', '', TRUE);
        $this->load->model('ScheduleModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $totalPercentage = 0;
            $sId = 0;
            $scheduleType = [];
            $totalTarget = 0;
            $scheduleId = 0;
            $totalApproved = 0;
            $getSchedule = $this->input->get('schedule');
            if (!empty($getSchedule)) {
                $scheduleId = $this->input->get('schedule');
            }
            $schedules = $this->ScheduleModel->getAllSchedules();
            if (!empty($schedules)) {
                $sId = $schedules[0]['id'];
            }
            if ($scheduleId != 0) {
                $sId = $scheduleId;
            }
            $totalTarget = $this->ProjectTypeModel->getProjectTotalTarget($sId);
            $totalApproved = $this->ProjectTypeModel->getProjectTotalApproved($sId);
            foreach ($schedules as $val) {
                $scid = $val['id'];
                $scheduleType[$scid] = $val['schedule'];
            }
            $projectTypeSum = $this->ProjectTypeModel->getProjectDashboard($sId);
            if ($projectTypeSum) {
                $sumTarget = $projectTypeSum[0]['totalTarget'];
                $sumApproved = $projectTypeSum[0]['totalApproved'];
                $totalPercentage = ($sumApproved / $sumTarget ) * 100;
            }
            if ($totalTarget) {
                $target = $totalTarget[0]['totalTarget'];
            }
            if ($totalApproved) {
                $approved = $totalApproved[0]['totalApproved'];
            }
            $data['schedules'] = $scheduleType;
            $data['selectdSchedule'] = $scheduleId;
            $data['projectTypes'] = $this->ProjectTypeModel->getProjectDashboardData($sId);
            $data['totalTarget'] = number_format((float) $target, 2, '.', '');
            $data['totalApproved'] = number_format((float) $approved, 2, '.', '');
            $data['totalPercentage'] = number_format((float) $totalPercentage, 2, '.', '');
            $this->load->view('ProjectDashboard/Dashboard', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function OrganizationReportPiechart() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $scheduleId = $this->input->get('schedule');
            $projectType = [];
            $response = [];
            $totalTarget = 0;
            $totalApproved = 0;
            $totalPercentage = [];
            $totalAchievedPercentage = 0;
            $projectTypeSum = $this->ProjectTypeModel->getProjectDashboard();
            if ($projectTypeSum) {
                $totalTarget = $projectTypeSum[0]['totalTarget'];
                $totalApproved = $projectTypeSum[0]['totalApproved'];
            }
            $scheduleProjectTypeSum = $this->ProjectTypeModel->getProjectDashboard($scheduleId);
            if ($scheduleProjectTypeSum) {
                $sumTarget = $scheduleProjectTypeSum[0]['totalTarget'];
                $sumApproved = $scheduleProjectTypeSum[0]['totalApproved'];
                $totalPercentage = ($sumApproved / $sumTarget ) * 100;
            }
            $data['projectTypes'] = $this->ProjectTypeModel->getProjectDashboardData($scheduleId);
            foreach ($data['projectTypes'] as $val) {
                $projectType[] = $val['project_type'];
            }
            if ($totalTarget > 0) {
                $totalAchievedPercentage = ($totalApproved / $totalTarget ) * 100;
            }
            $remainingPercentage = 100 - $totalAchievedPercentage;
            $percentageOfAccomplishment = array(round($totalAchievedPercentage), round($remainingPercentage));
            $response['totalPercentage'] = $percentageOfAccomplishment;
            $response['totalSchedulePercentage'] = number_format((float) $totalPercentage, 2, '.', '');
            $remainingPercentage = 100 - $response['totalSchedulePercentage'];
            $percentageOfAccomplishment = array($response['totalSchedulePercentage'], $remainingPercentage);
            $response['remainingPercentage'] = $percentageOfAccomplishment;
            $response['labels'] = $projectType;
            echo(json_encode($response));
            exit;
        } else {
            echo"Error Occured";
            exit;
        }
    }

    public function ProjectTypePiechart() {
        if ($this->session->userdata('logged_in')) {
            $scheduleId = $this->input->get('schedule');
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $projectType = [];
            $response = [];
            $response['projectTypes'] = $this->ProjectTypeModel->getProjectDashboardData($scheduleId);
            echo(json_encode($response));
            exit;
        } else {
            echo"Error Occured";
            exit;
        }
    }

    public function viewProposed() {
        //using url for get id
        $this->load->helper('url');
        $id = $this->uri->segment(3);
        $result = $this->ProjectTypeModel->getProposedById($id);
        $data['allProjects'] = $result;
        $this->load->view('ProjectDashboard/viewProposed', $data);
    }

    public function viewApproved() {
        //using url for get id
        $this->load->helper('url');
        $id = $this->uri->segment(3);
        $result = $this->ProjectTypeModel->getApprovedById($id);
        $data['allProjects'] = $result;
        $this->load->view('ProjectDashboard/viewApproved', $data);
    }

}
