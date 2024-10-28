<?php

Class LeaveApprovals extends CI_Model {

    function getLeaveRequest($talent) {
        $this->db->select('tl.*,t.first_name,count(tld.id) as number');
        $this->db->from('talent_leave tl ');
        $this->db->join('talents t ', 't.id=tl.talent', 'inner');
        $this->db->join('talent_leave_date tld', 'tl.id=tld.talent_leave', 'left');
        $this->db->where('t.reporting_manager', $talent);
        $this->db->where('tl.is_approved !=', 3);
        $this->db->group_by('tl.`id`');
        $this->db->order_by('tl.is_approved', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler:leaveApprval
     */

    function getLeaveRequestFinYearBased($talent, $year, $isResigned, $selectedTalent) {
        $this->db->select('tl.*,concat(first_name," ",middle_name," ",last_name) as first_name,count(tld.id) as number');
        $this->db->from('talent_leave tl ');
        $this->db->join('talents t ', 't.id=tl.talent', 'inner');
        $this->db->join('talent_leave_date tld', 'tl.id=tld.talent_leave', 'left');
        $this->db->where('t.is_resigned', $isResigned);
        $this->db->where('((YEAR(tl.from_date) = ' . ($year) . ' AND MONTH(tl.from_date) > 3) '
                . 'OR (YEAR(tl.from_date) = ' . ($year + 1) . ' AND MONTH(tl.from_date) < 4)) ');
        $this->db->where('t.reporting_manager', $talent);
        if ($selectedTalent) {
            $this->db->where('t.id', $selectedTalent);
        }
        $this->db->where('tl.is_approved !=', 3);
        $this->db->group_by('tl.`id`');
        $this->db->order_by('tl.is_approved asc,tl.from_date DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler:leaveApprval
     */

    function getLeaveRequestFinYearBasedAll($talent, $year, $isResigned, $isManagement, $selectedTalent) {
        $this->db->select('tl.*,concat(first_name," ",middle_name," ",last_name) as first_name,count(tld.id) as number');
        $this->db->from('talent_leave tl ');
        $this->db->join('talents t ', 't.id=tl.talent', 'left');
        $this->db->join('talent_leave_date tld', 'tl.id=tld.talent_leave', 'left');
        $this->db->where('t.is_resigned', $isResigned);
        if (1 != $isManagement)
            $this->db->where('t.reporting_manager', $talent);
        if ($selectedTalent) {
            $this->db->where('t.id', $selectedTalent);
        }
        $this->db->where('((YEAR(tl.from_date) = ' . ($year) . ' AND MONTH(tl.from_date) > 3) '
                . 'OR (YEAR(tl.from_date) = ' . ($year + 1) . ' AND MONTH(tl.from_date) < 4)) ');
        $this->db->where('tl.is_approved !=', 3);
        $this->db->group_by('tl.`id`');
        $this->db->order_by('tl.is_approved asc,tl.from_date DESC');
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }

    function gettalent($talent_leave_id) {
        $this->db->select('talent');
        $this->db->from('talent_leave');
        $this->db->where('id', $talent_leave_id);
        $query = $this->db->get();
        $ret = $query->row();
        return $ret->talent;
    }

    function getdatetalent($talent_leave_id) {
        $this->db->select('tl.talent,tld.date_of_leave');
        $this->db->from('talent_leave_date tld');
        $this->db->join('talent_leave tl ', 'tld.talent_leave=tl.id', 'inner');
        $this->db->where('tld.talent_leave', $talent_leave_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getactualhours($talent, $date) {
        $this->db->select('productive_hrs');
        $this->db->from('talent_attendance');
        $this->db->where('attendance_date', $date);
        $this->db->where('talent_id', $talent);
        $query = $this->db->get();
        $ret = $query->row();
        if ($ret == "") {
            $res = "00:00:00";
        } else {
            $res = $ret->productive_hrs;
        }
        return $res;
    }

    function getalldataleave($id) {
        $this->db->select('*');
        $this->db->from('talent_leave');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getLeaveDayStatus($id) {
        $this->db->select('t.date_of_leave,m.type,t.leave_type');
        $this->db->from('talent_leave_date t');
        $this->db->join('master_leaves m', 'm.id=t.leave_type', 'left');
        $this->db->where('t.talent_leave', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function checktalentlop($talent_id) {
        $this->db->select('count(id) as numb');
        $this->db->from('talent_lop');
        $this->db->where('talent', $talent_id);
        $query = $this->db->get();
        $ret = $query->row();
        return $ret->numb;
    }

    /*
     * used in
     * controller : home
     */

    function getCountLeaveDueToAbsentReportingOfficer($talent) {
        $minimumAttendanceForFullDay = $this->defaultValues->getValue(30);
        $managementRoleStream = $this->defaultValues->getValue(28);
        $this->db->select("ta.id,ta.talent_id,ta.attendance_date,ta.actual_hours_spend,ta.od_hours,"
                . "ta.extra_attendance_hrs,ta.extra_od_hrs,ta.productive_hrs,t.first_name,"
                . "ta.is_leave,ta.absent_raise_status");
        $this->db->from("talent_attendance ta");
        $this->db->join('talents t ', 't.id=ta.talent_id', 'left');
        $this->db->join('role r ', 'r.id=t.role', 'left');
        $this->db->where('t.is_resigned', 0);
        $this->db->where('r.role_stream !=', $managementRoleStream);
        $this->db->where('ta.is_holiday', 0);
        $this->db->where('ta.is_leave', 0);
        $this->db->where('ta.absent_raise_status', 0);
        $this->db->where('t.reporting_manager', $talent);
        $this->db->where('productive_hrs<', $minimumAttendanceForFullDay);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*
     * used in
     * controller : LeaveApproval
     */

    function getLeaveDueToAbsentReportingOfficer($talent, $year, $isResigned, $selectedTalent) {
        $minimumAttendanceForFullDay = $this->defaultValues->getValue(30);
        $managementRoleStream = $this->defaultValues->getValue(28);
        $this->db->select("ta.id,ta.talent_id,ta.attendance_date,ta.actual_hours_spend,ta.od_hours,"
                . "ta.extra_attendance_hrs,ta.extra_od_hrs,ta.productive_hrs,t.first_name,"
                . "ta.is_leave,ta.absent_raise_status");
        $this->db->from("talent_attendance ta");
        $this->db->join('talents t ', 't.id=ta.talent_id', 'left');
        $this->db->join('role r ', 'r.id=t.role', 'left');
        $this->db->where('t.is_resigned', $isResigned);
        $this->db->where('r.role_stream !=', $managementRoleStream);
        $this->db->where('ta.is_holiday', 0);
        $this->db->where('ta.is_leave', 0);
        $this->db->where('ta.absent_raise_status', 0);
        $this->db->where('t.reporting_manager', $talent);
        if ($selectedTalent) {
            $this->db->where('t.id', $selectedTalent);
        }
        $this->db->where('productive_hrs<', $minimumAttendanceForFullDay);
        $this->db->where('((YEAR(ta.attendance_date) = ' . ($year) . ' AND MONTH(ta.attendance_date) > 3) '
                . 'OR (YEAR(ta.attendance_date) = ' . ($year + 1) . ' AND MONTH(ta.attendance_date) < 4)) ');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * used in
     * controller : LeaveApproval
     */

    function getLeaveDueToAbsent($talent, $year, $isResigned, $isManagement, $selectedTalent) {
        $minimumAttendanceForFullDay = $this->defaultValues->getValue(30);
        $managementRoleStream = $this->defaultValues->getValue(28);
        $this->db->select("ta.id,ta.talent_id,ta.attendance_date,ta.actual_hours_spend,ta.od_hours,"
                . "ta.extra_attendance_hrs,ta.extra_od_hrs,ta.productive_hrs,t.first_name,"
                . "ta.is_leave,ta.absent_raise_status");
        $this->db->from("talent_attendance ta");
        $this->db->join('talents t ', 't.id=ta.talent_id', 'left');
        $this->db->join('role r ', 'r.id=t.role', 'left');
        $this->db->where('t.is_resigned', $isResigned);
        $this->db->where('r.role_stream !=', $managementRoleStream);
        $this->db->where('ta.is_holiday', 0);
        $this->db->where('ta.is_leave', 0);
        $this->db->where('ta.absent_raise_status', 0);
        if (1 != $isManagement)
            $this->db->where('t.reporting_manager', $talent);
        if ($selectedTalent) {
            $this->db->where('t.id', $selectedTalent);
        }
        $this->db->where('productive_hrs<', $minimumAttendanceForFullDay);
        $this->db->where('((YEAR(ta.attendance_date) = ' . ($year) . ' AND MONTH(ta.attendance_date) > 3) '
                . 'OR (YEAR(ta.attendance_date) = ' . ($year + 1) . ' AND MONTH(ta.attendance_date) < 4)) ');
        $query = $this->db->get();
        return $query->result_array();
    }

}
