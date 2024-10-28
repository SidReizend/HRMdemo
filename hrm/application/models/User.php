<?php

Class User extends CI_Model {

    function login($username, $password) {
        $this->db->select('id, username, password,force_password_change,role');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->where('password', MD5($password));
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }
    function passwordCheck($id) {
        $this->db->select('password');
        $this->db->from('users');
        $this->db->where('id', $id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    function getUserByTaletId($talent) {
        $this->db->select('id, username, password,is_enabled,is_resigned');
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getUserByUserId($id) {
        $this->db->select('id, username, password,is_enabled');
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function is_enabled($user_id) {
        $this->db->select('is_enabled');
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $this->db->limit(1);

        $query = $this->db->get();

        $result = $query->result();
        return isset($result[0]) ? $result[0] : 0;
    }

    function getUsername($username) {
        $this->db->select('id, username, password');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    function getUsernameUserId($username, $userId) {
        $this->db->select('id, username, password');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->where('id  != ', $userId);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    function getUserPasswordChangeDetails($username) {
        $this->db->select('id, username, email,password_change_token, p_c_token_used, p_c_token_expire_date');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }
    function getDetailsByPasswordChangeKey($key) {
        $this->db->select('id, username, email,password_change_token, p_c_token_used, p_c_token_expire_date');
        $this->db->from('users');
        $this->db->where('password_change_token', $key);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }
    function getLocalIp(){
        $this->db->select('value');
        $this->db->from('default_values');
        $this->db->where('id', 9);
        $query = $this->db->get();
        return $query->row();
       
    }

}
