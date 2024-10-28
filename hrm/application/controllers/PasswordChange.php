<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PasswordChange extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $this->load->library('form_validation');
            $this->form_validation->set_rules('newPassword', 'New Password', 'trim|required|xss_clean|matches[confirmPassword]');
            $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('oldPassword', 'Old Password', 'trim|required|xss_clean|callback_oldPasswordCheck');

            $password = $this->input->post('newPassword');
            $password_enc = md5($password);

            if ($this->form_validation->run() == TRUE) {
                $session_data = $this->session->userdata('logged_in');
                $session_data['force_password_change'] = 0;
                $this->session->set_userdata('logged_in', $session_data);
                $data = array('force_password_change' => 0, 'password' => $password_enc);
                $this->db->where('id', $session_data['id']);
                $this->db->update('users', $data);
                $this->session->set_flashdata('flashSuccess', 'Password changed completed successfully');
            }
            $this->load->view('password_change_request/passwordChange', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function oldPasswordCheck($oldPassword) {
        $session_data = $this->session->userdata('logged_in');
        $oldPassword = md5($oldPassword);
        $oldPasswordHash = $this->user->passwordCheck($session_data['id']);
        $oldPasswordDb = $oldPasswordHash->password;
        if ($oldPassword != $oldPasswordDb) {
            $this->form_validation->set_message('oldPasswordCheck', 'Old password not match');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
