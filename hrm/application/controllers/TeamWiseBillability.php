<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TeamWiseBillability extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('TimePredictionModel', '', TRUE);
        $this->load->model('TaskManagementModel', '', TRUE);
        $this->load->model('ProjectMappingModel', '', TRUE);
        $this->load->model('Projects', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('claim', '', TRUE);
        $this->load->model('Talent', '', TRUE);
    }

    

}
