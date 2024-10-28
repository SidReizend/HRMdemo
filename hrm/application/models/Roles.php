<?php

Class Roles extends CI_Model {

    function isManagement($talent_id) {
        
    }

    function getRole() {
        $this->db->select('r.id,r.role_name,r.description,r.quarterly_earned_leaves,r.average_working_hour_per_day,rs.role_stream,r.average_hour_calculation');
        $this->db->from('role r');
        $this->db->join('role_stream rs', 'rs.id=r.role_stream', 'left');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getRoleById($id) {
        $this->db->select('*');
        $this->db->from('role');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getRoleDetailsById($id) {
        $this->db->select('rs.id as role_stream');
        $this->db->from('role r');
        $this->db->join('role_stream rs', 'rs.id=r.role_stream', 'left');
        $this->db->where('r.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function deleteRoleById($id) {
        $this->db->where('role', $id);
        $this->db->delete('role_holiday');
        $this->db->where('id', $id);
        $this->db->delete('role');
        return;
    }

    function getRoleStreams() {
        $this->db->select('*');
        $this->db->from('role_stream');
        $this->db->order_by('role_stream', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getRoleIdByStramId($stream_id) {
        $this->db->select('id');
        $this->db->from('role');
        $this->db->where('role_stream', $stream_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getEmailIdByRole($roll_arr) {
        $this->db->select('email');
        $this->db->from('talents');
        $this->db->where_in('role', $roll_arr);
        $query = $this->db->get();
        $res = $query->result_array();
        $data = array();

        foreach ($res as $value) {
            $data[] = $value['email'];
        }
        return $data;
    }

}
