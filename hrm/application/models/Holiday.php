<?php

Class Holiday extends CI_Model {

    function getHolidays() {
        $this->db->select('*');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
        $this->db->order_by('date_of_holiday', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentAllowedToApply($holiday) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as added_by,pt.created_at');
        $this->db->from('public_holiday_working_talents pt');
        $this->db->join('talents t', 't.id=pt.talent', 'left');
        $this->db->join('talents ta', 'ta.id=pt.added_by', 'left');
        $this->db->where('pt.is_deleted', '0');
        $this->db->where('pt.holiday', $holiday);
        $this->db->order_by('pt.created_at', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getHolidayById($id) {
        $this->db->select('*');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getHolidayByDay($dateOfHoliday, $id) {
        $this->db->select('*');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
        $this->db->where('date_of_holiday', $dateOfHoliday);
        $this->db->where('id !=', $id);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    function getHolidayId($date) {
        $this->db->select('*');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
        $this->db->where('date_of_holiday', $date);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row()->id;
        } else {
            return false;
        }
    }

    function checkPermissionGiven($id, $talent) {
        $this->db->select('*');
        $this->db->from('public_holiday_working_talents');
        $this->db->where('is_deleted', '0');
        $this->db->where('holiday', $id);
        $this->db->where('talent', $talent);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row()->id;
        } else {
            return false;
        }
    }

    function deleteHolidayById($id) {
        $updateData = array(
            'is_deleted' => '1'
        );
        $this->db->where('id', $id);
        $this->db->update('holidays', $updateData);
        return true;
    }

    function getHolidayStatus($fromDate, $toDate) {
        $this->db->select('*');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
//        $this->db->where_not_in('date_of_holiday', "SELECT date_to_work FROM strict_working_date");
        $this->db->where('date_of_holiday BETWEEN "' . date('Y-m-d', strtotime($fromDate)) . '" and "' . date('Y-m-d', strtotime($toDate)) . '"');
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkDateInStrictlyWorking($date) {
        $this->db->select('date_to_work');
        $this->db->from('strict_working_date');
        $this->db->where('date_to_work', date('Y-m-d', strtotime($date)));
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getHolidaysForDashboard() {
        $this->db->select('date_format(date_of_holiday,"%d/%m/%Y") as dt_holiday,name_of_holiday');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
        $this->db->where('date_of_holiday >= ', date('Y-m-d'));
        $this->db->order_by('date_of_holiday', 'asc');
        $this->db->limit(6);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getHolidaysInRange($from, $to) {
        $this->db->select('date_of_holiday,name_of_holiday');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
        $this->db->where('date_of_holiday >=', $from);
        $this->db->where('date_of_holiday <=', $to);
        $this->db->order_by('date_of_holiday', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

}
