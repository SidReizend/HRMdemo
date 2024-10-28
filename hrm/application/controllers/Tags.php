<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tags extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('KnowledgeRepository', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['tags'] = $this->KnowledgeRepository->tags();

            $this->load->view('talentKnowledgeProcess/tags', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addTag() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            //helper for form and date
            $this->load->helper(array('form'));

            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('tagName', 'Name Tag', 'trim|required|xss_clean');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                $tagName = $this->input->post('tagName');


                // Do insert operation here
                $insertData = array(
                    'tag_name' => $tagName
                );
                $queryResult = $this->db->insert('tag', $insertData);
                if ($queryResult) {
                    //if success
                    $this->session->set_flashdata('flashSuccess', 'New tag added successfully');
                    redirect('tags', 'refresh');
                } else {
                    //failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $this->load->view('talentKnowledgeProcess/addTag', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editTag() {
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $tagId = $this->uri->segment(3);
        $editData['tagId'] = $tagId;


        $this->load->library('form_validation');

        //for form validayion, setting rules
        $this->form_validation->set_rules('tagName', 'Name Of Tag', 'trim|required|xss_clean');

        $tagName = $this->input->post('tagName');


        if ($this->form_validation->run() == TRUE) {
            //Validation Success.  
            // Do update operation here
            $updateData = array(
                'tag_name' => $tagName
            );
            $this->db->where('id', $tagId);
            $queryResult = $this->db->update('tag', $updateData);
            if ($queryResult) {
                //for get user id
                $this->session->set_flashdata('flashSuccess', 'Tag Updated successfully');
                redirect('tags', 'refresh');
            } else {
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        }
        //for get edit details
        $editData['tagDetails'] = $this->KnowledgeRepository->getTagId($tagId);

        $this->load->view('talentKnowledgeProcess/editTag', $editData);
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
