<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class VerifyLogin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    function index() {
        //This method will have the credentials validation
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

        if ($this->form_validation->run() == FALSE) {
            //Field validation failed.  User redirected to login page
            $this->load->view('login/login');
        } else {
            //Go to private area
            redirect('home', 'refresh');
        }
    }

    function check_database($password) {
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');

        //query the database
        $result = $this->user->login($username, $password);

        if ($result) {
            $sess_array = array();
            foreach ($result as $row) {
                $resultTalent = $this->talent->getTalentByUserId($row->id);
                $talentId = isset($resultTalent[0]->id) ? $resultTalent[0]->id : null;
                $firstName = isset($resultTalent[0]->first_name) ? $resultTalent[0]->first_name : null;
                $middleName = isset($resultTalent[0]->middle_name) ? $resultTalent[0]->middle_name : null;
                $lastName = isset($resultTalent[0]->last_name) ? $resultTalent[0]->last_name : null;
                $role = isset($resultTalent[0]->role_name) ? $resultTalent[0]->role_name : null;
                $role_stream_id = isset($resultTalent[0]->role_stream_id) ? $resultTalent[0]->role_stream_id : null;
                $role_stream = isset($resultTalent[0]->role_stream) ? $resultTalent[0]->role_stream : null;
                $imagePath = isset($resultTalent[0]->profile_image) ? $resultTalent[0]->profile_image : null;

                $is_administrator = ("admin" === $row->username) ? true : false;
                $pmoRoleStream = explode(',', $this->defaultValues->getValue(29));
                $is_pmo = in_array($role_stream_id, $pmoRoleStream) ? true : false;
                $is_management = ($this->defaultValues->getValue(28) == $role_stream_id) ? true : false;
                //ip of local machine

                $localIpDetails = $this->user->getLocalIp();
                $localIp = $localIpDetails->value;
                $is_approval_user = false;
                if ($is_management) {
                    $is_talent = false;
                } else {
                    $is_talent = null === $talentId ? false : true;
                    $is_approval_user = false;
                }

                $user_role = $row->role;

                if (true === $is_management || true === $is_talent) {
                    $is_approval_user = $this->talent->isApprovalUser($talentId);
                }


                $user_roles_array = $this->set_roles($user_role, $is_management, $is_talent, $is_approval_user);

                $this->load->library('user_agent');

                $sess_array = array(
                    'id' => $row->id,
                    'force_password_change' => $row->force_password_change,
                    'username' => $row->username,
                    'talentId' => $talentId,
                    'firstName' => $firstName,
                    'middleName' => $middleName,
                    'lastName' => $lastName,
                    'role' => $role,
                    'imagePath' => $imagePath,
                    'is_management' => $is_management,
                    'is_pmo' => $is_pmo,
                    'is_administrator' => $is_administrator,
                    'role_stream' => $role_stream,
                    'role_stream_id' => $role_stream_id,
                    'is_approval_user' => $is_approval_user,
                    'is_talent' => $is_talent,
                    'user_roles' => $user_roles_array,
                    'ip' => $this->input->ip_address(),
                    'localIp' => $localIp,
                );
                $this->session->set_userdata('logged_in', $sess_array);

                // Getting Data from user agent


                $userAgentData = array(
                    'user' => $row->id,
                    'platform' => $this->agent->platform,
                    'browser' => $this->agent->browser,
                    'version' => $this->agent->version,
                    'mobile' => $this->agent->mobile,
                    'robot' => $this->agent->robot,
                    'referer' => $this->agent->referer,
                    'ip' => $this->input->ip_address(),
                    'logged_in_at' => date('Y-m-d H:i:s'),
                );
                $queryResult = $this->db->insert('login_logs', $userAgentData);
            }
            //die();
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Invalid username or password');
            return false;
        }
    }

    /*
     * Set Role on Login and store to session
     */

    function set_roles($user_role, $is_management, $is_talent, $is_approval_user) {

        $user_role_array = array();

        if ("" !== $user_role) {
            $user_role_array = explode(",", $user_role);
//            if (($key = array_search("2", $user_role_array)) !== false) {
//                unset($user_role_array[$key]);
//            }
//            if (($key = array_search("3", $user_role_array)) !== false) {
//                unset($user_role_array[$key]);
//            }
//            if (($key = array_search("4", $user_role_array)) !== false) {
//                unset($user_role_array[$key]);
//            }
//            if (($key = array_search("", $user_role_array)) !== false) {
//                unset($user_role_array[$key]);
//            }
        }
        if (true === $is_management) {
            $user_role_array[] = 2;
        }
        if (true === $is_talent) {
            $user_role_array[] = 3;
        }
        if (true === $is_approval_user) {
            $user_role_array[] = 4;
        }
        return $user_role_array;
    }

}
