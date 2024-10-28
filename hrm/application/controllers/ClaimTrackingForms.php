<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ClaimTrackingForms extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('claim', '', TRUE);
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
            $settlementDropdown = array(
                '' => '[SEL. Settlement]',
                'Account' => 'Account',
                'Salary' => 'Salary'
            );
            $statusDropdown = array(
                '0' => 'Planned',
                '1' => 'Settled'
            );
            $categorys = $this->claim->getClaimCategory();
            $categoryArray = array();
            foreach ($categorys as $category) {
                $id = $category['id'];
                $categoryArray[$id]['value'] = $category['value'];
                $categoryArray[$id]['planned'] = '0';
                $categoryArray[$id]['settled'] = '0';
            }
            $data['isResigned'] = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');
            $talents = $this->talent->getTalentsAllByResignStatus($data['isResigned']);
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $statusSearchDropdown = array(
                '' => '[SEL. Status]',
                '1' => 'Planned',
                '2' => 'Settled'
            );
            $currentMonth = date('m');
            if ($currentMonth <= 3) {
                $SelectBoxyear = date('Y') - 1;
            } else {
                $SelectBoxyear = date('Y');
            }
            $data['selectedYear'] = $this->input->post('year') == '' ? $SelectBoxyear : $this->input->post('year');
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['categoryArray'] = $categoryArray;
            $data['selectedSettlement'] = $this->input->post('settlement');
            $data['selectedStatusSearch'] = $this->input->post('statusSearch');
            $data['selectedTalent'] = $this->input->post('talent');
            $data['statusSearchDropdown'] = $statusSearchDropdown;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['statusDropdown'] = $statusDropdown;
            $data['settlementDropdown'] = $settlementDropdown;
            $data['years'] = $yearArray;
            $data['claims'] = $this->claim->getClaimsByResignedStatus($data['selectedSettlement'], $data['selectedYear'], $data['isResigned'], $data['selectedStatusSearch'], $data['selectedTalent']);

            $this->load->view('claimManagement/claims', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewClaim() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $talents = $this->talent->getTalents();
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $settlementDropdown = array(
                'Account' => 'Account',
                'Salary' => 'Salary'
            );
            $statusDropdown = array(
                '0' => 'Planned',
                '1' => 'Settled'
            );
            $categoryDropdown = array("" => "[SELECT Category]");
            $categorys = $this->claim->getClaimCategory();
            foreach ($categorys as $category) {
                $id = $category['id'];
                $categoryDropdown[$id] = $category['value'];
            }
            $projectDropdown = array("" => "[SELECT Project]");
            $projects = $this->projects->getProjects();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('talent', 'Talent', 'trim|required|xss_clean');
            $selectedTalent = $this->input->post('talent');
            $claimId = $this->input->post('claimId');
            $selectedSettlement = $this->input->post('settlement');
            $selectedProject = $this->input->post('project');
            $selectedCategory = $this->input->post('category');
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
                    'claim_id' => $claimId,
                    'talent' => $selectedTalent,
                    'settlement' => $selectedSettlement,
                    'project' => $selectedProject,
                    'amount' => $amount,
                    'date_of_approval' => $dateOfApproval,
                    'approved_by' => $selectedApprovedBy,
                    'created_by' => $createdBy,
                    'status' => $selectedStatus,
                    'task_code' => $subProject,
                    'note' => $note,
                    'category' => $selectedCategory,
                );
                $queryResult = $this->db->insert('claims', $insertUserData);
                if ($queryResult) {
                    //if success
                    $this->session->set_flashdata('flashSuccess', 'New Claim is added successfully');
                    redirect('ClaimTrackingForms', 'refresh');
                } else {
                    //failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $data['projectDropdown'] = $projectDropdown;
            $data['categoryDropdown'] = $categoryDropdown;
            $data['selectedCategory'] = $selectedCategory;
            $data['talentId'] = $session_data['talentId'];
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedTalent'] = $selectedTalent;
            $data['settlementDropdown'] = $settlementDropdown;
            $data['selectedSettlement'] = $selectedSettlement;
            $data['selectedProject'] = $selectedProject;
            $data['selectedApprovedBy'] = $selectedApprovedBy;
            $data['selectedStatus'] = $selectedStatus;
            $data['statusDropdown'] = $statusDropdown;
            $data['projectDropdown'] = $projectDropdown;
            $this->load->view('claimManagement/addNewClaim', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editClaim() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $this->load->helper('url');
            $claimTableId = $this->uri->segment(3);
            $claim = $this->claim->getClaimById($claimTableId);
            $talents = $this->talent->getTalentsAll();
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $settlementDropdown = array(
                'Account' => 'Account',
                'Salary' => 'Salary'
            );
            $statusDropdown = array(
                '0' => 'Planned',
                '1' => 'Settled'
            );
            $projectDropdown = array("" => "[SELECT Project]");
            $projects = $this->projects->getProjects();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }
            $categoryDropdown = array("" => "[SELECT Category]");
            $categorys = $this->claim->getClaimCategory();
            foreach ($categorys as $category) {
                $id = $category['id'];
                $categoryDropdown[$id] = $category['value'];
            }
            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('talent', 'Talent', 'trim|required|xss_clean');
            if ($this->input->post()) {
                $selectedTalent = $this->input->post('talent');
                $claimId = $this->input->post('claimId');
                $selectedSettlement = $this->input->post('settlement');
                $selectedProject = $this->input->post('project');
                $selectedCategory = $this->input->post('category');
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
                        'claim_id' => $claimId,
                        'talent' => $selectedTalent,
                        'settlement' => $selectedSettlement,
                        'project' => $selectedProject,
                        'amount' => $amount,
                        'date_of_approval' => $dateOfApproval,
                        'approved_by' => $selectedApprovedBy,
                        'status' => $selectedStatus,
                        'task_code' => $selectedSubProject,
                        'note' => $note,
                        'category' => $selectedCategory,
                    );
                    $this->db->where('id', $claimTableId);
                    $queryResult = $this->db->update('claims', $updateData);
                    if ($queryResult) {
                        //if success
                        $this->session->set_flashdata('flashSuccess', 'Claim updated successfully');
                        redirect('ClaimTrackingForms', 'refresh');
                    } else {
                        //failure
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                }
            } else {
                $claimId = $claim->claim_id;
                $selectedTalent = $claim->talent;
                $selectedSettlement = $claim->settlement;
                $selectedProject = $claim->project;
                $selectedCategory = $claim->category;
                $selectedApprovedBy = $claim->approved_by;
                $selectedStatus = $claim->status;
                $selectedSubProject = $claim->task_code;
                $amount = $claim->amount;
                $note = $claim->note;
                $dateOfApproval = date('d/m/Y', strtotime($claim->date_of_approval));
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
            $data['claimId'] = $claimId;
            $data['subProjectDropdown'] = $subProjectDropdown;
            $data['categoryDropdown'] = $categoryDropdown;
            $data['selectedCategory'] = $selectedCategory;
            $data['projectDropdown'] = $projectDropdown;
            $data['talentsDropdown'] = $talentsDropdown;
            $data['selectedSubProject'] = $selectedSubProject;
            $data['subProjects'] = $subProjects;
            $data['selectedTalent'] = $selectedTalent;
            $data['settlementDropdown'] = $settlementDropdown;
            $data['selectedSettlement'] = $selectedSettlement;
            $data['selectedProject'] = $selectedProject;
            $data['amount'] = $amount;
            $data['dateOfApproval'] = $dateOfApproval;
            $data['selectedApprovedBy'] = $selectedApprovedBy;
            $data['selectedStatus'] = $selectedStatus;
            $data['statusDropdown'] = $statusDropdown;
            $data['projectDropdown'] = $projectDropdown;
            $data['note'] = $note;
            $this->load->view('claimManagement/editClaim', $data);
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
        $this->db->update('claims', $updateData);
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
