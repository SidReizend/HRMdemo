<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ExpenseHeads extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('projects', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['headDetails'] = $this->projects->getAllHeads();

            $this->load->view('headManagement/heads', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewHeads() {
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];

        //helper for form and date
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        //for form validayion, setting rules
        $this->form_validation->set_rules('headName', 'Name Of Head', 'trim|required|xss_clean');

        if ($this->form_validation->run() == TRUE) {
            //Validation Success.  
            $headName = $this->input->post('headName');
            $headCode = $this->input->post('headCode');


            // Do insert operation here
            $insertUserData = array(
                'project_code' => $headCode,
                'name' => $headName,
                'is_head' => 1
            );
            $queryResult = $this->db->insert('project', $insertUserData);
            if ($queryResult) {
                //if success
                $this->session->set_flashdata('flashSuccess', 'New Head is added successfully');
                redirect('expenseHeads', 'refresh');
            } else {
                //failure
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        }

        $this->load->view('headManagement/addNewHead', $data);
    }

    public function editHead() {
        $this->load->helper('date');
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $headId = $this->uri->segment(3);
        $editData['headId'] = $headId;

        if ($this->input->post()) {

            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('headCode', 'Name Of Head', 'trim|required|xss_clean');

            $headName = $this->input->post('headName');
            $headCode = $this->input->post('headCode');


            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                // Do update operation here
                $updateData = array(
                    'project_code' => $headCode,
                    'name' => $headName,
                    'is_head' => 1
                );
                $this->db->where('id', $headId);
                $queryResult = $this->db->update('project', $updateData);
                if ($queryResult) {
                    //for get user id
                    $this->session->set_flashdata('flashSuccess', 'Expense Head Updated successfully');
                    redirect('expenseHeads', 'refresh');
                } else {
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }
            $editData["headDetails"] = [0 => array(
                    'head_name' => $headName,
                    'head_code' => $headCode,
                    'is_head' => 1
            )];
        } else {
            //for get edit details
            $editData['headDetails'] = $this->projects->getheadDetauksById($headId);
        }
        $this->load->view('headManagement/editHead', $editData);
    }

}
