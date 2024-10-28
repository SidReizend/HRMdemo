<?php

Class TalentAttendenceSheet extends CI_Model {

    function getCheckTalentsDateDataExist($talent, $dateOfAttendence) {
        $this->db->select('*');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $dateOfAttendence);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    function getTalentAttendanceForDate($talent, $dateOfAttendence) {
        $this->db->select('*');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $dateOfAttendence);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    function getTalentAttendanceDate($talent, $dateOfAttendence) {
        $this->db->select('*');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $dateOfAttendence);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row;
        } else {
            return FALSE;
        }
    }

    function getNumberOfWorkingDaysTalent($talent, $fromDate, $toDate) {
        $this->db->select('count(*) as count');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('avg_consider', 1);
        $this->db->where('attendance_date>=', $fromDate);
        $this->db->where('attendance_date<=', $toDate);
        $query = $this->db->get();
//        echo $this->db->last_query();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->count;
        } else {
            return 0;
        }
    }

    function getDayCompOffStatus($talent, $dateOfAttendence) {
        $this->db->select('holiday_hr_status');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $dateOfAttendence);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getMonths() {
        $this->db->select('ml.id,ml.month');
        $this->db->from('master_months ml');
        $this->db->order_by('ml.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     *  controler:talentMonthlyReport
     */

    function getMonthYearReport($dateFrom, $dateTo, $talentId) {
        $this->db->select('ta.id,ta.attendance_date,ta.actual_hours_spend,in_out_log,'
                . 'ta.od_hours,ta.productive_hrs,ta.travel_time,ta.extra_attendance_hrs,encashment,'
                . 'ta.extra_od_hrs,ta.is_holiday,ta.is_leave,ta.avg_consider,ta.talent_id,'
                . ' date_format(ta.attendance_date,"%d/%m/%Y") as attendance_date_formatted,'
                . 'date_format(ta.attendance_date,"[%a]") as day,holiday_hr_status');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->where('attendance_date BETWEEN "' . date('Y-m-d', strtotime($dateFrom)) . '" and "' . date('Y-m-d', strtotime($dateTo)) . '"');
        $this->db->where('attendance_date >= t.date_of_joining');
        $this->db->where('talent_id', $talentId);
        $this->db->order_by('attendance_date', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler : talentAttendance
     */

    function getTalentLog($logId) {
        $this->db->select('in_out_log,attendance_date,actual_hours_spend,productive_hrs,od_hours,avg_consider');
        $this->db->from('talent_attendance');
        $this->db->where('id', $logId);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentIdUsingAttendanceId($id) {
        $this->db->select('talent_id,attendance_date');
        $this->db->from('talent_attendance');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCompOffForApproval($reporterId) {
        $this->db->select('ta.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->where("(ta.is_holiday='1' or ta.is_leave='1')");
        $this->db->where("(ta.actual_hours_spend !=0 or ta.od_hours !='0')");
        $this->db->where('t.reporting_manager', $reporterId);
        $this->db->order_by('holiday_hr_status', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler :compOffManagement
     */

    public function getCompOffForApprovalFinYearBased($reporterId, $year, $isResigned, $selectedTalent) {
        $this->db->select('ta.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->where('((YEAR(ta.attendance_date) = ' . ($year) . ' AND MONTH(ta.attendance_date) > 3) '
                . 'OR (YEAR(ta.attendance_date) = ' . ($year + 1) . ' AND MONTH(ta.attendance_date) < 4)) ');
        $this->db->where("(ta.is_holiday='1' or ta.is_leave='1')");
        $this->db->where('t.is_resigned', $isResigned);
        $this->db->where("(ta.actual_hours_spend !=0 or ta.od_hours !='0')");
        $this->db->where("ta.avg_consider", 1);
        $this->db->where('t.reporting_manager', $reporterId);
        if ($selectedTalent) {
            $this->db->where('t.id', $selectedTalent);
        }
        $this->db->order_by('holiday_hr_status asc,ta.attendance_date desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCompOffForApprovalFinYearBasedAll($reporterId, $year, $isResigned, $isMangement, $selectedTalent) {
        $managementRoleStream = $this->defaultValues->getValue(28);
        $this->db->select('ta.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->where('((YEAR(ta.attendance_date) = ' . ($year) . ' AND MONTH(ta.attendance_date) > 3) '
                . 'OR (YEAR(ta.attendance_date) = ' . ($year + 1) . ' AND MONTH(ta.attendance_date) < 4)) ');
        $this->db->where("(ta.is_holiday='1' or ta.is_leave='1')");
        $this->db->where('t.is_resigned', $isResigned);
        $this->db->where('r.role_stream !=', $managementRoleStream);
        if ($selectedTalent) {
            $this->db->where('t.id', $selectedTalent);
        }
        $this->db->where("ta.avg_consider", 1);
        if (1 != $isMangement)
            $this->db->where('t.reporting_manager', $reporterId);
        $this->db->order_by('holiday_hr_status asc,ta.attendance_date desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getEncashmentForApproval($reporterId) {
        $query = $this->db->query("SELECT ta.*,concat(t.first_name,' ',t.middle_name,' ',t.last_name) as name FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             WHERE t.reporting_manager='$reporterId'
                 AND (((ta.encashment=0 OR ta.encashment=2) AND ta.productive_hrs >='13:30:00') OR ta.encashment=1)
             ORDER BY ta.attendance_date DESC");

        return $query->result_array();
    }

    public function getEncashmentForApprovalFinYearBased($reporterId, $year, $isResigned, $selectedTalent) {
        $sql = "SELECT ta.*,concat(t.first_name,' ',t.middle_name,' ',t.last_name) as name FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             WHERE t.reporting_manager='$reporterId' AND t.is_resigned='$isResigned' AND ((YEAR(ta.attendance_date) = ($year) AND MONTH(ta.attendance_date) > 3) "
                . "OR (YEAR(ta.attendance_date) = ($year+1) AND MONTH(ta.attendance_date) < 4))
                 AND (((ta.encashment=0 OR ta.encashment=2) AND ta.productive_hrs >='13:30:00') OR ta.encashment=1)";
        if ($selectedTalent) {
            $sql .= " AND t.id= '$selectedTalent'";
        }
        $sql .= " ORDER BY encashment asc,ta.attendance_date DESC";
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    public function getEncashmentForApprovalFinYearBasedALL($reporterId, $year, $isResigned, $isManagement, $selectedTalent) {
        $managementRoleStream = $this->defaultValues->getValue(28);
        $sql = "SELECT ta.*,concat(t.first_name,' ',t.middle_name,' ',t.last_name) as name FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             LEFT JOIN role r ON (t.role=r.id)
             WHERE t.is_resigned='$isResigned' AND ((YEAR(ta.attendance_date) = ($year) AND MONTH(ta.attendance_date) > 3) "
                . "OR (YEAR(ta.attendance_date) = ($year+1) AND MONTH(ta.attendance_date) < 4))
                 AND (((ta.encashment=0 OR ta.encashment=2) AND ta.productive_hrs >='13:30:00') OR ta.encashment=1)
                 AND r.role_stream!= '$managementRoleStream'
             ";
        if ($selectedTalent) {
            $sql .= " AND t.id= '$selectedTalent'";
        }
        if (1 != $isManagement)
            $sql .= " AND t.reporting_manager='$reporterId' ";
        $sql .= "ORDER BY encashment asc,ta.attendance_date DESC";
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    public function hourToSecondsConvertion($str_time) {
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        return($time_seconds);
    }
    public function monthlyPlanhourToSecondsConvertion($str_time) {
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds =  $hours * 3600 + $minutes * 60 ;
        return($time_seconds);
    }

    public function addingTime($time1, $time2) {
        $time = "06:58:00";
        $time2 = "00:40:00";

        $secs = strtotime($time2) - strtotime("00:00:00");
        $result = date("H:i:s", strtotime($time) + $secs);
        return($result);
    }

    /*
     * quarterly report
     */

    public function getAllMonthYearReportOfTalent($month, $year, $talent) {
        $this->db->select('tmr.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,mm.month as month_words');
        $this->db->from('talent_monthly_report tmr');
        $this->db->join('talents t', 'tmr.talent_id=t.id', 'left');
        $this->db->join('master_months mm', 'tmr.month=mm.id', 'left');
        $this->db->where('tmr.month', $month);
        $this->db->where('year', $year);
        $this->db->where('tmr.talent_id', $talent);
        $this->db->order_by('t.first_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getAllMonthReport($month, $year) {
        $this->db->select('tmr.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,t.is_resigned,mm.month as month_words');
        $this->db->from('talent_monthly_report tmr');
        $this->db->join('talents t', 'tmr.talent_id=t.id', 'left');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->join('role_stream rs', 'rs.id=r.role_stream', 'left');
        $this->db->where('show_in_reports', 1);
        $this->db->join('master_months mm', 'tmr.month=mm.id', 'left');
        $this->db->where('tmr.month', $month);
        $this->db->where('year', $year);
        $this->db->order_by('t.first_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getAllMonthYearReport($month, $year) {
        $this->db->select('tmr.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,mm.month as month_words');
        $this->db->from('talent_monthly_report tmr');
        $this->db->join('talents t', 'tmr.talent_id=t.id', 'left');
        $this->db->join('master_months mm', 'tmr.month=mm.id', 'left');
        $this->db->where('tmr.month', $month);
        $this->db->where('year', $year);
        $this->db->order_by('t.first_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getAllYearReport($year, $talent_id) {

        $this->db->select('tyr.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talent_yearly_report tyr');
        $this->db->join('talents t', 'tyr.talent=t.id', 'left');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->join('role_stream rs', 'rs.id=r.role_stream', 'left');
        $this->db->where('show_in_reports', 1);
        $this->db->where('year', $year);
        if (!null == $talent_id) {
            $this->db->where('t.id', $talent_id);
        }
        $this->db->order_by('t.first_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler : AttendenceSheetUpload
     */

    public function getUploadedData() {
        $this->db->select('tas.*,u.username');
        $this->db->from('talents_attendance_sheet tas');
        $this->db->join('users u', 'tas.uploaded_by_user_id=u.id', 'left');
        $this->db->order_by('tas.attendance_date', 'DESC');
        $this->db->limit(31);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler : AttendenceSheetUpload
     */

    public function detailOfAttendance($attendanceId) {
        $this->db->select('ta.actual_hours_spend,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents_attendance_sheet tas', 'ta.attendance_date=tas.attendance_date', 'left');
        $this->db->join('talents t', 't.id=ta.talent_id', 'left');
        $this->db->where('tas.id', $attendanceId);
        $this->db->order_by('t.first_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler : AttendenceSheetUpload
     */

    public function getGrandTotalHours($dateFrom, $dateTo, $talent_id) {
        $this->db->select('SEC_TO_TIME(sum(time_to_sec(actual_hours_spend))) as act_hrs_sum, SEC_TO_TIME(sum(time_to_sec(od_hours))) as od_hrs_sum');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->where('attendance_date BETWEEN "' . date('Y-m-d', strtotime($dateFrom)) . '" and "' . date('Y-m-d', strtotime($dateTo)) . '"');
        $this->db->where('attendance_date >= t.date_of_joining');
//        $this->db->where('avg_consider!=1');
        $this->db->where('talent_id', $talent_id);
        $query = $this->db->get();
        $actual_time_array = [];
        $nonPrdArray = $query->result_array();
        foreach ($nonPrdArray as $nonPrdArray) {
            $actual_time_array[] = $nonPrdArray['act_hrs_sum'];
            $actual_time_array[] = $nonPrdArray['od_hrs_sum'];
        }
        return $actual_time_array;
    }

    /*
     * controler : attendanceSheetUpload
     */

    function lastAttendanceUploadDate() {
        $this->db->select('tas.attendance_date');
        $this->db->from('talents_attendance_sheet tas');
        $this->db->order_by('tas.attendance_date', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row()->attendance_date;
    }

    function getLastDate() {
        $this->db->select('m.last_date');
        $this->db->from('monthly_process_status m');
        $this->db->limit(1);
        $this->db->order_by('m.id', 'DESC');
        $query = $this->db->get();
        return $query->row();
    }

    function getAllFinYear() {
        $this->db->select('*');
        $this->db->from('master_financial_year');
        $query = $this->db->get();
        return $query->result();
    }

    function updateMasterQuarters() {
        $query = $this->db->query("UPDATE master_quarters SET start_month = DATE_ADD(start_month, INTERVAL 1 YEAR), end_month = DATE_ADD(end_month, INTERVAL 1 YEAR) ");
        return;
    }

    public function getCountCompWaiting($reporterId) {
        $this->db->select('ta.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->where("(ta.is_holiday='1' or ta.is_leave='1')");
        $this->db->where("(ta.actual_hours_spend !=0 or ta.od_hours !='0')");
        $this->db->where('ta.holiday_hr_status', 0);
        $this->db->where('ta.avg_consider', 1);
        $this->db->where('t.reporting_manager', $reporterId);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountCompWaitingTalent($talentId) {
        $this->db->select('ta.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->where("(ta.is_holiday='1' or ta.is_leave='1')");
        $this->db->where("(ta.actual_hours_spend !=0 or ta.od_hours !='0')");
        $this->db->where('ta.avg_consider', 1);
        $this->db->where('ta.holiday_hr_status', 0);
        $this->db->where('t.id', $talentId);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getCountencashmentWaiting($reporterId) {
        $query = $this->db->query("SELECT ta.id FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             WHERE t.reporting_manager='$reporterId'
                 AND ta.encashment=0 AND ta.productive_hrs >='13:30:00'
             ORDER BY ta.attendance_date DESC");
        return $query->num_rows();
    }

    public function getCountencashmentWaitingTalent($talentId) {
        $query = $this->db->query("SELECT ta.id FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             WHERE t.id='$talentId'
                 AND ta.encashment=0 AND ta.productive_hrs >='13:30:00'
             ORDER BY ta.attendance_date DESC");
        if ($query->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function dateUploadCheck($dateOfAttendence) {
        $query = $this->db->query("SELECT `attendance_date` FROM `talents_attendance_sheet`"
                . " WHERE `attendance_date`='" . $dateOfAttendence . "'");
        if ($query->num_rows()) {
            return true;
        } else {
            $query = $this->db->query("SELECT `attendance_date` FROM `talents_attendance_sheet`");
            if ($query->num_rows()) {
                return false;
            } else {
                return TRUE;
            }
        }
    }

    public function dateUploadCheckWithoutSingleAttendanceSheet($dateOfAttendence) {
        $query = $this->db->query("SELECT `attendance_date` FROM `talents_attendance_sheet`"
                . " WHERE `attendance_date`='" . $dateOfAttendence . "'");
        if ($query->num_rows()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * model: Talent Attendance
     */

    public function secondsToMinitues($inputSeconds) {
        $hours = floor($inputSeconds / 3600);
        $minutes = floor(($inputSeconds / 60) % 60);
        if ($minutes < 10) {
            $minutes = '0' . $minutes;
        }
        $seconds = $inputSeconds % 60;
        if ($seconds < 10) {
            $seconds = '0' . $seconds;
        }
        if ($hours < 10) {
            $hours = '0' . $hours;
        }
        $inhour = $hours . ':' . $minutes . ':' . $seconds;
        return $inhour;
    }

    public function getCompApprovalPending() {
        $roleStream = $this->defaultValues->getValue(28);
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,concat(tr.first_name," ",tr.middle_name," ",tr.last_name) as tr_name,date_format(ta.attendance_date,"%d/%m/%Y") as attendance_date_f');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->join('talents tr', 'tr.id=t.reporting_manager', 'left');
        $this->db->where("(ta.is_holiday='1' or ta.is_leave='1')");
        $this->db->where("(ta.actual_hours_spend !=0 or ta.od_hours !='0')");
        $this->db->where('ta.holiday_hr_status', 0);
        $this->db->where('ta.avg_consider', 1);
        $this->db->where("r.role_stream != '$roleStream'");
        $query = $this->db->get();
        return $query->result();
    }

    public function getEncashmentApprovalPending() {
        $roleStream = $this->defaultValues->getValue(28);
        $query = $this->db->query("SELECT concat(t.first_name,' ',t.middle_name,' ',t.last_name) as name,concat(tr.first_name,' ',tr.middle_name,' ',tr.last_name) as tr_name,productive_hrs,date_format(ta.attendance_date,'%d/%m/%Y') as attendance_date_f FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             LEFT JOIN role r ON (t.role=r.id)
             LEFT JOIN talents tr ON (tr.id=t.reporting_manager)
             WHERE ta.encashment=0 AND r.role_stream!='$roleStream' AND ta.productive_hrs >='13:30:00' AND t.reporting_manager>0
             ORDER BY ta.attendance_date DESC");
        return $query->result();
    }

    /*
     * controler : home
     */

    public function getLatestUploadedDate($limit = null) {
        $limit_actual = null === $limit ? 1 : $limit;
        $this->db->select('attendance_date');
        $this->db->from('talents_attendance_sheet ts');
        $this->db->order_by('ts.attendance_date', 'DESC');
        $this->db->limit($limit_actual);
        $query = $this->db->get();
        return $query->result();
    }

    public function getAbscondingList($date_limit) {

        $latest_uploaded_date = $this->getLatestUploadedDate($date_limit);

        $uploaded_date_string = "";

        foreach ($latest_uploaded_date as $l_up_dt) {
            $uploaded_date_string .= $uploaded_date_string == "" ? '"' . $l_up_dt->attendance_date . '"' : ' ,  "' . $l_up_dt->attendance_date . '"';
        }
        $this->db->select('t.talent_id,ts.first_name,ts.middle_name,ts.last_name,tr.first_name as r_first_name,tr.middle_name as r_middle_name,tr.last_name as r_last_name');
        $this->db->from('talent_attendance t');
        $this->db->join('talents ts', 't.talent_id=ts.id', 'left');
        $this->db->join('talents tr', 'ts.reporting_manager=tr.id', 'left');
        $this->db->where('t.is_holiday', 0);
        $this->db->where('t.is_leave', 0);
        $this->db->where('t.avg_consider', 0);
        $this->db->where('t.attendance_date in (' . $uploaded_date_string . ')');
        $this->db->group_by('t.talent_id');
        $this->db->having('count(t.id)>=2');
        $this->db->order_by('t.attendance_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    function getDateDetails($talent, $dateOfAttendence) {
        $this->db->select('*');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $dateOfAttendence);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    function getEditDeatils($attendanceId) {
        $this->db->select('au.old_actual,au.updated_on,au.old_productive,first_name');
        $this->db->from('attendance_update au');
        $this->db->join('talents t', 't.id=au.updated_by', 'left');
        $this->db->where('attendance_id', $attendanceId);
        $this->db->order_by('au.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    function reportOfTheQuarter($quarters, $year, $talentId) {
        $monthYearReport1 = array();
        $monthYearReport2 = array();
        $monthYearReport3 = array();
        $monthYearReport4 = array();
        $monthYearReport5 = array();
        $monthYearReport6 = array();
        $monthYearReport7 = array();
        $monthYearReport8 = array();
        $monthYearReport9 = array();
        $monthYearReport10 = array();
        $monthYearReport11 = array();
        $monthYearReport12 = array();
        $previousReport = array(
            'name' => '',
            'lop_by_process' => '0',
            'hrs_required' => '00:00:00',
            'hrs_earned' => '00:00:00',
            'hrs_shortage' => '00:00:00',
            'total_extra_hours' => '00:00:00',
            'lop_by_shortage' => '0',
            'encashment_for_extra' => '0',
            'total_present_days' => '0',
            'total_lop' => '0',
            'total_encashment' => '0',
        );
        foreach ($quarters as $quarter) {
            if ($quarter == 1) {
                $result1 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(4, $year, $talentId);
                $result2 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(5, $year, $talentId);
                $result3 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(6, $year, $talentId);
                if (!empty($result1)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result1);
                }
                if (!empty($result2)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result2);
                }
                if (!empty($result3)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result3);
                }
            }
            if ($quarter == 2) {
                $result1 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(7, $year, $talentId);
                $result2 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(8, $year, $talentId);
                $result3 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(9, $year, $talentId);
                if (!empty($result1)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result1);
                }
                if (!empty($result2)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result2);
                }
                if (!empty($result3)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result3);
                }
            }
            if ($quarter == 3) {
                $result1 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(10, $year, $talentId);
                $result2 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(11, $year, $talentId);
                $result3 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(12, $year, $talentId);
                if (!empty($result1)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result1);
                }
                if (!empty($result2)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result2);
                }
                if (!empty($result3)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result3);
                }
            }
            if ($quarter == 4) {
                $result1 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(1, ($year + 1), $talentId);
                $result2 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(2, ($year + 1), $talentId);
                $result3 = $this->talentAttendenceSheet->getAllMonthYearReportOfTalent(3, ($year + 1), $talentId);
                if (!empty($result1)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result1);
                }
                if (!empty($result2)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result2);
                }
                if (!empty($result3)) {
                    $previousReport = $this->talentAttendenceSheet->getMonthReport($previousReport, $result3);
                }
            }
        }
        return $previousReport;
    }

    function getMonthReport($finalResult, $monthYearReports) {
        foreach ($monthYearReports as $monthYearReport) {
            $finalResult['name'] = $monthYearReport['name'];
            $finalResult['lop_by_process'] = $finalResult['lop_by_process'] + $monthYearReport['lop_by_process'];
            $finalResult['hrs_required'] = $this->talentAttendenceSheet->sumofHours($monthYearReport['hrs_required'], $finalResult['hrs_required']);
            $finalResult['hrs_earned'] = $this->talentAttendenceSheet->sumofHours($monthYearReport['hrs_earned'], $finalResult['hrs_earned']);
            $finalResult['hrs_shortage'] = $this->talentAttendenceSheet->sumofHours($monthYearReport['hrs_shortage'], $finalResult['hrs_shortage']);
            $finalResult['total_extra_hours'] = $this->talentAttendenceSheet->sumofHours($monthYearReport['total_extra_hours'], $finalResult['total_extra_hours']);
            $finalResult['lop_by_shortage'] += $monthYearReport['lop_by_shortage'];
            $finalResult['encashment_for_extra'] += $monthYearReport['encashment_for_extra'];
            $finalResult['total_lop'] += ($monthYearReport['lop_by_shortage'] + $monthYearReport['lop_by_process'] + $monthYearReport['lop_due_to_lop']);
            $finalResult['total_encashment'] += ($monthYearReport['encashment_for_extra'] + $monthYearReport['encashment']);
        }
        return $finalResult;
    }

    function sumofHours($hour1, $hour2) {
        $sec1 = $this->talentAttendenceSheet->hourToSecondsConvertion($hour1);
        $sec2 = $this->talentAttendenceSheet->hourToSecondsConvertion($hour2);
        $sumSec = $sec1 + $sec2;
        $resultHour = $this->talentAttendenceSheet->secondsToMinitues($sumSec);
        return $resultHour;
    }

    function startTime() {
        $this->db->select('value');
        $this->db->from('default_values');
        $this->db->where('id', 7);
        $query = $this->db->get();
        $row = $query->row();
        return $row->value;
    }

    function endTime() {
        $this->db->select('value');
        $this->db->from('default_values');
        $this->db->where('id', 8);
        $query = $this->db->get();
        $row = $query->row();
        return $row->value;
    }

}
