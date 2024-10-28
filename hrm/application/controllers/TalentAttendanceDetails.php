<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TalentAttendanceDetails extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('talentAttendance', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('talent', '', TRUE);
    }

    public function signOnTalentsDetails() {
        $this->load->helper(array('form'));

        $login_details = $this->session->userdata('logged_in');

        $is_management = $login_details['is_management'];
        $talentId_user = $login_details['talentId'];


        $subordinates = $this->talent->getSubordinatesByStatus($talentId_user, $is_management, 0);
        $talentsDropdown = array("" => "[SELECT-Talent]");

        foreach ($subordinates as $subord) {
            $id = $subord['id'];
            $talentsDropdown[$id] = $subord['name'];
        }
        $this->TalentsDetails($talentsDropdown, 0);
    }

    public function resignedTalentsDetails() {
        $this->load->helper(array('form'));

        $login_details = $this->session->userdata('logged_in');

        $is_management = $login_details['is_management'];
        $talentId_user = $login_details['talentId'];


        $subordinates = $this->talent->getSubordinatesByStatus($talentId_user, $is_management, 1);
        $talentsDropdown = array("" => "[SELECT-Talent]");

        foreach ($subordinates as $subord) {
            $id = $subord['id'];
            $talentsDropdown[$id] = $subord['name'];
        }
        $this->TalentsDetails($talentsDropdown, 1);
    }

    public function TalentsDetails($talentsDropdown, $resignedStatus) {
        $this->load->helper(array('form'));
        $this->load->helper('url');
        //$year_selected = null !== $this->input->post('year') ? $this->input->post('year') : $current_year;
        $selectedTalent = null !== $this->input->post('talent') ? $this->input->post('talent') : null;
        $isResigned = null !== $this->input->post('resigned') ? $this->input->post('resigned') : null;

        $talent_id = $selectedTalent;
        $current_year = date('Y');

        $yearArray = array(
            $current_year - 2 => $current_year - 2,
            $current_year - 1 => $current_year - 1,
            $current_year => $current_year,
            $current_year + 1 => $current_year + 1,
            $current_year + 2 => $current_year + 2,
        );

        $details = array();


        $event_string = "[";
        if (null !== $selectedTalent) {
            foreach ($yearArray as $year) {
                $year_selected = $year;
                for ($i = 1; $i <= 12; $i++) {
                    $month = 2 === strlen($i) ? $i : "0" . $i;
                    $details[$i]['defaultDate'] = $year_selected . '-' . $month . '-01';
                    $from = $year_selected . "-" . $month . "-01";
                    //Get Last day of the month
                    $to = date("Y-m-t", strtotime($from));
                    $attendanceStatus = $this->talentAttendance->getTalentAttendanceDetails($from, $to, $talent_id);
                    $plannedLeaves = $this->leaves->checkPlannedDateFrom($from, $to, $talent_id);
                    foreach ($plannedLeaves as $plannedLeaveSpecific) {
                        if ($plannedLeaveSpecific['is_approved'] == 1) {
                            $color = '#39cccc';
                        } else {
                            $color = '#dd4b39';
                        }
                        $event_string .= "{ title: 'Planned Leave' , start : '" . $plannedLeaveSpecific['from_date'] . "', end : '" . $plannedLeaveSpecific['to_date'] . "',backgroundColor:'$color',borderColor:'$color' } ,";
                    }
                    $futtureLeaves = $this->leaves->futureLeaves($from, $to, $talent_id);
                    foreach ($futtureLeaves as $futtureLeave) {
                        $leaveType = $futtureLeave['type'] == '' ? 'LOP' : $futtureLeave['type'];
                        $event_string .= "{ title: 'Leave(" . $leaveType . ")' , start : '" . $futtureLeave['date_of_leave'] . "',backgroundColor:'#f39c12',borderColor:'#f39c12' } ,";
                    }
                    foreach ($attendanceStatus as $attSt) {
                        //var_dump($attSt);
                        $attendance_date = $attSt['attendance_date'];

                        $is_leave = $attSt['is_leave'];
                        $avg_consider = $attSt['avg_consider'];
                        $is_holiday = $attSt['is_holiday'];
                        if (1 == $is_holiday) {
                            $event_string .= "{ title: 'Holiday' , start : '" . $attendance_date . "',backgroundColor:'#3c8dbc',borderColor:'#3c8dbc' } ,";
                            if (1 == $is_leave) {
                                $leaveType = $this->leaves->getDayleaveType($attendance_date, $talent_id);
                                $event_string .= "{ title: 'Leave(" . $leaveType . ")' , start : '" . $attendance_date . "',backgroundColor:'#f39c12',borderColor:'#f39c12' } ,";
                            }
                        } else if (1 == $is_leave) {
                            $leaveType = $this->leaves->getDayleaveType($attendance_date, $talent_id);
                            $event_string .= "{ title: 'Leave(" . $leaveType . ")' , start : '" . $attendance_date . "',backgroundColor:'#f39c12',borderColor:'#f39c12' } ,";
                        } else if (0 == $avg_consider) {
                            $event_string .= "{ title: 'Absent' , start : '" . $attendance_date . "',backgroundColor:'#f56954',borderColor:'#f56954' } ,";
                        }
                        if (1 == $avg_consider) {
                            $event_string .= "{ title: 'Worked' , start : '" . $attendance_date . "',backgroundColor:'#00a65a',borderColor:'#00a65a' } ,";
                        }


                        //$holiday = $evnt['date_of_holiday'];
                        //$holiday_reason = $evnt['name_of_holiday'];
                        //$event_string .= "{ title: '" . $holiday_reason . "' , start : '" . $holiday . "' } ,";
                    }
                    /// events: [ {  title: 'All Day Event',  start: '2016-08-01' } , ]
                }
            }
        }
        $event_string .= "]";
        $data['events'] = $event_string;
        //die;
        //$data['selectedYear'] = $year_selected;
        $data['selectedTalent'] = $selectedTalent;
        $data['talentsDropdown'] = $talentsDropdown;
        $data['years'] = $yearArray;
        $data['details'] = $details;
        if ($resignedStatus == 1) {
            $data['isResigned'] = 1;
        } else {
            $data['isResigned'] = 0;
        }

        $this->load->view('talent_attendance_details/holiday_calendar', $data);
    }

}
