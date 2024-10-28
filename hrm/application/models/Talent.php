<?php

Class Talent extends CI_Model {

    function getTalents() {
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 't.role,t.address,t.qualification,t.gender,t.dob,t.mobile,t.email,t.date_of_joining,'
                . 't.emergency_contact_name,t.emergency_contact_relationship,t.emergency_contact_relationship,'
                . 't.date_of_resignation,t.is_resigned,t.reporting_manager,r.role_name,timesheet_restriction');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->where('is_resigned', '0');
        $this->db->order_by('t.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAverageHourOfTalent() {
        
    }

    function getTalentsAll() {
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 't.role,t.address,t.qualification,t.gender,t.dob,t.mobile,t.email,t.date_of_joining,'
                . 't.emergency_contact_name,t.emergency_contact_relationship,t.emergency_contact_relationship,'
                . 't.date_of_resignation,t.is_resigned,t.reporting_manager,r.role_name,r.role_stream');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->order_by('t.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentsAllByResignStatus($status) {
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 't.role,t.address,t.qualification,t.gender,t.dob,t.mobile,t.email,t.date_of_joining,'
                . 't.emergency_contact_name,t.emergency_contact_relationship,t.emergency_contact_relationship,'
                . 't.date_of_resignation,t.is_resigned,t.reporting_manager,r.role_name,r.role_stream,r.average_working_hour_per_day');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->where('is_resigned', $status);
        $this->db->order_by('t.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getResignedTalentsDetails() {
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 't.role,t.address,t.qualification,t.gender,t.dob,t.mobile,t.email,t.date_of_joining,'
                . 't.emergency_contact_name,t.emergency_contact_relationship,t.emergency_contact_relationship,'
                . 't.date_of_resignation,t.is_resigned,t.reporting_manager,r.role_name');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->where('is_resigned', '1');
        $this->db->order_by('t.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getReportingManagers() {
        $this->db->select('id,concat(first_name," ",middle_name," ",last_name) as name');
        $this->db->from('talents');
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentById($id) {
        $this->db->select('t.id,t.first_name,t.middle_name,t.last_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 't.role,t.address,t.present_address,t.track,t.qualification,t.gender,t.dob,t.mobile,t.email,t.date_of_joining,'
                . 't.emergency_contact_name,t.emergency_contact_relationship,t.emergency_contact_relationship,'
                . 't.emergency_contact_number,t.date_of_resignation,t.reporting_manager,t.timesheet_manager,t.is_resigned,t.user,'
                . 'concat(r.role_name," ",r.description) as role_name,`average_working_hour_per_day`,`od_approval_auto`,autoTimeApproval');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->where('t.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentDetailsByTalentId($id) {
        $this->db->select('t.id,t.first_name,t.middle_name,t.last_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 't.role,t.address,t.present_address,t.qualification,t.gender,t.dob,t.mobile,t.email,t.date_of_joining,'
                . 't.emergency_contact_name,t.emergency_contact_relationship,t.emergency_contact_relationship,'
                . 't.emergency_contact_number,t.date_of_resignation,t.reporting_manager,t.is_resigned,t.user,'
                . 'concat(r.role_name," ",r.description) as role_name,r.role_stream,'
                . '`average_working_hour_per_day`,`od_approval_auto`,t.timesheet_restriction');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->where('t.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getTalentNameById($id) {

        $this->db->select('t.id,t.first_name,t.middle_name,t.last_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,');
        $this->db->from('talents t');

        $this->db->where('t.id', $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->name;
    }

    function is_resigned($user_id) {
        $this->db->select('is_resigned');
        $this->db->from('talents');
        $this->db->join('users', 'users.id=talents.user');
        $this->db->where('users.id', $user_id);
        $query = $this->db->get();
        $result = $query->result();

        return isset($result[0]) ? $result[0] : 0;
    }

    function getTalentByUserId($userId) {
        $this->db->select('t.id,t.first_name,t.middle_name,t.last_name,r.role_name,t.profile_image,rs.id as role_stream_id, rs.role_stream');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->join('role_stream rs', 'r.role_stream=rs.id', 'left');
        $this->db->where('user', $userId);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();
    }

    function getProfileImage($talentId) {
        $this->db->select('t.profile_image');
        $this->db->from('talents t');
        $this->db->where('t.id', $talentId);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    function getRolesAboveRole($role, $hierarchy) {
        $this->db->select('t.id,concat(first_name," ",middle_name," ",last_name) as name');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
//        $this->db->where('r.hierarchy > ', $hierarchy);
        $this->db->order_by('first_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getRolesAboveAndEqualRole($role, $hierarchy, $checkHierarchy = true) {
        $this->db->select('t.id,concat(first_name," ",middle_name," ",last_name) as name');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
        if ($checkHierarchy) {
            $this->db->where('r.hierarchy >= ', $hierarchy);
        }
        $this->db->order_by('first_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentCode() {
        $this->db->select('value');
        $this->db->from('default_values');
        $this->db->where('id', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentsCode() {
        $this->db->select('t.talent_code,t.id');
        $this->db->from('talents t');
        $this->db->where('is_resigned', 0);
        $this->db->order_by('t.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentsByTalentCode($talentCode) {
        $this->db->select('t.id,r.role_stream');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->where('talent_code', $talentCode);
        $this->db->where('is_resigned', 0);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    function getAverageHour($talentId) {
        $this->db->select('r.average_working_hour_per_day');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->where('t.id', $talentId);
        $query = $this->db->get();
        return $query->row();
    }

    function getTalentNearBirthday() {

        $query = $this->db->query("SELECT t.first_name,t.middle_name,t.last_name,t.profile_image,date_format(t.dob,'%D %b') as t_dob FROM talents t WHERE 
            DATE_ADD(dob, INTERVAL YEAR(CURDATE())-YEAR(dob) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(dob),1,0) YEAR)  
            BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY) AND `t`.`is_resigned`='0'
            ORDER BY STR_TO_DATE( dob, '%m/%d/%Y' ) DESC             
");

        return $query->result_array();
    }

    function getNewJoiners() {

        $query = $this->db->query("SELECT t.first_name,t.middle_name,t.last_name,t.profile_image,date_format(t.date_of_joining,'%D %b') as t_dob FROM talents t WHERE  
            date_of_joining BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 15 DAY) AND `t`.`is_resigned`='0'
            ORDER BY STR_TO_DATE( date_of_joining, '%m/%d/%Y' ) DESC             
");

        return $query->result_array();
    }

    function getTalentAnniversary() {
        $query = $this->db->query("SELECT t.first_name,t.middle_name,t.last_name,t.profile_image,date_format(t.date_of_joining,'%D %b') as t_doj, TIMESTAMPDIFF(DAY, date_of_joining, CURDATE()) AS t_years  FROM talents t WHERE  
           DATE_ADD(date_of_joining, 
                INTERVAL YEAR(CURDATE())-YEAR(date_of_joining)
                YEAR)  
            BETWEEN DATE_SUB(CURDATE(), INTERVAL 3 DAY) AND DATE_ADD(CURDATE(), INTERVAL 5 DAY) AND `t`.`is_resigned`='0'
            AND TIMESTAMPDIFF(YEAR, date_of_joining, CURDATE()) > 0
            ORDER BY STR_TO_DATE( date_of_joining, '%m/%d/%Y' ) DESC             
");

        return $query->result_array();
    }

    function isApprovalUser($talent) {
        $this->db->select('count(t.id) as cnt');
        $this->db->from('talents t');
        $this->db->where('t.reporting_manager', $talent);
        $query = $this->db->get();
        $result = $query->row();
        return $result->cnt > 0 ? true : false;
    }

    function getSubordinates($talent, $is_management) {

        $this->db->select('id,email,concat(first_name," ",middle_name," ",last_name) as name, id');
        $this->db->from('talents');
        if (false === $is_management) {
            $this->db->where('reporting_manager', $talent);
        }
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getSubordinatesByStatus($talent, $is_management, $isResigned) {

        $this->db->select('id,email,concat(first_name," ",middle_name," ",last_name) as name, id');
        $this->db->from('talents');
        if (false === $is_management) {
            $this->db->where('reporting_manager', $talent);
        }
        $this->db->where('is_resigned', $isResigned);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getSubordinateswithShowInReport($talent, $is_management) {

        $this->db->select('t.id,concat(first_name," ",middle_name," ",last_name) as name');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->join('role_stream rs', 'rs.id=r.role_stream', 'left');
        $this->db->where('show_in_reports', 1);
        if (false === $is_management) {
            $this->db->where('reporting_manager', $talent);
        }
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getUserByTalentId($id) {
        $this->db->select('t.user');
        $this->db->from('talents t');

        $this->db->where('t.id', $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->user;
    }

    function getTalentEmail($id) {
        $this->db->select('t.email');
        $this->db->from('talents t');

        $this->db->where('t.id', $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->email;
    }

    /*
     * controller talentAttendanceDateWise
     */

    function getTalentJoinedBefore($year, $month, $talent, $is_management) {
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        $endDayOfMonth = $this->defaultValues->getValue(13);
        $this->db->select('t.id,concat(first_name," ",middle_name," ",last_name) as name,is_resigned,date_of_resignation');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->join('role_stream rs', 'rs.id=r.role_stream', 'left');
        $this->db->where('show_in_reports', 1);
        $this->db->where('date_of_joining<=', $year . '-' . $month . '-' . $endDayOfMonth);
        if (false === $is_management) {
            $this->db->where('reporting_manager', $talent);
        }
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentsByDateOfJoining($dateTo, $talentsProcessDoneList) {

        $this->db->select("talents.id,talent_code,concat(first_name,' ',middle_name,' ' ,last_name) as name,average_working_hour_per_day,"
                . "average_hour_calculation,role.role_stream,rs.monthly_process,date_of_joining");
        $this->db->from('talents');
        $this->db->join('role', 'talents.role=role.id', 'left');
        $this->db->join('role_stream rs', 'role.role_stream=rs.id', 'left');
        $this->db->where('date_of_joining<=', $dateTo);
        $this->db->where('is_resigned', 0);
        if (!empty($talentsProcessDoneList)) {
            $this->db->where_not_in('talents.id', $talentsProcessDoneList);
        }
        $this->db->order_by('talents.id', 'asc');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    function getTalentDetailsForMonthlyProcess($talent) {

        $this->db->select("talents.id,talent_code,concat(first_name,' ',middle_name,' ' ,last_name) as name,average_working_hour_per_day,"
                . "average_hour_calculation,role.role_stream,rs.monthly_process,date_of_joining,date_of_resignation");
        $this->db->from('talents');
        $this->db->join('role', 'talents.role=role.id', 'left');
        $this->db->join('role_stream rs', 'role.role_stream=rs.id', 'left');
        $this->db->where('talents.id', $talent);
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

    public function getTalentsMonthlyProcessAlreadyDone($month, $year) {
        $this->db->select('t.talent_id');
        $this->db->from('talent_monthly_report t');
        $this->db->where('t.month', $month);
        $this->db->where('t.year', $year);
        $query = $this->db->get();
        $result = $query->result_array();

        foreach ($result as $res) {
            $talentIdArray[] = $res['talent_id'];
        }
        return $talentIdArray;
    }

    public function getTalentsYearEndProcessAlreadyDone($financialYear) {
        $this->db->select('t.talent');
        $this->db->from('talent_yearly_report t');
        $this->db->where('t.financial_year', $financialYear);
        $query = $this->db->get();
        $result = $query->result_array();

        foreach ($result as $res) {
            $talentIdArray[] = $res['talent'];
        }
        return $talentIdArray;
    }

    public function checkCurrentDayIsLop($talent, $date) {
        $this->db->select('tld.*');
        $this->db->from('talent_leave_date tld');
        $this->db->join('talent_leave tl', 'tld.talent_leave=tl.id', 'left');
        $this->db->where('tld.date_of_leave', $date);
        $this->db->where('tl.talent', $talent);
        $this->db->where('tl.is_approved', 1);
        $query = $this->db->get();
        $data = $query->row();
        $result = $data->leave_type == 0 ? true : false;
        return $result;
    }

    public function attendanceOfTalentForDate($talent, $date) {
        $this->db->select('*');
        $this->db->from('talent_attendance');
        $this->db->where('attendance_date', $date);
        $this->db->where('talent_id', $talent);
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

    public function getAttendandanceUploadedCheck($date) {
        $this->db->select('*');
        $this->db->from('talents_attendance_sheet');
        $this->db->where('attendance_date', $date);
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

    public function checkMonthlyProcessDoneForTheTalent($talent, $month, $year) {
        $this->db->select('*');
        $this->db->from('talent_monthly_report');
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where('talent_id', $talent);
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

    public function checkYearlyProcessDoneForTheTalent($talent, $year) {
        $this->db->select('*');
        $this->db->from('talent_yearly_report');
        $this->db->where('financial_year', $year);
        $this->db->where('talent', $talent);
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

    public function getStartAndEndDateToFindReport($talent, $dateOfResign) {
        $day = date('d', strtotime($dateOfResign));
        $month = date('m', strtotime($dateOfResign));
        $year = date('Y', strtotime($dateOfResign));
        $endDayOfMonth = $this->defaultValues->getValue(13);
        $startDayOfMonth = $this->defaultValues->getValue(12);
        //check crone job is done for current month
        $monthlyReportDoneCheck = $this->talentAttendance->getMonthlyReportResult($month, $year);
        $startDateOfReport = '';
        if ($monthlyReportDoneCheck == true) {
            if ($day > $endDayOfMonth) {
                $startDateOfReport = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . $startDayOfMonth)));
            } else {
                $startDateOfReport = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . $startDayOfMonth) . " -1 month"));
            }
        } else {
            $startDateOfReport = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . $startDayOfMonth) . " -1 month"));
        }
        return $startDateOfReport;
    }

    public function getManagersEmails() {
        $roleStreamId = $this->defaultValues->getValue(28);
        $this->db->select('t.email');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->where('r.role_stream', $roleStreamId);
        $this->db->where('t.is_resigned', 0);
        $query = $this->db->get();
        $result = $query->result_array();
        $emails = '';
        foreach ($result as $eachEmail) {
            $emails .= ',' . $eachEmail['email'];
        }
        return $emails;
    }

    public function getCompanies() {
        $this->db->select('*');
        $this->db->from('company');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getInteractionTypes() {
        $this->db->select('*');
        $this->db->from('interaction_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getReportingManagerAll() {
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 't.role,t.address,t.qualification,t.gender,t.dob,t.mobile,t.email,t.date_of_joining,'
                . 't.emergency_contact_name,t.emergency_contact_relationship,t.emergency_contact_relationship,'
                . 't.date_of_resignation,t.is_resigned,t.reporting_manager,r.role_name,r.role_stream');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->order_by('t.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getInteractionsAll($talentIn, $typeIn, $managerIn, $fromDate, $toDate) {
        $this->db->select('i.id, i.from_date, concat(tn.first_name," ",tn.middle_name," ",tn.last_name) as talent, t.name as type, i.description, concat(rm.first_name," ",rm.middle_name," ",rm.last_name) as reporting_manager');
        $this->db->from('interaction_records i');
        $this->db->join('interaction_type t', 't.id=i.interaction_type', 'left');
        $this->db->join('talents tn', 'tn.id=i.talent', 'left');
        $this->db->join('talents rm', 'rm.id=i.reporting_manager', 'left');
        $this->db->where("i.from_date BETWEEN '$fromDate' AND '$toDate'");
        if ($talentIn != "") {
            $this->db->where('i.talent', $talentIn);
        }
        if ($typeIn != "") {
            $this->db->where('i.interaction_type', $typeIn);
        }
        if ($managerIn != "") {
            $this->db->where('i.reporting_manager', $managerIn);
        }
        $this->db->order_by('i.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getInteractionById($id) {
        $this->db->select('i.*');
        $this->db->from('interaction_records i');
        $this->db->where('i.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

}
