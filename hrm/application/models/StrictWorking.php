<?php

Class StrictWorking extends CI_Model {
    public function getStrictWorkingDays() {
        $this->db->select('*');
        $this->db->from('strict_working_date');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
    function getDayToWork($date) {
        $this->db->select('*');
        $this->db->from('strict_working_date');
        $this->db->where('date_to_work', $date);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return false;
        }
    }
}
