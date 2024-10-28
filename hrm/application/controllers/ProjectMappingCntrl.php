<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectMappingCntrl extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('ProjectMappingModel', '', TRUE);
        $this->load->model('Leaves', '', TRUE);
        $this->load->model('holiday', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $this->load->helper(array('form'));
            $this->load->view('ProjectMapping/ProjectMapDash');
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function ProjectWiseView() {

        $projects = $this->ProjectMappingModel->getAllProjects();
        $projectArray = array();
        $projectArray[''] = "Select";
        foreach ($projects as $project) {
            $projectArray[$project['id']] = $project['name'] . "-" . $project['project_code'];
        }
        $data['projects'] = $projectArray;

        $this->load->helper(array('form'));
        $this->load->view('ProjectMapping/ProjectWiseView', $data);
    }

    public function ProjectWiseViewAjax() {

        if ($this->input->get('date') != "") {
            $query_date = $this->input->get('date');
        } else {
            $query_date = date('Y-m-d');
        }
        $data['projectwise'] = 1;

        $data['project_id'] = $this->input->get('project');
        $data['mapping'] = $this->input->get('mapping');
        $data['date'] = date('Y-m', strtotime($query_date));

        $this->load->view('ProjectMapping/calendarAjax', $data);
    }

    public function TalentWiseView() {

        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        //helper for form and date.
        $is_management = $session_data['is_management'];
        $talentId = $session_data['talentId'];
        $is_pmo = $session_data['is_pmo'];
        $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
        $talentArray = array();
        $talentArray[''] = "Select";
        foreach ($talents as $talent) {
            $talentArray[$talent['id']] = $talent['name'];
        }
        $data['talents'] = $talentArray;

        $this->load->helper(array('form'));
        $this->load->view('ProjectMapping/TalentWiseView', $data);
    }

    public function TalentWiseViewAjax() {
        if ($this->input->get('date') != "") {
            $query_date = $this->input->get('date');
        } else {
            $query_date = date('Y-m-d');
        }
        $data['talentwise'] = 1;
        $data['mapping'] = $this->input->get('mapping');
        $data['talent_id'] = $this->input->get('talent');
        $data['date'] = date('Y-m', strtotime($query_date));

        $this->load->view('ProjectMapping/calendarAjax', $data);
    }

    public function TalentWiseMapping() {
        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //helper for form and date.
            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

            $projects = $this->ProjectMappingModel->getAllProjectsNotClosed();
            $projectArray = array();
            $projectArray[''] = "Select";
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['name'] . "-" . $project['project_code'];
            }
            $data['projects'] = $projectArray;


            //helper for form and date
            $this->load->helper(array('form'));

            $this->load->view('ProjectMapping/TalentWiseMapping', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function Talentwisesaving() {
        $status = 0;
        $message = "Successfully added";
        $talent_id = $this->input->get('talent_id');
        $talent_date = $this->input->get('talent_date');
        $project_id = $this->input->get('project_id');
        $textarea_prjt = $this->input->get('textarea_prjt');
        $rating = $this->input->get('rating');
        $checkexist = $this->ProjectMappingModel->CheckProjectExistMapping($talent_id, $talent_date, $project_id);
        if ($checkexist > 0) {
            $status = 1;
            $message = "Already Assigned to The Date";
        }
        $checkProjectToDate = $this->ProjectMappingModel->checkProjectToDate($project_id, $talent_date);
        if (FALSE === $checkProjectToDate) {
            $status = 1;
            $message = "Project's to date is less than the date you are chosen";
        }
        if ($status == 0) {
            $insertData = array(
                'talent_id' => $talent_id,
                'date' => $talent_date,
                'project_id' => $project_id,
                'description' => $textarea_prjt,
                'rating' => $rating
            );
            //inserting
            $queryResult = $this->db->insert('project_mapping', $insertData);
            if (!$queryResult) {
                $status = 1;
                $message = "Database Insertion Problem";
            }
        }
        $arr = array(
            'status' => $status,
            'message' => $message,
        );
        echo json_encode($arr);
    }

    public function Allsaving() {

        $message = "Successfully added";
        $talent_id = $this->input->get('talent_id');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('talent_from_date'));
        $fromDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('talent_to_date'));
        $toDate = $date->format('Y-m-d');

        $daterange = $this->Leaves->createDateRangeArray($fromDate, $toDate);



        $project_id = $this->input->get('project_id');
        $textarea_prjt = $this->input->get('textarea_prjt');
        $rating = $this->input->get('rating');
        $count = 0;
        foreach ($daterange as $talent_date) {
            $status = 0;
            $checkexist = $this->ProjectMappingModel->CheckProjectExistMapping($talent_id, $talent_date, $project_id);

            if ($checkexist > 0) {
                $status = 1;
                $message = "Already Assigned to The Date";
            }
            if ($status == 0) {
                $insertData = array(
                    'talent_id' => $talent_id,
                    'date' => $talent_date,
                    'project_id' => $project_id,
                    'description' => $textarea_prjt,
                    'rating' => $rating
                );
                //inserting
                $queryResult = $this->db->insert('project_mapping', $insertData);
                if (!$queryResult) {
                    $status = 1;
                    $message = "Database Insertion Problem";
                } else {
                    $count++;
                }
            }
        }
        if ($count > 0) {
            $status = 0;
            $message = $count . "Dates assigned";
        }
        $arr = array(
            'status' => $status,
            'message' => $message,
        );
        echo json_encode($arr);
    }

    public function Talentwisedeleting() {
        $status = 0;
        $message = "Successfully Deleted";
        $dlt = $this->input->get('dlt');
        $this->db->where('id', $dlt);
        $queryResult = $this->db->delete('project_mapping');
        if (!$queryResult) {
            $status = 0;
            $message = "Some error Found";
        }
        $arr = array(
            'status' => $status,
            'message' => $message,
        );
        echo json_encode($arr);
    }

    public function ProjectWiseMapping() {
        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //helper for form and date.
            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
            $talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

            $projects = $this->ProjectMappingModel->getAllProjectsNotClosed();
            $projectArray = array();
            $projectArray[''] = "Select";
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['name'] . "-" . $project['project_code'];
            }
            $data['projects'] = $projectArray;


            //helper for form and date
            $this->load->helper(array('form'));

            $this->load->view('ProjectMapping/ProjectWiseMapping', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function Showdetails() {
        $shw = $this->input->get('shw');
        $getProjectMapped = $this->ProjectMappingModel->getProjectMapped($shw);
        $data['getProjectMapped'] = $getProjectMapped;
        $data['home_page'] = 1;
        $this->load->view('ProjectMapping/MappingAjax', $data);
    }

    public function MappedProjects() {
        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //helper for form and date.
            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $data['talent'] = $talentId;

            //helper for form and date
            $this->load->helper(array('form'));

            $this->load->view('ProjectMapping/MappedProjects', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

}
