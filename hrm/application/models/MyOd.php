<?php

Class MyOd extends CI_Model {
    /*
     * controler : MyOds
     */

    function getMyOds($talent_id) {
        $this->db->select("tod.id,tod.od_type,tod.reason,tod.is_approved,odt.od_type_name,"
                . "DATE_FORMAT(tod.from_date,'%d/%m/%Y') AS from_date,"
                . "DATE_FORMAT(tod.to_date,'%d/%m/%Y') AS to_date,tod.decline_note");
        $this->db->from('talent_od tod');
        $this->db->join('od_types odt', 'tod.od_type=odt.id', 'left');
        $this->db->where('tod.is_deleted', '0');
        $this->db->where('talent', $talent_id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getMyOdTypeById($id) {
        $this->db->select('tod.id,tod.od_type,tod.reason,tod.is_approved,odt.od_type_name,tod.from_date,tod.to_date,tod.talent');
        $this->db->from('talent_od tod');
        $this->db->join('od_types odt', 'tod.od_type=odt.id', 'left');
        $this->db->where('tod.is_deleted', '0');
        $this->db->where('tod.id', $id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getMyOdSpecificByMyOdId($myOdId) {
        $this->db->select('*');
        $this->db->from('talent_od_specific');
        $this->db->where('talent_od_specific.talent_od', $myOdId);
        $this->db->order_by('date_of_od', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentByOdId($odId) {
        $this->db->select('talent');
        $this->db->from('talent_od');
        $this->db->where('id', $odId);
        $query = $this->db->get();
        $row = $query->row();
        if ($row) {
            return $row->talent;
        } else {
            return;
        }
    }

    /*
     * Controller : OdWaitingForApproval
     */

    function getOdwaitingForApproval($talent) {
        $this->db->select("tod.id,tod.od_type,tod.reason,tod.is_approved,"
                . "odt.od_type_name,DATE_FORMAT(tod.from_date,'%d/%m/%Y') AS from_date,"
                . "DATE_FORMAT(tod.to_date,'%d/%m/%Y') AS to_date,tod.decline_note,"
                . "concat(ts.first_name,' ',ts.middle_name,' ',ts.last_name) as name,");
        $this->db->from('talent_od tod');
        $this->db->join('od_types odt', 'tod.od_type=odt.id', 'left');
        $this->db->join('talents ts', 'ts.id=tod.talent', 'left');
        $this->db->where('ts.reporting_manager', $talent);
        $this->db->order_by('tod.is_approved', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Controller : OdWaitingForApproval
     */

    function getOdwaitingForApprovalFinYearBased($talent, $year, $isResigned, $selectedTalent) {
        $this->db->select("tod.id,tod.od_type,tod.reason,tod.is_approved,ts.id as talent,"
                . "odt.od_type_name,DATE_FORMAT(tod.from_date,'%d/%m/%Y') AS from_date,"
                . "DATE_FORMAT(tod.to_date,'%d/%m/%Y') AS to_date,tod.decline_note,"
                . "concat(ts.first_name,' ',ts.middle_name,' ',ts.last_name) as name,");
        $this->db->from('talent_od tod');
        $this->db->join('od_types odt', 'tod.od_type=odt.id', 'left');
        $this->db->join('talents ts', 'ts.id=tod.talent', 'left');
        $this->db->where('ts.reporting_manager', $talent);
        if ($selectedTalent) {
            $this->db->where('ts.id', $selectedTalent);
        }
        $this->db->where('ts.is_resigned', $isResigned);
        $this->db->where('((YEAR(tod.from_date) = ' . ($year) . ' AND MONTH(tod.from_date) > 3) '
                . 'OR (YEAR(tod.from_date) = ' . ($year + 1) . ' AND MONTH(tod.from_date) < 4)) ');
        $this->db->order_by('tod.is_approved asc,tod.from_date desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Controller : OdWaitingForApproval
     */

    function getOdwaitingForApprovalFinYearBasedAll($talent, $year, $isResigned, $isManagement, $selectedTalent) {
        $this->db->select("tod.id,tod.od_type,tod.reason,tod.is_approved,ts.id as talent,"
                . "odt.od_type_name,DATE_FORMAT(tod.from_date,'%d/%m/%Y') AS from_date,"
                . "DATE_FORMAT(tod.to_date,'%d/%m/%Y') AS to_date,tod.decline_note,"
                . "concat(ts.first_name,' ',ts.middle_name,' ',ts.last_name) as name,");
        $this->db->from('talent_od tod');
        $this->db->join('od_types odt', 'tod.od_type=odt.id', 'left');
        $this->db->join('talents ts', 'ts.id=tod.talent', 'left');
        if (1 != $isManagement) {
            $this->db->where('ts.reporting_manager', $talent);
        }
        if ($selectedTalent) {
            $this->db->where('ts.id', $selectedTalent);
        }
        $this->db->where('ts.is_resigned', $isResigned);
        $this->db->where('((YEAR(tod.from_date) = ' . ($year) . ' AND MONTH(tod.from_date) > 3) '
                . 'OR (YEAR(tod.from_date) = ' . ($year + 1) . ' AND MONTH(tod.from_date) < 4)) ');
        $this->db->order_by('tod.is_approved asc,tod.from_date desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler :talentMonthlyReport
     */

    function getOdDayStatus($talent_id, $fromDate, $toDate) {
        $this->db->select('tos.date_of_od,tos.hours_of_od,is_approved');
        $this->db->from('talent_od_specific tos');
        $this->db->join('talent_od to', 'to.id=tos.talent_od', 'left');
        $this->db->where('to.talent', $talent_id);
        $this->db->where('tos.date_of_od BETWEEN "' . date('Y-m-d', strtotime($fromDate)) . '" and "' . date('Y-m-d', strtotime($toDate)) . '"');
        $this->db->order_by('tos.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getOdDayStatusWithoutCurrentOd($talent_id, $fromDate, $toDate, $myOdId) {
        $this->db->select('tos.date_of_od,tos.hours_of_od,tos.id');
        $this->db->from('talent_od_specific tos');
        $this->db->join('talent_od to', 'to.id=tos.talent_od', 'left');
        $this->db->where('to.talent', $talent_id);
        $this->db->where('to.id !=', $myOdId);
        $this->db->where('tos.date_of_od BETWEEN "' . date('Y-m-d', strtotime($fromDate)) . '" and "' . date('Y-m-d', strtotime($toDate)) . '"');
        $this->db->order_by('tos.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getEeachOdDates($odId) {
        $this->db->select('tos.date_of_od,tos.hours_of_od,to.talent');
        $this->db->from('talent_od_specific tos');
        $this->db->join('talent_od to', 'to.id=tos.talent_od', 'left');
        $this->db->where('talent_od', $odId);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller: talents/checkResignIssues
     */

    function getCountOdWaitingTalent($talent) {
        $this->db->select('tod.id');
        $this->db->from('talent_od tod');
        $this->db->join('talents ts', 'ts.id=tod.talent', 'left');
        $this->db->where('ts.id', $talent);
        $this->db->where('tod.is_approved', 0);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * check od is approved
     */

    function checkOdIsApproved($id) {
        $this->db->select('tod.id');
        $this->db->from('talent_od tod');
        $this->db->where('tod.is_approved', 0);
        $this->db->where('tod.id', $id);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }

    function getCountOdWaiting($talent) {
        $this->db->select('tod.id');
        $this->db->from('talent_od tod');
        $this->db->join('od_types odt', 'tod.od_type=odt.id', 'left');
        $this->db->join('talents ts', 'ts.id=tod.talent', 'left');
        $this->db->where('ts.reporting_manager', $talent);
        $this->db->where('tod.is_approved', 0);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getPendingODApprovals() {

        $this->db->select('t.first_name,t.middle_name,t.last_name,tr.first_name as tr_first_name,tr.middle_name as tr_middle_name ,tr.last_name as tr_last_name,date_format(tl.from_date,"%d/%m/%Y") as from_date_f,date_format(tl.to_date,"%d/%m/%Y") as to_date_f,tl.reason');
        $this->db->from('talent_od tl ');
        $this->db->join('talents t ', 't.id=tl.talent', 'left');
        $this->db->join('talents tr ', 'tr.id=t.reporting_manager', 'left');
        $this->db->where('tl.is_approved =', 0);
        $this->db->order_by('tl.is_approved', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    function getOdAutoApprovalStatus($talentId) {

        $this->db->select('od_approval_auto');
        $this->db->from('talents t');
        $this->db->where('id', $talentId);
        $query = $this->db->get();
        $result = $query->row();
        if ($result->od_approval_auto == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getStatusOdWaitingForApprovalBetweenDates($startDate, $endDate) {
        $query = $this->db->query("SELECT `tos`.`talent_od` "
                . "FROM `talent_od_specific` tos "
                . "JOIN `talent_od` `to` ON `to`.`id`=`tos`.`talent_od` "
                . "WHERE `tos`.`date_of_od` BETWEEN '$startDate' AND '$endDate' "
                . "AND `to`.`is_approved`='0' "
                . "GROUP BY `tos`.`talent_od` ");
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getReportingOdWaitingForApprovalBetweenDates($startDate, $endDate) {
        $query = $this->db->query("SELECT `r`.`email` "
                . "FROM `talent_od_specific` tos "
                . "JOIN `talent_od` `to` ON `to`.`id`=`tos`.`talent_od` "
                . "JOIN `talents` `t` ON `to`.`talent`=`t`.`id` "
                . "JOIN `talents` `r` ON `t`.`reporting_manager`=`r`.`id` "
                . "WHERE `tos`.`date_of_od` BETWEEN '$startDate' AND '$endDate' "
                . "AND `to`.`is_approved`='0' "
                . "GROUP BY `r`.`reporting_manager` ");
        return $query->result_array();
    }

    function findOdDetailsByOdIdAndDate($dateCount, $myOdId) {
        $dateCount = date('Y-m-d', strtotime($dateCount));
        $this->db->select('hours_of_od');
        $this->db->from('talent_od_specific');
        $this->db->where('talent_od', $myOdId);
        $this->db->where('date_of_od', $dateCount);
        $query = $this->db->get();
        return $query->row()->hours_of_od;
    }

    function getOdDetailsBYOdId($id) {
        $this->db->select('*');
        $this->db->from('talent_od');
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row();
    }

}
