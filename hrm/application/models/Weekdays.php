<?php

Class Weekdays extends CI_Model {

    function getWeekDays() {
        $this->db->select('id, week_day');
        $this->db->from('master_week_days');

        $query = $this->db->get();
        return $query->result_array();
    }

}
