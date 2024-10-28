<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Advance extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('advanceModel', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('projects', '', TRUE);
        $this->load->model('timePredictionModel', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $statusDropdown = array(
                '0' => 'Advance',
                '1' => 'Reconcile'
            );
            $data['isResigned'] = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
            $talents = $this->talent->getTalentsAllByResignStatus($data['isResigned']);
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $statusSearchDropdown = array(
                '' => '[SEL. Status]',
                '1' => 'Advance',
                '2' => 'Reconcile'
            );
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            if ($this->input->post()) {
                $data['selectedYear'] = $this->input->post('year');
            } else {
                $data['selectedYear'] = date('Y');
            }
            $data['selectedStatusSearch'] = $this->input->post('statusSearch');
            $data['selectedTalent'] = $this->input->post('talent');
            $data['statusSearchDropdown'] = $statusSearchDropdown;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['statusDropdown'] = $statusDropdown;
            $data['years'] = $yearArray;
            $data['advances'] = $this->advanceModel->getAdvanceByResignedStatus($data['selectedYear'], $data['isResigned'],$data['selectedStatusSearch'],$data['selectedTalent']);

            $this->load->view('advanceManagement/advance', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewAdvance() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $talents = $this->talent->getTalents();
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $statusDropdown = array(
                '0' => 'Advance',
                '1' => 'Reconcile'
            );
            $projectDropdown = array("" => "[SELECT Project]");
            $projects = $this->projects->getProjects();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }

            $data['projectDropdown'] = $projectDropdown;
            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('talent', 'Talent', 'trim|required|xss_clean');
            $selectedTalent = $this->input->post('talent');
            $selectedProject = $this->input->post('project');
            $selectedApprovedBy = $this->input->post('approvedBy');
            $selectedStatus = $this->input->post('status');
            $subProject = $this->input->post('subProject');
            $note = $this->input->post('note');
            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                $amount = $this->input->post('amount');
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfApproval'));
                $dateOfApproval = $date->format('Y-m-d');
                $createdBy = $session_data['talentId'];
                //die();
                // Do insert operation here
                $insertUserData = array(
                    'talent' => $selectedTalent,
                    'project' => $selectedProject,
                    'amount' => $amount,
                    'date_of_approval' => $dateOfApproval,
                    'approved_by' => $selectedApprovedBy,
                    'created_by' => $createdBy,
                    'status' => $selectedStatus,
                    'task_code' => $subProject,
                    'note' => $note,
                );
                $queryResult = $this->db->insert('advance', $insertUserData);
                if ($queryResult) {
                    //if success
                    $this->session->set_flashdata('flashSuccess', 'New Advance is added successfully');
                    redirect('Advance', 'refresh');
                } else {
                    //failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $data['talentId'] = $session_data['talentId'];
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['selectedProject'] = $selectedProject;
            $data['selectedApprovedBy'] = $selectedApprovedBy;
            $data['selectedStatus'] = $selectedStatus;
            $data['statusDropdown'] = $statusDropdown;
            $data['projectDropdown'] = $projectDropdown;
            $this->load->view('advanceManagement/addNewAdvance', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editAdvance() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $this->load->helper('url');
            $advanceId = $this->uri->segment(3);
            $advance = $this->advanceModel->getAdvanceById($advanceId);
            $talents = $this->talent->getTalentsAll();
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $statusDropdown = array(
                '0' => 'Advance',
                '1' => 'Reconcile'
            );
            $projectDropdown = array("" => "[SELECT Project]");
            $projects = $this->projects->getProjects();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }
            $data['projectDropdown'] = $projectDropdown;
            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('talent', 'Talent', 'trim|required|xss_clean');
            if ($this->input->post()) {
                $selectedTalent = $this->input->post('talent');
                $selectedProject = $this->input->post('project');
                $selectedApprovedBy = $this->input->post('approvedBy');
                $selectedStatus = $this->input->post('status');
                $selectedSubProject = $this->input->post('subProject');
                $note = $this->input->post('note');
                if ($this->form_validation->run() == TRUE) {
                    //Validation Success.  
                    $amount = $this->input->post('amount');
                    $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfApproval'));
                    $dateOfApproval = $date->format('Y-m-d');

                    // Do insert operation here
                    $updateData = array(
                        'talent' => $selectedTalent,
                        'project' => $selectedProject,
                        'amount' => $amount,
                        'date_of_approval' => $dateOfApproval,
                        'approved_by' => $selectedApprovedBy,
                        'status' => $selectedStatus,
                        'task_code' => $selectedSubProject,
                        'note' => $note,
                    );
                    $this->db->where('id', $advanceId);
                    $queryResult = $this->db->update('advance', $updateData);
                    if ($queryResult) {
                        //if success
                        $this->session->set_flashdata('flashSuccess', 'Advance updated successfully');
                        redirect('Advance', 'refresh');
                    } else {
                        //failure
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                }
            } else {
                $selectedTalent = $advance->talent;
                $selectedProject = $advance->project;
                $selectedApprovedBy = $advance->approved_by;
                $selectedStatus = $advance->status;
                $selectedSubProject = $advance->task_code;
                $amount = $advance->amount;
                $note = $advance->note;
                $dateOfApproval = date('d/m/Y', strtotime($advance->date_of_approval));
            }
            $subProjects = array();
            $getSubProjectStatus = $this->projects->getSubProjectStatus($selectedProject);
            if ($getSubProjectStatus) {
                $project_details = $this->projects->getProjectById($selectedProject);
                $assigned_tasks = explode(",", $project_details->assigned_tasks);
                $subProjects = $this->timePredictionModel->getProjectTask($assigned_tasks);
            }
            $subProjectDropdown = array("" => "[SELECT sub project]");
            foreach ($subProjects as $project) {
                $id = $project['id'];
                $subProjectDropdown[$id] = $project['task_code'];
            }
            $data['subProjectDropdown'] = $subProjectDropdown;

            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedSubProject'] = $selectedSubProject;
            $data['subProjects'] = $subProjects;
            $data['selectedTalent'] = $selectedTalent;
            $data['selectedProject'] = $selectedProject;
            $data['amount'] = $amount;
            $data['dateOfApproval'] = $dateOfApproval;
            $data['selectedApprovedBy'] = $selectedApprovedBy;
            $data['selectedStatus'] = $selectedStatus;
            $data['statusDropdown'] = $statusDropdown;
            $data['projectDropdown'] = $projectDropdown;
            $data['note'] = $note;
            $this->load->view('advanceManagement/editAdvance', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function statusUpdate() {
        //using url for get id
        $this->load->helper('url');
        $id = $this->input->get('id');
        $status = $this->input->get('status');
        $updateData = array(
            'status' => $status
        );
        $this->db->where('id', $id);
        $this->db->update('advances', $updateData);
        echo 1;
    }

    public function subProjectMapping() {
        //using url for get project
        $project = $this->input->get('project');
        $this->load->helper('form');

        //get the sub project exist status

        $getSubProjectStatus = $this->projects->getSubProjectStatus($project);
        if ($getSubProjectStatus) {
            $project_details = $this->projects->getProjectById($project);
            $assigned_tasks = explode(",", $project_details->assigned_tasks);
            $subProjects = $this->timePredictionModel->getProjectTask($assigned_tasks);
            //var_dump($subProjects);
            $subProjectDropdown = array("" => "[SELECT sub project]");
            foreach ($subProjects as $project) {
                $id = $project['id'];
                $subProjectDropdown[$id] = $project['task_code'];
            }
            $data['subProjectDropdown'] = $subProjectDropdown;
            $this->load->view('claimManagement/subProjects', $data);
        }
        //if sub project exists then shows the sub projects else not
    }

}
