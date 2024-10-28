<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PendingApprovals extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        $this->load->model('notifications');

        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];

//            if (false === $session_data['is_management']) {
//                $this->session->set_flashdata('flashError', 'Invalid Attempt');
//                redirect('home', 'refresh');
//            }

            $pending_leave_approvals = $this->pending_leave_requests();
            $pending_od_approvals = $this->pending_od_requests();
            $pending_compoff_approvals = $this->pending_compoff_requests();
            $pending_encashment_approvals = $this->pending_encashment_requests();

            $data['pending_leave_approvals'] = $pending_leave_approvals;
            $data['pending_od_approvals'] = $pending_od_approvals;
            $data['pending_compoff_approvals'] = $pending_compoff_approvals;
            $data['pending_encashment_approvals'] = $pending_encashment_approvals;



            //  $this->load->model("pending");



            $this->load->view('home/pending_approvals', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
            //$this->load->view('home');
        }
    }

    private function pending_leave_requests() {

        $this->load->model('leaves');
        return $leaveWaiting = $this->leaves->getPendingLeaveApprovals();
    }

    private function pending_od_requests() {
        $this->load->model('myOd');
        return $odWaiting = $this->myOd->getPendingODApprovals();
    }

    private function pending_compoff_requests() {
        $this->load->model('talentAttendenceSheet');
        return $compWaiting = $this->talentAttendenceSheet->getCompApprovalPending();
    }

    private function pending_encashment_requests() {
        $this->load->model('talentAttendenceSheet');
        return $compWaiting = $this->talentAttendenceSheet->getEncashmentApprovalPending();
    }

}
