<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function create_dashboard_message() {
        $this->load->model('notifications');

        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];

//            if (false === $session_data['is_management']) {
//                $this->session->set_flashdata('flashError', 'Invalid Attempt');
//                redirect('home', 'refresh');
//            }

            $this->load->helper(array('form'));

            $this->load->model("dashBoardMessages");




            if ($this->input->post()) {
                $this->load->library('form_validation');
           
                    $message = $this->input->post('message');


                    $data = array('message' => $message);
                    $this->db->where('id', 1);
                    $this->db->update('dashboard_messages', $data);
                      $this->session->set_flashdata('flashSuccess', 'Message Modified!!');
              
            }

            $message = $this->dashBoardMessages->getDBMessage();

            $data['message'] = $message->message;
            $data['show'] = $message->show;


            $this->load->view('home/create_message', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
            //$this->load->view('home');
        }
    }

}
