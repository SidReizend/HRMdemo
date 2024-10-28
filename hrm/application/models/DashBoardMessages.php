<?php

Class DashBoardMessages extends CI_Model {

    function getDBMessage() {
        $this->db->select('message,show');
        $this->db->from('dashboard_messages');
        $this->db->where('id',1);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

}
