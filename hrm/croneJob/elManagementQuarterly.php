<?php

include 'database.php';
//$con = mysql_connect('localhost', 'hrm_123', 'hrm_pass');
//$con = mysql_connect('192.168.0.100', 'hrm', 'hrmpass');
//--------------connecting db-------------//
//$db = mysql_select_db("123_hrm");
//$db = mysql_select_db("hrm_14_02_2017");
$date = date('Y-m-d');
$sql = "select id,start_month From master_quarters Where '$date' BETWEEN start_month AND end_month";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$quarter = $row['id'];
$quarterFromDate = $row['start_month'];
/*
 * This script should be run Just before starting a new quarter 
 *  And after the monthly quarter script run 
 * 
 * Eg: For first quarter run before march 31 but after monthly quarter of march.  
 */
if ($quarter <= 4) {

    $sql = "SELECT `id`,`role` FROM `talents` WHERE `is_resigned`='0' and date_of_joining <'" . $quarterFromDate . "'";

    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {

        $talent_id = $row['id'];
        $talent_role = $row['role'];

        /* Allotted EL Quaata for Every quarter for the role */
        $sql_quat_el = mysql_query("SELECT quarterly_earned_leaves from role where id='" . $talent_role . "' ");
        $row_quat_el = mysql_fetch_array($sql_quat_el);
        $quarterly_el = $row_quat_el['quarterly_earned_leaves'];
        if ($quarter == 4) {
            //for setting fourth quarter el of foxgreen because they have a change in calculaton
            if (getDefaultValue(35) != 0) {
                $quarterly_el = getDefaultValue(35);
            }
        }

        if (in_array($talent_id, [1, 2])) {
            $quarterly_el = 0;
        }


        $talent_el = 0;
        $talent_compoff = 0;

        $el_encashment = 0;
        $comp_off_encashment = 0;


        $sql_leave = mysql_query("SELECT `leave_left`,`leave_type` FROM `talent_leave_left` WHERE `leave_type` IN (1,2) AND `talent`='" . $talent_id . "' ");

        while ($row_leave = mysql_fetch_array($sql_leave)) {
            if (1 == $row_leave['leave_type']) { // Compoff
                $talent_compoff = $row_leave['leave_left'];
            }
            if (2 == $row_leave['leave_type']) { // Earned Leave 
                $talent_el = $row_leave['leave_left'];
            }
        }
        if (1 == $quarter) {

            /*
             * If EL for new year is exceeds 30 Then fixes to 30 
             * Remaining days is go for encashment
             *  
             */

            if ($talent_el > 30) {
                $el_encashment = $talent_el - 30;
                $talent_el_next_q = 15;
                //$el_encashment += 7.5;
            } else {
                $talent_el_next_q = round($talent_el / 2);
//                $el_encashment = ($talent_el - $talent_el_next_q) / 2;
            }


            /* EL quata of new quarter plus half of EL of current year */
            $talent_el_next_q = $talent_el_next_q + $quarterly_el;

            /* For the Lat Quarter Compoff will clear and go for encashment */
//            $comp_off_encashment = $talent_compoff / 2;
            $comp_off_encashment = $talent_compoff;
            $talent_compoff = 0;

            //for foxgreen no encashment is provided
            if (getDefaultValue(36) == 1) {
                $el_encashment = $talent_el;
                $talent_el_next_q = $quarterly_el;
                $comp_off_encashment = 0;
            }
            $sql_yearly = "INSERT into  `talent_yearly_report`  "
                    . "(`talent`,`year`,`el_encashment`,`compoff_encashment`) "
                    . "values ('" . $talent_id . "','" . date('Y', strtotime('-1 year')) . "','" . $el_encashment . "','" . $comp_off_encashment . "') ";
            mysql_query($sql_yearly);
        } else { // Otherthan final quarter
            $talent_el_next_q = $talent_el + $quarterly_el;
        }
        $sql_el = "UPDATE `talent_leave_left` SET `leave_left`='" . $talent_el_next_q . "' WHERE `talent`='" . $talent_id . "' AND `leave_type`='2'";
        $sql_compoff = "UPDATE `talent_leave_left` SET `leave_left`='" . $talent_compoff . "' WHERE `talent`='" . $talent_id . "' AND `leave_type`='1'";

        mysql_query($sql_compoff);
        mysql_query($sql_el);

        //inserts credict details of el

        $sql_credit = "INSERT INTO `talent_el`(`credicted_on`, `count`, `talent`) "
                . "VALUES ('" . date('Y-m-d') . "','$quarterly_el','$talent_id')";
        mysql_query($sql_credit);


        /* Leave Process Leave with considered = 0   */
        /* Deduct Leave then LOP as per leave application   */
    }
    if (1 == $quarter) {
        //update new financial year in table
        $current_year = date('Y');
        $next_year = (date('Y') + 1);
        $financial_year = $current_year . '-' . $next_year;
        $start_date = date('Y-m-d', strtotime($current_year . '-04-01'));
        $end_date = date('Y-m-d', strtotime($next_year . '-03-31'));
        $sql_insert_financial_year = "INSERT INTO `master_financial_year`(`financial_year`,"
                . "`from_date`,`to_date`)VALUES('$financial_year','$start_date','$end_date')";
        $result_insert = mysql_query($sql_insert_financial_year);
        $id_financial_year = mysql_insert_id();

        $sql_update_financial_year = "UPDATE `default_values` SET value='$id_financial_year' WHERE `id`='5'";
        mysql_query($sql_update_financial_year);

        /* UPDATE NEW DATES ON master_quarter */
        //update master quarters master_quarters

        $sql_update_master_quarter = "UPDATE `master_quarters` "
                . "SET start_month = DATE_ADD(start_month, INTERVAL 1 YEAR),end_month = DATE_ADD(end_month, INTERVAL 1 YEAR)";
        mysql_query($sql_update_master_quarter);
    }
}
leaveManagement();

function leaveManagement() {
    $sql = "SELECT * FROM `talent_leave` WHERE considered=0 AND is_approved in(0,1)";
    $resulthh = mysql_query($sql);
    while ($row = mysql_fetch_array($resulthh)) {
        $talent = $row['talent'];
        $fromDate = $row['from_date'];
        $toDate = $row['to_date'];
        $insert_id = $row['id'];
        $approved = $row['is_approved'];

        $talentWorking = getWorkingDayStatus($talent, $fromDate, $toDate);
        //get leave status
        $talentLeaves = getLeaveDayStatus($talent, $fromDate, $toDate);
        //finding holidays and leaves between the date
        $holidayArray = getHolidayStatus($fromDate, $toDate);
        //weekends
        $weekendArray = getHolidayTalent($talent);

        $count = 0;
        $compoff_count = 0;
        $el_count = 0;
        $lop = 0;
        $comp_left = leave_left(1, $talent);
        if ($talent == 6) {
            echo $comp_left . '<br/>';
        }
        $compoff = $comp_left;
        $el_left = leave_left(2, $talent);
        $el = $el_left;
        $leave_left = $comp_left + $el_left;
        $date_array = createDateRangeArray($fromDate, $toDate);
        foreach ($date_array as $date) {
            $id_to_update = 0;
            $insert = 0;
            $ltype = 0;
            $strToTimeDate = strtotime($date);
            $day = strtolower(date("l", $strToTimeDate));

            if (in_array($date, $talentWorking)) {
                $insert = 0;
            } else if (in_array($date, $talentLeaves)) {
                $insert = 0;
            } else if (in_array($date, $holidayArray)) {
                if ($leave_left < $count) {
                    $lop++;
                    $insert = 1;
                }
            } else if (in_array($day, $weekendArray)) {
                if ($leave_left < $count) {
                    $lop++;
                    $insert = 1;
                }
            } else {
                $count++;
                if ($leave_left < $count) {
                    $lop++;
                } else if ($compoff > 0) {
                    $ltype = 1;
                    $compoff--;
                    $compoff_count++;
                    $sql_select = "select min(id) as id FROM talent_compoff WHERE used=0 AND talent=$talent";
                    $res_select = mysql_query($sql_select);
                    $row_select = mysql_fetch_array($res_select);
                    if ($talent == 6) {
                        echo $compoff_count . '<br/>';
                        echo $sql_select;
                        var_dump($row_select);
                    }
                    $id_to_update = $row_select['id'];
                    $sql_upd = "UPDATE talent_compoff SET used=1 WHERE id=$id_to_update";
                    mysql_query($sql_upd);
                } else if ($el > 0) {
                    $ltype = 2;
                    $el--;
                    $el_count++;
                }
                $insert = 1;
            }
            if ($insert == 1) {
                $sql_ins = "INSERT INTO `talent_leave_date`
(`talent_leave`, `date_of_leave`, `leave_type`, `is_approved`,compoff_id)
 VALUES
('$insert_id',	'$date',	'$ltype',	'$approved','$id_to_update')";
                mysql_query($sql_ins);
                if ($talent == 6) {
                    echo $sql_ins;
                }
            }
        }
        if ($compoff_count > 0) {
            $insert_compoff = $comp_left - $compoff_count;
            $sql_upd = "UPDATE talent_leave_left SET leave_left='$insert_compoff' WHERE leave_type=1 AND talent=$talent  ";
            mysql_query($sql_upd);
        }
        if ($el_count > 0) {
            $insert_el = $el_left - $el_count;
            $sql_upd = "UPDATE talent_leave_left SET leave_left='$insert_el' WHERE leave_type=2 AND talent=$talent  ";
            mysql_query($sql_upd);
        }
        $sql_upd = "UPDATE talent_leave SET lop='$lop' WHERE id=$insert_id  ";
        mysql_query($sql_upd);

        $sql_check = "select lop FROM talent_lop WHERE talent=$talent";
        $result2 = mysql_query($sql_check);
        $CheckMyLopsLeft = mysql_num_rows($result2);
        if ($CheckMyLopsLeft > 0) {
            $sql_upd = "UPDATE talent_lop SET lop=lop+'$lop' WHERE talent=$talent  ";
            mysql_query($sql_upd);
        } else {
            $sql_upd = "INSERT INTO `talent_lop` (`talent`, `lop`) VALUES
($talent,	$lop)";
            mysql_query($sql_upd);
        }
        $sql_upd = "UPDATE talent_leave SET considered=1 WHERE id=$insert_id  ";
        mysql_query($sql_upd);
    }
}

function createDateRangeArray($strDateFrom, $strDateTo) {
    $aryRange = array();

    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return $aryRange;
}

function leave_left($type, $talent) {
    echo $sql = "SELECT leave_left FROM talent_leave_left WHERE talent='$talent' AND leave_type='$type'";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row['leave_left'];
}

function getWorkingDayStatus($talent_id, $fromDate, $toDate) {
    $sql = "SELECT attendance_date FROM talent_attendance WHERE talent_id='$talent_id' AND avg_consider='1'
AND attendance_date BETWEEN " . date('Y-m-d', strtotime($fromDate)) . " AND " . date('Y-m-d', strtotime($toDate)) . " ";
    $result = mysql_query($sql);
    $attendance_array = array();
    while ($row = mysql_fetch_array($result)) {
        $attendance_array[] = $row['attendance_date'];
    }
    return $attendance_array;
}

function getLeaveDayStatus($talent_id, $fromDate, $toDate) {
    $sql = "SELECT t.date_of_leave FROM talent_leave_date t INNER JOIN talent_leave tl ON(tl.id=t.talent_leave)
  WHERE tl.talent='$talent_id' AND t.is_approved in('0', '1')
AND t.date_of_leave  BETWEEN " . date('Y-m-d', strtotime($fromDate)) . " AND " . date('Y-m-d', strtotime($toDate)) . " ";
    $result = mysql_query($sql);
    $attendance_array = array();
    while ($row = mysql_fetch_array($result)) {
        $attendance_array[] = $row['date_of_leave'];
    }
    return $attendance_array;
}

function getHolidayStatus($fromDate, $toDate) {
    $sql = "SELECT date_of_holiday FROM holidays
  WHERE is_deleted='0'
AND date_of_holiday  BETWEEN " . date('Y-m-d', strtotime($fromDate)) . " AND " . date('Y-m-d', strtotime($toDate)) . " ";
    $result = mysql_query($sql);
    $attendance_array = array();
    while ($row = mysql_fetch_array($result)) {
        $attendance_array[] = $row['date_of_holiday'];
    }
    return $attendance_array;
}

function getHolidayTalent($talent) {

    $sql = "SELECT m.week_day FROM talents t LEFT JOIN role_holiday r ON(t.role=r.role)
LEFT JOIN master_week_days m ON(m.id=r.week_day)
  WHERE t.id='$talent' ";
    $result = mysql_query($sql);
    $attendance_array = array();
    while ($row = mysql_fetch_array($result)) {
        $strToTimeDate = strtotime($row['week_day']);
        $day = strtolower(date("l", $strToTimeDate));
        $attendance_array[] = $day;
    }
    return $attendance_array;
}

function getDefaultValue($id) {
    $sqlDefaultValue = "SELECT `value` FROM `default_values` WHERE `id`='$id'";
    $resultDefaultValue = mysql_query($sqlDefaultValue);
    $rowDefaultValue = mysql_fetch_array($resultDefaultValue);
    return $rowDefaultValue['value'];
}

?>
