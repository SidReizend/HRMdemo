<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TalentCertificates extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('certificate', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('talent', '', TRUE);
    }

    public function signOnTalents() {
        $this->load->helper('date');
        $this->load->helper(array('form'));
        
         $login_details = $this->session->userdata('logged_in');

        $is_management = $login_details['is_management'];
        $talentId_user = $login_details['talentId'];


        $subordinates = $this->talent->getSubordinatesByStatus($talentId_user, $is_management,0);
        
        
        $talentsArray = array(
            '' => 'SELECT'
        );
        foreach ($subordinates as $subord) {
            $talentsArray[$subord['id']] = $subord['name'];
        }
        $this->documents($talentsArray,0);
    }
    public function resignedTalents() {
        $this->load->helper('date');
        $this->load->helper(array('form'));
        
         $login_details = $this->session->userdata('logged_in');

        $is_management = $login_details['is_management'];
        $talentId_user = $login_details['talentId'];


        $subordinates = $this->talent->getSubordinatesByStatus($talentId_user, $is_management,1);
        
        
        $talentsArray = array(
            '' => '[SELECT Talent]'
        );
        foreach ($subordinates as $subord) {
            $talentsArray[$subord['id']] = $subord['name'];
        }
        $this->documents($talentsArray,1);
    }
    public function documents($talentsArray,$resignedStatus) {
        $data['talentsUnderReportManager'] = $talentsArray;
        $data['selectedTalent'] = $this->input->post('talent');
        if($resignedStatus == 1){
            $data['isResigned'] = 1;
        }else {
            $data['isResigned'] = 0;
        }
        $talent = '';
        if ($this->input->post()) {
         $talent = $this->input->post('talent');
        }
        $data['myCertificates'] = $this->certificate->getMyCerificates($talent);
        $this->load->view('certificatesManagement/talentCertificates', $data);
    }

}
