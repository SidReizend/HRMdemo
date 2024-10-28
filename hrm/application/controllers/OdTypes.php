<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OdTypes extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('odType', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['odTypeDetails'] = $this->odType->getodTypes();

            $this->load->view('odManagement/odTypes', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewOdType() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->helper('date');

            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('odTypeName', 'Name Of OD Type', 'trim|required|xss_clean');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                $odTypeName = $this->input->post('odTypeName');


                // Do insert operation here
                $insertUserData = array(
                    'od_type_name' => $odTypeName
                );
                $queryResult = $this->db->insert('od_types', $insertUserData);
                if ($queryResult) {
                    //if success
                    $this->session->set_flashdata('flashSuccess', 'New OD type is added successfully');
                    redirect('odTypes', 'refresh');
                } else {
                    //failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $this->load->view('odManagement/addNewOdType', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editOdType() {
        $this->load->helper('date');
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $odTypeId = $this->uri->segment(4);
        $editData['odTypeId'] = $odTypeId;

        if ($this->input->post()) {

            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('odTypeName', 'Name Of OD Type', 'trim|required|xss_clean');

            $odTypeName = $this->input->post('odTypeName');


            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                // Do update operation here
                $updateData = array(
                    'od_type_name' => $odTypeName
                );
                $this->db->where('id', $odTypeId);
                $queryResult = $this->db->update('od_types', $updateData);
                if ($queryResult) {
                    //for get user id
                    $this->session->set_flashdata('flashSuccess', 'OD Type Updated successfully');
                    redirect('odTypes', 'refresh');
                } else {
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }
            $editData["odTypeDetailsById"] = [0 => array(
                    'od_type_name' => $odTypeName
            )];
        } else {
            //for get edit details
            $editData['odTypeDetailsById'] = $this->odType->getOdTypeById($odTypeId);
        }
        $this->load->view('odManagement/editOdType', $editData);
    }

    public function deleteOdType() {
        //using url for get id
        $this->load->helper('url');
        $odTypeId = $this->uri->segment(4);
        $result = $this->odType->deleteOdTypeById($odTypeId);
        if ($result) {
            $this->session->set_flashdata('flashSuccess', 'OD Type deleted successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        redirect('odTypes', 'refresh');
    }

}
