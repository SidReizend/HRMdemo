<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CompanyPolicyManagement extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('policy', '', TRUE);
    }

    public function index() {
        $data['policies'] = $this->policy->getPolicies();
        $this->load->view('companyPolicyManagement/policies', $data);
    }

    public function addNewPolicy() {
        //helper for form and date
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        //for form validayion, setting rules
        $this->form_validation->set_rules('policyName', 'Name Of OD Type', 'trim|required|xss_clean');
        if ($this->form_validation->run() == TRUE) {
            //Validation Success.  
            $policyName = $this->input->post('policyName');
            $policy = $this->input->post('policy');
            // Do insert operation here
            $insertUserData = array(
                'policy_name' => $policyName,
                'content' => $policy
            );
            $queryResult = $this->db->insert('policies', $insertUserData);
            if ($queryResult) {
                //if success
                $this->session->set_flashdata('flashSuccess', 'New policy is added successfully');
            } else {
                //failure
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        }
        $this->load->view('companyPolicyManagement/addNewPolicy');
    }

    public function viewPolicy() {
        //using url for get id
        $this->load->helper('url');
        $policyId = $this->uri->segment(3);
        $result = $this->policy->getPolicyById($policyId);
        if ($result) {
            $data['policyDetails'] = $result;
            $this->load->view('companyPolicyManagement/viewPolicy', $data);
        } else {
            redirect('home', 'refresh');
        }
    }

    public function editPolicy() {
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $policyId = $this->uri->segment(3);
        $editData['policyId'] = $policyId;
        $this->load->library('form_validation');
        //for form validayion, setting rules
        $this->form_validation->set_rules('policyName', 'Name Of Policy', 'trim|required|xss_clean');
        if ($this->form_validation->run() == TRUE) {
            $policyName = $this->input->post('policyName');
            $policy = $this->input->post('policy');
            //Validation Success.  
            // Do update operation here
            $updateData = array(
                'policy_name' => $policyName,
                'content' => $policy
            );
            $this->db->where('id', $policyId);
            $queryResult = $this->db->update('policies', $updateData);
            if ($queryResult) {
                $this->session->set_flashdata('flashSuccess', 'Policy Updated successfully');
                redirect('CompanyPolicyManagement', 'refresh');
            } else {
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        } else {
            //for get edit details
            $editData['policyDetails'] = $this->policy->getPolicyById($policyId);
        }
        $this->load->view('companyPolicyManagement/editPolicy', $editData);
    }

    public function deletePolicy() {
        //using url for get id
        $this->load->helper('url');
        $policyId = $this->uri->segment(3);
        $updateData = array(
            'is_deleted' => 1,
        );
        $this->db->where('id', $policyId);
        $queryResult = $this->db->update('policies', $updateData);
        if ($queryResult) {
            $this->session->set_flashdata('flashSuccess', 'Policy deleted successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        redirect('companyPolicyManagement', 'refresh');
    }

}
