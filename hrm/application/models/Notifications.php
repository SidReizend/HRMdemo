<?php

Class Notifications extends CI_Model {

    function getNotificationByUser($user_id, $unread = false, $limit = null) {
        $this->db->select('type, message, date_format(message_time,"%d/%m/%Y %r") as message_time_formatted');
        $this->db->from('notifications');
        $this->db->where('user', $user_id);
        if (false !== $unread) {
            $this->db->where('read', 0);
        }
        $this->db->order_by('message_time', 'DESC');
        if (null !== $limit) {
            $this->db->limit($limit);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    function getUnreadNotification($user_id) {
        $this->db->select('count(id) as cnt');
        $this->db->from('notifications');
        $this->db->where('user', $user_id);
        $this->db->where('read', 0);
        $query = $this->db->get();
        return $query->row();
    }

}
