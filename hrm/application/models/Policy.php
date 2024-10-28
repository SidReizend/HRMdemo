<?php

Class Policy extends CI_Model {

    function getPolicies() {
        $this->db->select('*');
        $this->db->from('policies');
        $this->db->where('is_deleted', '0');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getPolicyById($id) {
        $this->db->select('*');
        $this->db->from('policies');
        $this->db->where('id', $id);
        $this->db->where('is_deleted', '0');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->row();
    }

}
