<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('ScheduleModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $data['schedules'] = $this->ScheduleModel->getAllSchedules();
            $this->load->view('Schedule/View', $data);
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
            //helper for form and date
            $this->load->helper(array('form'));

            $this->load->library('form_validation');
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'schedule',
                    'label' => 'Schedule',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'comments',
                    'label' => 'Comments',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            $this->form_validation->set_rules('from', 'From Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('to', 'To Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {
                $error_flag = 0;
                //creating notification for reporting manager
                $schedule = $this->input->post('schedule');
                $fromDate = DateTime::createFromFormat('d/m/Y', $this->input->post('from'));
                $fromDateOfProject = $fromDate->format('Y-m-d');
                $toDate = DateTime::createFromFormat('d/m/Y', $this->input->post('to'));
                $toDateOfProject = $toDate->format('Y-m-d');
                $comments = $this->input->post('comments');
                $insertData = array(
                    'schedule' => $schedule,
                    'from' => $fromDateOfProject,
                    'to' => $toDateOfProject,
                    'comments' => $comments,
                );
                //inserting
                $queryResult = $this->db->insert('schedule', $insertData);
                if (!$queryResult) {
                    $error_flag = 1;
                }
                if ($error_flag == 0) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Schedule Created successfully');
                    redirect('Schedule', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('Schedule/Creation', $data);
                }
            }
            $this->load->view('Schedule/Creation', $data);
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

        $editData['scheduleDetails'] = $this->ScheduleModel->getScheduleData($id);

        if ($this->input->post()) {
            $this->db->trans_begin();
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'schedule',
                    'label' => 'Schedule',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'comments',
                    'label' => 'Comments',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            $this->form_validation->set_rules('from', 'From Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('to', 'To Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {

                $schedule = $this->input->post('schedule');
                $fromDateOfProject = $this->input->post('from');
                $toDateOfProject = $this->input->post('to');
                $comments = $this->input->post('comments');

                $updateSpecificData = array(
                    'schedule' => $schedule,
                    'from' => $fromDateOfProject,
                    'to' => $toDateOfProject,
                    'comments' => $comments,
                );
                $this->db->where('id', $id);
                $queryResult = $this->db->update('schedule', $updateSpecificData);
                if ($queryResult) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Updated successfully');
                    redirect('Schedule', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('Schedule/Edit', $editData);
                }
            }
        }
        $this->load->view('Schedule/Edit', $editData);
    }

}
