<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SalarySlip extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talent', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('salarySlips', '', TRUE);
    }

    public function index() {
        $this->load->helper('date');
        $this->load->helper(array('form'));
        $sess_data = $this->session->userdata('logged_in');
        $isResigned = $this->input->post('resigned') == '' ? 0 : $this->input->post('resigned');

        $yearArray = array(
            '' => '[SELECT YEAR]'
        );
        $earliest_year = 2015;
        foreach (range(date('Y'), $earliest_year) as $year) {
            $yearArray[$year] = $year;
        }
        $monthArray = array(
            '' => '[SELECT MONTH]'
        );
        $months = $this->talentAttendenceSheet->getMonths();
        foreach ($months as $month) {
            $monthArray[$month['id']] = $month['month'];
        }
        
        $data['selectedMonth'] = $this->input->post('month') == '' ? date('m') : $this->input->post('month');
        $data['selectedYear'] = $this->input->post('year') == '' ? date('Y') : $this->input->post('year');
        $data['years'] = $yearArray;
        $data['months'] = $monthArray;

        $data['isResigned'] = $isResigned;
        $data['salarySlips'] = $this->salarySlips->getSlipsByResignedStatus($isResigned,$data['selectedMonth'],$data['selectedYear']);
        $this->load->view('salarySlipManagement/salarySlip', $data);
    }

    public function addNewSalarySlip() {
        $this->load->helper('date');
        $this->load->helper(array('form'));
        $sess_data = $this->session->userdata('logged_in');
        $user_id = $sess_data['id'];

        //talents for select box
        $talents = $this->talent->getTalents();
        $talentsDropdown = array("" => "[SELECT Talent]");
        foreach ($talents as $talent) {
            $id = $talent['id'];
            $talentsDropdown[$id] = $talent['name'];
        }
        $yearArray = array(
            '' => '[SELECT YEAR]'
        );
        $earliest_year = 2015;
        foreach (range(date('Y'), $earliest_year) as $year) {
            $yearArray[$year] = $year;
        }
        $monthArray = array(
            '' => '[SELECT MONTH]'
        );
        $months = $this->talentAttendenceSheet->getMonths();
        foreach ($months as $month) {
            $monthArray[$month['id']] = $month['month'];
        }
        $selectedTalent = null !== $this->input->post('talent') ? $this->input->post('talent') : null;
        $selectedMonth = null !== $this->input->post('month') ? $this->input->post('month') : null;
        $selectedYear = null !== $this->input->post('year') ? $this->input->post('year') : null;
        if ($this->input->post()) {
            //Validation Success.  
            // Do insert operation here
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'jpg|png|jpeg|gif|pdf';
            $this->load->library('upload', $config);
            $upload_data = $this->upload->data();
            $upload_error = false;
            if ($_FILES['uploadFile']['error'] != 4) {
                if (!$this->upload->do_upload('uploadFile')) {
                    $upload_error = true;
                    $this->session->set_flashdata('flashError', $this->upload->display_errors());
                } else {
                    $note = $this->input->post('note');
                    $upload_data = $this->upload->data();
                    $fileName = $upload_data['file_name'];
                }
            }
            if (false === $upload_error) {
                $insertSlip = array(
                    'talent' => $selectedTalent,
                    'month' => $selectedMonth,
                    'year' => $selectedYear,
                    'uploaded_by' => $user_id,
                    'file_name' => $fileName,
                    'note' => $note,
                    'date_of_upload' => date('Y-m-d H:i:s')
                );
                $queryResult = $this->db->insert('salary_slip', $insertSlip);
                $this->session->set_flashdata('flashSuccess', 'File Uploaded successfully');
                redirect('salarySlip', 'refresh');
            } else {
                $this->session->set_flashdata('flashError', 'File Upload Failed');
            }
        }
        $data['talentsDropdown'] = $talentsDropdown;
        $data['monthDropdown'] = $monthArray;
        $data['yearDropdown'] = $yearArray;
        $data['selectedTalent'] = $selectedTalent;
        $data['selectedMonth'] = $selectedMonth;
        $data['selectedYear'] = $selectedYear;
        $this->load->view('salarySlipManagement/addNewSalarySlip', $data);
    }

    public function deleteSalarySlip() {
        //using url for get id
        $this->load->helper('url');
        $slipId = $this->uri->segment(4);
        $result = $this->salarySlips->deleteSlipById($slipId);
        if ($result) {
            $this->session->set_flashdata('flashSuccess', 'Holiday deleted successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        redirect('salarySlip', 'refresh');
    }

    public function checkAlreadyExist() {
        $talent = $this->input->get("talent");
        $month = $this->input->get("month");
        $year = $this->input->get("year");
        $resultCheckExist = $this->salarySlips->checkExistSalarySlip($talent, $month, $year);
        if ($resultCheckExist == TRUE) {
            echo 'Sorry! Salary slip uploaded for the talent,month,year';
        } else {
            echo TRUE;
        }
    }

    public function mySalarySlips() {
        $this->load->helper('date');
        $this->load->helper(array('form'));
        $sess_data = $this->session->userdata('logged_in');
        $data['salarySlips'] = $this->salarySlips->getMySlips($sess_data['talentId']);
        $this->load->view('salarySlipManagement/mySalarySlip', $data);
    }

}
