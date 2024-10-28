<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('roles', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('weekdays', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //get role details
            //$this->load->model('role');
            $roleDetails = $this->roles->getRole();
            $roleHoliday = array();
            foreach ($roleDetails as $role) {
                $roleHoliday[$role['id']] = $this->holidaysWeek->getHolidaysWeek($role['id']);
            }

            $data['roleDetails'] = $roleDetails;
            $data['roleHoliday'] = $roleHoliday;

            $this->load->view('talentManagement/role', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewRole() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $weekDays = $this->weekdays->getWeekDays();
            $weekDayArray = array();
            foreach ($weekDays as $day) {
                $weekDayArray[$day['id']] = $day['week_day'];
            }
            $data['weekDays'] = $weekDayArray;

            $roleStreams = $this->roles->getRoleStreams();
            $roleStreamArray = array();
            foreach ($roleStreams as $roleStream) {
                $roleStreamArray[$roleStream['id']] = $roleStream['role_stream'];
            }
            $data['roleStreams'] = $roleStreamArray;
            $this->load->helper(array('form'));

            $this->load->library('form_validation');


            $this->form_validation->set_rules('role', 'Role', 'trim|required|xss_clean');
            $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
//            $this->form_validation->set_rules('holidays[]', 'Holidays', 'trim|required|xss_clean');
            $this->form_validation->set_rules('quarterLeaves', 'Quarter Leaves', 'trim|required|callback_numeric_wcomma|xss_clean');
            $this->form_validation->set_rules('averageWorkingHour', 'Average Working Hour', 'trim|required|xss_clean');
            $data['selectedAverageHourCalculation'] = $this->input->post('averageHourCalculation');
            $data['selected_holidays'] = $this->input->post('holidays');
            $data['selectedRoleStream'] = $this->input->post('roleStream');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                // Do insert operation here
                $role = $this->input->post('role');
                $description = $this->input->post('description');
                $quarterLeaves = $this->input->post('quarterLeaves');
                $roleStream = $this->input->post('roleStream');
                $averageWorkingHour = $this->input->post('averageWorkingHour');
                $averageWorkingMinitue = $this->input->post('averageWorkingMinitue');
                $averageHourCalculation = $this->input->post('averageHourCalculation');

                $workingTime = date("H:i:s", strtotime($averageWorkingHour . ':' . $averageWorkingMinitue . ':00'));
                $insertData = array(
                    'role_name' => $role,
                    'description' => $description,
                    'quarterly_earned_leaves' => $quarterLeaves,
                    'average_working_hour_per_day' => $workingTime,
                    'role_stream' => $roleStream,
                    'average_hour_calculation' => $averageHourCalculation,
                );
                $holidays = $this->input->post('holidays[]');
                $this->db->trans_begin();
                $queryResult = $this->db->insert('role', $insertData);

                if ($queryResult) {
                    $insert_id = $this->db->insert_id();
                    foreach ($holidays as $holiday) {
                        $insertRoleHolidays = array(
                            'role' => $insert_id,
                            'week_day' => $holiday,
                        );
                        $queryResult = $this->db->insert('role_holiday', $insertRoleHolidays);
                    }
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'New role is added successfully');
                    redirect('role', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                }

                // set flash message
            }

            $this->load->view('talentManagement/addNewRole', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editRole() {

        $weekDays = $this->weekdays->getWeekDays();
        $weekDayArray = array();
        foreach ($weekDays as $day) {
            $weekDayArray[$day['id']] = $day['week_day'];
        }
        $editData['weekDays'] = $weekDayArray;
        //using url for get id
        $this->load->helper('url');
        $roleId = $this->uri->segment(4);
        $editData['roleId'] = $roleId;
        //for get edit details
        $editData['roleDetailsById'] = $this->roles->getRoleById($roleId);

        //holidays
        $editData['holidaysOfRole'] = $this->holidaysWeek->getHolidaysWeek($roleId);

        $this->load->helper(array('form'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('role', 'Role', 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
//        $this->form_validation->set_rules('holidays[]', 'Holidays', 'trim|required|xss_clean');
        $this->form_validation->set_rules('quarterLeaves', 'Quarter Leaves', 'trim|required|callback_numeric_wcomma|xss_clean');



        $editData['selected_holidays'] = $this->input->post('holidays');

        if ($this->form_validation->run() == TRUE) {
            //Validation Success.  
            $roleId = $this->input->post('roleId');
            $role = $this->input->post('role');
            $description = $this->input->post('description');
            $quarterLeaves = $this->input->post('quarterLeaves');
            $averageWorkingMinitue = $this->input->post('averageWorkingMinitue');
            $averageWorkingHour = $this->input->post('averageWorkingHour');
            $averageHourCalculation = $this->input->post('averageHourCalculation');
            $workingTime = date("H:i:s", strtotime($averageWorkingHour . ':' . $averageWorkingMinitue . ':00'));
            //die();
            $averageHourCalculation = $this->input->post('averageHourCalculation');

            // Do update operation here
            $updateData = array(
                'role_name' => $role,
                'description' => $description,
                'quarterly_earned_leaves' => $quarterLeaves,
                'average_working_hour_per_day' => $workingTime,
                'average_hour_calculation' => $averageHourCalculation
            );
            $holidays = $this->input->post('holidays[]');
            $this->db->trans_begin();
            $this->db->where('id', $roleId);
            $queryResultRoleUpdate = $this->db->update('role', $updateData);

            if ($queryResultRoleUpdate) {
                $this->db->where('role', $roleId);
                $this->db->delete('role_holiday');
                foreach ($holidays as $holiday) {
                    $insertRoleHolidays = array(
                        'role' => $roleId,
                        'week_day' => $holiday,
                    );
                    $queryResultHoliday = $this->db->insert('role_holiday', $insertRoleHolidays);
                }
                $this->db->trans_commit();
                //die();
                $this->session->set_flashdata('flashSuccess', 'Role Updated successfully');
                redirect('role', 'refresh');
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flashError', 'Some error found');
            }
            // set flash message
        }

        $this->load->view('talentManagement/editRole', $editData);
    }

//    public function deleteRole() {
//        //using url for get id
//        $this->load->helper('url');
//        $roleId = $this->uri->segment(4);
//        $editData['roleId'] = $roleId;
//        $this->roles->deleteRoleById($roleId);
//        $this->session->set_flashdata('flashSuccess', 'Role deleted successfully');
//        redirect('role', 'refresh');
//    }

    function numeric_wcomma($str) {
        $this->form_validation->set_message('numeric_wcomma', 'Should be a number');
        return preg_match('/^[0-9,]+$/', $str) ? TRUE : FALSE;
    }

}
