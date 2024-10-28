<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TimesheetRemapping extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('projects', '', TRUE);
        $this->load->model('TaskManagementModel', '', TRUE);
        $this->load->model('ProjectMappingModel', '', TRUE);
        $this->load->model('TimesheetRemappingModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            //dropdown for previous project
            $projectDropdown = array("" => "[SELECT Previous Project]");
            $projects = $this->projects->getProjects();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }
            $selectedProject = $this->input->post('previous_project');
            $talents = $this->ProjectMappingModel->getTalents('', TRUE, TRUE);
            //dropdown for task
            $taskDropdown = array("" => "[SELECT Task]");
            $selectedTask = $this->input->post('task_code');
            //dropdown for actual project
            $talentsDropdown = array("" => "[ALL]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $actualprojectDropdown = array("" => "[SELECT Actual Project]");
            foreach ($projects as $project) {
                $id = $project['id'];
                $actualprojectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }
            $selectedactualProject = $this->input->post('actual_project');
            //helper for form and date
            $this->load->helper(array('form'));
            //table data
            $tabledata = $this->TimesheetRemappingModel->gettimesheetRemaps();

            $data['talentId'] = $session_data['talentId'];
            $data['selectedProject'] = $selectedProject;
            $data['projectDropdown'] = $projectDropdown;
            $data['selectedactualProject'] = $selectedactualProject;
            $data['actualprojectDropdown'] = $actualprojectDropdown;
            $data['selectedTask'] = $selectedTask;
            $data['taskDropdown'] = $taskDropdown;
            $data['tabledata'] = $tabledata;
            $data['talentsDropdown'] = $talentsDropdown;

            $this->load->view('timesheetRemapping/timesheetRemap', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function databaseoperations() {
        if ($this->session->userdata('logged_in')) {

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $createdBy = $session_data['talentId'];
            $createdAt = date('Y-m-d');
            //getting values from ajax;
            $selectedProject = $this->input->post('previous_project');
            $talent = $this->input->post('talent');
            $selectedactualProject = $this->input->post('actual_project');
            $selectedTask = $this->input->post('task_code');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');
            $targetTask = $this->input->post('target_task_code');
            if ($fromDate) {
                $date = DateTime::createFromFormat('d/m/Y', $fromDate);
                $fromDate = $date->format('Y-m-d');
            } else {
                $fromDate = '';
            }
            if ($toDate) {
                $date = DateTime::createFromFormat('d/m/Y', $toDate);
                $toDate = $date->format('Y-m-d');
            } else {
                $toDate = '';
            }
            // Do insert operation here
            $insertUserData = array(
                'previous_project' => $selectedProject,
                'actual_project' => $selectedactualProject,
                'task_code' => $selectedTask,
                'talents' => $talent,
                'from_date' => $fromDate,
                'created_by' => $createdBy,
                'created_at' => $createdAt,
                'to_date' => $toDate,
                'target_task_code' => $targetTask,
            );
            $queryResult = $this->db->insert('timesheet_project_remapping', $insertUserData);
            if ($queryResult) {
                //to update time_prediction table
                $queryResult = $this->TimesheetRemappingModel->updateTimePrediction($selectedactualProject, $selectedProject, $selectedTask, $fromDate, $toDate, $talent, $targetTask);
                if ($queryResult) {
                    //success
                    $this->session->set_flashdata('flashSuccess', 'Timesheet remapped successfully');
                    redirect('timesheetRemapping', 'refresh');
                } else {
                    //failed to update time_prediction table
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            } else {
                //failure
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

}
