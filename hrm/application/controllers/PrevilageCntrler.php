<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PrevilageCntrler extends CI_Controller {

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
            $rolesArray = array(
                '' => 'SELECT'
            );
            foreach ($roles as $role) {
                $rolesArray[$role['id']] = $role['role_name'];
            }
            $data['roles'] = $rolesArray;

            /*
             * Data tobe arrange like two sections
             */
            $number_of_menus = $this->previlageModel->getnumberMenu();
            $first_part_of_menu = $number_of_menus / 2;
            $second_part_of_menu = $number_of_menus - $first_part_of_menu;
            $data['first_part_of_menu_details'] = $this->previlageModel->getallMenu($first_part_of_menu, 0);
            $data['second_part_of_menu_details'] = $this->previlageModel->getallMenu($second_part_of_menu+1, $first_part_of_menu);


            $data['selected_role'] = $this->input->post('roles');
            $this->load->view('previlageView/PrevilageView', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function allprevilages() {
        /*
         * To show the role previlage
         */
        $id = $this->input->get('request');
        $nestedData = $this->previlageModel->getallRoleMenus($id);
        echo json_encode($nestedData);  // send data as json format
    }

    public function saveprevilages() {
        /*
         * To show the role previlage
         */
        $role = $this->input->get('role');
        $menu = $this->input->get('menu');
        $check = $this->input->get('check');
        $status=1;
        $message="Success";
        $number = $this->previlageModel->checkRoleMenu($role, $menu);
        if ($check == 1) {
            if ($number == 0) {
                $insertNotificationData = array(
                    'role' => $role,
                    'menu' => $menu,
                );
                //inserting
                $queryResult = $this->db->insert('role_menu', $insertNotificationData);
                if (!$queryResult) {
                    $status=0;
                    $message="Some error Found";
                }
            }
        } else {
            if ($number > 0) {
                $this->db->where('role', $role);
                $this->db->where('menu', $menu);
                $queryResult = $this->db->delete('role_menu');
                if (!$queryResult) {
                    $status=0;
                    $message="Some error Found";
                }
            }
        }
        
        $array=array('status' => $status,
            'message' => $message);
        echo json_encode($array);  // send data as json format
    }

}
