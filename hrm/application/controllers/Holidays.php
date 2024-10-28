<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Holidays extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('holiday', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('ProjectMappingModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['holidayDetails'] = $this->holiday->getHolidays();

            $this->load->view('holiday/holidays', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function allowWorking() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $session_data = $this->session->userdata('logged_in');
            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentsArray = array(
                '' => 'SELECT'
            );
            foreach ($talents as $talent) {
                $talentsArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentsArray;
            $data['holidayDetails'] = $this->holiday->getHolidays();

            $this->load->view('holiday/allowWorking', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function talentAllowedToApply() {
        $id = $this->input->post('id');
        $holiday = $this->holiday->getHolidayById($id);
        $talentAllowedToApply = $this->holiday->getTalentAllowedToApply($id);

        $data['holiday'] = $holiday;
        $data['talentAllowedToApply'] = $talentAllowedToApply;
        $this->load->view('holiday/talentAllowedToApply', $data);
    }

    public function addTalent() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login', 'refresh');
        }
        $session_data = $this->session->userdata('logged_in');
        $createdBy = $session_data['talentId'];
        $createdAt = date('Y-m-d H:i:s');
        $this->load->helper(array('form'));
        $holiday = $this->input->post('holidayId');
        $talent = $this->input->post('talent');
        $status = "error";
        $msg = "Some error found";
        if (!$talent && !$holiday) {
            print_r(json_encode(['msg' => $msg, 'status' => $status]));
        } else {
            $isAlreadyExist = $this->holiday->checkPermissionGiven($holiday, $talent);
            if ($isAlreadyExist) {
                $msg = "Already Added";
                print_r(json_encode(['msg' => $msg, 'status' => $status]));
            } else {
                $insertUserData = array(
                    'talent' => $talent,
                    'holiday' => $holiday,
                    'created_at' => $createdAt,
                    'added_by' => $createdBy,
                    'is_deleted' => 0,
                );
                $this->db->insert('public_holiday_working_talents', $insertUserData);
                $status = "success";
                $msg = "Successfully added";
                print_r(json_encode(['msg' => $msg, 'status' => $status]));
            }
        }
    }

    public function addNewHoliday() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->helper('date');

            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('dateOfHoliday', 'Date Of Holiday', 'trim|required|xss_clean');
            $this->form_validation->set_rules('nameOfHoliday', 'Name Of Holiday', 'trim|required|xss_clean');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfHoliday'));
                $dateOfHoliday = $date->format('Y-m-d');
                $nameOfHoliday = $this->input->post('nameOfHoliday');

                $this->db->trans_begin();

                // Do insert operation here
                $insertUserData = array(
                    'date_of_holiday' => $dateOfHoliday,
                    'name_of_holiday' => $nameOfHoliday
                );
                $queryResult = $this->db->insert('holidays', $insertUserData);
                if ($queryResult) {
                    // Do update in attendance table operation here
                    $updateHoliday = array(
                        'is_holiday' => 1,
                    );
                    $this->db->where('attendance_date', $dateOfHoliday);
                    $queryResult = $this->db->update('talent_attendance', $updateHoliday);

                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'New Holiday is added successfully');
                    redirect('holidays', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    //failure
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $this->load->view('holiday/addNewHoliday', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editHoliday() {
        $this->load->helper('date');
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $holidayId = $this->uri->segment(4);
        $editData['holidayId'] = $holidayId;

        if ($this->input->post()) {

            $this->load->library('form_validation');

            $this->form_validation->set_rules('dateOfHoliday', 'Date Of Holiday', 'trim|required|xss_clean|callback_check_database_holiday');
            $this->form_validation->set_rules('nameOfHoliday', 'Name Of Holiday', 'trim|xss_clean|required');

            $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfHoliday'));
            $dateOfHoliday = $date->format('Y-m-d');
            $nameOfHoliday = $this->input->post('nameOfHoliday');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                // Do update operation here
                $updateHolidayData = array(
                    'date_of_holiday' => $dateOfHoliday,
                    'name_of_holiday' => $nameOfHoliday
                );
                $this->db->where('id', $holidayId);
                $queryResult = $this->db->update('holidays', $updateHolidayData);
                if ($queryResult) {
                    //for get user id
                    $this->session->set_flashdata('flashSuccess', 'Holiday Updated successfully');
                    redirect('holidays', 'refresh');
                } else {
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }
            $editData["holidayDetailsById"] = [0 => array(
                    'date_of_holiday' => date('Y-m-d', strtotime($dateOfHoliday)),
                    'name_of_holiday' => $nameOfHoliday
            )];
        } else {
            //for get edit details
            $editData['holidayDetailsById'] = $this->holiday->getHolidayById($holidayId);
        }
        $this->load->view('holiday/editHoliday', $editData);
    }

    function check_database_holiday($dateOfHoliday) {
        //Field validation succeeded.  Validate against database
        $dateOfHoliday = $this->input->post('dateOfHoliday');
        $holidayId = $this->uri->segment(4);

        //query the database
        $result = $this->holiday->getHolidayByDay(date('Y-m-d', strtotime($dateOfHoliday)), $holidayId);
        if ($result) {
            $this->form_validation->set_message('check_database_holiday', 'Holiday already exists');
            return false;
        } else {
            return TRUE;
        }
    }

    public function deleteHoliday() {
        //using url for get id
        $this->load->helper('url');
        $holidayId = $this->uri->segment(4);
        $result = $this->holiday->deleteHolidayById($holidayId);
        if ($result) {
            $this->session->set_flashdata('flashSuccess', 'Holiday deleted successfully');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
        }
        redirect('holidays', 'refresh');
    }

}
