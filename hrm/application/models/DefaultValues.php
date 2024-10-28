<?php

Class DefaultValues extends CI_Model {

    function getCurrentMonthStartEnd() {
        $this->db->select('id,value');
        $this->db->from('default_values');
        $this->db->where('id IN (3,4)');
        $query = $this->db->get();
        $result = $query->result_array();

        $return_array = array();

        foreach ($result as $res) {
            $id = $res['id'];
            $val = $res['value'];
            $return_array[$id] = $val;
        }
        return $return_array;
    }

    function getHrmEmail() {
        $this->db->select('value');
        $this->db->from('default_values');
        $this->db->where('id', 6);
        $query = $this->db->get()->row();
        $result = $query;
        return $result->value;
    }

    function getStartYear() {
        $this->db->select('value');
        $this->db->from('default_values');
        $this->db->where('id', 10);
        $query = $this->db->get()->row();
        $result = $query;
        return $result->value;
    }

    function getTalentAdminEmail() {
        $this->db->select('u.email');
        $this->db->from('users u');
        $this->db->where("u.role LIKE '%1%'");
        $query = $this->db->get();
        $result = $query->result_array();
        $emails = '';
        foreach ($result as $user) {
            if ($emails == '') {
                $emails = $user['email'];
            } else {
                $emails .= ',' . $user['email'];
            }
        }
        return $emails;
    }
    function getTalentAdminsIds() {
        $this->db->select('u.email,u.id');
        $this->db->from('users u');
        $this->db->where("u.role LIKE '%1%'");
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    function getValue($id) {
        $this->db->select('value');
        $this->db->from('default_values');
        $this->db->where('id', $id);
        $result = $this->db->get()->row();
        return $result->value;
    }

    function getAllDefaultValues() {
        $this->db->select('*');
        $this->db->from('default_values');
        $this->db->where('id in (1,6,7,8,9,14,16,19,20,21,22,23,24)');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

}
