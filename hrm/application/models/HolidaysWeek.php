<?php

Class HolidaysWeek extends CI_Model {

    function getHolidaysWeek($role) {
        $this->db->select('m.week_day,m.id');
        $this->db->from('role_holiday r');
        $this->db->Join('master_week_days m','m.id=r.week_day','left');
        $this->db->where('r.role', $role);
        $query = $this->db->get();
        return $query->result_array();
    }
    function getCountOfRoleHoliday($role,$day){
        $this->db->select('id');
        $this->db->from('role_holiday');
        $this->db->where('role', $role);
        $this->db->where('week_day', $day);
        $query = $this->db->get();
        return $query->num_rows();
    }
    function getHolidayTalent($talent) {
        $this->db->select('m.week_day');
        $this->db->from('talents t');
        $this->db->Join('role_holiday r','t.role=r.role','left');
        $this->db->Join('master_week_days m','m.id=r.week_day','left' );
        $this->db->where('t.id', $talent);
        $query = $this->db->get();
        return $query->result_array();
    }
}
