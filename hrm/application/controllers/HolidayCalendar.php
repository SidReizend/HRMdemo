<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HolidayCalendar extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        
        $this->load->helper(array('form'));
        $this->load->model('holiday');



        $current_year = date('Y');

        $year_selected = null !== $this->input->post('year') ? $this->input->post('year') : $current_year;

        $yearArray = array(
            $current_year - 2 => $current_year - 2,
            $current_year - 1 => $current_year - 1,
            $current_year => $current_year,
            $current_year + 1 => $current_year + 1,
            $current_year + 2 => $current_year + 2,
        );

        $details = array();
        for ($i = 1; $i <= 12; $i++) {
            $month = 2 === strlen($i) ? $i : "0" . $i;
            $details[$i]['defaultDate'] = $year_selected . '-' . $month . '-01';

            $event_string = "[";
            $from = $year_selected . "-".$month."-01";
            //Get Last day of the month
            $to = date("Y-m-t", strtotime($from));
            $events = $this->holiday->getHolidaysInRange($from, $to);

            foreach ($events as $evnt) {
                $holiday = $evnt['date_of_holiday'];
                $holiday_reason = $evnt['name_of_holiday'];
                $event_string .= "{ title: '" . $holiday_reason . "' , start : '" . $holiday . "' } ,";
            }
            $event_string .= "]";
            $details[$i]['events'] = $event_string;
            /// events: [ {  title: 'All Day Event',  start: '2016-08-01' } , ]
        }
        //die;

        $data['selectedYear'] = $year_selected;
        $data['years'] = $yearArray;
        $data['details'] = $details;


        $this->load->view('holiday/holiday_calendar', $data);
    }

}
