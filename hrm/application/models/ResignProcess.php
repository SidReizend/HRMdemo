<?php

Class ResignProcess extends CI_Model {

    function getResignedTalents($talent, $is_management) {

        $this->db->select('id,email,concat(first_name," ",middle_name," ",last_name) as name, id');
        $this->db->from('talents');
        if (false === $is_management) {
            $this->db->where('reporting_manager', $talent);
        }
        $this->db->where('is_resigned', 1);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function resignDetails($talent) {

        $this->db->select('r.*,t.date_of_resignation,t.date_of_joining');
        $this->db->from('talent_monthly_report_resigned r');
        $this->db->join('talents t', 't.id=r.talent_id', 'left');
        $this->db->where('r.talent_id', $talent);
        $this->db->order_by('r.id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            return $query->row();
        } else {
            $this->db->select('r.*,t.date_of_resignation,t.date_of_joining');
            $this->db->from('talent_monthly_report_resigned_temp r');
            $this->db->join('talents t', 't.id=r.talent_id', 'left');
            $this->db->where('r.talent_id', $talent);
            $this->db->order_by('r.id', 'desc');
            $query = $this->db->get();
            return $query->row();
        }
    }

    function sumClaimPending($talent) {

        $this->db->select('sum(amount) as total_claim');
        $this->db->from('claims');
        $this->db->where('talent', $talent);
        $this->db->where('status', 0);
        $query = $this->db->get();
        return $query->row();
    }

    function lastResignCalculator() {

        $this->db->select('r.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,');
        $this->db->from('resignationProcessCalculator r');
        $this->db->join('talents t', 't.id=r.talent', 'left');
        $this->db->order_by('id desc');
        $this->db->limit(0);
        $query = $this->db->get();
        return $query->row();
    }

    public function getMonthlyReport($month, $year, $talent) {
        $this->db->select('m.*');
        $this->db->from('talent_monthly_report m');
        $this->db->where('talent_id', $talent);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $query = $this->db->get();
        return $query->row();
    }

    public function getNoOfDaysPayInMonth($month, $year, $dateOfJoining) {
        $firstDateOfMonth = date('Y-m-d', strtotime($year . '-' . $month . '-1'));
        if (strtotime($dateOfJoining) < strtotime($firstDateOfMonth)) {
            return cal_days_in_month(CAL_GREGORIAN, $month, $year);
        } else {
            $lastDayOfMonth = date("Y-m-t", strtotime($dateOfJoining));
            $date1 = date_create($dateOfJoining);
            $date2 = date_create($lastDayOfMonth);
            $diff = date_diff($date1, $date2);
            return $diff->format("%a");
        }
    }

    public function getNoOfDaysPay($fromDate, $dateOfJoining, $dateOfResigning) {
        if (strtotime($dateOfJoining) < strtotime($fromDate)) {
            $date1 = date_create($fromDate);
            $date2 = date_create($dateOfResigning);
            $diff = date_diff($date1, $date2);
            return $diff->format("%a") + 1;
        } else {
            $date1 = date_create($dateOfJoining);
            $date2 = date_create($dateOfResigning);
            $diff = date_diff($date1, $date2);
            return $diff->format("%a") + 1;
        }
    }

}
