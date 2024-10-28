<?php

Class OdType extends CI_Model {

    function getOdTypes() {
        $this->db->select('*');
        $this->db->from('od_types');
        $this->db->where('is_deleted','0');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
    function getOdTypeById($id) {
        $this->db->select('*');
        $this->db->from('od_types');
        $this->db->where('is_deleted','0');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
        
    }
    function deleteOdTypeById($id) {
        $updateData = array(
            'is_deleted' => '1'
        );
        $this->db->where('id', $id);
        $this->db->update('od_types',$updateData);
        return true;
    }
}
