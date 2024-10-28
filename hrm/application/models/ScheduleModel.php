<?php

Class ScheduleModel extends CI_Model {

    function getAllSchedules() {
        $this->db->select('schedule.*');
        $this->db->from('schedule');
        $this->db->order_by('id','desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getScheduleData($id) {
        $this->db->select('schedule.*');
        $this->db->from('schedule');
        $this->db->where('schedule.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

}
