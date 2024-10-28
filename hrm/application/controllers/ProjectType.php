<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectType extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('ProjectTypeModel', '', TRUE);
        $this->load->model('ScheduleModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $data['projectTypes'] = $this->ProjectTypeModel->getAllProjectTypes();
            $this->load->view('ProjectType/View', $data);
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
            $from = [];
            $to = [];
            $schedules = $this->ScheduleModel->getAllSchedules();
            $scheduleDropdown = array("" => "[Select]");
            foreach ($schedules as $type) {
                $id = $type['id'];
                $from = $type['from'];
                $to = $type['to'];
                $scheduleDropdown[$id] = $type['schedule'];
            }
            $data['selectedSchedule'] = '';
            $data['schedules'] = $scheduleDropdown;
            $data['from'] = $from;
            $data['to'] = $to;

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->helper('date');

            $this->load->library('form_validation');
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'project_type',
                    'label' => 'Project Type',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'targeted_value',
                    'label' => 'Target Value',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            $this->form_validation->set_rules('target_date', 'Date of target accomplishment', 'trim|required|xss_clean');
            $this->form_validation->set_rules('valid_date', 'Valid Till', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {
                $error_flag = 0;
                //creating notification for reporting manager
                $projectType = $this->input->post('project_type');
                $targetValue = $this->input->post('targeted_value');
                $targetDate = DateTime::createFromFormat('d/m/Y', $this->input->post('target_date'));
                $targetDateOfProject = $targetDate->format('Y-m-d');
                $validTill = DateTime::createFromFormat('d/m/Y', $this->input->post('valid_date'));
                $validDateOfProject = $validTill->format('Y-m-d');
                $selectedSchedule = $this->input->post('schedule_id');

                $insertData = array(
                    'project_type' => $projectType,
                    'targeted_value' => $targetValue,
                    'target_date' => $targetDateOfProject,
                    'schedule_id' => $selectedSchedule,
                    'valid_date' => $validDateOfProject
                );
                //inserting
                $queryResult = $this->db->insert('project_type', $insertData);
                if (!$queryResult) {
                    $error_flag = 1;
                }
                if ($error_flag == 0) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Task Created successfully');
                    redirect('ProjectType', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('ProjectType/Creation', $data);
                }
            }
            $this->load->view('ProjectType/Creation', $data);
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
        $schedules = $this->ScheduleModel->getAllSchedules();
        $scheduleDropdown = array("" => "[Select]");
        foreach ($schedules as $type) {
            $scheduleid = $type['id'];
            $scheduleDropdown[$scheduleid] = $type['schedule'];
        }
        $editData['projectTypeDetails'] = $this->ProjectTypeModel->getProjectTypeData($id);
        $editData['selectedSchedule'] = $editData['projectTypeDetails'][0]['schedule_id'];
        $editData['schedules'] = $scheduleDropdown;

        if ($this->input->post()) {
            $this->db->trans_begin();
            $this->load->library('form_validation');

            //for form validayion, setting rules
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'project_type',
                    'label' => 'Project Type',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'targeted_value',
                    'label' => 'Target Value',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            $this->form_validation->set_rules('target_date', 'Date of target accomplishment', 'trim|required|xss_clean');
            $this->form_validation->set_rules('valid_date', 'Valid Till', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {
                $projectType = $this->input->post('project_type');
                $targetValue = $this->input->post('targeted_value');
                $targetDate = $this->input->post('target_date');
                $validTill = $this->input->post('valid_date');
                $selectedSchedule = $this->input->post('schedule_id');

                $updateSpecificData = array(
                    'project_type' => $projectType,
                    'schedule_id' => $selectedSchedule,
                    'targeted_value' => $targetValue,
                    'target_date' => $targetDate,
                    'valid_date' => $validTill
                );
                $this->db->where('id', $id);
                $queryResult = $this->db->update('project_type', $updateSpecificData);
                if ($queryResult) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Updated successfully');
                    redirect('ProjectType', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('ProjectType/Edit', $editData);
                }
            }
        }
        $this->load->view('ProjectType/Edit', $editData);
    }

}
