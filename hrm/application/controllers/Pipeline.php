<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pipeline extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('PipelineModel', '', TRUE);
        $this->load->model('ProjectTypeModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $data['allProjects'] = $this->PipelineModel->getAllProjects();
            $this->load->view('Pipeline/View', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function Add() {
        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $proposedValue = 0;
            $projectTypes = $this->ProjectTypeModel->getAllProjectTypes();
            $projectTypeDropdown = array("" => "[Select]");
            foreach ($projectTypes as $type) {
                $id = $type['id'];
                $projectTypeDropdown[$id] = $type['project_type'];
            }

            //helper for form and date
            $this->load->helper(array('form'));

            $this->load->library('form_validation');
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'ongoing_projects',
                    'label' => 'Ongoing Projects',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'value',
                    'label' => 'Value',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'comments',
                    'label' => 'Comments',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            //stage option
            $stageArray = array(
                'Proposed' => 'Proposed',
                'Not-Proposed' => 'Not-Proposed',
                'Approved' => 'Approved',
            );
            $data['stages'] = $stageArray;
            $data['projectTypes'] = $projectTypeDropdown;
            $data['selectedStage'] = '';
            $data['selectedProjectType'] = '';
            $this->form_validation->set_rules('stage', 'Stage', 'trim|required|xss_clean');
            $this->form_validation->set_rules('project_type', 'Project Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {
                $error_flag = 0;
                //creating notification for reporting manager
                $ongoingProject = $this->input->post('ongoing_projects');
                $stage = $this->input->post('stage');
                $comments = $this->input->post('comments');
                $selectedStage = $this->input->post('stage');
                $selectedProjectType = $this->input->post('project_type');
                if ($stage == 'Approved') {
                    $approvedValue = $this->input->post('value');
                    $proposedValue = 0;
                } else {
                    $proposedValue = $this->input->post('value');
                    $approvedValue = 0;
                }
                $insertData = array(
                    'ongoing_projects' => $ongoingProject,
                    'approved_value' => $approvedValue,
                    'value' => $proposedValue,
                    'stage' => $stage,
                    'comments' => $comments,
                    'project_type' => $selectedProjectType
                );
                //inserting
                $queryResult = $this->db->insert('pipe_line', $insertData);
                if (!$queryResult) {
                    $error_flag = 1;
                }
                if ($error_flag == 0) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Task Created successfully');
                    redirect('Pipeline', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('Pipeline/Creation', $data);
                }
            }
            $this->load->view('Pipeline/Creation', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function Edit() {
        $session_data = $this->session->userdata('logged_in');
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $id = $this->uri->segment(4);
        $editData['id'] = $id;

        $editData['projectDetails'] = $this->PipelineModel->getProjectData($id);
        $proposedValue = $editData['projectDetails'][0]['value'];
        $projectTypes = $this->ProjectTypeModel->getAllProjectTypes();
        $projectTypeDropdown = array("" => "[Select]");
        foreach ($projectTypes as $type) {
            $projectTypeDropdown[$type['id']] = $type['project_type'];
        }
        $editData['selectedProjectType'] = $editData['projectDetails'][0]['project_id'];
        $editData['selectedStage'] = $editData['projectDetails'][0]['stage'];
        //stage option
        $stageArray = array(
            'Proposed' => 'Proposed',
            'Not-Proposed' => 'Not-Proposed',
            'Approved' => 'Approved',
        );
        $editData['stages'] = $stageArray;
        $editData['projectTypes'] = $projectTypeDropdown;

        if ($this->input->post()) {
            $this->db->trans_begin();
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'ongoing_projects',
                    'label' => 'Ongoing Projects',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'value',
                    'label' => 'Value',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'comments',
                    'label' => 'Comments',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            //stage option
            $stageArray = array(
                'Proposed' => 'Proposed',
                'Not-Proposed' => 'Not-Proposed',
                'Approved' => 'Approved',
            );
            $data['stages'] = $stageArray;
            $data['projectTypes'] = $projectTypeDropdown;
            $data['selectedStage'] = '';
            $data['selectedProjectType'] = '';
            $this->form_validation->set_rules('stage', 'Stage', 'trim|required|xss_clean');
            $this->form_validation->set_rules('project_type', 'Project Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {

                $ongoingProject = $this->input->post('ongoing_projects');
                $value = $this->input->post('value');
                $stage = $this->input->post('stage');
                $comments = $this->input->post('comments');
                $selectedStage = $this->input->post('stage');
                $selectedProjectType = $this->input->post('project_type');
                if ($stage == 'Approved') {
                    $approvedValue = $this->input->post('value');
                    $proposdValue = $proposedValue;
                } else {
                    $proposedValue = $value;
                    $approvedValue = 0;
                }
                $updateSpecificData = array(
                    'ongoing_projects' => $ongoingProject,
                    'stage' => $stage,
                    'approved_value' => $approvedValue,
                    'value' => $proposedValue,
                    'comments' => $comments,
                    'project_type' => $selectedProjectType
                );
                $this->db->where('id', $id);
                $queryResult = $this->db->update('pipe_line', $updateSpecificData);
                if ($queryResult) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Updated successfully');
                    redirect('Pipeline', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('Pipeline/Edit', $editData);
                }
            }
        }
        $this->load->view('Pipeline/Edit', $editData);
    }

}
