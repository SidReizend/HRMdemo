<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class masterSettings extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        $this->load->library('form_validation');
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('content', 'content', 'trim|required|xss_clean');
            $this->form_validation->set_rules('description', 'description', 'trim|required|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                $name = $this->input->post('name');
                $content = $this->input->post('content');
                $description = $this->input->post('description');
                $id = $this->input->post('masterId');
                // Do update operation here
                $updateData = array(
                    'item' => $name,
                    'value' => $content,
                    'description' => $description,
                );
                $this->db->where('id', $id);
                $queryResult = $this->db->update('default_values', $updateData);
                if ($queryResult) {
                    //if success
                    $this->session->set_flashdata('flashSuccess', 'Updated successfully');
                    redirect('masterSettings', 'refresh');
                } else {
                    //failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            } else {
                //failure
                $this->session->set_flashdata('flashError', 'Fill all fileds');
            }
        }
        $this->load->helper(array('form'));
        $data['masterData'] = $this->defaultValues->getAllDefaultValues();
        $this->load->view('masterSettings/masterData', $data);
    }

}
