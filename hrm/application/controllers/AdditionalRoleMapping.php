<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdditionalRoleMapping extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('previlageModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //helper for form and date
            $this->load->helper(array('form'));

            $roles = $this->previlageModel->getallSystemRoles();
            $users = $this->previlageModel->getAllUsers();
            $usersArray = array(
                '' => 'SELECT'
            );
            foreach ($users as $user) {
                $usersArray[$user['id']] = $user['username'];
            }
            $data['user'] = $usersArray;

            /*
             * Data tobe arrange like two sections
             */
            $number_of_roles = $this->previlageModel->getNumberSystemRoles();
            $first_part_of_roles = $number_of_roles / 2;
            $second_part_of_roles = $number_of_roles - $first_part_of_roles;
            $data['first_part_of_role_details'] = $this->previlageModel->geAllRole($first_part_of_roles, 0);
            $data['second_part_of_role_details'] = $this->previlageModel->geAllRole($second_part_of_roles + 1, $first_part_of_roles);


            $data['selected_user'] = $this->input->post('user');
            $this->load->view('previlageView/additionalRole', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function allAdditionalRoles() {
        /*
         * To show the role previlage
         */
        $id = $this->input->get('request');
        $nestedData = $this->previlageModel->getAllAdditionalRoles($id);
        $nestedArray = (explode(",", $nestedData->role));
        echo json_encode($nestedArray);  // send data as json format
    }

    public function saveAdditionalRoles() {
        /*
         * To show the role previlage
         */
        $role = $this->input->get('role');
        $user = $this->input->get('user');
        $check = $this->input->get('check');
        $status = 1;
        $message = "Success";
        if ($check == 1) {
            $this->db->set('role', "CONCAT(role,',','$role')", FALSE);
            $this->db->where('id', $user);
            $queryResult = $this->db->update('users');
            if (!$queryResult) {
                $status = 0;
                $message = "Some error Found";
            }
        } else {
            $roles = $this->previlageModel->getAllAdditionalRoles($user);
            $roleArray = (explode(",", $roles->role));
            $roleToInsert = '';
            foreach ($roleArray as $roleSpecific) {
                if ($role != $roleSpecific && $roleSpecific != '')
                    $roleToInsert .= $roleSpecific . ',';
            }
            $updateData = array(
                'role' => $roleToInsert,
            );
            $this->db->where('id', $user);
            $queryResult = $this->db->update('users',$updateData);
            if (!$queryResult) {
                $status = 0;
                $message = "Some error Found";
            }
        }

        $array = array('status' => $status,
            'message' => $message);
        echo json_encode($array);  // send data as json format
    }

}
