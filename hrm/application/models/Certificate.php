<?php

Class Certificate extends CI_Model {
    public function getCerificateTypes() {
        $this->db->select('*');
        $this->db->from('certificate_type');
        $this->db->order_by('type', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getMyCerificates($talent) {
        $this->db->select('c.*,ct.type as type_name');
        $this->db->from('certificates c');
        $this->db->join('certificate_type ct','c.type=ct.id','left');
        $this->db->where('c.talent', $talent);
        $this->db->order_by('c.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
}
