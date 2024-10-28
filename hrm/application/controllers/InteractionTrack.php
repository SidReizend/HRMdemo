<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class InteractionTrack extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talent', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $this->load->helper(array('form'));

            $fromDateNotStandered = $this->input->post('fromDate') ? $this->input->post('fromDate') : date('d/m/Y', strtotime('first day of january this year'));;
            $toDateNotStandered = $this->input->post('toDate') ? $this->input->post('toDate') : date('d/m/Y');
            $date = DateTime::createFromFormat('d/m/Y', $fromDateNotStandered);
            $fromDate = $date->format('Y-m-d');
            $date = DateTime::createFromFormat('d/m/Y', $toDateNotStandered);
            $toDate = $date->format('Y-m-d');
            $talentIn = $this->input->post('talent');
            $typeIn = $this->input->post('type');
            $managerIn = $this->input->post('manager');

            $talents = $this->talent->getTalentsAll();
            $talentArray = array();
            $talentArray[''] = "--Talent--";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

            $managers = $this->talent->getReportingManagerAll();
            $managersArray = array();
            $managersArray[''] = "--Reported By--";
            foreach ($managers as $talent) {
                $managersArray[$talent['id']] = $talent['name'];
            }
            $data['managers'] = $managersArray;

            $types = $this->talent->getInteractionTypes();
            $typeArray = array();
            $typeArray[''] = "--Type--";
            foreach ($types as $type) {
                $typeArray[$type['id']] = $type['name'];
            }
            $data['types'] = $typeArray;
            $data['fromDate'] = $fromDateNotStandered;
            $data['toDate'] = $toDateNotStandered;
            $data['selectedtalent'] = $talentIn;
            $data['selectedtype'] = $typeIn;
            $data['selectedmanager'] = $managerIn;

            $data['username'] = $session_data['username'];
            $interactionDetails = $this->talent->getInteractionsAll($talentIn,$typeIn, $managerIn, $fromDate, $toDate);
            $data['interactionDetails'] = $interactionDetails;
            $this->load->view('InteractionTrack/index', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewInteraction() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $talents = $this->talent->getTalentsAll();
            $talentArray = array();
            $talentArray[''] = "--Talent--";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

            $managers = $this->talent->getReportingManagerAll();
            $managersArray = array();
            $managersArray[''] = "--Reported By--";
            foreach ($managers as $talent) {
                $managersArray[$talent['id']] = $talent['name'];
            }
            $data['managers'] = $managersArray;

            $types = $this->talent->getInteractionTypes();
            $typeArray = array();
            $typeArray[''] = "--Type--";
            foreach ($types as $type) {
                $typeArray[$type['id']] = $type['name'];
            }
            $data['types'] = $typeArray;

            $this->load->helper(array('form'));
            $this->load->library('form_validation');

            $data['selectedType'] = $this->input->post('type');
            $data['selectedTalent'] = $this->input->post('stable_talents');
            $data['selectedManager'] = $this->input->post('managers');
            $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('stable_talents', 'Talent', 'trim|required|xss_clean');
            $this->form_validation->set_rules('managers', 'Reported By', 'trim|required|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                $fromDate = "0000-00-00";
                if ($this->input->post('date') != "") {
                    $date = DateTime::createFromFormat('d/m/Y', $this->input->post('date'));
                    $fromDate = $date->format('Y-m-d');
                }
                $type = $this->input->post('type');
                $description = $this->input->post('description');
                $stable_talents = $this->input->post('stable_talents');
                $managers = $this->input->post('managers');
                
                $insertData = array(
                    'from_date' => $fromDate,
                    'talent' => $stable_talents,
                    'interaction_type' => $type,
                    'description' => $description,
                    'reporting_manager' => $managers,
                );
                $this->db->trans_begin();
                $queryResult = $this->db->insert('interaction_records', $insertData);
                if ($queryResult) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'New Interaction is added successfully');
                    redirect('interactionTrack', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $this->load->view('InteractionTrack/addNewInteraction', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }


    public function editInteraction() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $talents = $this->talent->getTalentsAll();
            $talentArray = array();
            $talentArray[''] = "--Talent--";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $editData['talents'] = $talentArray;

            $managers = $this->talent->getReportingManagerAll();
            $managersArray = array();
            $managersArray[''] = "--Reported By--";
            foreach ($managers as $talent) {
                $managersArray[$talent['id']] = $talent['name'];
            }
            $editData['managers'] = $managersArray;

            $types = $this->talent->getInteractionTypes();
            $typeArray = array();
            $typeArray[''] = "--Type--";
            foreach ($types as $type) {
                $typeArray[$type['id']] = $type['name'];
            }
            $editData['types'] = $typeArray;
            //using url for get id
            $this->load->helper('url');
            $this->load->library('form_validation');
            $ptojectId = $this->uri->segment(3);
            $editData['ptojectId'] = $ptojectId;
            //for get edit details
            $editData['interactionDetails'] = $this->talent->getInteractionById($ptojectId);
            $this->load->helper(array('form'));

            $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('stable_talents', 'Talent', 'trim|required|xss_clean');
            $this->form_validation->set_rules('managers', 'Reported By', 'trim|required|xss_clean');

            $editData['selectedBillable'] = $this->input->post('billable');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success. 
                $fromDate = "0000-00-00";
                if ($this->input->post('date') != "") {
                    $date = DateTime::createFromFormat('d/m/Y', $this->input->post('date'));
                    $fromDate = $date->format('Y-m-d');
                }
                $type = $this->input->post('type');
                $description = $this->input->post('description');
                $stable_talents = $this->input->post('stable_talents');
                $managers = $this->input->post('managers');
                
                $insertData = array(
                    'from_date' => $fromDate,
                    'talent' => $stable_talents,
                    'interaction_type' => $type,
                    'description' => $description,
                    'reporting_manager' => $managers,
                );
                $this->db->trans_begin();
                $this->db->where('id', $ptojectId);
                $queryResultRoleUpdate = $this->db->update('interaction_records', $insertData);

                if ($queryResultRoleUpdate) {
                    $this->db->trans_commit();
                    //die();
                    $this->session->set_flashdata('flashSuccess', 'Interaction Updated successfully');
                    redirect('interactionTrack', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
                // set flash message
            }

            $this->load->view('InteractionTrack/editInteraction', $editData);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }
}
