<?php

Class LoginLogs extends CI_Model {

    function getRecentLogins($user) {
        $this->db->select('date_format(logged_in_at,"%d/%m/%Y %r") as logged_in_time, ip, platform, browser, version, mobile, robot, referer');
        $this->db->from('login_logs');
        $this->db->where('user', $user);
        $this->db->order_by('logged_in_at', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }

}
